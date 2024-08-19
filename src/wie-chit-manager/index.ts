class WieChitManager extends CardManager<BTWIEChit> {
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

  setupDiv(token: BTWIEChit, div: HTMLElement) {
    div.classList.add('bt_marker');
  }

  setupFrontDiv(token: BTWIEChit, div: HTMLElement) {
    const faction = token.id.split('_')[1];
    div.classList.add('bt_marker_side');
    div.setAttribute('data-side', 'front');
    div.setAttribute('data-faction', faction);
    div.setAttribute('data-type', `wieChit`);
    div.setAttribute('data-value', token.value + '');
  }

  setupBackDiv(token: BTWIEChit, div: HTMLElement) {
    const faction = token.id.split('_')[1];
    div.classList.add('bt_marker_side');
    div.setAttribute('data-side', 'back');
    div.setAttribute('data-faction', faction);
    div.setAttribute('data-type', `wieChit`);
  }

  isCardVisible(token: BTWIEChit) {
    return token.revealed;
  }
}
