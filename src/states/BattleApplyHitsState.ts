class BattleApplyHitsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringBattleApplyHitsStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringBattleApplyHitsStateArgs) {
    debug('Entering BattleApplyHitsState');
    this.args = args;
    this.game.tabbedColumn.changeTab('battle');
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving BattleApplyHitsState');
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
      text: this.args.eliminate ? _('${you} must select a unit to eliminate') : _('${you} must select a unit to apply a Hit to'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.spaceId][this.args.faction];
    stack.open();
    this.setUnitsSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({ unit }: { unit: BTUnit }) {
    this.game.clearPossible();

    this.game.setUnitSelected({ id: unit.id });

    this.game.clientUpdatePageTitle({
      text: this.args.eliminate ? _('Eliminate ${unitName}?') : _('Apply Hit to ${unitName}?'),
      args: {
        unitName: _(
          this.game.gamedatas.staticData.units[unit.counterId].counterText
        ),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actBattleApplyHits',
        args: {
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

  private setUnitsSelectable() {
    this.args.units.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: (event: PointerEvent) => {
          event.preventDefault();
          event.stopPropagation();
          this.updateInterfaceConfirm({ unit });
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
