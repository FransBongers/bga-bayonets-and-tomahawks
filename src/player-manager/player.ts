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
    this.game = game;
    const playerId = player.id;
    this.playerId = Number(playerId);
    this.playerData = player;
    this.playerName = player.name;
    this.playerColor = player.color;
    this.faction = player.faction;
    const gamedatas = game.gamedatas;

    this.setupPlayer({ gamedatas });
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  updatePlayer(gamedatas: BayonetsAndTomahawksGamedatas) {
    this.updatePlayerPanel(gamedatas);
  }

  // Setup functions
  setupPlayer({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    const playerGamedatas = gamedatas.players[this.playerId];

    this.setupPlayerPanel(gamedatas);
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

  setupPlayerPanel(gamedatas: BayonetsAndTomahawksGamedatas) {
    const playerBoardDiv: HTMLElement = $('player_board_' + this.playerId);
    playerBoardDiv.insertAdjacentHTML(
      'beforeend',
      tplPlayerPanel({ playerId: this.playerId, faction: this.faction })
    );

    this.updatePlayerPanel(gamedatas);
  }

  updatePlayerPanel(gamedatas: BayonetsAndTomahawksGamedatas) {
    const playerGamedatas = gamedatas.players[this.playerId];

    if (this.game.framework().scoreCtrl?.[this.playerId]) {
      this.game
        .framework()
        .scoreCtrl[this.playerId].setValue(Number(playerGamedatas.score));
    }

    this.setCardInfo(this.faction, playerGamedatas.actionPoints[this.faction], gamedatas.cardsInPlay[this.faction]);

    if (this.faction === FRENCH) {
      this.setCardInfo(INDIAN, playerGamedatas.actionPoints[INDIAN], gamedatas.cardsInPlay[INDIAN]);
    }

    if (playerGamedatas.actionPoints.reactionActionPointId) {
      this.setReactionActionPointId(
        playerGamedatas.actionPoints.reactionActionPointId
      );
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

  public setCardInfo(
    faction: Faction,
    actionPoints: BTActionPoint[],
    card: BTCard | null
  ) {
    const playerPanelNode = document.getElementById(`${faction}_action_points`);

    actionPoints.forEach(({ id }) => {
      playerPanelNode.insertAdjacentHTML(
        'beforeend',
        tplLogTokenActionPoint(this.faction, id)
      );
    });

    if (!card) {
      return;
    }

    this.game.tooltipManager.addCardTooltip({
      nodeId: `bt_card_info_container_${faction}`,
      cardId: card.id,
    });

    if (!card.event) {
      return;
    }

    const playerPanelEventNode = document.getElementById(
      `${faction}_event_title`
    );
    playerPanelEventNode.replaceChildren(card.event.title);
  }

  public updateActionPoints(
    faction: Faction,
    actionPoints: string[],
    operation: string
  ) {
    actionPoints.forEach((actionPoint) => {
      const playerPanelNode = document.getElementById(
        `${faction}_action_points`
      );
      if (operation === ADD_AP) {
        playerPanelNode.insertAdjacentHTML(
          'beforeend',
          tplLogTokenActionPoint(faction, actionPoint)
        );
      } else if (operation === REMOVE_AP) {
        for (let i = 0; i < playerPanelNode.children.length; i++) {
          const node = playerPanelNode.children.item(i);
          if (
            node.children.item(0).getAttribute('data-ap-id') === actionPoint
          ) {
            node.remove();
            break;
          }
        }
      }
    });
  }

  public clearPlayerPanel(faction: string) {
    const playerPanelNode = document.getElementById(`${faction}_action_points`);
    if (playerPanelNode) {
      playerPanelNode.replaceChildren();
    }
    const playerPanelEventNode = document.getElementById(
      `${faction}_event_title`
    );
    if (playerPanelEventNode) {
      playerPanelEventNode.replaceChildren();
    }
    this.game.tooltipManager.removeTooltip(`bt_card_info_container_${faction}`);
  }

  public setReactionActionPointId(actionPointId: string) {
    const playerPanelNode = document.getElementById(
      `${this.faction}_action_points`
    );
    for (let i = 0; i < playerPanelNode.children.length; i++) {
      const node = playerPanelNode.children.item(i);
      if (node.children.item(0).getAttribute('data-ap-id') === actionPointId) {
        node.children.item(0).setAttribute('data-reaction', 'true');
        break;
      }
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
