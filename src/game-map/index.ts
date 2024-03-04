// ..######......###....##.....##.########....##.....##....###....########.
// .##....##....##.##...###...###.##..........###...###...##.##...##.....##
// .##.........##...##..####.####.##..........####.####..##...##..##.....##
// .##...####.##.....##.##.###.##.######......##.###.##.##.....##.########.
// .##....##..#########.##.....##.##..........##.....##.#########.##.......
// .##....##..##.....##.##.....##.##..........##.....##.##.....##.##.......
// ..######...##.....##.##.....##.########....##.....##.##.....##.##.......

class GameMap {
  protected game: BayonetsAndTomahawksGame;

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
      const unit = gamedatas.units.find((unit) => unit.location === space.id);
      if (!unit) {
        return;
      }
      const node = document.querySelectorAll(
        `.bt_space[data-space-id="${space.id}"]`
      );
      if (node.length === 0) {
        return;
      }
      node[0].insertAdjacentHTML(
        "afterbegin",
        tplUnit({
          faction: gamedatas.staticData.units[unit.counterId].faction,
          counterId: unit.counterId,
        })
      );
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
