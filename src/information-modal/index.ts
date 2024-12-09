class InformationModal {
  protected game: BayonetsAndTomahawksGame;

  private modal: Modal;

  private selectedTab: TabId = 'actions';
  private tabs: Record<TabId, { text: string }> = {
    actions: {
      text: _('Actions'),
    },
    gameMap: {
      text: _('Game Map'),
    },
  };

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

  updateInterface({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {}

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  private addButton({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    const configPanel = document.getElementById('info_panel_buttons');
    if (configPanel) {
      configPanel.insertAdjacentHTML('beforeend', tplInformationButton());
    }
  }

  private setupModal({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    this.modal = new Modal(`information_modal`, {
      class: 'information_modal',
      closeIcon: 'fa-times',
      // titleTpl:
      //   '<h2 id="popin_${id}_title" class="${class}_title">${title}</h2>',
      // title: _("Info"),
      contents: tplInformationModalContent({
        tabs: this.tabs,
        game: this.game,
      }),
      closeAction: 'hide',
      verticalAlign: 'flex-start',
      breakpoint: 740,
    });
  }

  // Setup functions
  setup({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    this.addButton({ gamedatas });
    this.setupModal({ gamedatas });
    
    this.changeTab({ id: this.selectedTab });
    Object.keys(this.tabs).forEach((id: TabId) => {
      dojo.connect($(`information_modal_tab_${id}`), 'onclick', () =>
        this.changeTab({ id })
      );
    });

    dojo.connect($(`information_button`), 'onclick', () => this.modal.show());
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

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private changeTab({ id }: { id: TabId }) {
    const currentTab = document.getElementById(
      `information_modal_tab_${this.selectedTab}`
    );
    const currentTabContent = document.getElementById(
      `bt_${this.selectedTab}`
    );
    currentTab.removeAttribute('data-state');
    if (currentTabContent) {
      currentTabContent.style.display = 'none';
    }

    this.selectedTab = id;
    const tab = document.getElementById(`information_modal_tab_${id}`);
    const tabContent = document.getElementById(`bt_${this.selectedTab}`);
    tab.setAttribute('data-state', 'selected');
    if (tabContent) {
      tabContent.style.display = '';
    }
  }
}
