// .########...#######...#######..##........######.
// .##.....##.##.....##.##.....##.##.......##....##
// .##.....##.##.....##.##.....##.##.......##......
// .########..##.....##.##.....##.##........######.
// .##........##.....##.##.....##.##.............##
// .##........##.....##.##.....##.##.......##....##
// .##.........#######...#######..########..######.

class Pools {
  protected game: BayonetsAndTomahawksGame;
  
  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
      const gamedatas = game.gamedatas;

    this.setupPools({ gamedatas });
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  updatePools({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // Setup functions
  setupPools({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
    .getElementById("bt_play_area")
    .insertAdjacentHTML("beforeend", tplPoolsContainer());
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
