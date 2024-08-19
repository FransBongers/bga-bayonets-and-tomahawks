class BattleCombineReducedUnitsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringBattleCombineReducedUnitsStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringBattleCombineReducedUnitsStateArgs) {
    debug('Entering BattleCombineReducedUnitsState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving BattleCombineReducedUnitsState');
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
      text: _('${you} must select a unit to flip to Full'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.spaceId][this.args.faction];
    stack.open();
    // this.setUnitsSelectable();
    Object.entries(this.args.options).forEach(([unitType, units]) => {
      if (units.length < 2) {
        return;
      }
      units.forEach((unit) =>
        this.game.setUnitSelectable({
          id: unit.id,
          callback: () =>
            this.updateInterfaceSelectUnitToFlip({ flip: unit, unitType }),
        })
      );
    });

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectUnitToFlip({
    flip,
    unitType,
  }: {
    flip: BTUnit;
    unitType: string;
  }) {
    const unitsToEliminate = this.args.options[unitType].filter(
      (unit) => unit.id !== flip.id
    );
    if (unitsToEliminate.length === 1) {
      this.updateInterfaceConfirm({
        flip,
        unitType,
        eliminate: unitsToEliminate[0],
      });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a unit to eliminate'),
      args: {
        you: '${you}',
      },
    });
    this.game.setUnitSelected({ id: flip.id });

    unitsToEliminate.forEach((unit) =>
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () =>
          this.updateInterfaceConfirm({ flip, unitType, eliminate: unit }),
      })
    );

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    flip,
    eliminate,
    unitType,
  }: {
    flip: BTUnit;
    eliminate: BTUnit;
    unitType: string;
  }) {
    this.game.clearPossible();

    this.game.setUnitSelected({ id: flip.id });
    this.game.setUnitSelected({ id: eliminate.id });

    this.game.clientUpdatePageTitle({
      text: _(
        'Eliminate ${tkn_unit_eliminate} and flip ${tkn_unit_flip} to Full ?'
      ),
      args: {
        tkn_unit_eliminate: `${eliminate.counterId}:reduced`,
        tkn_unit_flip: `${flip.counterId}:reduced`,
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actBattleCombineReducedUnits',
        args: {
          flipUnitId: flip.id,
          eliminateUnitId: eliminate.id,
          unitType,
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
