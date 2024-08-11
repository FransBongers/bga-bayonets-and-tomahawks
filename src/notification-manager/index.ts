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
    console.log('notifications subscriptions setup');

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
      'log',
      'message',
      'addSpentMarkerToUnits',
      'advanceBattleVictoryMarker',
      'battle',
      'battleCleanup',
      'battleRemoveMarker',
      'battleReroll',
      'battleReturnCommander',
      'battleStart',
      'battleSelectCommander',
      'commanderDraw',
      'discardCardFromHand',
      'discardCardFromHandPrivate',
      'discardCardInPlay',
      'drawCardPrivate',
      'drawnReinforcements',
      'eliminateUnit',
      'indianNationControl',
      'loseControl',
      'moveRaidPointsMarker',
      'moveRoundMarker',
      'moveStack',
      'moveYearMarker',
      'moveUnit',
      'placeStackMarker',
      'placeUnitInLosses',
      'placeUnits',
      'raidPoints',
      'flipUnit',
      'removeMarkerFromStack',
      'removeMarkersEndOfActionRound',
      'returnToPool',
      'revealCardsInPlay',
      'scoreVictoryPoints',
      'selectReserveCard',
      'selectReserveCardPrivate',
      'takeControl',
      'vagariesOfWarPickUnits',
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
          if (msg != '') {
            $('gameaction_status').innerHTML = msg;
            $('pagemaintitletext').innerHTML = msg;
            $('generalactions').innerHTML = '';

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
          'discardCardFromHand',
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
    debug('notif_log', notif.args);
  }

  async notif_message(notif: Notif<unknown>) {
    // Only here so messages get displayed in title bar
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
    this.game.gameMap.updateInterface({ gamedatas: updatedGamedatas });
  }

  async notif_addSpentMarkerToUnits(
    notif: Notif<NotifAddSpentMarkerToUnitsArgs>
  ) {
    const { units } = notif.args;
    units.forEach((unit) => {
      if (unit.spent === 1) {
        const element = document.getElementById(`spent_marker_${unit.id}`);
        if (element) {
          element.setAttribute('data-spent', 'true');
        }
      }
    });
  }

  async notif_advanceBattleVictoryMarker(
    notif: Notif<NotifAdvanceBattleVictoryMarkerArgs>
  ) {
    const { marker } = notif.args;

    await this.game.gameMap.battleTrack[marker.location].addCard(marker);
  }

  async notif_battle(notif: Notif<NotifBattleArgs>) {
    const { space } = notif.args;
    this.game.gameMap.addMarkerToSpace({
      spaceId: space.id,
      type: 'battle_marker',
    });
  }

  async notif_battleCleanup(notif: Notif<NotifBattleCleanupArgs>) {
    const { attackerMarker, defenderMarker, space } = notif.args;

    await Promise.all([
      this.game.gameMap.battleTrack[attackerMarker.location].addCard(
        attackerMarker
      ),
      this.game.gameMap.battleTrack[defenderMarker.location].addCard(
        defenderMarker
      ),
    ]);

    this.game.gameMap.removeMarkerFromSpace({
      spaceId: space.id,
      type: 'battle_marker',
    });
  }

  async notif_battleRemoveMarker(notif: Notif<NotifBattleRemoveMarkerArgs>) {
    const { space } = notif.args;
    
    this.game.gameMap.removeMarkerFromSpace({
      spaceId: space.id,
      type: 'battle_marker',
    });
  }

  async notif_battleReroll(notif: Notif<NotifBattleRerollArgs>) {
    const { commander } = notif.args;
    if (commander === null) {
      return;
    }
    await this.game.gameMap.commanderRerollsTrack[commander.location].addCard(
      commander
    );
  }

  async notif_battleReturnCommander(
    notif: Notif<NotifBattleReturnCommanderArgs>
  ) {
    const { commander } = notif.args;
    const unitStack =
      this.game.gameMap.stacks[commander.location][commander.faction];
    if (unitStack) {
      await (unitStack as UnitStack).addUnit(commander);
    }
  }

  async notif_battleSelectCommander(
    notif: Notif<NotifBattleSelectCommanderArgs>
  ) {
    const { commander } = notif.args;
    await this.game.gameMap.commanderRerollsTrack[commander.location].addCard(
      commander
    );
  }

  async notif_battleStart(notif: Notif<NotifBattleStartArgs>) {
    const { attackerMarker, defenderMarker } = notif.args;

    await Promise.all([
      this.game.gameMap.battleTrack[attackerMarker.location].addCard(
        attackerMarker
      ),
      this.game.gameMap.battleTrack[defenderMarker.location].addCard(
        defenderMarker
      ),
    ]);
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

  async notif_commanderDraw(notif: Notif<NotifCommanderDrawArgs>) {
    const { commander } = notif.args;
    await this.game.pools.stocks[commander.location].addCard(commander);
  }

  async notif_discardCardFromHandPrivate(
    notif: Notif<NotifDiscardCardFromHandPrivateArgs>
  ) {
    const { card, playerId } = notif.args;

    // TODO: check why card becomes 'invisibile' if it flips when discarding from hand
    card.location = 'hand_';

    await this.game.discard.addCard(card);
  }

  async notif_discardCardInPlay(notif: Notif<NotifDiscardCardsInPlayArgs>) {
    const { card } = notif.args;
    await this.game.discard.addCard(card);
  }

  async notif_drawCardPrivate(notif: Notif<NotifDrawCardPrivateArgs>) {
    const { card } = notif.args;
    await this.game.deck.addCard(card);
    await this.game.hand.addCard(card);
    return;
  }

  async notif_drawnReinforcements(notif: Notif<NotifDrawnReinforcementsArgs>) {
    const { units, location } = notif.args;
    await this.game.pools.stocks[location].addCards(units);
  }

  async notif_eliminateUnit(notif: Notif<NotifEliminateUnitArgs>) {
    const { unit } = notif.args;

    if (unit.location.startsWith('lossesBox_')) {
      await this.game.gameMap.losses[unit.location].addCard(unit);
    } else if (unit.location === REMOVED_FROM_PLAY) {
      await this.game.tokenManager.removeCard(unit);
    } else if (unit.location === POOL_FLEETS) {
      // TODO: move to pool
    }
  }

  async notif_indianNationControl(notif: Notif<NotifIndianNationControlArgs>) {
    const { indianNation, faction } = notif.args;
    this.game.gameMap.addMarkerToSpace({
      spaceId: indianNation === CHEROKEE ? CHEROKEE_CONTROL : IROQUOIS_CONTROL,
      type: `${faction}_control_marker`,
    });
  }

  async notif_loseControl(notif: Notif<NotifLoseControlArgs>) {
    const { space, faction } = notif.args;

    this.game.gameMap.removeMarkerFromSpace({
      spaceId: space.id,
      type: `${faction}_control_marker`,
    });
  }

  async notif_moveRaidPointsMarker(
    notif: Notif<NotifMoveRaidPointsMarkerArgs>
  ) {
    const { marker } = notif.args;

    // const element = document.getElementById(marker.id);
    // const toNode = document.getElementById(marker.location);

    // if (!(element && toNode)) {
    //   console.error('Unable to move marker');
    //   return;
    // }

    // await this.game.animationManager.attachWithAnimation(
    //   new BgaSlideAnimation({ element }),
    //   toNode
    // );

    this.game.gameMap.raidTrack[marker.location].addCard(marker);
  }

  async notif_moveRoundMarker(notif: Notif<NotifMoveRoundMarkerArgs>) {
    const { nextRoundStep, marker } = notif.args;

    await this.game.gameMap.actionRoundTrack[nextRoundStep].addCard(marker);
    // this.game.gameMap.moveRoundMarker({ nextRoundStep });
  }

  async notif_moveStack(notif: Notif<NotifMoveStackArgs>) {
    const { stack, destination, faction, markers } = notif.args;
    const unitStack = this.game.gameMap.stacks[destination.id][faction];
    if (unitStack) {
      await Promise.all([
        unitStack.addUnits(stack),
        unitStack.addUnits(markers),
      ]);
    }
  }

  async notif_moveUnit(notif: Notif<NotifMoveUnitArgs>) {
    const { unit, destination, faction } = notif.args;
    const unitStack = this.game.gameMap.stacks[destination.id][faction];
    if (unitStack) {
      await (unitStack as UnitStack).addUnit(unit);
    }

    if (unit.spent === 1) {
      const element = document.getElementById(`spent_marker_${unit.id}`);
      if (element) {
        element.setAttribute('data-spent', 'true');
      }
    }
  }

  async notif_moveYearMarker(notif: Notif<NotifMoveYearMarkerArgs>) {
    const { location, marker } = notif.args;

    await this.game.gameMap.yearTrack[location].addCard(marker);
    // await this.game.gameMap.moveYearMarker({ year });
  }

  async notif_placeUnits(notif: Notif<NotifPlaceUnitsArgs>) {
    const { units, spaceId, faction } = notif.args;
    const unitStack = this.game.gameMap.stacks[spaceId][faction];
    if (!unitStack) {
      return;
    }
    await unitStack.addUnits(units);
  }

  async notif_placeStackMarker(notif: Notif<NotifPlaceStackMarkerArgs>) {
    const { marker } = notif.args;
    await this.game.gameMap.addMarkerToStack(marker);
  }

  async notif_placeUnitInLosses(notif: Notif<NotifPlaceUnitInLossesArgs>) {
    const { unit } = notif.args;

    await this.game.gameMap.losses[unit.location].addCard(unit);
    // await this.game.gameMap.moveYearMarker({ year });
  }

  async notif_raidPoints(notif: Notif<NotifPlaceRaidPointsArgs>) {
    const { space, faction } = notif.args;
    const element = document.getElementById(`${space.id}_markers`);
    if (!element) {
      return;
    }
    element.insertAdjacentHTML(
      'beforeend',
      tplMarkerOfType({ type: `${faction}_raided_marker` })
    );
  }

  async notif_flipUnit(notif: Notif<NotifReduceUnitArgs>) {
    const { unit } = notif.args;
    this.game.tokenManager.updateCardInformations(unit);
  }

  async notif_removeMarkerFromStack(
    notif: Notif<NotifRemoveMarkerFromStackArgs>
  ) {
    const { marker, from } = notif.args;
    const [spaceId, faction] = from.split('_');
    await this.game.gameMap.stacks[spaceId][faction].removeCard(marker);
    // await this.game.tokenManager.removeCard(marker);
  }

  async notif_removeMarkersEndOfActionRound(
    notif: Notif<NotifRemoveMarkersEndOfActionRoundArgs>
  ) {
    const { spentUnits } = notif.args;

    spentUnits.forEach((unit) => {
      const element = document.getElementById(`spent_marker_${unit.id}`);
      if (element) {
        element.setAttribute('data-spent', 'false');
      }
    });
  }

  async notif_returnToPool(notif: Notif<NotifReturnToPoolArgs>) {
    const { unit } = notif.args;
    await this.game.pools.stocks[unit.location].addCard(unit);
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

  async notif_scoreVictoryPoints(notif: Notif<NotifScoreVictoryPointsArgs>) {
    const { marker, points } = notif.args;

    Object.entries(points).forEach(([playerId, score]) => {
      if (this.game.framework().scoreCtrl?.[playerId]) {
        this.game.framework().scoreCtrl[playerId].setValue(Number(score));
      }
    });

    await this.game.gameMap.victoryPointsTrack[marker.location].addCard(marker);
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

  async notif_takeControl(notif: Notif<NotifTakeControlArgs>) {
    const { space, playerId, faction } = notif.args;

    if (space.control !== space.homeSpace) {
      // Place faction marker
      this.game.gameMap.addMarkerToSpace({
        spaceId: space.id,
        type: `${faction}_control_marker`,
      });
    } else {
      // Remove enemy marker
      this.game.gameMap.removeMarkerFromSpace({
        spaceId: space.id,
        type: `${faction}_control_marker`,
      });
    }
  }

  async notif_vagariesOfWarPickUnits(
    notif: Notif<NotifVagariesOfWarPickUnitsArgs>
  ) {
    const { units, location } = notif.args;
    await this.game.pools.stocks[location].addCards(units);
  }
}
