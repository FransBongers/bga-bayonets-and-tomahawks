class LightMovementState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringLightMovementStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringLightMovementStateArgs) {
    debug('Entering LightMovementState');
    this.args = args;
    this.selectedUnits = [];
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving LightMovementState');
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
      text: _('${you} must select units to move'),
      args: {
        you: '${you}',
      },
    });

    const stack: UnitStack<BTUnit> =
      this.game.gameMap.stacks[this.args.origin.id][this.args.faction];
    stack.open();
    this.setUnitsSelectable();

    this.game.addConfirmButton({
      callback: () => {
        this.game.clearPossible();
        this.game.takeAction({
          action: 'actLightMovement',
          args: {
            unitIds: this.selectedUnits.map((unit) => unit.id),
          },
        });
      },
    });

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
    this.checkConfirmDisabled();
  }

  // private updateInterfaceSelectUnits({
  //   space,
  //   remainingConnectionLimit,
  // }: {
  //   space: BTSpace;
  //   remainingConnectionLimit: number;
  // }) {
  //   this.game.clearPossible();
  //   this.game.setLocationSelected({ id: space.id });

  //   this.game.clientUpdatePageTitle({
  //     text: _('${you} must select units to move to ${spaceName}'),
  //     args: {
  //       you: '${you}',
  //       spaceName: _(space.name),
  //     },
  //   });

  //   const stack: UnitStack<BTUnit> =
  //     this.game.gameMap.stacks[space.id][this.args.faction];
  //   stack.open();

  //   this.setUnitsSelectable();

  //   this.game.addConfirmButton({
  //     callback: () => {
  //       if (this.selectedUnits.length === 0) {
  //         return;
  //       }
  //       this.game.clearPossible();
  //       this.game.takeAction({
  //         action: 'actLightMovement',
  //         args: {
  //           unitIds: this.selectedUnits.map((unit) => unit.id),
  //           spaceId: space.id,
  //         },
  //       });
  //     },
  //   });
  //   this.game.addCancelButton({
  //     callback: () => {
  //       this.selectedUnits = [];
  //     },
  //   });
  // }

  // private updateInterfaceConfirm({ space }: { space: BTSpace }) {
  //   this.game.clearPossible();

  //   this.selectedUnits.forEach((unit) =>
  //     this.game.setUnitSelected({ id: unit.id + '' })
  //   );
  //   this.game.setLocationSelected({ id: space.id });
  //   this.game.clientUpdatePageTitle({
  //     text:
  //       this.selectedUnits.length === 1
  //         ? _('Move ${unitName} to ${spaceName}?')
  //         : _('Move selected units to ${spaceName}?'),
  //     args: {
  //       you: '${you}',
  //       spaceName: _(space.name),
  //       unitName: _(
  //         this.game.gamedatas.staticData.units[this.selectedUnits[0].counterId]
  //           .counterText
  //       ),
  //     },
  //   });

  //   const callback = () => {
  //     this.game.clearPossible();
  //     this.game.takeAction({
  //       action: 'actLightMovement',
  //       args: {
  //         unitIds: this.selectedUnits.map((unit) => unit.id),
  //         spaceId: space.id,
  //       },
  //     });
  //   };

  //   if (
  //     this.game.settings.get({
  //       id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
  //     }) === PREF_ENABLED
  //   ) {
  //     callback();
  //   } else {
  //     this.game.addConfirmButton({
  //       callback,
  //     });
  //   }

  //   this.game.addCancelButton({
  //     callback: () => {
  //       this.selectedUnits = [];
  //     },
  //   });
  // }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  // private setSpacesSelectable() {
  //   Object.values(this.args.destinations).forEach((destination) => {
  //     this.game.setLocationSelectable({
  //       id: destination.space.id,
  //       callback: () => {
  //         if (
  //           this.args.lightUnits.length === 1 &&
  //           (this.args.commanders.length === 0 || this.args.isIndianAP)
  //         ) {
  //           (this.selectedUnits = this.args.lightUnits),
  //             this.updateInterfaceConfirm({ space: destination.space });
  //         } else {
  //           this.updateInterfaceSelectUnits(destination);
  //         }
  //       },
  //     });
  //   });
  // }

  // TODO: commanders
  private setUnitsSelectable() {
    this.args.lightUnits.forEach((unit) => {
      this.game.setLocationSelectable({
        id: '' + unit.id,
        callback: (event: PointerEvent) => {
          event.preventDefault();
          event.stopPropagation();
          this.handleUnitClick({ unit });
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

  handleUnitClick({ unit }: { unit: BTUnit }) {
    const index = this.selectedUnits.findIndex((item) => item.id === unit.id);
    const element = document.getElementById(unit.id + '');
    if (!element) {
      return;
    }
    if (index < 0) {
      // Unit selected
      this.selectedUnits.push(unit);
      element.classList.add(BT_SELECTED);
      element.classList.remove(BT_SELECTABLE);
      this.game.setUnitSelected({ id: '' + unit.id });
    } else {
      // Unit unselected
      this.selectedUnits.splice(index, 1);
      element.classList.add(BT_SELECTABLE);
      element.classList.remove(BT_SELECTED);
    }
    this.checkConfirmDisabled();
  }

  checkConfirmDisabled() {
    const button = document.getElementById('confirm_btn');
    if (!button) {
      return;
    }
    if (this.selectedUnits.length === 0) {
      button.classList.add(DISABLED);
    } else {
      button.classList.remove(DISABLED);
    }
  }
}
