class RaidSelectTargetState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringRaidSelectTargetStateArgs;
  // private selectedUnit: BTUnit | null = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringRaidSelectTargetStateArgs) {
    debug('Entering RaidSelectTargetState');
    this.args = args;
    // this.selectedUnit = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving RaidSelectTargetState');
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
      this.updateInterfaceSelectUnit({ space, path: paths[0] });
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

    Object.entries(counts).forEach(([id, count]) => {
      if (count.count > 1) {
        this.game.setLocationSelected({ id });
      } else {
        this.game.setLocationSelectable({
          id,
          callback: () => {
            this.updateInterfaceSelectUnit({
              path: paths[count.paths[0]],
              space,
            });
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

  private updateInterfaceSelectUnit({
    space,
    path,
  }: {
    space: BTSpace;
    path: string[];
  }) {
    if (this.args.units.length === 1) {
      this.updateInterfaceConfirm({ space, path, unit: this.args.units[0] });
      return;
    }

    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a unit to Raid with'),
      args: {
        you: '${you}',
      },
    });

    this.game.setLocationSelected({ id: space.id });

    path.forEach((spaceId) => {
      this.game.setLocationSelected({ id: spaceId });
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.originId][this.args.faction];
    stack.open();
    this.args.units.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => this.updateInterfaceConfirm({ space, path, unit }),
      });
    });
    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    space,
    path,
    unit,
  }: {
    space: BTSpace;
    path: string[];
    unit: BTUnit;
  }) {
    this.game.clearPossible();

    this.game.setLocationSelected({ id: space.id });
    this.game.setUnitSelected({ id: unit.id });

    path.forEach((spaceId) => {
      this.game.setLocationSelected({ id: spaceId });
    });

    this.game.clientUpdatePageTitle({
      text: _('Raid ${spaceName}?'),
      args: {
        spaceName: _(space.name),
      },
    });
    // console.log('selectedUnit', this.selectedUnit);
    this.game.addConfirmButton({
      callback: () => {
        this.game.clearPossible();
        this.game.takeAction({
          action: 'actRaidSelectTarget',
          args: {
            path,
            spaceId: space.id,
            unitId: unit.id,
          },
        });
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
