//  .########..##..........###....##....##.########.########.
//  .##.....##.##.........##.##....##..##..##.......##.....##
//  .##.....##.##........##...##....####...##.......##.....##
//  .########..##.......##.....##....##....######...########.
//  .##........##.......#########....##....##.......##...##..
//  .##........##.......##.....##....##....##.......##....##.
//  .##........########.##.....##....##....########.##.....##

class BatPlayer {
  protected game: BayonetsAndTomahawksGame;
  protected playerColor: string;
  // private playerHexColor: string;
  protected playerId: number;
  private playerName: string;
  public faction: BRITISH_FACTION | FRENCH_FACTION;

  public playerData: BayonetsAndTomahawksPlayerData;

  constructor({
    game,
    player,
  }: {
    game: BayonetsAndTomahawksGame;
    player: BayonetsAndTomahawksPlayerData;
  }) {
    // console.log("Player", player);
    this.game = game;
    const playerId = player.id;
    this.playerId = Number(playerId);
    this.playerData = player;
    this.playerName = player.name;
    this.playerColor = player.color;
    this.faction = player.faction;
    // this.playerHexColor = player.hexColor;
    const gamedatas = game.gamedatas;

    // if (this.playerId === this.game.getPlayerId()) {
    //   dojo.place(tplPlayerHand({ playerId: this.playerId, playerName: this.playerName }), 'pp_player_tableaus', 1);
    // }

    this.setupPlayer({ gamedatas });
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  updatePlayer(playerGamedatas: BayonetsAndTomahawksPlayerData ) {
    this.updatePlayerPanel({ playerGamedatas });
  }

  // Setup functions
  setupPlayer({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    const playerGamedatas = gamedatas.players[this.playerId];

    this.setupPlayerPanel({ playerGamedatas });
    this.setupHand({ playerGamedatas });
  }

  setupHand({
    playerGamedatas,
  }: {
    playerGamedatas: BayonetsAndTomahawksPlayerData;
  }) {
    if (this.playerId === this.game.getPlayerId()) {
      // console.log('hand', this.game.hand);
      this.game.hand.getStock().addCards(playerGamedatas.hand);
    }
  }

  setupPlayerPanel({
    playerGamedatas,
  }: {
    playerGamedatas: BayonetsAndTomahawksPlayerData;
  }) {
    const playerBoardDiv: HTMLElement = $("player_board_" + this.playerId);
    playerBoardDiv.insertAdjacentHTML(
      "beforeend",
      tplPlayerPanel({ playerId: this.playerId, faction: this.faction })
    );

    this.updatePlayerPanel({ playerGamedatas });
  }

  updatePlayerPanel({ playerGamedatas }: { playerGamedatas: BgaPlayer }) {
    if (this.game.framework().scoreCtrl?.[this.playerId]) {
      this.game
        .framework()
        .scoreCtrl[this.playerId].setValue(Number(playerGamedatas.score));
    }
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

  getColor(): string {
    return this.playerColor;
  }

  // getHexColor(): string {
  //   return this.playerHexColor;
  // }

  getName(): string {
    return this.playerName;
  }

  getPlayerId(): number {
    return this.playerId;
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  // ....###.....######..########.####..#######..##....##..######.
  // ...##.##...##....##....##.....##..##.....##.###...##.##....##
  // ..##...##..##..........##.....##..##.....##.####..##.##......
  // .##.....##.##..........##.....##..##.....##.##.##.##..######.
  // .#########.##..........##.....##..##.....##.##..####.......##
  // .##.....##.##....##....##.....##..##.....##.##...###.##....##
  // .##.....##..######.....##....####..#######..##....##..######.
}
