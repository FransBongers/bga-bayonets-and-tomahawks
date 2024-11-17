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
    if (this.args.units.length === 1) {
      this.updateInterfaceSelectTargetSpace({ unit: this.args.units[0] });
      return;
    }

    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a unit to Raid with'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.originId][this.args.faction];
    stack.open();
    this.args.units.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => this.updateInterfaceSelectTargetSpace({ unit }),
      });
    });

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectTargetSpace({ unit }: { unit: BTUnit }) {
    this.game.clearPossible();
    this.game.clientUpdatePageTitle({
      text: _('${you} must select a target Space to Raid'),
      args: {
        you: '${you}',
      },
    });

    Object.values(this.args.raidTargets).forEach((target) => {
      this.game.setLocationSelectable({
        id: target.space.id,
        callback: () => {
          this.updateInterfaceConfirm({ ...target, unit });
        },
      });
    });

    this.game.setUnitSelected({ id: unit.id });

    if (this.args.units.length === 1) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.game.addCancelButton();
    }
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

    this.game.setUnitSelected({ id: unit.id });

    path.forEach((spaceId, index) => {
      if (index === 0 && path.length !== 1) {
        return;
      }
      this.game.setLocationSelected({ id: spaceId });
    });

    Object.values(this.args.raidTargets)
      .filter((target) => target.space.id !== space.id)
      .forEach((target) => {
        this.game.setLocationSelectable({
          id: target.space.id,
          callback: () => {
            this.updateInterfaceConfirm({ ...target, unit });
          },
        });
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
            // path,
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

  // private setTargetsSelectable() {
  //   Object.values(this.args.raidTargets).forEach((target) => {
  //     this.game.setLocationSelectable({
  //       id: target.space.id,
  //       callback: () => {
  //         this.updateInterfaceSelectPath(target);
  //       },
  //     });
  //   });
  // }

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
