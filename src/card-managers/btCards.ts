class BTCardManager extends CardManager<BTCard> {
  constructor(public game: BayonetsAndTomahawksGame) {
    super(game, {
      getId: (card) => card.id,
      setupDiv: (card, div) => this.setupDiv(card, div),
      setupFrontDiv: (card, div: HTMLElement) => this.setupFrontDiv(card, div),
      setupBackDiv: (card, div: HTMLElement) => this.setupBackDiv(card, div),
      isCardVisible: (card) => this.isCardVisible(card),
      animationManager: game.animationManager,
    });
  }

  clearInterface() {}

  setupDiv(card: BTCard, div: HTMLElement) {
    div.style.width = 'calc(var(--btCardScale) * 250px)';
    div.style.height = 'calc(var(--btCardScale) * 179px)';

    div.style.position = 'relative';
    div.classList.add('bt_card_container');
  }

  setupFrontDiv(card: BTCard, div: HTMLElement) {
    div.classList.add('bt_card');
    div.setAttribute('data-card-id', card.id);
    div.style.width = 'calc(var(--btCardScale) * 250px)';
    div.style.height = 'calc(var(--btCardScale) * 179px)';

    this.game.tooltipManager.addCardTooltip({ nodeId: card.id, cardId: card.id });
  }

  setupBackDiv(card: BTCard, div: HTMLElement) {
    div.classList.add('bt_card');
    div.setAttribute('data-card-id', `${card.faction}_back`);
    div.style.width = 'calc(var(--btCardScale) * 250px)';
    div.style.height = 'calc(var(--btCardScale) * 179px)';
  }

  isCardVisible(card: BTCard) {
    if (
      card.location.startsWith('hand_') ||
      card.location.startsWith('cardInPlay_') ||
      card.location.startsWith('selected_')
    ) {
      return true;
    }
    return false;
    // if (card.type === EMPIRE_CARD_CONTAINER) {
    //   return true;
    // }

    // const { location, type } = card;
    // if (location && location.startsWith("deck")) {
    //   return false;
    // }
    // if (location === "market_west_0" || location === "market_east_0") {
    //   return false;
    // }
    // if (type === EMPIRE_CARD && card.side === REPUBLIC) {
    //   return false;
    // }
    // return true;
  }
}
