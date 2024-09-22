class WinterQuartersReturnToColoniesSelectStackState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersReturnToColoniesSelectStackStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringWinterQuartersReturnToColoniesSelectStackStateArgs) {
    debug('Entering WinterQuartersReturnToColoniesSelectStackState');
    this.args = args;
    this.selectedUnits = [];
    // this.selectedOption = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersReturnToColoniesSelectStackState');
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
      text: _('${you} must select a stack to move (${number} remaining)'),
      args: {
        you: '${you}',
        number: Object.keys(this.args.options).length
      },
    });

    this.setStacksSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectDestination({
    option,
  }: {
    option: WinterQuartersReturnToColoniesSelectStackOption;
  }) {
    const destinations = Object.entries(option.destinations);
    if (destinations.length === 1) {
      this.updateInterfaceConfirm({
        origin: option.space,
        destination: destinations[0][1],
      });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select Space to move your stack to'),
      args: {
        you: '${you}',
      },
    });

    this.game.setStackSelected({
      spaceId: option.space.id,
      faction: this.args.faction,
    });

    destinations.forEach(
      ([destinationId, destinationOption]) => {
        this.game.setLocationSelectable({
          id: destinationId,
          callback: () =>
            this.updateInterfaceConfirm({
              origin: option.space,
              destination: destinationOption,
            }),
        });
      }
    );

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    origin,
    destination,
  }: {
    origin: BTSpace;
    destination: WinterQuartersReturnToColoniesSelectStackDestination;
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Move stack from ${originSpaceName} to ${destinationSpaceName}?'),
      args: {
        originSpaceName: _(origin.name),
        destinationSpaceName: _(destination.space.name),
      },
    });
    destination.path.forEach((spaceId) =>
      this.game.setLocationSelected({ id: spaceId })
    );

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actWinterQuartersReturnToColoniesSelectStack',
        args: {
          originId: origin.id,
          destinationId: destination.space.id,
          path: destination.path,
        },
      });
    };

    if (
      this.game.settings.get({
        id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
      }) === PREF_ENABLED
    ) {
      callback();
    } else {
      this.game.addConfirmButton({
        callback,
      });
    }
    this.game.addCancelButton();
  }
  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private setStacksSelectable() {
    Object.entries(this.args.options).forEach(([spaceId, option]) => {
      this.game.setLocationSelectable({
        id: `${spaceId}_${this.args.faction}_stack`,
        callback: () => this.updateInterfaceSelectDestination({ option }),
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
