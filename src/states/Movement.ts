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

    const usesForcedMarch = this.usesForcedMarch();

    this.game.addPrimaryActionButton({
      id: 'move_btn',
      text: usesForcedMarch ? _('Move with Forced March') : _('Move'),
      callback: () => {
        this.game.clearPossible();
        this.game.takeAction({
          action: 'actMovement',
          args: {
            destinationId: this.destination.id,
            selectedUnitIds: this.selectedUnits.map(({ id }) => id),
          },
        });
      },
      extraClasses:
        this.selectedUnits.length > 0 && this.destination !== null
          ? ''
          : DISABLED,
    });

    if (!this.isIndianAPAndMultipleIndianNations()) {
      this.game.addSecondaryActionButton({
        id: 'select_all_btn',
        text: _('Select all'),
        callback: () => {
          (this.selectedUnits = this.args.units),
            this.updateInterfaceInitialStep();
        },
      });
    }

    if (
      this.selectedUnits.length === 0 ||
      this.selectedUnits.length === this.args.requiredUnitIds.length
    ) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.game.addCancelButton();
    }

    // this.checkConfirmDisabled();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private usesForcedMarch(): boolean {
    if (!this.args.forcedMarchAvailable) {
      return false;
    }

    if (
      !this.selectedUnits.some(
        (unit) => this.game.getUnitStaticData(unit).type !== LIGHT
      )
    ) {
      return false;
    }

    const regularArmyAPLimit =
      this.args.count === 2 &&
      [ARMY_AP, SAIL_ARMY_AP, FRENCH_LIGHT_ARMY_AP].includes(this.args.source);

    const doubleArmyAPLimit =
      this.args.count === 4 &&
      [ARMY_AP_2X, SAIL_ARMY_AP_2X, FRENCH_LIGHT_ARMY_AP].includes(
        this.args.source
      );

    return regularArmyAPLimit || doubleArmyAPLimit;
  }

  private setUnitsSelectable() {
    const units = this.args.units.filter((unit) => {
      // Check commander movement with light units
      if (
        // Source is light AP
        [LIGHT_AP, LIGHT_AP_2X].includes(this.args.source) &&
        // Units is commander
        this.game.getUnitStaticData(unit).type === COMMANDER &&
        // Another Commander has already been selected
        this.selectedUnits.some(
          (selectedUnit) =>
            this.game.getUnitStaticData(selectedUnit).type === COMMANDER &&
            selectedUnit.id !== unit.id
        )
      ) {
        return false;
      }

      // Check same Indian Nation with indian units
      if (
        [INDIAN_AP, INDIAN_AP_2X].includes(this.args.source) &&
        this.selectedUnits.some(
          (selectedUnit) => unit.counterId !== selectedUnit.counterId
        )
      ) {
        return false;
      }

      return true;
    });

    units.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          // Unselect unit
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
            // Add units to selected units
            this.selectedUnits.push(unit);
          }
          // Reset destination unless there is a fixed destination
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
    const canUsePath = !this.selectedUnits.some(
      (unit) => ![LIGHT, FLEET].includes(this.game.getUnitStaticData(unit).type)
    );
    const requiresHighway =
      this.selectedUnits.filter(
        (unit) => this.getUnitStaticData(unit).type === ARTILLERY
      ).length > 1;

    const requiresCoastal = this.selectedUnits.some(
      (unit) => this.game.getUnitStaticData(unit).type === FLEET
    );

    const validDestinations = this.args.adjacent.filter(
      ({ connection, space }) => {
        // TODO: lone commander movement:
        if (
          // If only commanders are selected:
          this.selectedUnits.some(
            (unit) => this.game.getUnitStaticData(unit).type === COMMANDER
          ) &&
          !this.selectedUnits.some(
            (unit) => this.game.getUnitStaticData(unit).type !== COMMANDER
          )
        ) {
          return false;
        }

        if (requiresHighway && connection.type !== HIGHWAY) {
          return false;
        }
        if (
          requiresCoastal &&
          !this.game.getConnectionStaticData(connection).coastal
        ) {
          return false;
        }
        if (!canUsePath && connection.type === PATH) {
          return false;
        }
        if (
          numberOfUnitsForConnectionLimit > this.getRemainingLimit(connection)
        ) {
          return false;
        }
        if (
          this.args.faction === FRENCH &&
          this.game.getSpaceStaticData(space).britishBase
        ) {
          return false;
        }

        return true;
      }
    );

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

  private isIndianAPAndMultipleIndianNations() {
    if (![INDIAN_AP, INDIAN_AP_2X].includes(this.args.source)) {
      return false;
    }

    const indianNations: string[] = this.args.units.reduce(
      (carry: string[], current: BTUnit) => {
        if (!carry.includes(current.counterId)) {
          carry.push(current.counterId);
        }
        return carry;
      },
      []
    );

    return indianNations.length > 1;
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
