class MovementLightState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringMovementLightStateArgs;
  private selectedUnits = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringMovementLightStateArgs) {
    debug('Entering MovementLightState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving MovementLightState');
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
      text: _('${you} must select a Space to move units to'),
      args: {
        you: '${you}',
      },
    });

    this.setSpacesSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectUnits({
    space,
    remainingConnectionLimit,
  }: {
    space: BTSpace;
    remainingConnectionLimit: number;
  }) {
    this.game.clearPossible();
    this.game.setLocationSelected({ id: space.id });

    this.game.clientUpdatePageTitle({
      text: _('${you} must select units to move'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack<BTUnit> =
      this.game.gameMap.stacks[space.id][this.args.faction];
    stack.open();

    this.setUnitsSelectable();

    this.game.addConfirmButton({
      callback: () => {
        if (this.selectedUnits.length === 0) {
          return;
        }
        this.game.clearPossible();
        this.game.takeAction({
          action: 'actMovementLight',
          args: {
            unitIds: this.selectedUnits.map((unit) => unit.id),
            spaceId: space.id,
          },
        });
      },
    });
    this.game.addCancelButton({
      callback: () => {
        this.selectedUnits = [];
      },
    });
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private setSpacesSelectable() {
    Object.values(this.args.destinations).forEach((destination) => {
      this.game.setLocationSelectable({
        id: destination.space.id,
        callback: () => {
          this.updateInterfaceSelectUnits(destination);
        },
      });
    });
  }

  private setUnitsSelectable() {
    this.args.lightUnits.forEach((unit) => {
      this.game.setLocationSelectable({
        id: '' + unit.id,
        callback: (event: PointerEvent) => {
          event.preventDefault();
          event.stopPropagation();
          this.selectedUnits.push(unit);
          this.game.setLocationSelected({ id: '' + unit.id });
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
