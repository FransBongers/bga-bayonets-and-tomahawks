// ..######......###....##.....##.########....##.....##....###....########.
// .##....##....##.##...###...###.##..........###...###...##.##...##.....##
// .##.........##...##..####.####.##..........####.####..##...##..##.....##
// .##...####.##.....##.##.###.##.######......##.###.##.##.....##.########.
// .##....##..#########.##.....##.##..........##.....##.#########.##.......
// .##....##..##.....##.##.....##.##..........##.....##.##.....##.##.......
// ..######...##.....##.##.....##.########....##.....##.##.....##.##.......

class GameMap {
  protected game: BayonetsAndTomahawksGame;
  public stacks: Record<string, ManualPositionStock<BTUnit>> = {};

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
        this.stacks[space.id] = new ManualPositionStock<BTUnit>(
          this.game.tokenManager,
          document.getElementById(space.id),
          { },
          (element: HTMLElement, cards: BTUnit[], lastCard: BTUnit, stock: ManualPositionStock<BTUnit>) => {
            cards.forEach((card, index) => {
              const unitDiv = stock.getCardElement(card);
              unitDiv.style.position = 'absolute';
              unitDiv.style.top = `${index * -5}px`;
              unitDiv.style.left = `${index * 5}px`;
            })
            // console.log('card',lastCard);
            // console.log('cards',cards);
          }
        );
      }

      const units = gamedatas.units.filter((unit) => unit.location === space.id);
      if (units.length > 0) {

      }
      this.stacks[space.id].addCards(units);
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
          "beforeend",
          tplMarker({ id: markers[YEAR_MARKER].id })
        );
    }
    if (markers[ROUND_MARKER]) {
      document
        .getElementById(`action_round_track_${markers[ROUND_MARKER].location}`)
        .insertAdjacentHTML(
          "beforeend",
          tplMarker({ id: markers[ROUND_MARKER].id })
        );
    }
  }

  updateGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // Setup functions
  setupGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
      .getElementById("play_area_container")
      .insertAdjacentHTML("afterbegin", tplGameMap({ gamedatas }));
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

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...
}
