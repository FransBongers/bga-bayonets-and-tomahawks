class WinterQuartersReturnToColoniesRedeployCommandersState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersReturnToColoniesRedeployCommandersStateArgs;

  private redeployedCommanders: Record<string, string> = null; // {unitId: spaceId}

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(
    args: OnEnteringWinterQuartersReturnToColoniesRedeployCommandersStateArgs
  ) {
    debug('Entering WinterQuartersReturnToColoniesRedeployCommandersState');
    this.args = args;

    this.redeployedCommanders = {};

    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersReturnToColoniesRedeployCommandersState');
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
      text: _('${you} may select a Commander to redeploy'),
      args: {
        you: '${you}',
      },
    });

    this.args.commanders.forEach((unit) => {
      const stack: UnitStack =
        this.game.gameMap.stacks[unit.location][this.args.faction];
      stack.open();

      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          this.updateInterfaceSelectSpace({ unit });
        },
      });
    });

    this.game.addPrimaryActionButton({
      id: 'done_btn',
      text: _('Done'),
      callback: () => this.updateInterfaceConfirm(),
      extraClasses:
        Object.keys(this.redeployedCommanders).length === 0 ? DISABLED : '',
    });

    if (Object.keys(this.redeployedCommanders).length === 0) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.addCancelButton();
    }
  }

  private updateInterfaceSelectSpace({ unit }: { unit: BTUnit }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a stack to redeploy ${tkn_unit} to'),
      args: {
        you: '${you}',
        tkn_unit: unit.counterId,
      },
    });

    Object.keys(this.args.stacks).forEach((spaceId) => {
      this.game.setStackSelectable({
        spaceId,
        faction: this.args.faction,
        callback: async (event: PointerEvent) => {
          if (spaceId === unit.location) {
            delete this.redeployedCommanders[unit.id];
          } else {
            this.redeployedCommanders[unit.id] = spaceId;
          }
          await this.game.gameMap.stacks[spaceId][this.args.faction].addUnit(
            unit
          );
          this.updateInterfaceInitialStep();
        },
      });
    });

    this.addCancelButton();
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Confirm unit placement?'),
      args: {},
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actWinterQuartersReturnToColoniesRedeployCommanders',
        args: {
          redeployedCommanders: this.redeployedCommanders,
        },
      });
    };

    // if (
    //   this.game.settings.get({
    //     id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
    //   }) === PREF_ENABLED
    // ) {
    callback();
    // } else {
    //   this.game.addConfirmButton({
    //     callback,
    //   });
    // }
    // this.addCancelButton();
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private addCancelButton() {
    this.game.addDangerActionButton({
      id: 'cancel_btn',
      text: _('Cancel'),
      callback: async () => {
        await this.revertLocalMoves();
        this.game.onCancel();
      },
    });
  }

  private async revertLocalMoves() {
    const promises = this.args.commanders.map((unit) => {
      this.game.gameMap.stacks[unit.location][this.args.faction].addUnit(unit);
    });

    await Promise.all(promises);
  }

  // private getPossibleSpacesToPlaceUnit(unit: BTUnit) {
  //   if (this.game.gamedatas.staticData.units[unit.counterId].type === LIGHT) {
  //     return this.args.spaces.map((space) => space.id);
  //   } else {
  //     return this.args.spaces
  //       .filter(
  //         (space) =>
  //           space.colony ===
  //           this.game.gamedatas.staticData.units[unit.counterId].colony
  //       )
  //       .map((space) => space.id);
  //   }
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
