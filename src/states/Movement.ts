class MovementState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringMovementStateArgs;
  private selectedUnits: BTUnit[] = [];
  private destination: BTSpace = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringMovementStateArgs) {
    debug('Entering MovementState');
    this.args = args;
    this.selectedUnits = this.args.units.filter(({ id }) =>
      this.args.requiredUnitIds.includes(id)
    );
    this.destination = this.args.destination;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving MovementState');
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

    const fixedDestination = this.args.destination !== null;

    if (fixedDestination) {
      this.game.clientUpdatePageTitle({
        text: _('${you} must select units to move to ${spaceName}'),
        args: {
          you: '${you}',
          spaceName: _(this.args.destination.name),
        },
      });
    } else {
      this.game.clientUpdatePageTitle({
        text: _('${you} must select units and a destination'),
        args: {
          you: '${you}',
        },
      });
    }

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.fromSpace.id][this.args.faction];
    stack.open();

    this.setUnitsSelectable();
    this.setUnitsSelected();

    if (this.destination !== null) {
      this.game.setLocationSelected({ id: this.destination.id });
    }

    if (this.selectedUnits.length > 0 && this.destination === null) {
      this.setDestinationsSelectable();
    }

    this.game.addPrimaryActionButton({
      id: 'move_btn',
      text: _('Move'),
      callback: () => this.updateInterfaceConfirm(),
      extraClasses:
        this.selectedUnits.length > 0 && this.destination !== null
          ? ''
          : DISABLED,
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

    // this.checkConfirmDisabled();
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Move selected units to ${spaceName}?'),
      args: {
        spaceName: _(this.destination.name),
      },
    });

    this.game.setLocationSelected({ id: this.destination.id });
    this.setUnitsSelected();

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actMovement',
        args: {
          destinationId: this.destination.id,
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
    this.args.units.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          if (
            this.selectedUnits.some(
              (selectedUnit) => selectedUnit.id === unit.id
            ) &&
            !this.args.requiredUnitIds.includes(unit.id)
          ) {
            this.selectedUnits = this.selectedUnits.filter(
              (selectedUnit) => selectedUnit.id !== unit.id
            );
          } else {
            this.selectedUnits.push(unit);
          }
          if (this.args.destination === null) {
            this.destination = null;
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

  private setDestinationsSelectable() {
    const numberOfUnitsForConnectionLimit = this.selectedUnits.filter(
      (unit) => {
        const staticData = this.getUnitStaticData(unit);
        return ![COMMANDER, FLEET].includes(staticData.type);
      }
    ).length;
    const lightOnly = !this.selectedUnits.some(
      (unit) => this.getUnitStaticData(unit).type !== LIGHT
    );
    const requiresHighway =
      this.selectedUnits.filter(
        (unit) => this.getUnitStaticData(unit).type === ARTILLERY
      ).length > 1;

    const validDestinations = this.args.adjacent.filter(({ connection }) => {
      if (requiresHighway && connection.type !== HIGHWAY) {
        return false;
      }
      if (!lightOnly && connection.type === PATH) {
        return false;
      }
      if (
        numberOfUnitsForConnectionLimit > this.getRemainingLimit(connection)
      ) {
        return false;
      }
      return true;
    });

    validDestinations.forEach(({ space }) => {
      this.game.setLocationSelectable({
        id: space.id,
        callback: () => {
          this.destination = space;
          this.updateInterfaceInitialStep();
        },
      });
    });
  }

  private getUnitStaticData(unit: BTUnit) {
    return this.game.gamedatas.staticData.units[unit.counterId];
  }

  private getRemainingLimit(connection: BTConnection) {
    const usedLimit =
      this.args.faction === BRITISH
        ? connection.britishLimit
        : connection.frenchLimit;

    const maxLimit = this.getMaxLimit(connection.type);
    return maxLimit - usedLimit;
  }

  private getMaxLimit(connectionType: string) {
    switch (connectionType) {
      case HIGHWAY:
        return 16;
      case ROAD:
        return 8;
      case PATH:
        return 4;
      default:
        return 0;
    }
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

  // handleUnitClick({ unit }: { unit: BTUnit }) {
  //   const index = this.selectedUnits.findIndex((item) => item.id === unit.id);
  //   const element = document.getElementById(unit.id + '');
  //   if (!element) {
  //     return;
  //   }
  //   if (index < 0) {
  //     // Unit selected
  //     this.selectedUnits.push(unit);
  //     element.classList.add(BT_SELECTED);
  //     element.classList.remove(BT_SELECTABLE);
  //     this.game.setUnitSelected({ id: '' + unit.id });
  //   } else {
  //     // Unit unselected
  //     this.selectedUnits.splice(index, 1);
  //     element.classList.add(BT_SELECTABLE);
  //     element.classList.remove(BT_SELECTED);
  //   }
  //   this.checkConfirmDisabled();
  // }

  // checkConfirmDisabled() {
  //   const button = document.getElementById('confirm_btn');
  //   if (!button) {
  //     return;
  //   }
  //   if (this.selectedUnits.length === 0) {
  //     button.classList.add(DISABLED);
  //   } else {
  //     button.classList.remove(DISABLED);
  //   }
  // }
}
