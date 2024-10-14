class MovementState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringMovementStateArgs;
  private selectedUnits: BTUnit[] = [];
  private unselectedUnits: BTUnit[] = [];
  private destination: BTSpace = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringMovementStateArgs) {
    debug('Entering MovementState');
    this.args = args;
    this.selectedUnits = this.args.units.filter(
      ({ id }) =>
        this.args.requiredUnitIds.includes(id) ||
        this.args.previouslyMovedUnitIds.includes(id)
    );
    this.updateUnselectedUnits();
    this.destination = this.args.destination;
    this.updateInterfaceInitialStep(true);
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

  private updateInterfaceInitialStep(firstStep = false) {
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

    const usesForcedMarch = this.usesForcedMarch();

    if (usesForcedMarch || this.args.destination !== null) {
      this.game.addPrimaryActionButton({
        id: 'move_btn',
        text: usesForcedMarch ? _('Move with Forced March') : _('Move'),
        callback: () => this.performMoveAction(),
        extraClasses:
          this.selectedUnits.length > 0 && this.destination !== null
            ? ''
            : DISABLED,
      });
    }

    const moveOnDestinationSelect = !usesForcedMarch && !this.args.destination;
    this.updateAdjacentSpaces(moveOnDestinationSelect);

    if (
      !this.isIndianAPAndMultipleIndianNations() &&
      this.unselectedUnits.length > 0
    ) {
      this.game.addSecondaryActionButton({
        id: 'select_all_btn',
        text: _('Select all'),
        callback: () => {
          this.selectedUnits = this.args.units;
          this.updateUnselectedUnits();
          this.updateInterfaceInitialStep();
        },
      });
    }
    if (this.selectedUnits.length > 0) {
      this.game.addSecondaryActionButton({
        id: 'Deselect_all_btn',
        text: _('Deselect all'),
        callback: () => {
          this.selectedUnits = [];
          this.updateUnselectedUnits();
          this.updateInterfaceInitialStep();
        },
      });
    }

    if (
      firstStep ||
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

  private performMoveAction() {
    this.game.clearPossible();
    this.game.takeAction({
      action: 'actMovement',
      args: {
        destinationId: this.destination.id,
        selectedUnitIds: this.selectedUnits.map(({ id }) => id),
      },
    });
  }

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
      this.args.resolvedMoves === 2 &&
      [ARMY_AP, SAIL_ARMY_AP, FRENCH_LIGHT_ARMY_AP].includes(this.args.source);

    const doubleArmyAPLimit =
      this.args.resolvedMoves === 4 &&
      [ARMY_AP_2X, SAIL_ARMY_AP_2X, FRENCH_LIGHT_ARMY_AP].includes(
        this.args.source
      );

    return regularArmyAPLimit || doubleArmyAPLimit;
  }

  private updateUnselectedUnits() {
    this.unselectedUnits = this.args.units.filter(
      (unit) =>
        !this.selectedUnits.some((selectedUnit) => selectedUnit.id === unit.id)
    );
  }

  private updateDestintionAfterUnitClick() {
    // Reset destination unless there is a fixed destination
    if (this.args.destination === null) {
      this.destination = null;
    }
  }

  private setUnitsSelectable() {
    const selectableUnits = this.unselectedUnits.filter((unit) => {
      // Check if commander has already been selected when moving with light units
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

      // Check same Indian Nation in case of Indian AP
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

    this.selectedUnits.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          // Unselect unit
          this.selectedUnits = this.selectedUnits.filter(
            (selectedUnit) => selectedUnit.id !== unit.id
          );
          this.updateUnselectedUnits();
          this.updateDestintionAfterUnitClick();

          this.updateInterfaceInitialStep();
        },
      });
    });

    selectableUnits.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          // Add units to selected units
          this.selectedUnits.push(unit);
          this.updateUnselectedUnits();
          this.updateDestintionAfterUnitClick();

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

  private onlyCommandersUnselected() {
    const unselectedCommanders = this.unselectedUnits.filter(
      (unit) => this.game.getUnitStaticData(unit).type === COMMANDER
    );
    return (
      this.unselectedUnits.length > 0 &&
      unselectedCommanders.length === this.unselectedUnits.length
    );
  }

  private setDestinationsSelectable(moveOnDestinationClick: boolean) {
    // Not possible to leave commanders behind without other units
    if (
      this.args.unitsThatCannotMoveCount === 0 &&
      this.onlyCommandersUnselected()
    ) {
      return;
    }

    const numberOfUnitsForConnectionLimit = this.selectedUnits.filter(
      (unit) => {
        const staticData = this.game.getUnitStaticData(unit);
        return ![COMMANDER, FLEET].includes(staticData.type);
      }
    ).length;

    const canUsePath = !this.selectedUnits.some(
      (unit) => ![LIGHT, FLEET].includes(this.game.getUnitStaticData(unit).type)
    );

    const requiresHighway =
      this.selectedUnits.filter(
        (unit) => this.game.getUnitStaticData(unit).type === ARTILLERY
      ).length > 1;

    const requiresCoastal = this.selectedUnits.some(
      (unit) => this.game.getUnitStaticData(unit).type === FLEET
    );

    const onlyCommandersSelected =
      this.selectedUnits.some(
        (unit) => this.game.getUnitStaticData(unit).type === COMMANDER
      ) &&
      !this.selectedUnits.some(
        (unit) => this.game.getUnitStaticData(unit).type !== COMMANDER
      );

    const validDestinations = this.args.adjacent.filter(
      ({ connection, space, requiredToMove, hasEnemyUnits }) => {
        if (
          // If only commanders are selected cannot move
          onlyCommandersSelected &&
          // Unless army movement and commander can move through friendly spaces,
          // free of enemy units
          (!this.args.isArmyMovement ||
            space.control !== this.args.faction ||
            hasEnemyUnits)
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
        if (requiredToMove > numberOfUnitsForConnectionLimit) {
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
          if (moveOnDestinationClick) {
            this.performMoveAction();
          } else {
            this.updateInterfaceInitialStep();
          }
        },
      });
    });
  }

  private updateAdjacentSpaces(moveOnDestinationClick: boolean) {
    if (this.destination !== null) {
      this.game.setLocationSelected({ id: this.destination.id });
    }

    if (this.selectedUnits.length > 0 && this.destination === null) {
      this.setDestinationsSelectable(moveOnDestinationClick);
    }
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
