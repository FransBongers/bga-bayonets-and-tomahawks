class MarshalTroopsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringMarshalTroopsStateArgs;
  private marshalledUnits: Record<string, string[]> = {};
  private activated: BTUnit = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringMarshalTroopsStateArgs) {
    debug('Entering MarshalTroopsState');
    this.args = args;
    this.marshalledUnits = {};
    Object.keys(this.args.marshal).forEach((spaceId) => {
      this.marshalledUnits[spaceId] = [];
    });

    this.activated = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving MarshalTroopsState');
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
      text: _('${you} must select a unit to activate'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.args.space.id][this.args.faction];
    stack.open();

    this.args.activate.forEach((unit) =>
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          this.activated = unit;
          this.updateInterfaceSelectUnits();
        },
      })
    );


    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectUnits() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select units to Marshall on ${spaceName}'),
      args: {
        you: '${you}',
        spaceName: _(this.args.space.name),
      },
    });

    this.game.setLocationSelected({ id: this.args.space.id });

    Object.keys(this.args.marshal).forEach((spaceId) => {
      const stack: UnitStack =
        this.game.gameMap.stacks[spaceId][this.args.faction];
      stack.open();
    });

    this.setUnitsSelectable();
    this.setUnitsSelected();

    this.game.addPrimaryActionButton({
      id: 'done_btn',
      text: _('Done'),
      callback: () => this.updateInterfaceConfirm(),
      extraClasses: this.getMarshallCount() === 0 ? DISABLED : '',
    });

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Marshal selected units on ${spaceName}?'),
      args: {
        spaceName: _(this.args.space.name),
      },
    });

    this.game.setLocationSelected({ id: this.args.space.id });
    this.setUnitsSelected();

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actMarshalTroops',
        args: {
          activatedUnitId: this.activated.id,
          marshalledUnitIds: this.marshalledUnits,
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

  private getMarshallCount() {
    return Object.values(this.marshalledUnits).reduce((carry, current) => {
      return carry + current.length;
    }, 0);
  }

  private setUnitsSelectable() {
    Object.entries(this.args.marshal).forEach(
      ([spaceId, { units, remainingLimit }]) => {
        units.forEach((unit) => {
          const unitIsSelected = this.marshalledUnits[spaceId].includes(
            unit.id
          );
          if (unitIsSelected) {
            this.game.setUnitSelectable({
              id: unit.id,
              callback: () => {
                this.marshalledUnits[spaceId] = this.marshalledUnits[
                  spaceId
                ].filter((selectedUnitId) => selectedUnitId !== unit.id);
                this.updateInterfaceSelectUnits();
              },
            });
          } else if (this.marshalledUnits[spaceId].length < remainingLimit) {
            this.game.setUnitSelectable({
              id: unit.id,
              callback: () => {
                this.marshalledUnits[spaceId].push(unit.id);

                this.updateInterfaceSelectUnits();
              },
            });
          }
        });
      }
    );
  }

  private setUnitsSelected() {
    Object.values(this.marshalledUnits).forEach((units) =>
      units.forEach((id) => this.game.setUnitSelected({ id }))
    );
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
