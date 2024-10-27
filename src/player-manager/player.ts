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

  updatePlayer(playerGamedatas: BayonetsAndTomahawksPlayerData) {
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
    const playerBoardDiv: HTMLElement = $('player_board_' + this.playerId);
    playerBoardDiv.insertAdjacentHTML(
      'beforeend',
      tplPlayerPanel({ playerId: this.playerId, faction: this.faction })
    );

    this.updatePlayerPanel({ playerGamedatas });
  }

  updatePlayerPanel({
    playerGamedatas,
  }: {
    playerGamedatas: BayonetsAndTomahawksPlayerData;
  }) {
    if (this.game.framework().scoreCtrl?.[this.playerId]) {
      this.game
        .framework()
        .scoreCtrl[this.playerId].setValue(Number(playerGamedatas.score));
    }

    this.setActionPoints(
      this.faction,
      playerGamedatas.actionPoints[this.faction]
    );

    if (this.faction === FRENCH) {
      this.setActionPoints(INDIAN, playerGamedatas.actionPoints[INDIAN]);
    }
  }

  clearInterface() {
    this.clearPlayerPanel(this.faction);
    if (this.faction === FRENCH) {
      this.clearPlayerPanel(INDIAN);
    }
  }

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

  public setActionPoints(faction: Faction, actionPoints: BTActionPoint[]) {
    const playerPanelNode = document.getElementById(`${faction}_action_points`);

    actionPoints.forEach(({ id }) => {
      playerPanelNode.insertAdjacentHTML(
        'beforeend',
        tplLogTokenActionPoint(this.faction, id)
      );
    });
  }

  public updateActionPoints(faction: Faction, actionPoints: string[], operation: string) {
    actionPoints.forEach((actionPoint) => {
      const playerPanelNode = document.getElementById(`${faction}_action_points`);
      if (operation === ADD_AP) {
        playerPanelNode.insertAdjacentHTML('beforeend', tplLogTokenActionPoint(faction, actionPoint))
      } else if (operation === REMOVE_AP) {
        for (let i = 0; i < playerPanelNode.children.length; i++) {
          const node = playerPanelNode.children.item(i);
          if (node.children.item(0).getAttribute('data-ap-id') === actionPoint) {
            node.remove();
            break;
          }
        }    
      }
    })
  }

  public clearPlayerPanel(faction: string) {
    const playerPanelNode = document.getElementById(`${faction}_action_points`);
    if (playerPanelNode) {
      playerPanelNode.replaceChildren();
    }
  }

  // ....###.....######..########.####..#######..##....##..######.
  // ...##.##...##....##....##.....##..##.....##.###...##.##....##
  // ..##...##..##..........##.....##..##.....##.####..##.##......
  // .##.....##.##..........##.....##..##.....##.##.##.##..######.
  // .#########.##..........##.....##..##.....##.##..####.......##
  // .##.....##.##....##....##.....##..##.....##.##...###.##....##
  // .##.....##..######.....##....####..#######..##....##..######.
}
