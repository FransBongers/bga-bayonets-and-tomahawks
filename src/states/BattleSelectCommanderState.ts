class BattleSelectCommanderState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringBattleSelectCommanderStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringBattleSelectCommanderStateArgs) {
    debug('Entering BattleSelectCommanderState');
    this.args = args;
    this.game.tabbedColumn.changeTab('battle');
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving BattleSelectCommanderState');
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
      text: _('${you} must select a Commander'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.space.id][this.args.faction];
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
      text: _('Select ${unitName}?'),
      args: {
        you: '${you}',
        unitName: _(
          this.game.gamedatas.staticData.units[unit.counterId].counterText
        ),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actBattleSelectCommander',
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
    this.args.commanders.forEach((unit) => {
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
