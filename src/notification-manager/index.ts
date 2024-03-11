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

    const notifs: [
      id: string,
      wait: number
      // predicate?: (notif: Notif<{ playerId: number }>) => void
    ][] = [
      // checked
      ["log", undefined],
      ["drawCardPrivate", undefined],
      ["revealCardsInPlay", undefined],
      ["selectReserveCard", undefined],
      ["selectReserveCardPrivate", undefined],
      // [
      //   "selectReserveCard",
      //   undefined,
      //   (notif) => notif.args.playerId == this.game.getPlayerId(),
      // ],
    ];

    // example: https://github.com/thoun/knarr/blob/main/src/knarr.ts
    notifs.forEach((notif) => {
      this.subscriptions.push(
        dojo.subscribe(notif[0], this, (notifDetails: Notif<unknown>) => {
          debug(`notif_${notif[0]}`, notifDetails); // log notif params (with Tisaac log method, so only studio side)
          // Show log messags in page title
          let msg = this.game.format_string_recursive(
            notifDetails.log,
            notifDetails.args as Record<string, unknown>
          );
          if (msg != "") {
            $("gameaction_status").innerHTML = msg;
            $("pagemaintitletext").innerHTML = msg;
          }

          const promise = this[`notif_${notif[0]}`](notifDetails);

          // tell the UI notification ends
          promise?.then(() =>
            this.game.framework().notifqueue.onSynchronousNotificationEnd()
          );
        })
      );

      // if (notif[2] !== undefined) {
      //   this.game
      //     .framework()
      //     .notifqueue.setIgnoreNotificationCheck(notif[0], notif[2]);
      // } else {
      // make all notif as synchronous
      // make all notif as synchronous
      this.game.framework().notifqueue.setSynchronous(notif[0], notif[1]);
      // }
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
