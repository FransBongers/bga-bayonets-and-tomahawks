class ActionRoundChooseCardState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringActionRoundChooseCardStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringActionRoundChooseCardStateArgs) {
    debug('Entering ActionRoundChooseCardState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ActionRoundChooseCardState');
  }

  setDescription(
    activePlayerId: number,
    args: OnEnteringActionRoundChooseCardStateArgs
  ) {
    this.args = args;
    // this.game.hand.open();
    if (this.args._private?.selectedCard) {
      this.game.setCardSelected({ id: this.args._private.selectedCard.id });
    }
  }

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
      text: _('${you} must choose your card for this Action Round'),
      args: {
        you: '${you}',
      },
    });
    this.game.hand.open();
    const { cards } = this.args._private;
    cards.forEach((card) => {
      this.game.setCardSelectable({
        id: card.id,
        callback: () => {
          this.updateInterfaceConfirm({ card });
        },
      });
    });
    this.setIndianCardSelected();

    // this.addButtons();
    // this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({ card }: { card: BTCard }) {
    this.game.clearPossible();
    this.game.setCardSelected({ id: card.id });
    this.setIndianCardSelected();
    this.game.clientUpdatePageTitle({
      text: _('Select card?'),
      args: {},
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actActionRoundChooseCard',
        args: {
          cardId: card.id,
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

  private setIndianCardSelected() {
    const { indianCard } = this.args._private;
    if (indianCard) {
      this.game.setCardSelected({ id: indianCard.id });
    }
  }

  // private addButtons() {
  //   this.args.options.forEach((apostasy, index) => {
  //     this.game.addPrimaryActionButton({
  //       id: `apostasy_btn_${index}`,
  //       text: this.apostasyTextMap[apostasy],
  //       callback: () => this.updateInterfaceConfirm({ apostasy }),
  //     });
  //   });
  // }

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
