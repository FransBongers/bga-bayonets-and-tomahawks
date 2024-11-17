class EventPlaceIndianNationUnitsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringEventPlaceIndianNationUnitsStateArgs;
  private autoSelectedUnit = false;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringEventPlaceIndianNationUnitsStateArgs) {
    debug('Entering EventPlaceIndianNationUnitsState');
    this.args = args;
    this.autoSelectedUnit = false;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving EventPlaceIndianNationUnitsState');
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
    if (Object.keys(this.args.options).length === 1) {
      const {unit, spaces} = Object.values(this.args.options)[0];
      this.autoSelectedUnit = true;
      this.updateInterfaceSelectSpace({unit, spaces});
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a unit to place'),
      args: {
        you: '${you}',
      },
    });

    Object.entries(this.args.options).forEach(([unitId, data]) =>
      this.game.setUnitSelectable({
        id: unitId,
        callback: () => this.updateInterfaceSelectSpace(data),
      })
    );
    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectSpace({
    unit,
    spaces,
  }: {
    unit: BTUnit;
    spaces: BTSpace[];
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select Space to place ${tkn_unit}'),
      args: {
        you: '${you}',
        tkn_unit: unit.counterId,
      },
    });

    this.game.setUnitSelected({ id: unit.id });

    spaces.forEach((space) =>
      this.game.setLocationSelectable({
        id: space.id,
        callback: () => this.updateInterfaceConfirm({ unit, space }),
      })
    );

    if (this.autoSelectedUnit) {
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
    unit,
  }: {
    space: BTSpace;
    unit: BTUnit;
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Place ${tkn_unit} on ${spaceName}?'),
      args: {
        tkn_unit: unit.counterId,
        spaceName: _(space.name),
      },
    });
    this.game.setLocationSelected({ id: space.id });
    this.game.setUnitSelected({ id: unit.id });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actEventPlaceIndianNationUnits',
        args: {
          unitId: unit.id,
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
