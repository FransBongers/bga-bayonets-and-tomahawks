class SailMovementState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringSailMovementStateArgs;
  private selectedUnits: BTUnit[] = [];
  private destination: BTSpace = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringSailMovementStateArgs) {
    debug('Entering SailMovementState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving SailMovementState');
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
      text: _('${you} must select units to move to the Sail Box'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.space.id][this.args.faction];
    stack.open();

    this.setUnitsSelectable();
    this.setUnitsSelected();

    if (this.destination !== null) {
      this.game.setLocationSelected({ id: this.destination.id });
    }

    this.game.addPrimaryActionButton({
      id: 'sail_move_btn',
      text: _('Sail Move'),
      callback: () => this.updateInterfaceConfirm(),
      extraClasses: this.isSailMovePossible() ? '' : DISABLED,
    });

    this.game.addSecondaryActionButton({
      id: 'select_all_btn',
      text: _('Select all'),
      callback: () => {
        (this.selectedUnits = this.args.units),
          this.updateInterfaceInitialStep();
      },
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

    // this.game.clientUpdatePageTitle({
    //   text: _('Move selected units to the Sail Box?'),
    //   args: {},
    // });

    // this.game.setLocationSelected({ id: this.destination.id });
    // this.setUnitsSelected();

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actSailMovement',
        args: {
          selectedUnitIds: this.selectedUnits.map(({ id }) => id),
        },
      });
    };

    // if (
    //   this.game.settings.get({
    //     id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
    //   }) === PREF_ENABLED
    // ) {
    callback();
    // } else {
    //   this.game.addConfirmButton({
    //     callback,
    //   });
    // }

    // this.game.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private setUnitsSelectable() {
    this.args.units.forEach((unit) => {
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
    this.selectedUnits.forEach((unit) =>
      this.game.setUnitSelected({ id: unit.id })
    );
  }

  private isSailMovePossible(): boolean {
    const selectedFleets = this.selectedUnits.filter(
      (unit) => this.game.getUnitStaticData(unit).type === FLEET
    ).length;
    const otherUnits = this.selectedUnits.filter(
      (unit) =>
        ![FLEET, COMMANDER].includes(
          this.game.getUnitStaticData(unit).type
        )
    ).length;
    return selectedFleets > 0 && otherUnits / selectedFleets <= 4;
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
