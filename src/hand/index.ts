// .##.....##....###....##....##.########.
// .##.....##...##.##...###...##.##.....##
// .##.....##..##...##..####..##.##.....##
// .#########.##.....##.##.##.##.##.....##
// .##.....##.#########.##..####.##.....##
// .##.....##.##.....##.##...###.##.....##
// .##.....##.##.....##.##....##.########.

class Hand {
  private game: BayonetsAndTomahawksGame;
  private hand: LineStock<BTCard>;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    this.setupHand();
  }

  clearInterface() {
    this.hand.removeAll();
  }

  updateHand() {}

  public setupHand() {
    const node: HTMLElement = $('game_play_area');
    node.insertAdjacentHTML('beforeend', tplHand());

    this.updateFloatingHandScale();

    const handWrapper = $('floating_hand_wrapper');
    $('floating_hand_button').addEventListener('click', () => {
      if (handWrapper.dataset.open && handWrapper.dataset.open == 'hand') {
        delete handWrapper.dataset.open;
      } else {
        handWrapper.dataset.open = 'hand';
      }
    });

    this.hand = new LineStock<BTCard>(
      this.game.cardManager,
      document.getElementById('player_hand'),
      { wrap: 'nowrap', gap: '12px', center: false }
    );
  }

  public updateFloatingHandScale()
  {
    const WIDTH = $('game_play_area').getBoundingClientRect()['width'];

    const wrapperNode = document.getElementById('floating_hand_wrapper');

    const MIN_WIDTH_THREE_CARDS = 800;

    if (WIDTH <= MIN_WIDTH_THREE_CARDS) {
      const handScale = WIDTH / MIN_WIDTH_THREE_CARDS;
      wrapperNode.style.setProperty('--handScale', `${handScale}`);
    } else {
      wrapperNode.style.setProperty('--handScale', '1');
    }

  }

  public async addCard(card: BTCard): Promise<void> {
    await this.hand.addCard(card);
  }

  public async removeCard(card: BTCard): Promise<void> {
    await this.hand.removeCard(card);
  }

  public getCards(): BTCard[] {
    return this.hand.getCards() as BTCard[];
  }

  public getStock(): LineStock<BTCard> {
    return this.hand;
  }

  public open(): void {
    const handWrapper = $('floating_hand_wrapper');
    if (handWrapper) {
      handWrapper.dataset.open = 'hand';
    }
  }

  public updateCardTooltips() {
    const cards = this.hand.getCards();
    cards.forEach((card) => {
      this.game.tooltipManager.removeTooltip(card.id);
      this.game.tooltipManager.addCardTooltip({ nodeId: card.id, cardId: card.id });
    });
  }
}
