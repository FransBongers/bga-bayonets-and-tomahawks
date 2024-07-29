// ..######......###....##.....##.########....##.....##....###....########.
// .##....##....##.##...###...###.##..........###...###...##.##...##.....##
// .##.........##...##..####.####.##..........####.####..##...##..##.....##
// .##...####.##.....##.##.###.##.######......##.###.##.##.....##.########.
// .##....##..#########.##.....##.##..........##.....##.#########.##.......
// .##....##..##.....##.##.....##.##..........##.....##.##.....##.##.......
// ..######...##.....##.##.....##.########....##.....##.##.....##.##.......

class GameMap {
  protected game: BayonetsAndTomahawksGame;
  public stacks: Record<
    string,
    {
      [BRITISH]: UnitStack;
      [FRENCH]: UnitStack;
    }
  > = {};
  public losses: {
    lossesBox_british: LineStock<BTToken>;
    lossesBox_french: LineStock<BTToken>;
  };

  public yearTrack: Record<string, LineStock<BTToken>> = {};
  public actionRoundTrack: Record<string, LineStock<BTToken>> = {};
  public victoryPointsTrack: Record<string, LineStock<BTToken>> = {};
  public battleTrack: Record<string, LineStock<BTToken>> = {};
  public raidTrack: Record<string, LineStock<BTToken>> = {};
  public openSeasMarkerSailBox: LineStock<BTToken>;

  public commanderRerollsTrack: Record<string, LineStock<BTToken>> = {};
  // {
  //   attacker: Record<string, LineStock<BTToken>>;
  //   defender: Record<string, LineStock<BTToken>>;
  // } = {
  //   attacker: {},
  //   defender: {},
  // };

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    const gamedatas = game.gamedatas;

