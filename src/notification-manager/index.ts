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

    dojo.connect(this.game.framework().notifqueue, 'addToLog', () => {
      this.game.addLogClass();
    });

    const notifs: string[] = [
      // Boilerplate
      'log',
      'message',
      'clearTurn',
      'refreshUI',
      'refreshUIPrivate',
      // Game
      'addSpentMarkerToUnits',
      'moveBattleVictoryMarker',
      'battle',
      'battleCleanup',
      'battleRemoveMarker',
      'battleReroll',
      'battleReturnCommander',
      'battleStart',
      'battleSelectCommander',
      'commanderDraw',
      'constructionFort',
      'constructionRoad',
      'discardCardFromHand',
      'discardCardFromHandPrivate',
      'discardCardInPlay',
      'drawCardPrivate',
      'drawnReinforcements',
      'eliminateUnit',
      'flipMarker',
      'flipUnit',
      'frenchLakeWarships',
      'indianNationControl',
      'loseControl',
      // 'marshalTroops',
      'moveRaidPointsMarker',
      'moveRoundMarker',
      'moveStack',
      'moveYearMarker',
      'moveUnit',
      'placeStackMarker',
      'placeUnitInLosses',
      'placeUnits',
      'raidPoints',
      'drawWieChit',
      'drawWieChitPrivate',
      'removeAllRaidedMarkers',
      'removeAllRoutAndOOSMarkers',
      'removeMarkerFromStack',
      'removeMarkersEndOfActionRound',
      'returnToPool',
      'returnWIEChitsToPool',
      'revealCardsInPlay',
      'scoreVictoryPoints',
      'selectReserveCard',
      'selectReserveCardPrivate',
      'takeControl',
      'vagariesOfWarPickUnits',
      'winterQuartersAddUnitsToPools',
      'winterQuartersDisbandColonialBrigades',
      'winterQuartersPlaceIndianUnits',
      'winterQuartersReturnFleets',
      'winterQuartersReturnToColoniesMove',
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

      ['discardCardFromHand', 'drawWieChit'].forEach((notifId) => {
        this.game
          .framework()
          .notifqueue.setIgnoreNotificationCheck(
            notifId,
            (notif: Notif<{ playerId: number }>) =>
              notif.args.playerId == this.game.getPlayerId()
          );
      });
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

  setUnitSpent(unit: BTUnit) {
    const element = document.getElementById(`spent_marker_${unit.id}`);
    if (element) {
      element.setAttribute('data-spent', 'true');
    }
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

  async notif_clearTurn(notif: Notif<NotifClearTurnArgs>) {
    const { notifIds } = notif.args;
    this.game.cancelLogs(notifIds);
  }

  // notif_smallRefreshHand(notif: Notif<NotifSmallRefreshHandArgs>) {
  //   const { hand, playerId } = notif.args;
  //   const player = this.getPlayer({ playerId });
  //   player.clearHand();
  //   player.setupHand({ hand });
  // }

  async notif_refreshUI(notif: Notif<NotifRefreshUIArgs>) {
    const updatedGamedatas = {
      ...this.game.gamedatas,
      ...notif.args.datas,
    };

    this.game.gamedatas = updatedGamedatas;
    this.game.clearInterface();

    this.game.playerManager.updatePlayers({ gamedatas: updatedGamedatas });
    this.game.gameMap.updateInterface(updatedGamedatas);
    this.game.pools.updateInterface(updatedGamedatas);
  }

  async notif_refreshUIPrivate(notif: Notif<NotifRefreshUIPrivateArgs>) {
    const { wieChit, faction } = notif.args;
    if (wieChit) {
      wieChit.revealed = true;
      this.game.gameMap.wieChitPlaceholders[faction].addCard(wieChit);
    }
  }

  async notif_addSpentMarkerToUnits(
    notif: Notif<NotifAddSpentMarkerToUnitsArgs>
  ) {
    const { units } = notif.args;
    units.forEach((unit) => {
      if (unit.spent === 1) {
        this.setUnitSpent(unit);
        // const element = document.getElementById(`spent_marker_${unit.id}`);
        // if (element) {
        //   element.setAttribute('data-spent', 'true');
        // }
      }
    });
  }

  async notif_moveBattleVictoryMarker(
    notif: Notif<NotifMoveBattleVictoryMarkerArgs>
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
    const { attackerMarker, defenderMarker, space, battleContinues } =
      notif.args;

    await Promise.all([
      this.game.gameMap.battleTrack[attackerMarker.location].addCard(
        attackerMarker
      ),
      this.game.gameMap.battleTrack[defenderMarker.location].addCard(
        defenderMarker
      ),
    ]);

    if (!battleContinues) {
      this.game.gameMap.removeMarkerFromSpace({
        spaceId: space.id,
        type: 'battle_marker',
      });
    }
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

  async notif_constructionFort(notif: Notif<NotifConstructionFortArgs>) {
    const { faction, fort, option, space } = notif.args;
    if (option === PLACE_FORT_CONSTRUCTION_MARKER) {
      this.game.gameMap.addMarkerToSpace({
        spaceId: space.id,
        type: FORT_CONSTRUCTION_MARKER,
      });
    } else {
      this.game.gameMap.removeMarkerFromSpace({
        spaceId: space.id,
        type: FORT_CONSTRUCTION_MARKER,
      });
    }
    if (fort === null) {
      return;
    }
    const stack = this.game.gameMap.stacks[space.id][faction];
    if (option === REPAIR_FORT) {
      this.game.tokenManager.updateCardInformations(fort);
    } else if (option === REPLACE_FORT_CONSTRUCTION_MARKER) {
      await stack.addUnit(fort);
    } else if (option === REMOVE_FORT) {
      await stack.removeCard(fort);
    }
  }

  async notif_constructionRoad(notif: Notif<NotifConstructionRoadArgs>) {
    const { connection } = notif.args;
    this.game.gameMap.connections[connection.id].setRoad(connection.road);
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

  async notif_frenchLakeWarships(notif: Notif<NotifFrenchLakeWarshipsArgs>) {
    const { connection } = notif.args;
    const node = document.getElementById(`${connection.id}_road`);
    if (!node) {
      return;
    }
    node.setAttribute('data-type', 'french_control_marker');
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

  // async notif_marshalTroops(notif: Notif<NotifMarshalTroopsArgs>) {
  //   const { activatedUnit } = notif.args;
  //   this.setUnitSpent(activatedUnit);
  // }

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
    const { stack, destinationId, faction, markers, connection } = notif.args;
    const unitStack = this.game.gameMap.stacks[destinationId][faction];

    if (connection) {
      const connectionUI = this.game.gameMap.connections[connection.id];
      if (faction === 'british') {
        connectionUI.toLimitValue({
          faction: 'british',
          value: connection.britishLimit,
        });
      } else {
        connectionUI.toLimitValue({
          faction: 'french',
          value: connection.frenchLimit,
        });
      }
    }

    if (unitStack) {
      await Promise.all([
        unitStack.addUnits(stack),
        unitStack.addUnits(markers),
      ]);
    }
  }

  async notif_moveUnit(notif: Notif<NotifMoveUnitArgs>) {
    const { unit, destination, faction } = notif.args;
    const destinationId =
      typeof destination === 'string' ? destination : destination.id;
    const unitStack = this.game.gameMap.stacks[destinationId][faction];
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
    if (BOXES.includes(spaceId)) {
      await this.game.gameMap.losses[spaceId].addCards(units);
    } else {
      const unitStack = this.game.gameMap.stacks[spaceId][faction];
      if (!unitStack) {
        return;
      }
      await unitStack.addUnits(units);
    }
  }

  async notif_placeStackMarker(notif: Notif<NotifPlaceStackMarkerArgs>) {
    const { markers } = notif.args;

    await Promise.all(
      markers.map((marker) => this.game.gameMap.addMarkerToStack(marker))
    );
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

  async notif_flipMarker(notif: Notif<NotifFlipMarkerArgs>) {
    const { marker } = notif.args;
    this.game.tokenManager.updateCardInformations(marker);
  }

  async notif_flipUnit(notif: Notif<NotifReduceUnitArgs>) {
    const { unit } = notif.args;
    this.game.tokenManager.updateCardInformations(unit);
  }

  async notif_drawWieChit(notif: Notif<NotifDrawWieChitArgs>) {
    const { placeChit, faction } = notif.args;
    if (placeChit) {
      await this.game.gameMap.placeFakeWieChit(faction);
    }
  }

  async notif_drawWieChitPrivate(notif: Notif<NotifDrawWieChitPrivateArgs>) {
    const { chit, currentChit, faction, placeChit } = notif.args;
    if (currentChit !== null && placeChit) {
      await this.game.wieChitManager.removeCard(currentChit);
    }

    if (placeChit) {
      chit.revealed = true;
      await this.game.gameMap.wieChitPlaceholders[faction].addCard(chit);
    }
  }

  async notif_removeAllRaidedMarkers(
    notif: Notif<NotifRemoveAllRaidedMarkersArgs>
  ) {
    const { spaceIds } = notif.args;
    spaceIds.forEach((spaceId) => {
      const markersContainer = document.getElementById(`${spaceId}_markers`);
      if (!markersContainer) {
        return;
      }
      markersContainer.childNodes.forEach((element: HTMLElement) => {
        if (element.getAttribute('data-type').endsWith('raided_marker')) {
          element.remove();
        }
      });
    });
  }

  async notif_removeAllRoutAndOOSMarkers(
    notif: Notif<NotifRemoveAllRoutAndOOSMarkersArgs>
  ) {
    const { markers } = notif.args;
    markers.forEach((marker) => this.game.tokenManager.removeCard(marker));
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
    const { spentUnits, markers, frenchLakeWarshipsConnectionId } = notif.args;

    spentUnits.forEach((unit) => {
      const element = document.getElementById(`spent_marker_${unit.id}`);
      if (element) {
        element.setAttribute('data-spent', 'false');
      }
    });
    this.game.gameMap.resetConnectionLimits();
    // TODO: markers
    await Promise.all(
      markers.map((marker) => this.game.tokenManager.removeCard(marker))
    );

    if (frenchLakeWarshipsConnectionId) {
      const highway = document.getElementById(
        `${frenchLakeWarshipsConnectionId}_road`
      );
      if (!highway) {
        return;
      }
      highway.setAttribute('data-type', 'none');
    }
  }

  async notif_returnToPool(notif: Notif<NotifReturnToPoolArgs>) {
    const { unit } = notif.args;
    await this.game.pools.stocks[unit.location].addCard(unit);
  }

  async notif_returnWIEChitsToPool(
    notif: Notif<NotifReturnWIEChitsToPoolArgs>
  ) {
    [BRITISH, FRENCH].forEach((faction: BRITISH_FACTION | FRENCH_FACTION) =>
      this.game.gameMap.wieChitPlaceholders[faction].removeAll()
    );
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
      console.log('remove enemy marker');
      // Remove enemy marker
      this.game.gameMap.removeMarkerFromSpace({
        spaceId: space.id,
        type: `${otherFaction(faction)}_control_marker`,
      });
    }
  }

  async notif_vagariesOfWarPickUnits(
    notif: Notif<NotifVagariesOfWarPickUnitsArgs>
  ) {
    const { units, location } = notif.args;
    await this.game.pools.stocks[location].addCards(units);
  }

  async notif_winterQuartersAddUnitsToPools(
    notif: Notif<NotifWinterQuartersAddUnitsToPoolsArgs>
  ) {
    const { units } = notif.args;
    await Promise.all(
      units.map((unit) => this.game.pools.stocks[unit.location].addCard(unit))
    );
  }

  async notif_winterQuartersDisbandColonialBrigades(
    notif: Notif<NotifWinterQuartersPlaceIndianUnitsArgs>
  ) {
    await this.game.gameMap.losses.disbandedColonialBrigades.addCards(
      notif.args.units
    );
  }

  async notif_winterQuartersPlaceIndianUnits(
    notif: Notif<NotifWinterQuartersPlaceIndianUnitsArgs>
  ) {
    const { units } = notif.args;

    await Promise.all(
      units.map((unit) => {
        if (unit.location.startsWith('lossesBox_')) {
          return this.game.gameMap.losses[unit.location].addCard(unit);
        } else {
          return this.game.gameMap.stacks[unit.location][unit.faction].addUnit(
            unit
          );
        }
      })
    );
  }

  async notif_winterQuartersReturnFleets(
    notif: Notif<NotifWinterQuartersReturnFleetsArgs>
  ) {
    const { fleets } = notif.args;
    this.game.pools.stocks[POOL_FLEETS].addCards(fleets);
  }

  async notif_winterQuartersReturnToColoniesMove(
    notif: Notif<NotifWinterQuartersReturnToColoniesMoveArgs>
  ) {
    const { units, toSpaceId, faction } = notif.args;

    await this.game.gameMap.stacks[toSpaceId][faction].addUnits(units);
  }
}
