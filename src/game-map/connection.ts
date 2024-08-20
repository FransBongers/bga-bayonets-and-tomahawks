class Connection {
  private game: BayonetsAndTomahawksGame;
  private playerId: number;
  private connection: BTConnection;
  private limits: {
    british: Counter;
    french: Counter;
  } = {
    british: new ebg.counter(),
    french: new ebg.counter(),
  };

  constructor({
    game,
    connection,
  }: {
    game: BayonetsAndTomahawksGame;
    connection: BTConnection;
  }) {
    this.game = game;
    this.connection = connection;
    this.setup(connection);
  }

  // .##.....##.##....##.########...#######.
  // .##.....##.###...##.##.....##.##.....##
  // .##.....##.####..##.##.....##.##.....##
  // .##.....##.##.##.##.##.....##.##.....##
  // .##.....##.##..####.##.....##.##.....##
  // .##.....##.##...###.##.....##.##.....##
  // ..#######..##....##.########...#######.

  clearInterface() {}

  updateInterface(connection: BTConnection) {
    this.updateUI(connection);
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  private setup(connection: BTConnection) {
    const { top, left, id } =
      this.game.gamedatas.staticData.connections[this.connection.id];
    document
      .getElementById('bt_game_map')
      .insertAdjacentHTML('afterbegin', tplConnection({ id, top, left }));

    this.limits.british.create(`${id}_britishLimit_counter`);
    this.limits.french.create(`${id}_frenchLimit_counter`);

    this.updateUI(connection);
  }

  private updateUI(connection: BTConnection) {
    this.setLimitValue({ faction: 'british', value: connection.britishLimit });
    this.setLimitValue({ faction: 'french', value: connection.frenchLimit });
    this.setRoad(connection.road);
  }

  public setRoad(roadStatus: number) {
    const element = document.getElementById(`${this.connection.id}_road`);
    if (!element) {
      return;
    }

    element.setAttribute('data-type', this.getRoadStatus(roadStatus));
  }

  private getRoadStatus(roadStatus: number) {
    switch (roadStatus) {
      case NO_ROAD:
        return 'none';
      case ROAD_UNDER_CONTRUCTION:
        return ROAD_CONSTRUCTION_MARKER;
      case HAS_ROAD:
        return ROAD_MARKER;
      default:
        return 'none';
    }
  }

  public setLimitValue({
    faction,
    value,
  }: {
    faction: BRITISH_FACTION | FRENCH_FACTION;
    value: number;
  }) {
    this.limits[faction].setValue(value);
    this.updateVisible(`${this.connection.id}_${faction}_limit`, value);
  }

  public toLimitValue({
    faction,
    value,
  }: {
    faction: BRITISH_FACTION | FRENCH_FACTION;
    value: number;
  }) {
    this.limits[faction].toValue(value);
    this.updateVisible(`${this.connection.id}_${faction}_limit`, value);
  }

  private updateVisible(elementId: string, value: number) {
    const containerElement = document.getElementById(elementId);
    if (!containerElement) {
      return;
    }
    if (value === 0) {
      containerElement.style.display = 'none';
    } else {
      containerElement.style.display = '';
    }
  }
}
