class BattleLog {
  protected game: BayonetsAndTomahawksGame;

  private modal: Modal;
  private battleLogContent: HTMLElement;

  /**
   * {
   *    1757: {
   *      ar1: []
   *    }
   * }
   */
  private logs: Record<number, Record<string, BTCustomLog[]>> = {};

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
    const gamedatas = game.gamedatas;

    this.setup({ gamedatas });
  }

  // .##.....##.##....##.########...#######.
  // .##.....##.###...##.##.....##.##.....##
  // .##.....##.####..##.##.....##.##.....##
  // .##.....##.##.##.##.##.....##.##.....##
  // .##.....##.##..####.##.....##.##.....##
  // .##.....##.##...###.##.....##.##.....##
  // ..#######..##....##.########...#######.

  clearInterface() {}

  updateInterface({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {}

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  private addButton({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {
    const configPanel = document.getElementById('info_panel_buttons');
    if (configPanel) {
      configPanel.insertAdjacentHTML('beforeend', tplBattleLogButton());
    }
  }

  private setupModal({
    gamedatas,
  }: {
    gamedatas: BayonetsAndTomahawksGamedatas;
  }) {
    this.modal = new Modal(`battle_log_modal`, {
      class: 'battle_log_modal',
      closeIcon: 'fa-times',
      // titleTpl:
      //   '<h2 id="popin_${id}_title" class="${class}_title">${title}</h2>',
      title: _('Battle Log'),
      contents: tplBattleLogModalContent(this.game),
      closeAction: 'hide',
      verticalAlign: 'flex-start',
      breakpoint: 740,
    });
    this.battleLogContent = document.getElementById('battle_log_content');
  }

  // Setup functions
  setup({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    this.addButton({ gamedatas });
    this.setupModal({ gamedatas });

    dojo.connect($(`battle_log_button`), 'onclick', () => this.modal.show());

    gamedatas.customLogs.forEach((log) => {
      if (log.type === 'battleResult') {
        this.addLogRecord(log);
      }
    });
  }

  // .##.....##.########..########.....###....########.########
  // .##.....##.##.....##.##.....##...##.##......##....##......
  // .##.....##.##.....##.##.....##..##...##.....##....##......
  // .##.....##.########..##.....##.##.....##....##....######..
  // .##.....##.##........##.....##.#########....##....##......
  // .##.....##.##........##.....##.##.....##....##....##......
  // ..#######..##........########..##.....##....##....########

  // ..######...#######..##....##.########.########.##....##.########
  // .##....##.##.....##.###...##....##....##.......###...##....##...
  // .##.......##.....##.####..##....##....##.......####..##....##...
  // .##.......##.....##.##.##.##....##....######...##.##.##....##...
  // .##.......##.....##.##..####....##....##.......##..####....##...
  // .##....##.##.....##.##...###....##....##.......##...###....##...
  // ..######...#######..##....##....##....########.##....##....##...

  public addLogRecord(record: BTCustomLog) {
    const { year, round } = record;
    let addSectionTitle = false;
    if (!this.logs[year]) {
      console.log('set year');
      this.logs[year] = {};
      addSectionTitle = true;
    }
    if (!this.logs[year][round]) {
      console.log('set round');
      this.logs[year][round] = [];
      addSectionTitle = true;
    }
    this.logs[year][round].push(record);
    if (addSectionTitle) {
      this.battleLogContent.insertAdjacentHTML(
        'beforeend',
        tplBattleLogSectionTitle(round, year)
      );
    }
    this.battleLogContent.insertAdjacentHTML(
      'beforeend',
      tplBattleLog(this.game, record)
    );
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
