class RaidRerollState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringRaidRerollStateArgs;
  // private selectedUnit: BTUnit | null = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringRaidRerollStateArgs) {
    debug('Entering RaidRerollState');
    this.args = args;
    // this.selectedUnit = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving RaidRerollState');
  }

  setDescription(activePlayerId: number) {}

  //  .####.##....##.########.########.########..########....###.....######..########
  //  ..##..###...##....##....##.......##.....##.##.........##.##...##....##.##......
  //  ..##..####..##....##....##.......##.....##.##........##...##..##.......##......
  //  ..##..##.##.##....##....######...########..######...##.....##.##.......######..
  //  ..##..##..####....##....##.......##...##...##.......#########.##.......##......
  //  ..##..##...###....##....##.......##....##..##.......##.....##.##....##.##......
  //  .####.##....##....##....########.##.....##.##.......##.....##..######..########

  // ..######..########.########.########...######.
  // .##....##....##....##.......##.....##.##....##
  // .##..........##....##.......##.....##.##......
  // ..######.....##....######...########...######.
  // .......##....##....##.......##..............##
  // .##....##....##....##.......##........##....##
  // ..######.....##....########.##.........######.

  private updateInterfaceInitialStep() {
    this.game.clearPossible();

    this.game.clearPossible();

    this.setPageTitle();

    this.game.addPrimaryActionButton({
      id: 'reroll_btn',
      text: _('Reroll'),
      callback: () => {
        this.game.clearPossible();
        this.game.takeAction({
          action: 'actRaidReroll',
          args: {
            reroll: true,
          },
        });
      },
    });

    this.game.addPassButton({
      text: _('Do not reroll'),
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }


  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private setPageTitle() {
    let text = '${you}';

    if (this.args.rollType === RAID_INTERCEPTION && this.args.source === PURSUIT_OF_ELEVATED_STATUS) {
      text = _('${you} may reroll the Interception roll with Pursuit of Elevated Status');
    } else if (this.args.rollType === RAID_RESOLUTION && this.args.source === PURSUIT_OF_ELEVATED_STATUS) {
      text = _('${you} may reroll the Raid roll with Pursuit of Elevated Status');
    }

    this.game.clientUpdatePageTitle({
      text,
      args: {
        you: '${you}',
      },
    });
  }

  //  ..######..##.......####..######..##....##
  //  .##....##.##........##..##....##.##...##.
  //  .##.......##........##..##.......##..##..
  //  .##.......##........##..##.......#####...
  //  .##.......##........##..##.......##..##..
  //  .##....##.##........##..##....##.##...##.
  //  ..######..########.####..######..##....##

  // .##.....##....###....##....##.########..##.......########..######.
  // .##.....##...##.##...###...##.##.....##.##.......##.......##....##
  // .##.....##..##...##..####..##.##.....##.##.......##.......##......
  // .#########.##.....##.##.##.##.##.....##.##.......######....######.
  // .##.....##.#########.##..####.##.....##.##.......##.............##
  // .##.....##.##.....##.##...###.##.....##.##.......##.......##....##
  // .##.....##.##.....##.##....##.########..########.########..######.
}
