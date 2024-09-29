/**
 * A stock with manually placed units
 */
class UnitStack extends ManualPositionStock<BTToken> {
  private hovering: boolean = false;
  private faction: 'british' | 'french';
  private isOpen: boolean = false;
  private unitsPerRow: number = 5;

  /**
   * @param manager the card manager
   * @param element the stock element (should be an empty HTML Element)
   */
  constructor(
    protected manager: CardManager<BTToken>,
    protected element: HTMLElement,
    settings: CardStockSettings,
    faction: 'british' | 'french'
    // protected updateDisplay: (
    // element: HTMLElement,
    // cards: T[],
    // lastCard: T,
    // stock: ManualPositionStock<T>
    // ) => any
  ) {
    super(
      manager,
      element,
      settings,
      (
        element: HTMLElement,
        cards: BTToken[],
        lastCard: BTToken,
        stock: ManualPositionStock<BTToken>
      ) => this.updateStackDisplay(element, cards, stock)
    );
    this.element.classList.add('bt_stack');
    this.faction = faction;
    this.element.addEventListener('mouseover', () => this.onMouseOver());
    this.element.addEventListener('mouseout', () => this.onMouseOut());
    this.element.addEventListener('click', () => {
      this.isOpen = !this.isOpen;
      this.updateStackDisplay(this.element, this.getCards(), this);
    });
  }

  // protected moveFromOtherStock(
  //   card: BTToken,
  //   cardElement: HTMLElement,
  //   animation: CardAnimation<BTToken>,
  //   settings?: AddCardSettings
  // ): Promise<boolean> {
  //   let promise: Promise<boolean>;

  //   const element = animation.fromStock.contains(card)
  //     ? this.manager.getCardElement(card)
  //     : // @ts-ignore
  //       animation.fromStock.element;
  //   const fromRect = element?.getBoundingClientRect();
  //   // Added hack
  //   this.updateDisplay(this.element, [...this.getCards(), card], card, this);
  //   this.addCardElementToParent(cardElement, settings);

  //   this.removeSelectionClassesFromElement(cardElement);

  //   promise = fromRect
  //     ? this.animationFromElement(cardElement, fromRect, {
  //         originalSide: animation.originalSide,
  //         rotationDelta: animation.rotationDelta,
  //         animation: animation.animation,
  //       })
  //     : Promise.resolve(false);
  //   // in the case the card was move inside the same stock we don't remove it
  //   if (animation.fromStock && animation.fromStock != this) {
  //     animation.fromStock.removeCard(card);
  //   }

  //   if (!promise) {
  //     console.warn(`CardStock.moveFromOtherStock didn't return a Promise`);
  //     promise = Promise.resolve(false);
  //   }

  //   return promise;
  // }

  // /**
  //  * Add a card to the stock.
  //  *
  //  * @param card the card to add
  //  * @param animation a `CardAnimation` object
  //  * @param settings a `AddCardSettings` object
  //  * @returns the promise when the animation is done (true if it was animated, false if it wasn't)
  //  */
  // public async addCard(
  //   card: BTToken,
  //   animation?: CardAnimation<BTToken>,
  //   settings?: AddCardSettings
  // ): Promise<boolean> {
  //   const result = await super.addCard(card, animation, settings);
  //   this.updateDisplay(this.element, this.getCards(), card, this);
  //   return result;
  // }

  /**
   * Add a unit to the stock.
   *
   * @param unit the unit to add
   * @param animation a `CardAnimation` object
   * @param settings a `AddCardSettings` object
   * @returns the promise when the animation is done (true if it was animated, false if it wasn't)
   */
  public addUnit(
    unit: BTToken,
    animation?: CardAnimation<BTToken>,
    settings?: AddCardSettings
  ): Promise<boolean> {
    const promise = super.addCard(unit, animation, settings);
    this.element.setAttribute('data-has-unit', 'true');
    return promise;
  }

  public addUnits(
    units: BTToken[],
    animation?: CardAnimation<BTToken>,
    settings?: AddCardSettings
  ) {
    const promise = super.addCards(units, animation, settings);
    this.element.setAttribute('data-has-unit', 'true');
    return promise;
  }

  public cardRemoved(unit: BTToken, settings?: RemoveCardSettings) {
    // if (unit.manager === 'units') {
    //   const element = document.getElementById(unit.id);
    //   console.log('element in cardRemoved', element);
    //   element.style.position = '';
    // }
    const unitDiv = this.getCardElement(unit);

    if (unitDiv) {
      unitDiv.style.top = undefined;
      unitDiv.style.left = undefined;
    }

    super.cardRemoved(unit, settings);
    if (this.getCards().length === 0) {
      this.element.removeAttribute('data-has-unit');
    }
  }

  private onMouseOver() {
    this.hovering = true;
    this.updateStackDisplay(this.element, this.getCards(), this);
  }

  private onMouseOut() {
    this.hovering = false;
    this.updateStackDisplay(this.element, this.getCards(), this);
  }

  public open() {
    this.isOpen = true;
    this.updateStackDisplay(this.element, this.getCards(), this);
  }

  private updateStackDisplay(
    element: HTMLElement,
    cards: BTToken[],
    stock: ManualPositionStock<BTToken>
  ) {
    const expanded = this.isOpen || this.hovering;
    if (expanded) {
      this.element.setAttribute('data-expanded', 'true');
    }

    cards.forEach((card, index) => {
      const unitDiv = stock.getCardElement(card);
      const row = Math.floor(index / this.unitsPerRow);
      const column = index % this.unitsPerRow;

      const offset = expanded ? 52 : 8;

      unitDiv.style.top = `calc(var(--btTokenScale) * ${
        expanded ? row * offset * -1 : index * -8
      }px)`;

      let left: number = expanded ? column * offset : index * offset;
      if (this.faction === FRENCH) {
        left = left * -1;
      }
      unitDiv.style.left = `calc(var(--btTokenScale) * ${left}px)`;
    });
    if (!expanded) {
      // TODO: add timeout for this so it is removed after transition finished?
      this.element.removeAttribute('data-expanded');
    }
    // console.log('card',lastCard);
    // console.log('cards',cards);
  }
}
