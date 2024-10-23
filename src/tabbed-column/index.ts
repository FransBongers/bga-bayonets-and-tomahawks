// .########...#######...#######..##........######.
// .##.....##.##.....##.##.....##.##.......##....##
// .##.....##.##.....##.##.....##.##.......##......
// .########..##.....##.##.....##.##........######.
// .##........##.....##.##.....##.##.............##
// .##........##.....##.##.....##.##.......##....##
// .##.........#######...#######..########..######.

class TabbedColumn {
  protected game: BayonetsAndTomahawksGame;

  private selectedTab: TabbedColumnId = 'cards';
  private tabs: TabbedColumnTabInfo = {
    cards: {
      text: _('Cards'),
    },
    battle: {
      text: _('Battle'),
    },
    pools: {
      text: _('Pools'),
    },
    playerAid: {
      text: _('Player aid'),
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

  updateInterface(gamedatas: BayonetsAndTomahawksGamedatas) {}

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  // Setup functions
  setup({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    document
      .getElementById('play_area_container')
      .insertAdjacentHTML('beforeend', tplTabbedColumn(this.tabs));

    this.changeTab(this.selectedTab);
    Object.keys(this.tabs).forEach((id: TabbedColumnId) => {
      dojo.connect($(`bt_tabbed_column_tab_${id}`), 'onclick', () =>
        this.changeTab(id)
      );
    });
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.

  // ..######..########.########.########.########.########...######.
  // .##....##.##..........##.......##....##.......##.....##.##....##
  // .##.......##..........##.......##....##.......##.....##.##......
  // ..######..######......##.......##....######...########...######.
  // .......##.##..........##.......##....##.......##...##.........##
  // .##....##.##..........##.......##....##.......##....##..##....##
  // ..######..########....##.......##....########.##.....##..######.

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public changeTab(id: TabbedColumnId) {
    const currentTab = document.getElementById(
      `bt_tabbed_column_tab_${this.selectedTab}`
    );
    const currentTabContent = document.getElementById(
      `bt_tabbed_column_content_${this.selectedTab}`
    );
    currentTab.setAttribute('data-state', 'inactive');
    if (currentTabContent) {
      currentTabContent.setAttribute('data-visible', 'false');
    }

    this.selectedTab = id;
    const tab = document.getElementById(`bt_tabbed_column_tab_${id}`);
    const tabContent = document.getElementById(
      `bt_tabbed_column_content_${this.selectedTab}`
    );
    tab.setAttribute('data-state', 'active');
    if (tabContent) {
      tabContent.setAttribute('data-visible', 'true');
    }
  }
}
