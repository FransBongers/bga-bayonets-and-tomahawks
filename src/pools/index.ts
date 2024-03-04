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

  setupUnits({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    POOLS.forEach((pool: string) => {
      const units = gamedatas.units.filter((unit) => unit.location === pool);
      // console.log('units ' + pool,units);
      if (units.length === 0) {
        return;
      }
      const node = document.querySelectorAll(`[data-pool-id="${pool}"]`);
      if (node.length === 0) {
        return;
      }
      
      units.forEach((unit) => {
        node[0].insertAdjacentHTML('beforeend', tplUnit({counterId: unit.counterId, style: 'position: relative;'}));
      })
    });
    // 
  }

  updatePools({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // Setup functions
  setupPools({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
    .getElementById("play_area_container")
    .insertAdjacentHTML("beforeend", tplPoolsContainer());
    this.setupUnits({gamedatas})
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
