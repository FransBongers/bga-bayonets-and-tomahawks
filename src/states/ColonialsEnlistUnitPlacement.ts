class ColonialsEnlistUnitPlacementState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringColonialsEnlistUnitPlacementStateArgs;

  private placedUnits: Record<string, string> = null; // {unitId: spaceId}
  private localMoves: Record<string, BTUnit[]>;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringColonialsEnlistUnitPlacementStateArgs) {
    debug('Entering ColonialsEnlistUnitPlacementState');
    this.args = args;
    this.localMoves = {};

    this.placedUnits = {};
    this.game.tabbedColumn.changeTab('pools');
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ColonialsEnlistUnitPlacementState');
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
    const unitsToPlace = this.args.units.filter((unit) => {
      return !Object.keys(this.placedUnits).includes(unit.id);
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

    if (Object.keys(this.placedUnits).length === 0) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.addCancelButton();
    }
  }

  private updateInterfaceSelectSpace({ unit }: { unit: BTUnit }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Space to place ${tkn_unit}'),
      args: {
        you: '${you}',
        tkn_unit: unit.counterId,
      },
    });

    const spacesToPlaceUnit = this.getPossibleSpacesToPlaceUnit(unit);
    spacesToPlaceUnit.forEach((id) => {
      this.game.setLocationSelectable({
        id,
        callback: async () => {
          this.placedUnits[unit.id] = id;
          this.addLocalMove({ fromSpaceId: unit.location, unit });
          await this.game.gameMap.stacks[id][unit.faction].addUnit(unit);
          this.updateInterfaceInitialStep();
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
        action: 'actColonialsEnlistUnitPlacement',
        args: {
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

  private getPossibleSpacesToPlaceUnit(unit: BTUnit) {
    if (this.game.gamedatas.staticData.units[unit.counterId].type === LIGHT) {
      return this.args.spaces.map((space) => space.id);
    } else {
      return this.args.spaces
        .filter(
          (space) =>
            space.colony ===
            this.game.gamedatas.staticData.units[unit.counterId].colony
        )
        .map((space) => space.id);
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
