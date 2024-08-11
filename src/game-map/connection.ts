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

  updateInterface({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {}

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
      .insertAdjacentHTML('beforeend', tplConnection({ id, top, left }));

    this.limits.british.create(`${id}_britishLimit_counter`);
    this.limits.french.create(`${id}_frenchLimit_counter`);

    this.updateUI(connection);
  }

  updateUI(connection: BTConnection) {
    this.setLimitValue({ faction: 'british', value: connection.britishLimit });
    this.setLimitValue({ faction: 'french', value: connection.frenchLimit });
    this.setRoad(connection.road);
  }

  public setRoad(roadStatus: number) {
    const element = document.getElementById(`${this.connection.id}_road`);
    if (!element) {
      return;
    }

    element.setAttribute('data-road', this.getRoadStatus(roadStatus));
  }

  private getRoadStatus(roadStatus: number) {
    switch (roadStatus) {
      case NO_ROAD:
        return 'false';
      case ROAD_UNDER_CONTRUCTION:
        return 'construction';
      case HAS_ROAD:
        return 'true';
      default:
        return 'false';
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
