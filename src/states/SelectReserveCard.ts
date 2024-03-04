class SelectReserveCardState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringSelectReserveCardStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringSelectReserveCardStateArgs) {
    debug("Entering SelectReserveCardState")
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug("Leaving SelectReserveCardState");
  }

  setDescription(activePlayerId: number) {
    // this.game.clientUpdatePageTitle({
    //   text: _("${player_name} must choose a Reservc"),
    //   args: {
    //     player_name: this.game.playerManager
    //       .getPlayer({ playerId: activePlayerId })
    //       .getName(),
    //   },
    //   nonActivePlayers: true,
    // });
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
      text: _("${you} must select a Reserve Card"),
      args: {
        you: "${you}",
      },
    });
    this.args._private.forEach((card) => {
      this.game.setCardSelectable({
        id: card.id,
        callback: () => {
          this.updateInterfaceConfirm({ card });
        },
      });
    });

    // this.addButtons();
    // this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({ card }: { card: BTCard }) {
    this.game.clearPossible();
    this.game.setCardSelected({ id: card.id });
    this.game.clientUpdatePageTitle({
      text: _("Select card?"),
      args: {},
    });

    const callback = () =>
      this.game.takeAction({
        action: "actSelectReserveCard",
        args: {
          cardId: card.id,
        },
      });

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
