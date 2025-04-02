class BattleMoveFleetState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringBattleMoveFleetStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringBattleMoveFleetStateArgs) {
    debug('Entering BattleMoveFleetState');
    this.args = args;
    this.game.tabbedColumn.changeTab('battle');
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving BattleMoveFleetState');
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
      text: _('${you} may select a Fleet to move'),
      args: {
        you: '${you}',
      },
    });

    if (this.args.units.length > 0) {
      this.game.openUnitStack(this.args.units[0]);
    }

    this.args.units.forEach((unit) =>
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => this.updateInterfaceSelectSpace({ unit }),
      })
    );

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectSpace({ unit }: { unit: BTUnit }) {
    if (this.args.destinationIds.length === 1) {
      this.updateInterfaceConfirm({
        destinationId: this.args.destinationIds[0],
        unit,
      });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Space to move ${tkn_unit} to'),
      args: {
        you: '${you}',
        tkn_unit: tknUnitLog(unit),
      },
    });

    this.game.setUnitSelected({ id: unit.id });

    this.args.destinationIds.forEach((id) =>
      this.game.setLocationSelectable({
        id,
        callback: () =>
          this.updateInterfaceConfirm({ destinationId: id, unit }),
      })
    );

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    destinationId,
    unit,
  }: {
    destinationId: string;
    unit: BTUnit;
  }) {
    this.game.clearPossible();

    this.game.setLocationSelected({ id: destinationId });
    this.game.setUnitSelected({ id: unit.id });

    this.game.clientUpdatePageTitle({
      text: _('Move ${tkn_unit} to ${spaceName}?'),
      args: {
        spaceName:
          destinationId === SAIL_BOX
            ? _('the Sail Box')
            : _(this.game.getSpaceStaticData(destinationId).name),
        tkn_unit: tknUnitLog(unit),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actBattleMoveFleet',
        args: {
          destinationId,
          unitId: unit.id,
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
