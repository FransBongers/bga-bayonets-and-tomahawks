class TokenManager extends CardManager<BTToken> {
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

  setupDiv(token: BTToken, div: HTMLElement) {
    if (token.manager === UNITS) {
      // div.style.position = 'relative';
      div.classList.add('bt_token');
      div.insertAdjacentHTML(
        'beforeend',
        `<div id="spent_marker_${token.id}" data-spent="${
          token.spent === 1 ? 'true' : 'false'
        }" class="bt_spent_marker"></div>`
      );
      const isCommander =
        this.game.gamedatas.staticData.units[token.counterId]?.type ===
        COMMANDER;
      if (isCommander) {
        div.setAttribute('data-commander', 'true');
      }
      if (this.isEliminatedUnitOnBattleInfoTab(token)) {
        div.setAttribute('data-eliminated', 'true');
      }
      // if (token.reduced) {
      //   div.setAttribute('data-reduced','true');
      // }
    } else if (token.manager === MARKERS) {
      div.classList.add('bt_marker');
    }
  }

  setupFrontDiv(token: BTToken, div: HTMLElement) {
    if (token.manager === UNITS) {
      div.classList.add('bt_token_side');
      div.setAttribute('data-counter-id', token.counterId);
      const isCommander =
        this.game.gamedatas.staticData.units[token.counterId]?.type ===
        COMMANDER;
      if (isCommander) {
        div.setAttribute('data-commander', 'true');
      }
      // div.style.width = "calc(var(--btCardScale) * 250px)";
      // div.style.height = "calc(var(--btCardScale) * 179px)";
      if (token.counterId.startsWith('VOW')) {
        this.game.tooltipManager.addUnitTooltip({
          nodeId: token.id,
          unit: token,
        });
      }
    } else if (token.manager === MARKERS) {
      div.classList.add('bt_marker_side');
      div.setAttribute('data-side', 'front');
      div.setAttribute('data-type', token.type);
    }
  }

  setupBackDiv(token: BTToken, div: HTMLElement) {
    if (token.manager === UNITS) {
      div.classList.add('bt_token_side');
      div.setAttribute('data-counter-id', `${token.counterId}_reduced`);
      const isCommander =
        this.game.gamedatas.staticData.units[token.counterId]?.type ===
        COMMANDER;
      if (isCommander) {
        div.setAttribute('data-commander', 'true');
      }
    } else if (token.manager === MARKERS) {
      div.classList.add('bt_marker_side');
      div.setAttribute('data-side', 'back');
      div.setAttribute('data-type', token.type);
    }
  }

  isCardVisible(token: BTToken) {
    if (token.manager === UNITS) {
      const data = this.game.getUnitStaticData(token);
      if (
        this.isEliminatedUnitOnBattleInfoTab(token) &&
        !(data.indian || data.type === COMMANDER)
      ) {
        return false;
      }
      return !token.reduced;
    } else if (token.manager === MARKERS) {
      return token.side === 'front';
    }
  }

  isEliminatedUnitOnBattleInfoTab(token: BTUnit) {
    const isEliminated =
      token.id.endsWith('_battle') &&
      [
        REMOVED_FROM_PLAY,
        POOL_FLEETS,
        LOSSES_BOX_BRITISH,
        LOSSES_BOX_FRENCH,
      ].includes(token.location);
    return isEliminated;
  }
}
