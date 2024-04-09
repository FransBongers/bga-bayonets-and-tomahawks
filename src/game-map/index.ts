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

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    const gamedatas = game.gamedatas;

    this.setupGameMap({ gamedatas });
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  setupUnits({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    gamedatas.spaces.forEach((space) => {
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
          }
        });
      // if (units.length > 0) {
      // }

      // this.stacks[space.id][BRITISH].addCards(units.filter((unit) => unit.));
      // if (!unit) {
      //   return;
      // }
      // const node = document.getElementById(space.id);
      // if (!node) {
      //   return;
      // }
      // node.insertAdjacentHTML(
      //   "afterbegin",
      //   tplUnit({
      //     faction: gamedatas.staticData.units[unit.counterId].faction,
      //     counterId: unit.counterId,
      //   })
      // );
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
  }

  updateGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // Setup functions
  setupGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
      .getElementById('play_area_container')
      .insertAdjacentHTML('afterbegin', tplGameMap({ gamedatas }));
    this.setupUnits({ gamedatas });
    this.setupMarkers({ gamedatas });
  }

  clearInterface() {}

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

    if (!marker && toNode) {
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

    if (!marker && toNode) {
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
