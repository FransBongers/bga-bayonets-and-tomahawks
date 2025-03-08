class BattleTab {
  protected game: BayonetsAndTomahawksGame;

  public stocks: {
    attacker: Record<string, LineStock<BTToken>>;
    defender: Record<string, LineStock<BTToken>>;
  } = {
    attacker: {},
    defender: {},
  };

  private counters: {
    score: {
      attacker: Counter;
      defender: Counter;
    };
    commanderRerolls: {
      attacker: Counter;
      defender: Counter;
    };
  } = {
    score: {
      attacker: new ebg.counter(),
      defender: new ebg.counter(),
    },
    commanderRerolls: {
      attacker: new ebg.counter(),
      defender: new ebg.counter(),
    },
  };

  private factionSideMap: {
    british: 'attacker' | 'defender';
    french: 'attacker' | 'defender';
  };
  private sideFactionMap: {
    attacker: BRITISH_FACTION | FRENCH_FACTION;
    defender: BRITISH_FACTION | FRENCH_FACTION;
  };
  private spaceId: string;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    const gamedatas = game.gamedatas;

    this.setupBattleInfo({ gamedatas });
  }

  // .##.....##.##....##.########...#######.
  // .##.....##.###...##.##.....##.##.....##
  // .##.....##.####..##.##.....##.##.....##
  // .##.....##.##.##.##.##.....##.##.....##
  // .##.....##.##..####.##.....##.##.....##
  // .##.....##.##...###.##.....##.##.....##
  // ..#######..##....##.########...#######.

  clearInterface() {
    BATTLE_SIDES.forEach((side) => {
      Object.values(this.stocks[side]).forEach((stock) => stock.removeAll());
    });
  }

  updateInterface(gamedatas: BayonetsAndTomahawksGamedatas) {
    if (gamedatas.activeBattleLog !== null) {
      this.setupActiveBattle(gamedatas);
    }
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  private setupCounters() {
    BATTLE_SIDES.forEach((side) => {
      this.counters.score[side].create(`bt_active_battle_${side}_score`);
      this.counters.commanderRerolls[side].create(
        `bt_active_battle_${side}_rerolls`
      );
    });
  }

  private setupStocks() {
    BATTLE_SIDES.forEach((side) => {
      [...BATTLE_ROLL_SEQUENCE, MILITIA, COMMANDER].forEach((stepId) => {
        this.stocks[side][stepId] = new LineStock<BTToken>(
          this.game.tokenManager,
          document.getElementById(
            `bt_active_battle_sequence_${stepId}_${side}_units`
          ),
          {
            center: false,
            gap: '4px',
          }
        );
      });

      this.stocks[side][COMMANDER_IN_PLAY] = new LineStock<BTToken>(
        this.game.tokenManager,
        document.getElementById(`bt_active_battle_${side}_commander`)
      );
    });
  }

  // Setup functions
  setupBattleInfo({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    const node = document.getElementById('bt_tabbed_column_content_battle');

    node.insertAdjacentHTML('beforeend', tplActiveBattleLog(this.game));
    node.insertAdjacentHTML('beforeend', tplBattleInfo(this.game));

    this.setupStocks();
    this.setupCounters();

    if (gamedatas.activeBattleLog !== null) {
      this.setupActiveBattle(gamedatas);
      this.game.tabbedColumn.changeTab('battle');
    } else {
      this.updateActiveBattleVisibility(false);
    }
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.

  // ..######..########.########.########.########.########...######.
  // .##....##.##..........##.......##....##.......##.....##.##....##
  // .##.......##..........##.......##....##.......##.....##.##......
  // ..######..######......##.......##....######...########...######.
  // .......##.##..........##.......##....##.......##...##.........##
  // .##....##.##..........##.......##....##.......##....##..##....##
  // ..######..########....##.......##....########.##.....##..######.

  private isMilitiaMarker(marker: BTMarker) {
    return [BRITISH_MILITIA_MARKER, FRENCH_MILITIA_MARKER].includes(
      marker.type
    );
  }

  public async addMarker(marker: BTMarker) {
    if (!this.spaceId || !this.isMilitiaMarker(marker)) {
      return;
    }
    const faction = marker.type === BRITISH_MILITIA_MARKER ? BRITISH : FRENCH;
    this.stocks[this.factionSideMap[faction]][MILITIA].addCard(
      updateUnitIdForBattleInfo({ ...marker })
    );
  }

  public async removeMarker(marker: BTMarker) {
    if (!this.spaceId || !this.isMilitiaMarker(marker)) {
      return;
    }
    const faction = marker.type === BRITISH_MILITIA_MARKER ? BRITISH : FRENCH;
    this.stocks[this.factionSideMap[faction]][MILITIA].removeCard(
      updateUnitIdForBattleInfo({ ...marker })
    );
  }

  public incScoreCounter(marker: BTMarker, value: number) {
    if (marker.location.includes(ATTACKER)) {
      this.counters.score.attacker.incValue(value);
    } else if (marker.location.includes(DEFENDER)) {
      this.counters.score.defender.incValue(value);
    }
  }

  public incCommanderRerolls(commander: BTUnit, value: number) {
    if (commander.location.includes(ATTACKER)) {
      this.counters.commanderRerolls.attacker.incValue(value);
    } else if (commander.location.includes(DEFENDER)) {
      this.counters.commanderRerolls.defender.incValue(value);
    }
  }

  private resetCounters() {
    BATTLE_SIDES.forEach((side) => {
      this.counters.commanderRerolls[side].setValue(0);
      this.counters.score[side].setValue(0);
    });
  }

  public battleIsActive(): boolean {
    return !!this.spaceId;
  }

  public async setCommanderInPlay(commander: BTUnit) {
    const side = getBattleSideFromLocation(commander);
    this.setCommanderCounterVisibility(side, true);
    this.counters.commanderRerolls[side].toValue(
      Number(commander.location.split('_')[4])
    );
    await this.stocks[side][COMMANDER_IN_PLAY].addCard(
      updateUnitIdForBattleInfo(commander)
    );
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public isEliminated(unit: BTUnit) {
    if (
      [
        REMOVED_FROM_PLAY,
        POOL_FLEETS,
        LOSSES_BOX_BRITISH,
        LOSSES_BOX_FRENCH,
      ].includes(unit.location)
    ) {
      return true;
    }
    return false;
  }

  public async eliminateUnit(unit: BTUnit) {
    if (!this.spaceId) {
      return;
    }
    const node = document.getElementById(getUnitIdForBattleInfo(unit));

    if (node) {
      node.setAttribute('data-eliminated', 'true');
    }
    const side = this.factionSideMap[unit.faction];
    console.timeLog
    if (node && this.game.getUnitStaticData(unit).type === COMMANDER) {
      await this.stocks[side][COMMANDER].addCard(updateUnitIdForBattleInfo({...unit}));
    }
  }

  public removeAllDiceResultsAndUnitsFromStocks() {
    BATTLE_SIDES.forEach((side) => {
      Object.values(this.stocks[side]).forEach((stock) => stock.removeAll());
    });

    [...BATTLE_ROLL_SEQUENCE, MILITIA].forEach((stepId) => {
      BATTLE_SIDES.forEach((side) => {
        const diceNode = document.getElementById(
          `bt_active_battle_sequence_${stepId}_${side}_rolls`
        );
        if (diceNode) {
          diceNode.replaceChildren();
        }
      });
    });
  }

  public battleEnd() {
    this.updateActiveBattleVisibility(false);
    this.spaceId = null;
    this.factionSideMap = null;
    this.sideFactionMap = null;
    this.removeAllDiceResultsAndUnitsFromStocks();
  }

  public battleStart({
    attacker,
    defender,
    spaceId,
    unitsPerFaction,
    setup = false,
  }: {
    attacker: BRITISH_FACTION | FRENCH_FACTION;
    defender: BRITISH_FACTION | FRENCH_FACTION;
    spaceId: string;
    unitsPerFaction: { british: BTUnit[]; french: BTUnit[] };
    setup?: boolean;
  }) {
    // Set active battle data
    this.factionSideMap = {
      british: attacker === BRITISH ? 'attacker' : 'defender',
      french: attacker === FRENCH ? 'attacker' : 'defender',
    };
    this.sideFactionMap = {
      attacker,
      defender,
    };

    this.spaceId = spaceId;
    this.updateTitle();
    this.updateMapImage();
    this.updateSides();
    this.hideCommanders();
    this.resetCounters();

    const unitsPerSide = {
      attacker: unitsPerFaction[this.sideFactionMap['attacker']],
      defender: unitsPerFaction[this.sideFactionMap['defender']],
    };
    this.addUnits(unitsPerSide);

    this.updateActiveBattleVisibility(true);
  }

  private updateActiveBattleVisibility(visible: boolean) {
    const node = document.getElementById('bt_active_battle_log');
    if (visible) {
      node.style.display = '';
    } else {
      node.style.display = 'none';
    }
  }

  private getUnitsForBattleRollSequenceStep(units: BTUnit[], stepId: string) {
    return units.filter((unit) => {
      if (unit.location.startsWith('commander_rerolls_track_')) {
        return false;
      }
      const staticData = this.game.getUnitStaticData(unit);

      switch (stepId) {
        case NON_INDIAN_LIGHT:
          return !staticData.indian && staticData.type === LIGHT;
        case INDIAN:
          return staticData.indian;
        case HIGHLAND_BRIGADES:
          return staticData.highland && staticData.type === BRIGADE;
        case METROPOLITAN_BRIGADES:
          return (
            staticData.type === BRIGADE &&
            !staticData.highland &&
            staticData.metropolitan
          );
        case NON_METROPOLITAN_BRIGADES:
          return (
            staticData.type === BRIGADE &&
            !staticData.metropolitan &&
            !staticData.highland
          );
        case FLEETS:
          return staticData.type === FLEET;
        case BASTIONS_OR_FORT:
          return staticData.type === FORT || staticData.type === BASTION;
        case ARTILLERY:
          return staticData.type === ARTILLERY;
        case COMMANDER:
          return staticData.type === COMMANDER;
        default:
          return false;
      }
    });
  }

  private setupActiveBattle(gamedatas: BayonetsAndTomahawksGamedatas) {
    const activeBattleLog: BTActiveBattleLog = gamedatas.activeBattleLog;
    const { attacker, defender } = activeBattleLog;

    this.battleStart({
      attacker: activeBattleLog.attacker,
      defender: activeBattleLog.defender,
      spaceId: activeBattleLog.spaceId,
      unitsPerFaction: {
        british: activeBattleLog.british.units,
        french: activeBattleLog.french.units,
      },
    });

    this.placeCommandersInPlay({
      british: activeBattleLog.british.units,
      french: activeBattleLog.french.units,
    });

    this.setupPlaceMilitia({
      british: activeBattleLog.british.militia,
      french: activeBattleLog.french.militia,
    });

    [BRITISH_BATTLE_MARKER, FRENCH_BATTLE_MARKER].forEach((markerId) => {
      const marker = gamedatas.markers[markerId];
      let value = Number(marker.location.split('_')[4]);
      if (marker.location.includes('minus')) {
        value = value * -1;
      }
      this.counters.score[
        marker.location.includes(ATTACKER) ? ATTACKER : DEFENDER
      ].setValue(value);
    });

    BATTLE_SIDES.forEach((side) => {
      Object.entries(
        activeBattleLog[side === 'attacker' ? attacker : defender].rolls
      ).forEach(([stepId, dieResults]) => {
        this.updateDiceResults(stepId, dieResults, this.sideFactionMap[side]);
      });
    });
  }

  private updateTitle() {
    const titleNode = document.getElementById('bt_active_battle_title');
    titleNode.replaceChildren();
    titleNode.insertAdjacentHTML(
      'afterbegin',
      this.game.format_string_recursive(
        _('Battle in ${tkn_boldText_spaceName}'),
        {
          tkn_boldText_spaceName: _(
            this.game.getSpaceStaticData(this.spaceId).name
          ),
        }
      )
    );
  }

  private updateSides() {
    BATTLE_SIDES.forEach((side) => {
      const bannerNode = document.getElementById(
        `bt_active_battle_${side}_banner`
      );
      bannerNode.setAttribute('data-faction', this.sideFactionMap[side]);

      const factionContainerNode = document.getElementById(
        `bt_active_battle_${side}_faction_container`
      );
      factionContainerNode.setAttribute(
        'data-faction',
        this.sideFactionMap[side]
      );
    });
  }

  private setCommanderCounterVisibility(
    side: 'attacker' | 'defender',
    visible: boolean
  ) {
    const commanderNode = document.getElementById(
      `bt_active_battle_${side}_commander_container`
    );
    if (visible) {
      commanderNode.style.display = '';
    } else {
      commanderNode.style.display = 'none';
    }
  }

  private hideCommanders() {
    BATTLE_SIDES.forEach((side) =>
      this.setCommanderCounterVisibility(side, false)
    );
  }

  public placeCommandersInPlay(unitsPerSide: {
    british: BTUnit[];
    french: BTUnit[];
  }) {
    BATTLE_SIDES.forEach((side) => {
      const commanderInPlay = unitsPerSide[this.sideFactionMap[side]].find(
        (unit) => unit.location.includes(`commander_rerolls_track_${side}`)
      );
      if (commanderInPlay) {
        this.setCommanderCounterVisibility(side, true);
        this.stocks[side][COMMANDER_IN_PLAY].addCard(
          updateUnitIdForBattleInfo(commanderInPlay)
        );
        this.counters.commanderRerolls[side].setValue(
          Number(commanderInPlay.location.split('_')[4])
        );
      }
    });
  }

  public setupPlaceMilitia(input: { british: BTMarker[]; french: BTMarker[] }) {
    [BRITISH, FRENCH].forEach((faction) => {
      const side = this.factionSideMap[faction];
      const unitNodeId = `bt_active_battle_sequence_${MILITIA}_${side}_container`;
      if (input[faction].length === 0) {
        this.setHasUnits(unitNodeId, false);
        return;
      }
      this.stocks[side][MILITIA].addCards(
        input[faction].map(updateUnitIdForBattleInfo)
      );
      this.setHasUnits(unitNodeId, true);
    });
  }

  public updateMapImage() {
    if (!this.spaceId) {
      return;
    }
    const root = document.documentElement;
    const rightColumnScale = root.style.getPropertyValue('--rightColumnScale');

    const staticData = this.game.getSpaceStaticData(this.spaceId);
    const mapNode = document.getElementById('bt_active_battle_log_map_detail');

    const offsetX = Number(rightColumnScale) * 750; // half of map space width / height
    const offsetY = 75;
    const positionX = -0.705 * staticData.left + offsetX;
    const positionY = -0.705 * staticData.top + offsetY;

    mapNode.style.backgroundPositionX = `${positionX}px`;
    mapNode.style.backgroundPositionY = `${positionY}px`;
  }

  public updateDiceResults(
    stepId: string,
    diceResults: string[],
    faction: BRITISH_FACTION | FRENCH_FACTION
  ) {
    if (!this.spaceId) {
      return;
    }

    const diceRollsNode = document.getElementById(
      `bt_active_battle_sequence_${stepId}_${this.factionSideMap[faction]}_rolls`
    );
    diceRollsNode.replaceChildren();
    diceRollsNode.insertAdjacentHTML(
      'beforeend',
      diceResults
        .map((dieResult) => tplActiveBattleDieResult(dieResult))
        .join('')
    );
  }

  private addUnits(unitsPerSide: { attacker: BTUnit[]; defender: BTUnit[] }) {
    [...BATTLE_ROLL_SEQUENCE, COMMANDER].forEach((stepId) => {
      ['attacker', 'defender'].forEach((side) => {
        const unitNodeId = `bt_active_battle_sequence_${stepId}_${side}_container`;

        const units = this.getUnitsForBattleRollSequenceStep(
          unitsPerSide[side],
          stepId
        );
        if (units.length > 0) {
          this.stocks[side][stepId].addCards(
            units.map(updateUnitIdForBattleInfo)
          );
          this.setHasUnits(unitNodeId, true);
        } else {
          this.setHasUnits(unitNodeId, false);
        }
      });
    });
  }

  private setHasUnits(nodeId: string, hasUnits: boolean) {
    const unitNode = document.getElementById(nodeId);
    unitNode.setAttribute('data-has-units', hasUnits ? 'true' : 'false');
  }
}
