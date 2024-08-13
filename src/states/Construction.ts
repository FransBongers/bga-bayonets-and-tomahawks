class ConstructionState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringConstructionStateArgs;
  // private marshalledUnits: Record<string, string[]> = {};
  private activated: BTUnit = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringConstructionStateArgs) {
    debug('Entering ConstructionState');
    this.args = args;
    // this.marshalledUnits = {};
    // Object.keys(this.args.marshal).forEach((spaceId) => {
    //   this.marshalledUnits[spaceId] = [];
    // });

    this.activated = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ConstructionState');
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
          this.updateInterfaceConstructionOptions();
        },
      })
    );

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConstructionOptions() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a Connection or Construction option'),
      args: {
        you: '${you}',
        spaceName: _(this.args.space.name),
      },
    });

    this.game.setLocationSelected({ id: this.args.space.id });

    this.args.fortOptions.forEach((option) => {
      this.game.addSecondaryActionButton({
        id: `${option}_btn`,
        text: this.getFortConstructionButtonText(option),
        callback: () => this.updateInterfaceConfirm({ fortOption: option }),
      });
    });

    Object.entries(this.args.roadOptions).forEach(([connectionId, data]) => {
      this.game.setLocationSelectable({
        id: `${connectionId}_road`,
        callback: () => this.updateInterfaceConfirm({ connectionId }),
      });
    });

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    connectionId,
    fortOption,
  }: {
    fortOption?: string;
    connectionId?: string;
  }) {
    this.game.clearPossible();

    this.updateConfirmationPageTitle({ connectionId, fortOption });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actConstruction',
        args: {
          activatedUnitId: this.activated.id,
          connectionId: connectionId || null,
          fortOption: fortOption || null,
        },
      });
    };

    if (fortOption) {
      this.game.setLocationSelected({ id: this.args.space.id });
    } else if (connectionId) {
      this.game.setLocationSelected({ id: `${connectionId}_road` });
    }

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

  private updateConfirmationPageTitle({
    connectionId,
    fortOption,
  }: {
    fortOption?: string;
    connectionId?: string;
  }) {
    let text = '';
    let args = {};

    if (fortOption) {
      switch (fortOption) {
        case PLACE_FORT_CONSTRUCTION_MARKER:
          text = _('Place ${tkn_marker} on ${spaceName}?');
          args = {
            tkn_marker: FORT_CONSTRUCTION_MARKER,
            spaceName: this.args.space.name,
          };
          break;
        case REPLACE_FORT_CONSTRUCTION_MARKER:
          text = _('Replace ${tkn_marker} on ${spaceName} with Fort?');
          args = {
            tkn_marker: FORT_CONSTRUCTION_MARKER,
            spaceName: this.args.space.name,
          };
          break;
        case REPAIR_FORT:
          text = _('Repair ${tkn_unit} on ${spaceName}?');
          args = {
            tkn_unit: `${this.args.fort?.counterId}:${
              this.args.fort.reduced ? 'reduced' : 'full'
            }`,
            spaceName: this.args.space.name,
          };
          break;
        case REMOVE_FORT:
          text = _('Remove ${tkn_unit} from ${spaceName}?');
          args = {
            tkn_unit: `${this.args.fort?.counterId}:${
              this.args.fort.reduced ? 'reduced' : 'full'
            }`,
            spaceName: this.args.space.name,
          };
          break;
        case REMOVE_FORT_CONSTRUCTION_MARKER:
          text = _('Remove ${tkn_marker} from ${spaceName}?');
          args = {
            tkn_marker: FORT_CONSTRUCTION_MARKER,
            spaceName: this.args.space.name,
          };
          break;
        default:
          return '';
      }
    } else if (connectionId) {
      switch (this.args.roadOptions[connectionId].roadOption) {
        case PLACE_ROAD_CONSTRUCTION_MARKER:
          text = _(
            'Place ${tkn_marker} on Path between ${spaceNameOrigin} and ${spaceNameDestination}?'
          );
          args = {
            tkn_marker: ROAD_CONSTRUCTION_MARKER,
            spaceNameOrigin: _(this.args.space.name),
            spaceNameDestination: _(
              this.args.roadOptions[connectionId].space.name
            ),
          };
          break;
        case FLIP_ROAD_CONSTRUCTION_MARKER:
          text = _(
            'Flip ${tkn_marker_construction} to ${tkn_marker_road} on Path between ${spaceNameOrigin} and ${spaceNameDestination}?'
          );
          args = {
            tkn_marker_construction: ROAD_CONSTRUCTION_MARKER,
            tkn_marker_road: ROAD_MARKER,
            spaceNameOrigin: _(this.args.space.name),
            spaceNameDestination: _(
              this.args.roadOptions[connectionId].space.name
            ),
          };
          break;
      }
    }

    this.game.clientUpdatePageTitle({
      text,
      args,
    });
  }

  private getFortConstructionButtonText(option: string) {
    let text = '';
    let args = {};

    switch (option) {
      case PLACE_FORT_CONSTRUCTION_MARKER:
        text = _('Place ${tkn_marker}');
        args = {
          tkn_marker: FORT_CONSTRUCTION_MARKER,
        };
        break;
      case REPLACE_FORT_CONSTRUCTION_MARKER:
        text = _('Replace ${tkn_marker} with Fort');
        args = {
          tkn_marker: FORT_CONSTRUCTION_MARKER,
        };
        break;
      case REPAIR_FORT:
        text = _('Repair ${tkn_unit}');
        args = {
          tkn_unit: `${this.args.fort?.counterId}:${
            this.args.fort.reduced ? 'reduced' : 'full'
          }`,
        };
        break;
      case REMOVE_FORT:
        text = _('Remove ${tkn_unit}');
        args = {
          tkn_unit: `${this.args.fort?.counterId}:${
            this.args.fort.reduced ? 'reduced' : 'full'
          }`,
        };
        break;
      case REMOVE_FORT_CONSTRUCTION_MARKER:
        text = _('Remove ${tkn_marker}');
        args = {
          tkn_marker: FORT_CONSTRUCTION_MARKER,
        };
        break;
      default:
        return '';
    }

    return this.game.format_string_recursive(text, args);
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
