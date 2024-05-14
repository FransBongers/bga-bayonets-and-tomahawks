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
          this.game.tokenManager,
          document.getElementById(LOSSES_BOX_BRITISH),
          {
            center: false,
          }
        ),
        [LOSSES_BOX_FRENCH]: new LineStock<BTUnit>(
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
          tplCommonMarker({ type: `${space.raided}_raided_marker` })
        );
      }

      if (!this.stacks[space.id]) {
        // [BRITISH, FRENCH].forEach((faction) => {
        this.stacks[space.id] = {
          [BRITISH]: new UnitStack<BTUnit>(
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
          [FRENCH]: new UnitStack<BTUnit>(
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
    const { markers } = gamedatas;
    if (markers[YEAR_MARKER]) {
      document
        .getElementById(`year_track_${markers[YEAR_MARKER].location}`)
        .insertAdjacentHTML(
          'beforeend',
          tplMarker({ id: markers[YEAR_MARKER].id })
        );
    }
    if (markers[ROUND_MARKER]) {
      document
        .getElementById(`action_round_track_${markers[ROUND_MARKER].location}`)
        .insertAdjacentHTML(
          'beforeend',
          tplMarker({ id: markers[ROUND_MARKER].id })
        );
    }
    if (markers[BRITISH_RAID_MARKER]) {
      document
        .getElementById(`${markers[BRITISH_RAID_MARKER].location}`)
        .insertAdjacentHTML(
          'beforeend',
          tplMarker({ id: markers[BRITISH_RAID_MARKER].id })
        );
    }
    if (markers[FRENCH_RAID_MARKER]) {
      document
        .getElementById(`${markers[FRENCH_RAID_MARKER].location}`)
        .insertAdjacentHTML(
          'beforeend',
          tplMarker({ id: markers[FRENCH_RAID_MARKER].id })
        );
    }
    document
      .getElementById(markers[VICTORY_MARKER].location)
      .insertAdjacentHTML(
        'beforeend',
        tplMarker({ id: markers[VICTORY_MARKER].id })
      );
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
    const toNode = document.getElementById(
      `action_round_track_${nextRoundStep}`
    );

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
      console.error('Unable to move round marker');
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
