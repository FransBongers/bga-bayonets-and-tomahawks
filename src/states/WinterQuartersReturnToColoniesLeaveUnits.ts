class WinterQuartersReturnToColoniesLeaveUnitsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersReturnToColoniesLeaveUnitsStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(
    args: OnEnteringWinterQuartersReturnToColoniesLeaveUnitsStateArgs
  ) {
    debug('Entering WinterQuartersReturnToColoniesLeaveUnitsState');
    this.args = args;
    this.selectedUnits = [];
    // this.selectedOption = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersReturnToColoniesLeaveUnitsState');
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
    const unitsThatCanBeSelected = this.getUnitsThatCanBeSelected();
    if (
      unitsThatCanBeSelected.length === 0 ||
      (this.args.maxTotal !== null &&
        this.selectedUnits.length === this.args.maxTotal)
    ) {
      this.updateInterfaceConfirm();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text:
        this.args.maxTotal === 1
          ? _('${you} may select a unit to remain on ${spaceName}')
          : _('${you} may select units to remain on ${spaceName}'),
      args: {
        you: '${you}',
        spaceName: _(this.args.space.name),
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.space.id][this.args.faction];
    stack.open();

    this.setUnitsSelectable(unitsThatCanBeSelected);
    this.game.setElementsSelected(this.selectedUnits);

    this.game.addPrimaryActionButton({
      id: 'done_btn',
      text: _('Done'),
      callback: () => this.updateInterfaceConfirm(),
      extraClasses: this.selectedUnits.length === 0 ? DISABLED : '',
    });

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

    this.game.clientUpdatePageTitle({
      text:
        this.selectedUnits.length === 1
          ? _('Leave ${tkn_unit} on ${spaceName}?')
          : _('Leave selected units on ${spaceName}?'),
      args: {
        tkn_unit: this.selectedUnits[0].counterId,
        spaceName: _(this.args.space.name),
      },
    });
    this.game.setElementsSelected(this.selectedUnits);

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actWinterQuartersReturnToColoniesLeaveUnits',
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

  private getUnitsThatCanBeSelected() {
    const selectedBrigades = this.selectedUnits.filter(
      (unit) => this.game.getUnitStaticData(unit).type === BRIGADE
    ).length;
    return this.args.units.filter((unit) => {
      if (
        this.selectedUnits.some((selectedUnit) => selectedUnit.id === unit.id)
      ) {
        return false;
      }
      if (
        this.game.getUnitStaticData(unit).type === BRIGADE &&
        selectedBrigades === this.args.maxBrigades
      ) {
        return false;
      }
      return true;
    });
  }

  private setUnitsSelectable(selectableUnits: BTUnit[]) {
    selectableUnits.forEach((unit) => {
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
