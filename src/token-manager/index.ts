class TokenManager extends CardManager<BTUnit> {
  constructor(public game: BayonetsAndTomahawksGame) {
    super(game, {
      getId: (card) => `${card.id}`,
      setupDiv: (card, div) => this.setupDiv(card, div),
      setupFrontDiv: (card, div: HTMLElement) => this.setupFrontDiv(card, div),
      setupBackDiv: (card, div: HTMLElement) => this.setupBackDiv(card, div),
      isCardVisible: (card) => this.isCardVisible(card),
      animationManager: game.animationManager,
    });
  }

  clearInterface() {}

  setupDiv(card: BTUnit, div: HTMLElement) {
    // div.style.width = "calc(var(--btCardScale) * 250px)";
    // div.style.height = "calc(var(--btCardScale) * 179px)";
    // console.log('setup', card);
    div.style.position = 'relative';
    div.classList.add('bt_token');
    div.insertAdjacentHTML(
      'beforeend',
      `<div id="spent_marker_${card.id}" data-spent="${
        card.spent === 1 ? 'true' : 'false'
      }" class="bt_spent_marker"></div>`
    );
  }

  setupFrontDiv(card: BTUnit, div: HTMLElement) {
    div.classList.add('bt_token_side');
    div.setAttribute('data-counter-id', card.counterId);
    // div.style.width = "calc(var(--btCardScale) * 250px)";
    // div.style.height = "calc(var(--btCardScale) * 179px)";
  }

  setupBackDiv(card: BTUnit, div: HTMLElement) {
    div.classList.add('bt_token_side');
    div.setAttribute('data-counter-id', `${card.counterId}_reduced`);
  }

  isCardVisible(card: BTUnit) {
    return true;
  }
}
