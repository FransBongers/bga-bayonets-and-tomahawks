class BattleFortEliminationState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringBattleFortEliminationStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringBattleFortEliminationStateArgs) {
    debug('Entering BattleFortEliminationState');
    this.args = args;
    this.game.tabbedColumn.changeTab('battle');
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving BattleFortEliminationState');
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
      text: _(
        '${you} must eliminate ${tkn_unit_fort} in ${spaceName} or replace with ${tkn_unit_enemyFort}?'
      ),
      args: {
        you: '${you}',
        tkn_unit_fort: `${this.args.fort.counterId}:${
          this.args.fort.reduced ? 'reduced' : 'full'
        }`,
        tkn_unit_enemyFort: `${this.args.enemyFort.counterId}:${
          this.args.fort.reduced ? 'reduced' : 'full'
        }`,
        spaceName: _(this.args.space.name),
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.space.id][this.args.faction];
    stack.open();
    this.game.setUnitSelected({ id: this.args.fort.id });

    this.game.addPrimaryActionButton({
      text: _('Eliminate'),
      id: 'eliminate_btn',
      callback: () => this.updateInterfaceConfirm('eliminate'),
    });
    this.game.addPrimaryActionButton({
      text: _('Replace'),
      id: 'replace_btn',
      callback: () => this.updateInterfaceConfirm('replace'),
    });

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm(choice: string) {
    this.game.clearPossible();

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actBattleFortElimination',
        args: {
          choice,
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

    // this.game.addCancelButton();
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
