class ActionActivateStackState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringActionActivateStackStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringActionActivateStackStateArgs) {
    debug('Entering ActionActivateStackState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ActionActivateStackState');
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
      text: _('${you} must select a stack to activate'),
      args: {
        you: '${you}',
      },
    });

    this.setStacksSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectAction({ stackId, stackActions }: { stackId: string; stackActions: BTStackAction[] }) {
    this.game.clearPossible();
    this.game.setLocationSelected({ id: `${stackId}_${this.args.faction}_stack` });

    this.game.clientUpdatePageTitle({
      text: _('${you} must choose an action to perform'),
      args: {
        you: '${you}',
      },
    });

    stackActions.forEach((action) => {
      this.game.addPrimaryActionButton({
        text: _(action.name),
        id: `${action.id}_btn`,
        callback: () => this.updateInterfaceConfirm({ stackAction: action, stackId }),
      });
    });

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    stackAction,
    stackId,
  }: {
    stackAction: BTStackAction;
    stackId: string;
  }) {
    this.game.clearPossible();
    this.game.setLocationSelected({ id: `${stackId}_${this.args.faction}_stack` });

    this.game.clientUpdatePageTitle({
      text: _('Perform ${actionName} with stack in ${locationName}?'),
      args: {
        actionName: _(stackAction.name),
        locationName: stackId,
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actActionActivateStack',
        args: {
          action: stackAction.id,
          stack: stackId,
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

  private setStacksSelectable() {
    Object.entries(this.args.stacks).forEach(([stackId, stackActions], index) => {
      this.game.setLocationSelectable({
        id: `${stackId}_${this.args.faction}_stack`,
        callback: () => this.updateInterfaceSelectAction({ stackId, stackActions }),
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
