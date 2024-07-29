class FleetsArriveVagariesOfWarState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringFleetsArriveVagariesOfWarStateArgs;
  private selectedUnitIds: string[] = [];
  private selectedVoWToken: string = null;

  private vowTokenNumberOfUnitsMap = {
    [VOW_PICK_ONE_ARTILLERY_FRENCH]: 1,
    [VOW_PICK_TWO_ARTILLERY_BRITISH]: 2,
    [VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH]: 2,
  };

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringFleetsArriveVagariesOfWarStateArgs) {
    debug('Entering FleetsArriveVagariesOfWarState');
    this.args = args;
    this.selectedUnitIds = [];
    this.selectedVoWToken = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving FleetsArriveVagariesOfWarState');
  }

  setDescription(activePlayerId: number) {}

  //  .####.##....##.########.########.########..########....###.....######..########
  //  ..##..###...##....##....##.......##.....##.##.........##.##...##....##.##......
  //  ..##..####..##....##....##.......##.....##.##........##...##..##.......##......
  //  ..##..##.##.##....##....######...########..######...##.....##.##.......######..
  //  ..##..##..####....##....##.......##...##...##.......#########.##.......##......
  //  ..##..##...###....##....##.......##....##..##.......##.....##.##....##.##......
  //  .####.##....##....##....########.##.....##.##.......##.....##..######..########

  // ..######..########.########.########...######.
  // .##....##....##....##.......##.....##.##....##
  // .##..........##....##.......##.....##.##......
  // ..######.....##....######...########...######.
  // .......##....##....##.......##..............##
  // .##....##....##....##.......##........##....##
  // ..######.....##....########.##.........######.

  private updateInterfaceInitialStep() {
    if (Object.keys(this.args.options).length === 1) {
      this.selectedVoWToken = Object.keys(this.args.options)[0];
      this.updateInterfaceSelectUnits();
      return;
    }

    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Vagaries of War token to resolve'),
      args: {
        you: '${you}',
      },
    });

    Object.keys(this.args.options).forEach((counterId) => {
      this.game.addSecondaryActionButton({
        id: `${counterId}_btn`,
        text: this.game.format_string_recursive('${tkn_unit}', {
          tkn_unit: counterId,
        }),
        callback: () => {
          this.selectedVoWToken = counterId;
          this.updateInterfaceSelectUnits();
        },
      });
    });

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectUnits() {
    const numberOfUnitsToSelect =
      this.vowTokenNumberOfUnitsMap[this.selectedVoWToken];

    if (this.selectedUnitIds.length === numberOfUnitsToSelect) {
      this.updateInterfaceConfirm();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a unit (${number} remaining)'),
      args: {
        you: '${you}',
        number: numberOfUnitsToSelect - this.selectedUnitIds.length,
      },
    });
    this.selectedUnitIds.forEach((id) => this.game.setUnitSelected({ id }));

    this.args.options[this.selectedVoWToken].forEach((unit) =>
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          if (this.selectedUnitIds.includes(unit.id)) {
            this.selectedUnitIds = this.selectedUnitIds.filter(
              (unitId) => unitId !== unit.id
            );
          } else {
            this.selectedUnitIds.push(unit.id);
          }
          this.updateInterfaceSelectUnits();
        },
      })
    );

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Pick ${unitsLog} ?'),
      args: {
        unitsLog: this.createUnitsLog(
          this.args.options[this.selectedVoWToken].filter((unit) =>
            this.selectedUnitIds.includes(unit.id)
          )
        ),
      },
    });
    this.selectedUnitIds.forEach((id) => this.game.setUnitSelected({ id }));

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actFleetsArriveVagariesOfWar',
        args: {
          vowTokenId: this.selectedVoWToken,
          selectedUnitIds: this.selectedUnitIds,
        },
      });
    };

    if (
      this.game.settings.get({
        id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
      }) === PREF_ENABLED
    ) {
      callback();
    } else {
      this.game.addConfirmButton({
        callback,
      });
    }
    this.game.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private createUnitsLog(units: BTUnit[]) {
    let unitsLog = '';
    const unitsLogArgs = {};

    units.forEach((unit, index) => {
      const key = `tkn_unit_${index}`;
      unitsLog += '${' + key + '}';
      unitsLogArgs[key] = unit.counterId;
    });

    return {
      log: unitsLog,
      args: unitsLogArgs,
    };
  }

  //  ..######..##.......####..######..##....##
  //  .##....##.##........##..##....##.##...##.
  //  .##.......##........##..##.......##..##..
  //  .##.......##........##..##.......#####...
  //  .##.......##........##..##.......##..##..
  //  .##....##.##........##..##....##.##...##.
  //  ..######..########.####..######..##....##

  // .##.....##....###....##....##.########..##.......########..######.
  // .##.....##...##.##...###...##.##.....##.##.......##.......##....##
  // .##.....##..##...##..####..##.##.....##.##.......##.......##......
  // .#########.##.....##.##.##.##.##.....##.##.......######....######.
  // .##.....##.#########.##..####.##.....##.##.......##.............##
  // .##.....##.##.....##.##...###.##.....##.##.......##.......##....##
  // .##.....##.##.....##.##....##.########..########.########..######.
}
