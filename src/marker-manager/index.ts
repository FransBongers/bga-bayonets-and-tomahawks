class MarkerManager extends CardManager<BTMarker> {
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

  setupDiv(marker: BTMarker, div: HTMLElement) {

    // div.style.position = 'relative';
    div.classList.add('bt_marker');
  }

  setupFrontDiv(marker: BTMarker, div: HTMLElement) {
    // div.classList.add('bt_token_side');
    // div.setAttribute('data-counter-id', card.counterId);
    // div.style.width = "calc(var(--btCardScale) * 250px)";
    // div.style.height = "calc(var(--btCardScale) * 179px)";
    div.classList.add('bt_marker_side');
    div.setAttribute('data-side', 'front');
    div.setAttribute('data-type', marker.type);
  }

  setupBackDiv(marker: BTMarker, div: HTMLElement) {
    // div.classList.add('bt_token_side');
    div.classList.add('bt_marker_side');
    div.setAttribute('data-side', 'back');
    div.setAttribute('data-type', marker.type);
  }

  isCardVisible(marker: BTMarker) {
    return marker.side === 'front';
  }
}
