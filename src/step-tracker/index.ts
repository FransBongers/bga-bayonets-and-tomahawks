class StepTracker {
  protected game: BayonetsAndTomahawksGame;

  private currentRound: string;
  private currentStep: string;

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

  updateInterface(gamedatas: BayonetsAndTomahawksGamedatas) {
    this.update(gamedatas.currentRound.id, gamedatas.currentRound.step, gamedatas.currentRound.battleOrder);
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  // Setup functions
  setup({ gamedatas }: { gamedatas: BayonetsAndTomahawksGamedatas }) {
    const node = document.getElementById('player_boards');
    if (!node) {
      return;
    }
    node.insertAdjacentHTML(
      'beforeend',
      tplStepTracker(gamedatas.currentRound.id)
    );

    // if (gamedatas.currentRound.battleOrder.length > 0) {
    //   this.addBattleOrder(gamedatas.currentRound.battleOrder);
    // }

    this.currentRound = '';
    this.currentStep = '';
    if (gamedatas.currentRound.step) {
      this.update(gamedatas.currentRound.id, gamedatas.currentRound.step, gamedatas.currentRound.battleOrder);
    }
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

  public update(round: string, step: string, battleOrder: BattleOrder) {
    if (this.currentRound !== round) {
      // Update title
      const titleNode = document.getElementById('step_stracker_title');
      if (titleNode) {
        titleNode.replaceChildren(getCurrentRoundName(round));
      }

      const contentNode = document.getElementById('step_tracker_content');
      if (contentNode) {
        contentNode.replaceChildren();
        contentNode.insertAdjacentHTML(
          'afterbegin',
          tplStepTrackerContent(round)
        );
      }
      this.addBattleOrder(battleOrder);

      this.currentRound = round;
    }

    this.changeCurrentStep(step);
  }

  private changeCurrentStep(step: string) {
    if (this.currentStep) {
      const currentActiveStepNode = document.getElementById(this.currentStep);
      if (currentActiveStepNode) {
        currentActiveStepNode.setAttribute('data-active', 'false');
      }
    }

    this.currentStep = step;
    const newActiveStepNode = document.getElementById(step);
    if (newActiveStepNode) {
      newActiveStepNode.setAttribute('data-active', 'true');
    }
  }

  public addBattleOrder(battleOrder: BattleOrder) {
    const node = document.getElementById('resolveBattlesStep');

    if (!node) {
      return;
    }
    node.insertAdjacentHTML(
      'afterend',
      battleOrder.map((step) => tplBattleOrderStep(this.game, step)).join('')
    );
  }
}
