//  .########..#######...#######..##.......########.####.########.
//  ....##....##.....##.##.....##.##..........##.....##..##.....##
//  ....##....##.....##.##.....##.##..........##.....##..##.....##
//  ....##....##.....##.##.....##.##..........##.....##..########.
//  ....##....##.....##.##.....##.##..........##.....##..##.......
//  ....##....##.....##.##.....##.##..........##.....##..##.......
//  ....##.....#######...#######..########....##....####.##.......

//  .##.....##....###....##....##....###.....######...########.########.
//  .###...###...##.##...###...##...##.##...##....##..##.......##.....##
//  .####.####..##...##..####..##..##...##..##........##.......##.....##
//  .##.###.##.##.....##.##.##.##.##.....##.##...####.######...########.
//  .##.....##.#########.##..####.#########.##....##..##.......##...##..
//  .##.....##.##.....##.##...###.##.....##.##....##..##.......##....##.
//  .##.....##.##.....##.##....##.##.....##..######...########.##.....##

class TooltipManager {
  private game: BayonetsAndTomahawksGame;
  // This can't be used since some versions of safari don't support it
  // private idRegex = /(?<=id=")[a-z]*_[0-9]*_[0-9]*(?=")/;
  private idRegex = /id="[a-z]*_[0-9]*_[0-9]*"/;
  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  public addCardTooltip({ nodeId, cardId }: { nodeId: string; cardId: string }) {
    const html = tplCardTooltip({
      card: {id: cardId} as BTCard,
      game: this.game,
      imageOnly:
        this.game.settings.get({ id: PREF_CARD_INFO_IN_TOOLTIP }) === DISABLED,
    });
    this.game.framework().addTooltipHtml(nodeId, html, 500);
  }

  // public addUnitTooltip({nodeId, unit}: {nodeId: string, unit: BTUnit}) {
  //   const staticData = this.game.getUnitStaticData(unit);
  //   const html = tplTooltipWithIcon({
  //     title: _(staticData.counterText),
  //     text: '',
  //     iconHtml: `<div class="card-side front bt_token_side" data-counter-id="${unit.counterId}"></div>`,
  //     iconWidth: 78,
  //   });
  //   this.game.framework().addTooltipHtml(nodeId, html, 500);
  // }
  public addUnitTooltip({nodeId, unit}: {nodeId: string, unit: BTUnit}) {
    const staticData = this.game.getUnitStaticData(unit);
    const html = `<div class="bt_token_side" data-counter-id="${unit.counterId}"></div>`;
    this.game.framework().addTooltipHtml(nodeId, html, 500);
  }

  public addTextToolTip({ nodeId, text }: { nodeId: string; text: string }) {
    this.game.framework().addTooltip(nodeId, _(text), '', 500);
  }

  public removeTooltip(nodeId: string) {
    this.game.framework().removeTooltip(nodeId);
  }

  public setupTooltips() {}
}