    this.setupGameMap({ gamedatas });
  }

  // .##.....##.##....##.########...#######.
  // .##.....##.###...##.##.....##.##.....##
  // .##.....##.####..##.##.....##.##.....##
  // .##.....##.##.##.##.##.....##.##.....##
  // .##.....##.##..####.##.....##.##.....##
  // .##.....##.##...###.##.....##.##.....##
  // ..#######..##....##.########...#######.

  clearInterface() {
    this.losses.lossesBox_british.removeAll();
    this.losses.lossesBox_french.removeAll();

    Object.keys(this.stacks).forEach((spaceId) => {
      this.stacks[spaceId][BRITISH].removeAll();
      this.stacks[spaceId][FRENCH].removeAll();
      const element = document.getElementById(`${spaceId}_markers`);
      if (!element) {
        return;
      }
      element.replaceChildren();
    });

    // TODO: replace with remove all?
    [
      YEAR_MARKER,
      ROUND_MARKER,
      BRITISH_RAID_MARKER,
      FRENCH_RAID_MARKER,
      VICTORY_MARKER,
      OPEN_SEAS_MARKER,
    ].forEach((markerId) => {
      const node = document.getElementById(markerId);
      if (node) {
        node.remove();
      }
    });
  }

  updateInterface({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    this.setupUnitsAndSpaces({ gamedatas });
    this.setupMarkers({ gamedatas });
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  setupUnitsAndSpaces({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {
    if (!this.losses) {
      this.losses = {
        [LOSSES_BOX_BRITISH]: new LineStock<BTToken>(
          this.game.tokenManager,
          document.getElementById(LOSSES_BOX_BRITISH),
          {
            center: false,
          }
        ),
        [LOSSES_BOX_FRENCH]: new LineStock<BTToken>(
          this.game.tokenManager,
          document.getElementById(LOSSES_BOX_FRENCH),
          {
            center: false,
          }
        ),
      };
    }
    [LOSSES_BOX_BRITISH, LOSSES_BOX_FRENCH].forEach((box) => {
      const units = gamedatas.units.filter((unit) => unit.location === box);
      this.losses[box].addCards(units);
    });

    gamedatas.spaces.forEach((space) => {
      if (space.raided) {
        const element = document.getElementById(`${space.id}_markers`);
        if (!element) {
          return;
        }
        element.insertAdjacentHTML(
          'beforeend',
          tplMarkerOfType({ type: `${space.raided}_raided_marker` })
        );
      }
      if (
        space.control !== space.homeSpace &&
        (space.control === BRITISH || space.control === FRENCH)
      ) {
        this.addMarkerToSpace({
          spaceId: space.id,
          type: `${space.control}_control_marker`,
        });
      }
      if (space.battle) {
        this.addMarkerToSpace({
          spaceId: space.id,
          type: 'battle_marker',
        });
      }

      if (!this.stacks[space.id]) {
        // [BRITISH, FRENCH].forEach((faction) => {
        this.stacks[space.id] = {
          [BRITISH]: new UnitStack(
            this.game.tokenManager,
            document.getElementById(`${space.id}_british_stack`),
            {},
            BRITISH
            // (
            //   element: HTMLElement,
            //   cards: BTUnit[],
            //   lastCard: BTUnit,
            //   stock: UnitStack<BTUnit>
            // ) => {
            //   cards.forEach((card, index) => {
            //     const unitDiv = stock.getCardElement(card);
            //     unitDiv.style.position = 'absolute';
            //     unitDiv.style.top = `${index * -5}px`;
            //     unitDiv.style.left = `${index * 5}px`;
            //   });
            //   // console.log('card',lastCard);
            //   // console.log('cards',cards);
            // }
          ),
          [FRENCH]: new UnitStack(
            this.game.tokenManager,
            document.getElementById(`${space.id}_french_stack`),
            {},
            FRENCH
          ),
        };
        // });
      }

      gamedatas.units
        .filter((unit) => unit.location === space.id)
        .forEach((unit) => {
          const data = this.game.getUnitData({ counterId: unit.counterId });
          if (data.faction === BRITISH) {
            this.stacks[space.id][BRITISH].addUnit(unit);
          } else if (data.faction === FRENCH) {
            this.stacks[space.id][FRENCH].addUnit(unit);
          } else if (data.faction === INDIAN) {
            this.stacks[space.id][FRENCH].addUnit(unit);
          }
        });
    });
  }

  setupMarkers({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    // Year track
    [1755, 1756, 1757, 1758, 1759].forEach((year) => {
      this.yearTrack[`year_track_${year}`] = new LineStock<BTToken>(
        this.game.tokenManager,
        document.getElementById(`year_track_${year}`)
      );
    });

    // Action round track
    for (let i = 1; i <= 9; i++) {
      this.actionRoundTrack[`action_round_track_ar${i}`] =
        new LineStock<BTToken>(
          this.game.tokenManager,
          document.getElementById(`action_round_track_ar${i}`),
          {
            gap: '0px',
            center: false,
          }
        );
    }
    this.actionRoundTrack.action_round_track_fleetsArrive =
      new LineStock<BTToken>(
        this.game.tokenManager,
        document.getElementById('action_round_track_fleetsArrive')
      );

    this.actionRoundTrack.action_round_track_colonialsEnlist =
      new LineStock<BTToken>(
        this.game.tokenManager,
        document.getElementById('action_round_track_colonialsEnlist')
      );
    this.actionRoundTrack.action_round_track_winterQuarters =
      new LineStock<BTToken>(
        this.game.tokenManager,
        document.getElementById('action_round_track_winterQuarters')
      );

    for (let j = 1; j <= 10; j++) {
      [BRITISH, FRENCH].forEach((faction) => {
        this.victoryPointsTrack[`victory_points_${faction}_${j}`] =
          new LineStock<BTToken>(
            this.game.tokenManager,
            document.getElementById(`victory_points_${faction}_${j}`)
          );
      });
    }

    // Battle track
    for (let i = -5; i <= 10; i++) {
      ['attacker', 'defender'].forEach((side) => {
        const sideId = `battle_track_${side}_${
          i < 0 ? 'minus' : 'plus'
        }_${Math.abs(i)}`;
        this.battleTrack[sideId] = new LineStock<BTToken>(
          this.game.tokenManager,
          document.getElementById(sideId)
        );
      });
    }
    this.battleTrack[BATTLE_MARKERS_POOL] = new LineStock<BTToken>(
      this.game.tokenManager,
      document.getElementById(BATTLE_MARKERS_POOL),
      {
        gap: '4px',
        center: false,
        wrap: 'nowrap',
      }
    );

    // Raid track
    for (let k = 0; k <= 8; k++) {
      this.raidTrack[`raid_track_${k}`] = new LineStock<BTToken>(
        this.game.tokenManager,
        document.getElementById(`raid_track_${k}`),
        {
          wrap: 'nowrap',
          gap: '0px',
        }
      );
    }

    // Commander rerolls track
    for (let l = 0; l <= 3; l++) {
      ['attacker', 'defender'].forEach((side) => {
        this.commanderRerollsTrack[`commander_rerolls_track_${side}_${l}`] =
          new LineStock<BTToken>(
            this.game.tokenManager,
            document.getElementById(`commander_rerolls_track_${side}_${l}`),
            {
              center: false,
            }
          );
      });
    }
    this.openSeasMarkerSailBox = new LineStock<BTToken>(
      this.game.tokenManager,
      document.getElementById(OPEN_SEAS_MARKER_SAIL_BOX),
      {
        wrap: 'nowrap',
        gap: '0px',
      }
    );

    this.updateMarkers({ gamedatas });
  }

  updateMarkers({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    const { markers } = gamedatas;
    const yearMarker = markers[YEAR_MARKER];
    if (yearMarker && this.yearTrack[yearMarker.location]) {
      this.yearTrack[yearMarker.location].addCard(yearMarker);
    }

    const roundMarker = markers[ROUND_MARKER];
    if (roundMarker && this.actionRoundTrack[roundMarker.location]) {
      this.actionRoundTrack[roundMarker.location].addCard(roundMarker);
    }

    const britishRaidMarker = markers[BRITISH_RAID_MARKER];
    if (britishRaidMarker && this.raidTrack[britishRaidMarker.location]) {
      this.raidTrack[britishRaidMarker.location].addCard(britishRaidMarker);
    }

    const frenchRaidMarker = markers[FRENCH_RAID_MARKER];
    if (frenchRaidMarker && this.raidTrack[frenchRaidMarker.location]) {
      this.raidTrack[frenchRaidMarker.location].addCard(frenchRaidMarker);
    }

    const bBattleMarker = markers[BRITISH_BATTLE_MARKER];
    if (bBattleMarker && this.battleTrack[bBattleMarker.location]) {
      // this.battleTrack[bBattleMarker.location].addCard(bBattleMarker);
      this.battleTrack[bBattleMarker.location].addCard(bBattleMarker);
    }
    const fBattleMarker = markers[FRENCH_BATTLE_MARKER];
    if (fBattleMarker && this.battleTrack[fBattleMarker.location]) {
      // this.battleTrack[fBattleMarker.location].addCard(fBattleMarker);
      this.battleTrack[fBattleMarker.location].addCard(fBattleMarker);
    }

    if (markers[OPEN_SEAS_MARKER]) {
      this.openSeasMarkerSailBox.addCard(markers[OPEN_SEAS_MARKER]);
    }

    const victoryMarker = markers[VICTORY_MARKER];
    if (victoryMarker && this.victoryPointsTrack[victoryMarker.location]) {
      this.victoryPointsTrack[victoryMarker.location].addCard(victoryMarker);
    }

    Object.entries(markers)
      .filter(
        ([id, marker]) =>
          id.startsWith('routeMarker') && !marker.location.startsWith('supply')
      )
      .forEach(([id, marker]) => {
        this.addMarkerToStack(marker);
      });

    // TODO: loop once through all units and place in correct stock?
    gamedatas.units
      .filter((unit) => {
        return unit.location.startsWith('commander_rerolls_track');
      })
      .forEach((commander) => {
        this.commanderRerollsTrack[commander.location].addCard(commander);
      });
  }

  updateGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // Setup functions
  setupGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
      .getElementById('play_area_container')
      .insertAdjacentHTML('afterbegin', tplGameMap({ gamedatas }));
    this.setupUnitsAndSpaces({ gamedatas });
    this.setupMarkers({ gamedatas });
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

  public async moveRoundMarker({ nextRoundStep }: { nextRoundStep: string }) {
    const marker = document.getElementById('round_marker');
    const toNode = document.getElementById(nextRoundStep);

    if (!(marker && toNode)) {
      console.error('Unable to move round marker');
      return;
    }

    await this.game.animationManager.attachWithAnimation(
      new BgaSlideAnimation({ element: marker }),
      toNode
    );
  }

  public async moveYearMarker({ year }: { year: number }) {
    const marker = document.getElementById('year_marker');
    const toNode = document.getElementById(`year_track_${year}`);

    if (!(marker && toNode)) {
      console.error('Unable to move year marker');
      return;
    }

    await this.game.animationManager.attachWithAnimation(
      new BgaSlideAnimation({ element: marker }),
      toNode
    );
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public async addMarkerToStack(marker: BTMarker)
  {
    const splitLocation = marker.location.split('_');
    this.stacks[splitLocation[0]][splitLocation[1]].addCard(marker);
  }

  public addMarkerToSpace({
    spaceId,
    type,
  }: {
    spaceId: string;
    type: string;
  }) {
    const element = document.getElementById(`${spaceId}_markers`);
    if (!element) {
      return;
    }
    element.insertAdjacentHTML(
      'beforeend',
      tplMarkerOfType({ id: `${spaceId}_${type}`, type })
    );
  }

  public removeMarkerFromSpace({
    spaceId,
    type,
  }: {
    spaceId: string;
    type: string;
  }) {
    const element = document.getElementById(`${spaceId}_${type}`);
    if (!element) {
      return;
    }
    element.remove();
  }
}
