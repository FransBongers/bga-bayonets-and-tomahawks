class EventWinteringRearAdmiralState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringEventWinteringRearAdmiralStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringEventWinteringRearAdmiralStateArgs) {
    debug('Entering EventWinteringRearAdmiralState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving EventWinteringRearAdmiralState');
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
      text: _('${you} must select a Fleet to place'),
      args: {
        you: '${you}',
      },
    });

    this.args.fleets.forEach((unit) =>
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => this.updateInterfaceSelectSpace({ fleet: unit }),
      })
    );

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
      text: _('Do not use'),
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectSpace({ fleet }: { fleet: BTUnit }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a space to place ${tkn_unit} on'),
      args: {
        you: '${you}',
        tkn_unit: fleet.counterId,
      },
    });

    this.game.setUnitSelected({ id: fleet.id });

    this.args.spaces.forEach((space) =>
      this.game.setLocationSelectable({
        id: space.id,
        callback: () => this.updateInterfaceConfirm({ space, fleet }),
      })
    );

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    fleet,
    space,
  }: {
    fleet: BTUnit;
    space: BTSpace;
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Place ${tkn_unit} on ${spaceName}?'),
      args: {
        tkn_unit: fleet.counterId,
        spaceName: _(space.name),
      },
    });

    this.game.setLocationSelected({ id: space.id });
    this.game.setUnitSelected({ id: fleet.id });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actEventWinteringRearAdmiral',
        args: {
          unitId: fleet.id,
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

    this.game.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

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
