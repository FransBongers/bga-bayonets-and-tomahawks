// .##.....##....###....##....##.########.
// .##.....##...##.##...###...##.##.....##
// .##.....##..##...##..####..##.##.....##
// .#########.##.....##.##.##.##.##.....##
// .##.....##.#########.##..####.##.....##
// .##.....##.##.....##.##...###.##.....##
// .##.....##.##.....##.##....##.########.

class CardsInPlay {
  private game: BayonetsAndTomahawksGame;
  private cards: {
    [BRITISH]: LineStock<BTCard>;
    [FRENCH]: LineStock<BTCard>;
    [INDIAN]: LineStock<BTCard>;
  };

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    this.setupCardsInPlay({ gamedatas: game.gamedatas });
  }

  clearInterface() {

  }

  updateCardsInPlay({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {
    FACTIONS.forEach((faction) => {
      if (!gamedatas.cardsInPlay[faction]) {
        return;
      }
      this.addCard({ faction, card: gamedatas.cardsInPlay[faction] });
    });
  }

  public setupCardsInPlay({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {
    const node: HTMLElement = document.getElementById(
      'bt_tabbed_column_content_cards'
    );
    node.insertAdjacentHTML('afterbegin', tplCardsInPlay());

    this.cards = {
      [BRITISH]: new LineStock<BTCard>(
        this.game.cardManager,
        document.getElementById('british_card_in_play'),
        { direction: 'column', center: false }
      ),
      [FRENCH]: new LineStock<BTCard>(
        this.game.cardManager,
        document.getElementById('french_card_in_play'),
        { direction: 'column', center: false }
      ),
      [INDIAN]: new LineStock<BTCard>(
        this.game.cardManager,
        document.getElementById('indian_card_in_play'),
        { direction: 'column', center: false }
      ),
    };

    this.updateCardsInPlay({ gamedatas });
  }

  public async addCard({
    card,
    faction,
  }: {
    card: BTCard;
    faction: Faction;
  }): Promise<void> {
    // const playerPanelNode = document.getElementById(`${faction}_action_points`);
    // card.actionPoints.forEach(({ id }) => {
    //   playerPanelNode.insertAdjacentHTML(
    //     'beforeend',
    //     tplLogTokenActionPoint(faction, id)
    //   );
    // });
    await this.cards[faction].addCard(card);
  }

  public async removeCard({
    card,
    faction,
  }: {
    card: BTCard;
    faction: Faction;
  }): Promise<void> {
    await this.cards[faction].removeCard(card);
  }

  public getCards({ faction }: { faction: Faction }): BTCard[] {
    return this.cards[faction].getCards() as BTCard[];
  }

  public getStock({ faction }: { faction: Faction }): LineStock<BTCard> {
    return this.cards[faction];
  }

  public updateCardTooltips() {
    [BRITISH, FRENCH, INDIAN].forEach((faction) => {
      const cards = this.cards[faction].getCards();
      cards.forEach((card) => {
        this.game.tooltipManager.removeTooltip(card.id);
        this.game.tooltipManager.addCardTooltip({
          nodeId: card.id,
          cardId: card.id,
        });
      });
    });
  }

}
