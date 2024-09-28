class WinterQuartersPlaceUnitsFromLossesBoxState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersPlaceUnitsFromLossesBoxStateArgs;

  private placedUnits: {
    [unitType: string]: {
      // unitId: location
      [unitId: string]: string;
    };
  } = null;
  private placedHighlandBrigade: boolean = false;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(
    args: OnEnteringWinterQuartersPlaceUnitsFromLossesBoxStateArgs
  ) {
    debug('Entering WinterQuartersPlaceUnitsFromLossesBoxState');
    this.args = args;

    this.placedUnits = {};
    Object.keys(this.args.options).forEach((unitType) => {
      this.placedUnits[unitType] = {};
    });
    this.placedHighlandBrigade = false;

    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersPlaceUnitsFromLossesBoxState');
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
    const setUnitsSelectable: boolean = this.setUnitsSelectable();
    if (!setUnitsSelectable) {
      this.updateInterfaceConfirm();
      return;
    }

    this.game.clientUpdatePageTitle({
      text: _('${you} may select a unit from the Losses Box'),
      args: {
        you: '${you}',
      },
    });

    // this.args.commanders.forEach((unit) => {
    //   const stack: UnitStack =
    //     this.game.gameMap.stacks[unit.location][this.args.faction];
    //   stack.open();

    //   this.game.setUnitSelectable({
    //     id: unit.id,
    //     callback: () => {
    //       this.updateInterfaceSelectSpace({ unit });
    //     },
    //   });
    // });

    // this.game.addPrimaryActionButton({
    //   id: 'done_btn',
    //   text: _('Done'),
    //   callback: () => this.updateInterfaceConfirm(),
    //   extraClasses:
    //     Object.keys(this.redeployedCommanders).length === 0 ? DISABLED : '',
    // });

    if (true) {
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
    spaceIds,
    unitType,
  }: {
    unit: BTUnit;
    spaceIds: string[];
    unitType: string;
  }) {
    if (spaceIds.length === 1) {
      this.placeUnit({ unit, spaceId: spaceIds[0], unitType });
      this.updateInterfaceInitialStep();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Space to place ${tkn_unit} on'),
      args: {
        you: '${you}',
        tkn_unit: unit.counterId,
      },
    });

    spaceIds.forEach((spaceId) =>
      this.game.setLocationSelectable({
        id: spaceId,
        callback: async () => {
          await this.placeUnit({ unit, spaceId, unitType });
          this.updateInterfaceInitialStep();
        },
      })
    );

    // Object.keys(this.args.stacks).forEach((spaceId) => {
    //   this.game.setStackSelectable({
    //     spaceId,
    //     faction: this.args.faction,
    //     callback: async (event: PointerEvent) => {
    //       if (spaceId === unit.location) {
    //         delete this.redeployedCommanders[unit.id];
    //       } else {
    //         this.redeployedCommanders[unit.id] = spaceId;
    //       }
    //       await this.game.gameMap.stacks[spaceId][this.args.faction].addUnit(
    //         unit
    //       );
    //       this.updateInterfaceInitialStep();
    //     },
    //   });
    // });

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
        action: 'actWinterQuartersPlaceUnitsFromLossesBox',
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

  private async placeUnit({
    unit,
    spaceId,
    unitType,
  }: {
    unit: BTUnit;
    spaceId: string;
    unitType: string;
  }) {
    this.placedUnits[unitType][unit.id] = spaceId;
    if(this.game.getUnitStaticData(unit).highland) {
      this.placedHighlandBrigade = true;
    }
    if (spaceId === DISBANDED_COLONIAL_BRIGADES) {
      await this.game.gameMap.losses.disbandedColonialBrigades.addCard(unit);
    } else {
      await this.game.gameMap.stacks[spaceId][this.args.faction].addUnit(unit);
    }
  }

  private setUnitsSelectable() {
    let setUnitsSelectable = false;
    Object.entries(this.args.options).forEach(([unitType, data]) => {
      const placedUnitsOfType = Object.keys(this.placedUnits[unitType]);
      if (placedUnitsOfType.length === data.numberToPlace) {
        return;
      }
      data.units.forEach((unit) => {
        if (placedUnitsOfType.includes(unit.id)) {
          return;
        }
        if (
          unitType === METROPOLITAN_BRIGADES &&
          this.game.getUnitStaticData(unit).highland &&
          this.placedHighlandBrigade
        ) {
          return;
        }
        this.game.setUnitSelectable({
          id: unit.id,
          callback: () =>
            this.updateInterfaceSelectSpace({ unit, spaceIds: data.spaceIds, unitType }),
        });
        setUnitsSelectable = true;
      });
    });
    return setUnitsSelectable;
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
    let units = [];

    Object.values(this.args.options).forEach((data) => {
      units = units.concat(data.units);
    });
    const stock =
      this.game.gameMap.losses[
        this.args.faction === BRITISH ? LOSSES_BOX_BRITISH : LOSSES_BOX_FRENCH
      ];
    await stock.addCards(units);
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
