// ..######......###....##.....##.########....##.....##....###....########.
// .##....##....##.##...###...###.##..........###...###...##.##...##.....##
// .##.........##...##..####.####.##..........####.####..##...##..##.....##
// .##...####.##.....##.##.###.##.######......##.###.##.##.....##.########.
// .##....##..#########.##.....##.##..........##.....##.#########.##.......
// .##....##..##.....##.##.....##.##..........##.....##.##.....##.##.......
// ..######...##.....##.##.....##.########....##.....##.##.....##.##.......

const LOCAL_STORAGE_MAP_ZOOM_KEY = "BayonetsAndTomahawks-map-zoom";
const ZOOM_LEVELS = [0.25, 0.375, 0.5, 0.625, 0.75, 0.875, 1];
const MAX_MAP_HEIGHT = 2318;
const MAX_MAP_WIDTH = 1500;

class GameMap {
  protected game: BayonetsAndTomahawksGame;
  private zoomLevel: number;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    this.zoomLevel =
      Number(localStorage.getItem(LOCAL_STORAGE_MAP_ZOOM_KEY)) || 1;
      console.log('localStorage zoomLevel', this.zoomLevel);
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

  updateGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // Setup functions
  setupGameMap({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
      .getElementById("bt_play_area")
      .insertAdjacentHTML("afterbegin", tplGameMap({gamedatas}));
    this.updateGameMapSize();
    this.setupZoomButtons();
  }

  setupZoomButtons() {
    dojo.connect($("bt_game_map_zoom_out_button"), "onclick", this, () =>
      this.zoom({ type: "out" })
    );
    dojo.connect($("bt_game_map_zoom_in_button"), "onclick", this, () =>
      this.zoom({ type: "in" })
    );
    this.checkZoomButtonClasses();
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

  private getCurrentZoomIndex(): number {
    console.log('zoomLevel',this.zoomLevel);
    return ZOOM_LEVELS.indexOf(Number(localStorage.getItem(LOCAL_STORAGE_MAP_ZOOM_KEY)) || 1);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private checkZoomButtonClasses() {
    const zoomInButton: HTMLElement = $("bt_game_map_zoom_in_button");
    const zoomOutButton: HTMLElement = $("bt_game_map_zoom_out_button");
    
    zoomInButton.classList.remove(DISABLED);
    zoomOutButton.classList.remove(DISABLED);
    
    if (this.zoomLevel === ZOOM_LEVELS[0]) {
      zoomOutButton.classList.add(DISABLED);
    } else if (this.zoomLevel === ZOOM_LEVELS[ZOOM_LEVELS.length - 1]) {
      zoomInButton.classList.add(DISABLED);
    }
  }

  public updateGameMapSize() {
    const map = document.getElementById("bt_game_map");
    map.style.transform = `scale(${this.zoomLevel})`;
    const mapContainer = document.getElementById('bt_game_map_containter');
    mapContainer.style.width = `${this.zoomLevel * MAX_MAP_WIDTH}px`;
    mapContainer.style.height = `${this.zoomLevel * MAX_MAP_HEIGHT + 56}px`;
  }

  private zoom({ type }: { type: "in" | "out"; }) {
    const currentZoomIndex = this.getCurrentZoomIndex();
    if (type === "in" && currentZoomIndex !== ZOOM_LEVELS.length - 1) {
      this.zoomLevel = ZOOM_LEVELS[currentZoomIndex + 1];
    } else if (type === "out" && currentZoomIndex > 0) {
      this.zoomLevel = ZOOM_LEVELS[currentZoomIndex - 1];
    }
    this.updateGameMapSize();
    this.checkZoomButtonClasses();
    this.game.updatePlayAreaSize();
    localStorage.setItem(LOCAL_STORAGE_MAP_ZOOM_KEY, this.zoomLevel + "");
  }
}
