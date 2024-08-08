class EventDiseaseInBritishCampState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: EventDiseaseInBritishCampStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: EventDiseaseInBritishCampStateArgs) {
    debug('Entering EventDiseaseInBritishCampState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving EventDiseaseInBritishCampState');
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
    const remaining =
      this.args.year <= 1756
        ? 1 - this.selectedUnits.length
        : 2 - this.selectedUnits.length;
    if (remaining === 0) {
      this.updateInterfaceConfirm();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text:
        this.args.year <= 1756
          ? _('${you} must select 1 Brigade')
          : _(
              '${you} must select 1 Colonial Brigade and 1 Metropolitan Brigade (${number} remaining)'
            ),
      args: {
        you: '${you}',
        number: remaining,
      },
    });

    this.setUnitsSelectable();
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

    const text = _('Eliminate ${unitsLog}?');
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
        action: 'actEventDiseaseInBritishCamp',
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

  private setUnitsSelectable() {
    let units = [];
    if (this.args.year <= 1756) {
      units = this.args.brigades;
    } else {
      const colonyBrigadeSelected = this.selectedUnits.some(
        (unit) => !!this.game.gamedatas.staticData.units[unit.counterId].colony
      );
      const metropolitanBrigadeSelected = this.selectedUnits.some(
        (unit) =>
          !!this.game.gamedatas.staticData.units[unit.counterId].metropolitan
      );
      if (!colonyBrigadeSelected) {
        units = this.args.colonialBrigades;
      }
      if (!metropolitanBrigadeSelected) {
        units = units.concat(this.args.metropolitanBrigades);
      }
    }

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
