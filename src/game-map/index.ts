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
      [BRITISH]: UnitStack<BTUnit>;
      [FRENCH]: UnitStack<BTUnit>;
    }
  > = {};
  public losses: {
    lossesBox_british: LineStock<BTUnit>;
    lossesBox_french: LineStock<BTUnit>;
  };
  public yearTrack: {
    year_track_1755: LineStock<BTMarker>;
    year_track_1756: LineStock<BTMarker>;
    year_track_1757: LineStock<BTMarker>;
    year_track_1758: LineStock<BTMarker>;
    year_track_1759: LineStock<BTMarker>;
  };

  public actionRoundTrack: {
    action_round_track_ar1: LineStock<BTMarker>;
    action_round_track_ar2: LineStock<BTMarker>;
    action_round_track_ar3: LineStock<BTMarker>;
    action_round_track_ar4: LineStock<BTMarker>;
    action_round_track_ar5: LineStock<BTMarker>;
    action_round_track_ar6: LineStock<BTMarker>;
    action_round_track_ar7: LineStock<BTMarker>;
    action_round_track_ar8: LineStock<BTMarker>;
    action_round_track_ar9: LineStock<BTMarker>;
    action_round_track_fleetsArrive: LineStock<BTMarker>;
    action_round_track_colonialsEnlist: LineStock<BTMarker>;
    action_round_track_winterQuarters: LineStock<BTMarker>;
  };

  public victoryPointsTrack: {
    victory_points_french_10: LineStock<BTMarker>;
    victory_points_french_9: LineStock<BTMarker>;
    victory_points_french_8: LineStock<BTMarker>;
    victory_points_french_7: LineStock<BTMarker>;
    victory_points_french_6: LineStock<BTMarker>;
    victory_points_french_5: LineStock<BTMarker>;
    victory_points_french_4: LineStock<BTMarker>;
    victory_points_french_3: LineStock<BTMarker>;
    victory_points_french_2: LineStock<BTMarker>;
    victory_points_french_1: LineStock<BTMarker>;
    victory_points_british_1: LineStock<BTMarker>;
    victory_points_british_2: LineStock<BTMarker>;
    victory_points_british_3: LineStock<BTMarker>;
    victory_points_british_4: LineStock<BTMarker>;
    victory_points_british_5: LineStock<BTMarker>;
    victory_points_british_6: LineStock<BTMarker>;
    victory_points_british_7: LineStock<BTMarker>;
    victory_points_british_8: LineStock<BTMarker>;
    victory_points_british_9: LineStock<BTMarker>;
    victory_points_british_10: LineStock<BTMarker>;
  };

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

    [
      YEAR_MARKER,
      ROUND_MARKER,
      BRITISH_RAID_MARKER,
      FRENCH_RAID_MARKER,
      VICTORY_MARKER,
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
        [LOSSES_BOX_BRITISH]: new LineStock<BTUnit>(
          this.game.unitManager,
          document.getElementById(LOSSES_BOX_BRITISH),
          {
            center: false,
          }
        ),
        [LOSSES_BOX_FRENCH]: new LineStock<BTUnit>(
          this.game.unitManager,
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

      if (!this.stacks[space.id]) {
        // [BRITISH, FRENCH].forEach((faction) => {
        this.stacks[space.id] = {
          [BRITISH]: new UnitStack<BTUnit>(
            this.game.unitManager,
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
          [FRENCH]: new UnitStack<BTUnit>(
            this.game.unitManager,
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
    this.yearTrack = {
      year_track_1755: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('year_track_1755'),
        {
          gap: '0px',
          center: false,
        }
      ),
      year_track_1756: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('year_track_1756'),
        {
          gap: '0px',
          center: false,
        }
      ),
      year_track_1757: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('year_track_1757'),
        {
          gap: '0px',
          center: false,
        }
      ),
      year_track_1758: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('year_track_1758'),
        {
          gap: '0px',
          center: false,
        }
      ),
      year_track_1759: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('year_track_1759'),
        {
          gap: '0px',
          center: false,
        }
      ),
    };

    this.actionRoundTrack = {
      action_round_track_ar1: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar1'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar2: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar2'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar3: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar3'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar4: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar4'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar5: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar5'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar6: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar6'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar7: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar7'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar8: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar8'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_ar9: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_ar9'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_fleetsArrive: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_fleetsArrive'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_colonialsEnlist: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_colonialsEnlist'),
        {
          gap: '0px',
          center: false,
        }
      ),
      action_round_track_winterQuarters: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('action_round_track_winterQuarters'),
        {
          gap: '0px',
          center: false,
        }
      ),
    };

    this.victoryPointsTrack = {
      victory_points_french_10: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_10'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_9: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_9'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_8: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_8'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_7: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_7'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_6: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_6'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_5: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_5'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_4: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_4'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_3: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_3'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_2: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_2'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_french_1: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_french_1'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_1: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_1'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_2: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_2'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_3: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_3'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_4: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_4'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_5: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_5'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_6: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_6'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_7: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_7'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_8: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_8'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_9: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_9'),
        {
          gap: '0px',
          center: false,
        }
      ),
      victory_points_british_10: new LineStock<BTMarker>(
        this.game.markerManager,
        document.getElementById('victory_points_british_10'),
        {
          gap: '0px',
          center: false,
        }
      ),
    };

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

    // const britishRaidMarker = markers[BRITISH_RAID_MARKER];
    // if (britishRaidMarker && this.raidTrack[britishRaidMarker.location]) {
    //   this.raidTrack[britishRaidMarker.location].addCard(britishRaidMarker);
    // }

    // const frenchRaidMarker = markers[FRENCH_RAID_MARKER];
    // if (frenchRaidMarker && this.raidTrack[frenchRaidMarker.location]) {
    //   this.raidTrack[frenchRaidMarker.location].addCard(frenchRaidMarker);
    // }
    if (markers[BRITISH_RAID_MARKER]) {
      document
        .getElementById(`${markers[BRITISH_RAID_MARKER].location}`)
        .insertAdjacentHTML(
          'beforeend',
          tplMarkerSide({ id: markers[BRITISH_RAID_MARKER].id })
        );
    }
    if (markers[FRENCH_RAID_MARKER]) {
      document
        .getElementById(`${markers[FRENCH_RAID_MARKER].location}`)
        .insertAdjacentHTML(
          'beforeend',
          tplMarkerSide({ id: markers[FRENCH_RAID_MARKER].id })
        );
    }

    const victoryMarker = markers[VICTORY_MARKER];
    if (victoryMarker && this.victoryPointsTrack[victoryMarker.location]) {
      this.victoryPointsTrack[victoryMarker.location].addCard(victoryMarker);
    }
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
}
