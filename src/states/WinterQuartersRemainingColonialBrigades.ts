class WinterQuartersRemainingColonialBrigadesState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersRemainingColonialBrigadesStateArgs;
  private selectedUnits: BTUnit[] = [];
  private selectedOption: WinterQuartersRemainingColonialBrigadesOption;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(
    args: OnEnteringWinterQuartersRemainingColonialBrigadesStateArgs
  ) {
    debug('Entering WinterQuartersRemainingColonialBrigadesState');
    this.args = args;
    this.selectedUnits = [];
    this.selectedOption = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersRemainingColonialBrigadesState');
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
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Space'),
      args: {
        you: '${you}',
      },
    });

    Object.entries(this.args.options).forEach(([spaceId, option]) =>
      this.game.setLocationSelectable({
        id: spaceId,
        callback: () => {
          this.selectedOption = option;
          this.updateInterfaceSelectNumberToRemain();
        },
      })
    );

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectNumberToRemain() {
    if (this.selectedUnits.length === this.selectedOption.maxRemain) {
      this.updateInterfaceConfirm();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _(
        '${you} must select Colonial Brigades to remain on ${spaceName} (max ${maxPossible})'
      ),
      args: {
        you: '${you}',
        maxPossible: this.selectedOption.maxRemain,
        spaceName: _(this.selectedOption.space.name),
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.selectedOption.space.id][BRITISH];
    stack.open();
    
    this.selectedOption.units.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          if (
            this.selectedUnits.some(
              (selectedUnit) => selectedUnit.id === unit.id
            )
          ) {
            this.selectedUnits = this.selectedUnits.filter(
              (selectedUnit) => selectedUnit.id !== unit.id
            );
          } else {
            this.selectedUnits.push(unit);
          }

          this.updateInterfaceSelectNumberToRemain();
        },
      });
    });
    this.setUnitsSelected();

    this.game.addPrimaryActionButton({
      id: 'confirm_btn',
      text: _('Confirm'),
      callback: () => this.performAction(),
    });

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Leave ${unitsLog} on ${spaceName}?'),
      args: {
        spaceName: _(this.selectedOption.space.name),
        unitsLog: createUnitsLog(this.selectedUnits),
      },
    });
    this.setUnitsSelected();
    // this.game.setLocationSelected({ id: spaceId });

    const callback = () => this.performAction();

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

  private setUnitsSelected() {
    this.selectedUnits.forEach(({ id }) => this.game.setUnitSelected({ id }));
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

  private performAction() {
    this.game.clearPossible();
    this.game.takeAction({
      action: 'actWinterQuartersRemainingColonialBrigades',
      args: {
        spaceId: this.selectedOption.space.id,
        selectedUnitIds: this.selectedUnits.map(({ id }) => id),
      },
    });
  }
}
