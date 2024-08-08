class EventSmallpoxInfectedBlanketsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: EventSmallpoxInfectedBlanketsStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: EventSmallpoxInfectedBlanketsStateArgs) {
    debug('Entering EventSmallpoxInfectedBlanketsState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving EventSmallpoxInfectedBlanketsState');
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
    const remaining = 2 - this.selectedUnits.length;
    if (remaining === 0) {
      this.updateInterfaceConfirm();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _(
        '${you} must select a French-controlled Indian unit (${number} remaining)'
      ),
      args: {
        you: '${you}',
        number: remaining,
      },
    });

    this.setUnitsSelectable(this.args.units);
    this.setUnitsSelected();

    if (this.selectedUnits.length === 0) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.game.addCancelButton();
    }
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    const text = _('Place ${unitsLog} on the Losses Box?');
    this.game.clientUpdatePageTitle({
      text,
      args: {
        unitsLog: createUnitsLog(this.selectedUnits),
      },
    });
    this.selectedUnits.forEach(({ id }) => this.game.setUnitSelected({ id }));

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actEventSmallpoxInfectedBlankets',
        args: {
          selectedUnitIds: this.selectedUnits.map(({ id }) => id),
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

  private setUnitsSelectable(units: BTUnit[]) {
    units.forEach((unit) => {
      this.game.openUnitStack(unit);
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
          this.updateInterfaceInitialStep();
        },
      });
    });
  }

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
}
