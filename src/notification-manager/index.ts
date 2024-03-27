//  .##....##..#######..########.####.########
//  .###...##.##.....##....##.....##..##......
//  .####..##.##.....##....##.....##..##......
//  .##.##.##.##.....##....##.....##..######..
//  .##..####.##.....##....##.....##..##......
//  .##...###.##.....##....##.....##..##......
//  .##....##..#######.....##....####.##......

//  .##.....##....###....##....##....###.....######...########.########.
//  .###...###...##.##...###...##...##.##...##....##..##.......##.....##
//  .####.####..##...##..####..##..##...##..##........##.......##.....##
//  .##.###.##.##.....##.##.##.##.##.....##.##...####.######...########.
//  .##.....##.#########.##..####.#########.##....##..##.......##...##..
//  .##.....##.##.....##.##...###.##.....##.##....##..##.......##....##.
//  .##.....##.##.....##.##....##.##.....##..######...########.##.....##

class NotificationManager {
  private game: BayonetsAndTomahawksGame;
  private subscriptions: unknown[];

  constructor(game) {
    this.game = game;
    this.subscriptions = [];
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  setupNotifications() {
    console.log("notifications subscriptions setup");

    // const notifs: [
    //   id: string,
    //   wait: number,
    //   predicate?: (notif: Notif<{ playerId: number }>) => void
    // ][] = [
    //   // checked
    //   ["log", undefined],
    //   [
    //     "discardCardFromHand",
    //     undefined,
    //     (notif) => notif.args.playerId == this.game.getPlayerId(),
    //   ],
    //   ["discardCardFromHandPrivate", undefined],
    //   ["drawCardPrivate", undefined],
    //   ["revealCardsInPlay", undefined],
    //   ["selectReserveCard", undefined],
    //   ["selectReserveCardPrivate", undefined],
    //   // [
    //   //   "selectReserveCard",
    //   //   undefined,
    //   //   (notif) => notif.args.playerId == this.game.getPlayerId(),
    //   // ],
    // ];
    const notifs: string[] = [
      "log",
      "discardCardFromHand",
      "discardCardFromHandPrivate",
      "drawCardPrivate",
      "revealCardsInPlay",
      "selectReserveCard",
      "selectReserveCardPrivate",
    ];

    // example: https://github.com/thoun/knarr/blob/main/src/knarr.ts
    notifs.forEach((notifName) => {
      this.subscriptions.push(
        dojo.subscribe(notifName, this, (notifDetails: Notif<unknown>) => {
          debug(`notif_${notifName}`, notifDetails); // log notif params (with Tisaac log method, so only studio side)

          const promise = this[`notif_${notifName}`](notifDetails);
          const promises = promise ? [promise] : [];
          let minDuration = 1;

          // Show log messags in page title
          let msg = this.game.format_string_recursive(
            notifDetails.log,
            notifDetails.args as Record<string, unknown>
          );
          if (msg != "") {
            $("gameaction_status").innerHTML = msg;
            $("pagemaintitletext").innerHTML = msg;
            $("generalactions").innerHTML = "";

            // If there is some text, we let the message some time, to be read
            minDuration = MIN_NOTIFICATION_MS;
          }

          // Promise.all([...promises, sleep(minDuration)]).then(() =>
          //   this.game.framework().notifqueue.onSynchronousNotificationEnd()
          // );
          // tell the UI notification ends, if the function returned a promise.
          if (this.game.animationManager.animationsActive()) {
            Promise.all([...promises, sleep(minDuration)]).then(() =>
              this.game.framework().notifqueue.onSynchronousNotificationEnd()
            );
          } else {
            // TODO: check what this does
            this.game.framework().notifqueue.setSynchronousDuration(0);
          }
        })
      );
      this.game.framework().notifqueue.setSynchronous(notifName, undefined);

      this.game
        .framework()
        .notifqueue.setIgnoreNotificationCheck(
          "discardCardFromHand",
          (notif: Notif<{ playerId: number }>) =>
            notif.args.playerId == this.game.getPlayerId()
        );
    });
  }

  // Example code to show log messags in page title
  // I wont directly answer your issue, but propose something that will fix it and improve your game
  // put that inside any notification handler :
  // let msg = this.format_string_recursive(args.log, args.args);
  // if (msg != '') {
  //   $('gameaction_status').innerHTML = msg;
  //   $('pagemaintitletext').innerHTML = msg;
  // }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  destroy() {
    dojo.forEach(this.subscriptions, dojo.unsubscribe);
  }

  getPlayer({ playerId }: { playerId: number }): BatPlayer {
    return this.game.playerManager.getPlayer({ playerId });
  }

  // .##....##..#######..########.####.########..######.
  // .###...##.##.....##....##.....##..##.......##....##
  // .####..##.##.....##....##.....##..##.......##......
  // .##.##.##.##.....##....##.....##..######....######.
  // .##..####.##.....##....##.....##..##.............##
  // .##...###.##.....##....##.....##..##.......##....##
  // .##....##..#######.....##....####.##........######.

  async notif_log(notif: Notif<unknown>) {
    // this is for debugging php side
    debug("notif_log", notif.args);
  }

  // notif_smallRefreshHand(notif: Notif<NotifSmallRefreshHandArgs>) {
  //   const { hand, playerId } = notif.args;
  //   const player = this.getPlayer({ playerId });
  //   player.clearHand();
  //   player.setupHand({ hand });
  // }

  async notif_smallRefreshInterface(
    notif: Notif<NotifSmallRefreshInterfaceArgs>
  ) {
    const updatedGamedatas = {
      ...this.game.gamedatas,
      ...notif.args,
    };
    this.game.clearInterface();
    this.game.gamedatas = updatedGamedatas;
    this.game.playerManager.updatePlayers({ gamedatas: updatedGamedatas });
  }

  async notif_discardCardFromHand(notif: Notif<NotifDiscardCardFromHandArgs>) {
    const { faction, playerId } = notif.args;
    const fakeCard = {
      id: `bt_tempCardDiscard_${faction}`,
      faction,
      location: DISCARD,
    } as BTCard;
    const fromElement = document.getElementById(
      `overall_player_board_${playerId}`
    );
    await this.game.discard.addCard(fakeCard, { fromElement });
  }

  async notif_discardCardFromHandPrivate(
    notif: Notif<NotifDiscardCardFromHandPrivateArgs>
  ) {
    const { card, playerId } = notif.args;

    // TODO: check why card becomes 'invisibile' if it flips when discarding from hand
    card.location = 'hand_';

    await this.game.discard.addCard(card);
  }

  async notif_drawCardPrivate(notif: Notif<NotifDrawCardPrivateArgs>) {
    const { card } = notif.args;
    await this.game.deck.addCard(card);
    await this.game.hand.addCard(card);
    return;
  }

  async notif_revealCardsInPlay(notif: Notif<NotifRevealCardsInPlayArgs>) {
    // const {british, french, indian} = notif.args;

    const factions: Faction[] = [BRITISH, FRENCH, INDIAN];
    for (let faction of factions) {
      await this.game.cardsInPlay.addCard({
        card: notif.args[faction],
        faction,
      });
    }
  }

  async notif_selectReserveCard(notif: Notif<NotifSelectReserveCardArgs>) {
    const { faction } = notif.args;
    return;
  }

  async notif_selectReserveCardPrivate(
    notif: Notif<NotifSelectReserveCardPrivateArgs>
  ) {
    const { discardedCard } = notif.args;
    await this.game.discard.addCard(discardedCard);
    return;
  }
}
