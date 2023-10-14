//  .########..##..........###....##....##.########.########.
//  .##.....##.##.........##.##....##..##..##.......##.....##
//  .##.....##.##........##...##....####...##.......##.....##
//  .########..##.......##.....##....##....######...########.
//  .##........##.......#########....##....##.......##...##..
//  .##........##.......##.....##....##....##.......##....##.
//  .##........########.##.....##....##....########.##.....##

//  .##.....##....###....##....##....###.....######...########.########.
//  .###...###...##.##...###...##...##.##...##....##..##.......##.....##
//  .####.####..##...##..####..##..##...##..##........##.......##.....##
//  .##.###.##.##.....##.##.##.##.##.....##.##...####.######...########.
//  .##.....##.#########.##..####.#########.##....##..##.......##...##..
//  .##.....##.##.....##.##...###.##.....##.##....##..##.......##....##.
//  .##.....##.##.....##.##....##.##.....##..######...########.##.....##

class PlayerManager {
  private game: BayonetsAndTomahawksGame;
  private players: Record<number, BatPlayer>;

  constructor(game: BayonetsAndTomahawksGame) {
    console.log('Constructor PlayerManager');
    this.game = game;
    this.players = {};

    for (const playerId in game.gamedatas.players) {
      const player = game.gamedatas.players[playerId];
        this.players[playerId] = new BatPlayer({ player, game: this.game });
     
    }

  }

  getPlayer({ playerId }: { playerId: number }): BatPlayer {
    return this.players[playerId];
  }

  getPlayers(): BatPlayer[] {
    return Object.values(this.players);
  }

  getPlayerIds(): number[] {
    return Object.keys(this.players).map(Number);
  }

  updatePlayers({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    for (const playerId in gamedatas.players) {
      this.players[playerId].updatePlayer({ gamedatas });
    }
  }

  clearInterface() {
    Object.keys(this.players).forEach((playerId) => {
      this.players[playerId].clearInterface();
    });
  }
}
