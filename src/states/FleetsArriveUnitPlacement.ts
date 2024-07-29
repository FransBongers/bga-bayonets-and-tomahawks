class FleetsArriveUnitPlacementState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringFleetsArriveUnitPlacementStateArgs;

  private placedFleets: Record<string, string> = null; // {fleetId: spaceId}
  private placedUnits: Record<string, string> = null; // {unitId: spaceId}
  private placedCommanders: Record<string, string> = null; // {unitId: spaceId}
  private localMoves: Record<string, BTUnit[]>;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringFleetsArriveUnitPlacementStateArgs) {
    debug('Entering FleetsArriveUnitPlacementState');
    this.args = args;
    this.localMoves = {};

    this.placedFleets = {};
    this.placedUnits = {};
    this.placedCommanders = {};

    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving FleetsArriveUnitPlacementState');
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
    const fleetsToPlace = this.args.fleets.filter((fleet) => {
      return !Object.keys(this.placedFleets).includes(fleet.id);
    });
    if (fleetsToPlace.length === 0) {
      this.updateInterfacePlaceUnits();
      return;
    }

    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Fleet to place'),
      args: {
        you: '${you}',
      },
    });

    fleetsToPlace.forEach((fleet) => {
      this.game.setUnitSelectable({
        id: fleet.id,
        callback: () => {
          this.updateInterfaceSelectSpace({ unit: fleet, isFleet: true });
        },
      });
    });

    if (Object.keys(this.placedFleets).length === 0) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.addCancelButton();
    }
  }

  private updateInterfaceSelectSpace({
    unit,
    isFleet = false,
  }: {
    unit: BTUnit;
    isFleet?: boolean;
  }) {
    this.game.clearPossible();

    const commanderId = this.args.commandersPerUnit[unit.id] || null;

    this.game.clientUpdatePageTitle({
      text: commanderId
        ? _(
            '${you} must select a Space to place ${tkn_unit}${tkn_unit_commander}'
          )
        : _('${you} must select a Space to place ${tkn_unit}'),
      args: {
        you: '${you}',
        tkn_unit: unit.counterId,
        tkn_unit_commander: commanderId
          ? this.args.commanders[commanderId]?.counterId
          : '',
      },
    });

    if (isFleet) {
      this.args.spaces.forEach((space) => {
        this.game.setLocationSelectable({
          id: space.id,
          callback: async () => {
            this.placedFleets[unit.id] = space.id;
            this.addLocalMove({ fromSpaceId: unit.location, unit });
            await this.game.gameMap.stacks[space.id][unit.faction].addUnit(
              unit
            );
            this.updateInterfaceInitialStep();
          },
        });
      });
    } else {
      const spacesToPlaceUnit = this.getPossibleSpacesToPlaceUnit();
      spacesToPlaceUnit.forEach((id) => {
        this.game.setLocationSelectable({
          id,
          callback: async () => {
            this.placedUnits[unit.id] = id;
            this.addLocalMove({ fromSpaceId: unit.location, unit });
            const units = [unit];
            if (commanderId) {
              const commander = this.args.commanders[commanderId];
              this.addLocalMove({
                fromSpaceId: unit.location,
                unit: commander,
              });
              units.push(commander);
            }
            await this.game.gameMap.stacks[id][unit.faction].addUnits(units);
            this.updateInterfacePlaceUnits();
          },
        });
      });
    }

    this.addCancelButton();
  }

  private updateInterfacePlaceUnits() {
    const unitsToPlace = this.args.units.filter((unit) => {
      return (
        !Object.keys(this.placedFleets).includes(unit.id) &&
        !Object.keys(this.placedUnits).includes(unit.id)
      );
    });
    if (unitsToPlace.length === 0) {
      this.updateInterfaceConfirm();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a unit to place'),
      args: {
        you: '${you}',
      },
    });

    unitsToPlace.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          this.updateInterfaceSelectSpace({ unit });
        },
      });
    });

    this.addCancelButton();
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Confirm unit placement?'),
      args: {},
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actFleetsArriveUnitPlacement',
        args: {
          placedFleets: this.placedFleets,
          placedUnits: this.placedUnits,
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
    this.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private addLocalMove({
    fromSpaceId,
    unit,
  }: {
    fromSpaceId: string;
    unit: BTUnit;
  }) {
    if (this.localMoves[fromSpaceId]) {
      this.localMoves[fromSpaceId].push(unit);
    } else {
      this.localMoves[fromSpaceId] = [unit];
    }
  }

  private addCancelButton() {
    this.game.addDangerActionButton({
      id: 'cancel_btn',
      text: _('Cancel'),
      callback: async () => {
        await this.revertLocalMoves();
        this.game.onCancel();
      },
    });
  }

  private async revertLocalMoves() {
    const promises = [];
    Object.entries(this.localMoves).forEach(([spaceId, units]) => {
      promises.push(this.game.pools.stocks[spaceId].addCards(units));
    });

    await Promise.all(promises);
  }

  private getPossibleSpacesToPlaceUnit() {
    const fleetLocations = Object.values(this.placedFleets);
    if (fleetLocations.length > 0) {
      // return unique
      return fleetLocations.reduce((carry, current) => {
        if (carry.includes(current)) {
          return carry;
        } else {
          carry.push(current);
          return carry;
        }
      }, []);
    }
    const unitLocations = Object.values(this.placedUnits);
    if (unitLocations.length > 0) {
      // No fleets so all units must be placed in a single location
      return [unitLocations[0]];
    } else {
      return this.args.spaces.map((space) => space.id);
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
}
