class RaidState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringRaidStateArgs;
  private selectedUnits = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringRaidStateArgs) {
    debug('Entering RaidState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving RaidState');
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

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a target Space to raid'),
      args: {
        you: '${you}',
      },
    });

    this.setTargetsSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectPath({
    space,
    paths,
  }: {
    space: BTSpace;
    paths: string[][];
  }) {
    if (paths.length === 1) {
      this.updateInterfaceConfirm({ space, path: paths[0] });
      return;
    }

    this.game.clearPossible();

    const counts: Record<
      string,
      {
        paths: number[];
        count: number;
      }
    > = {};

    paths.forEach((path, index) => {
      path.forEach((spaceId) => {
        if (counts[spaceId]) {
          counts[spaceId].count += 1;
          counts[spaceId].paths.push(index);
        } else {
          counts[spaceId] = {
            count: 1,
            paths: [index],
          };
        }
      });
    });

    console.log('counts', counts);

    Object.entries(counts).forEach(([id, count]) => {
      if (count.count > 1) {
        this.game.setLocationSelected({ id });
      } else {
        this.game.setLocationSelectable({
          id,
          callback: () => {
            this.updateInterfaceConfirm({ path: paths[count.paths[0]], space });
          },
        });
      }
    });

    this.game.clientUpdatePageTitle({
      text: _('${you} must select the path to the target of the Raid'),
      args: {
        you: '${you}',
      },
    });

    // paths.forEach((path) => {
    //   path.forEach((spaceId) => {
    //     this.game.setLocationSelected({ id: spaceId });
    //   });
    // });
  }

  private updateInterfaceConfirm({
    space,
    path,
  }: {
    space: BTSpace;
    path: string[];
  }) {
    this.game.clearPossible();

    this.game.setLocationSelected({ id: space.id });

    path.forEach((spaceId) => {
      this.game.setLocationSelected({ id: spaceId });
    });

    this.game.clientUpdatePageTitle({
      text: _('Raid ${spaceName}?'),
      args: {
        you: '${you}',
        spaceName: _(space.name)
      },
    });

    this.game.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private setTargetsSelectable() {
    Object.values(this.args.raidTargets).forEach((target) => {
      this.game.setLocationSelectable({
        id: target.space.id,
        callback: () => {
          this.updateInterfaceSelectPath(target);
        },
      });
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
