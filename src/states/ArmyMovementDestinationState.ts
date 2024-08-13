class ArmyMovementDestinationState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringArmyMovementDestinationStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringArmyMovementDestinationStateArgs) {
    debug('Entering ArmyMovementDestinationState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ArmyMovementDestinationState');
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
      text: _('${you} must select a Space to move your units to'),
      args: {
        you: '${you}',
      },
    });

    this.setUnitsSelected();

    this.setSpacesSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({ space }: { space: BTSpace }) {
    this.game.clearPossible();

    this.setUnitsSelected();
    this.game.setLocationSelected({ id: space.id });

    this.game.clientUpdatePageTitle({
      text:
        this.args.units.length === 1
          ? _('Move ${unitName} to ${spaceName}?')
          : _('Move selected units to ${spaceName}?'),
      args: {
        you: '${you}',
        spaceName: _(space.name),
        unitName: _(
          this.game.gamedatas.staticData.units[this.args.units[0].counterId]
            .counterText
        ),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actArmyMovementDestination',
        args: {
          spaceId: space.id,
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
        callback: () =>
          this.updateInterfaceConfirm({ space: destination.space }),
      });
    });
  }

  private setUnitsSelected() {
    this.args.units.forEach((unit) => {
      this.game.setUnitSelected({ id: unit.id });
    });
  }
}
