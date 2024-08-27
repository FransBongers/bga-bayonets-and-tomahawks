class ActionRoundChooseReactionState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringActionRoundChooseReactionStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringActionRoundChooseReactionStateArgs) {
    debug('Entering ActionRoundChooseReactionState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ActionRoundChooseReactionState');
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
      text: _('${you} may choose an Action Point to hold for Reaction'),
      args: {
        you: '${you}',
      },
    });

    this.addActionPointButtons();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
      text: _('Do not hold AP for Reaction'),
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({
    actionPoint,
  }: {
    actionPoint: BTActionPoint;
  }) {
    this.game.clearPossible();
    this.game.clientUpdatePageTitle({
      text: _('Hold ${ap} for Reaction?'),
      args: {
        ap: actionPoint.id,
      },
    });

    this.game.addConfirmButton({
      callback: () => {
        this.game.clearPossible();
        this.game.takeAction({
          action: 'actActionRoundChooseReaction',
          args: {
            actionPointId: actionPoint.id,
          },
        });
      },
    });
    this.game.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private addActionPointButtons() {
    this.args.actionPoints.forEach((ap, index) =>
      this.game.addPrimaryActionButton({
        text: _(ap.id),
        id: `action_point_btn_${index}`,
        callback: () => this.updateInterfaceConfirm({ actionPoint: ap }),
      })
    );
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
