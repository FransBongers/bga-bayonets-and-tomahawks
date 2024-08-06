class EventRoundUpMenAndEquipmentState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: EventRoundUpMenAndEquipmentStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: EventRoundUpMenAndEquipmentStateArgs) {
    debug('Entering EventRoundUpMenAndEquipmentState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving EventRoundUpMenAndEquipmentState');
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
      text: _('${you} must select 1 Brigade to eliminate'),
      args: {
        you: '${you}',
      },
    });

    // this.args.diceResults.forEach((dieResult) =>
    //   this.game.addPrimaryActionButton({
    //     id: `die_result_${dieResult.index}_btn`,
    //     text: this.game.format_string_recursive('${tkn_dieResult}', {
    //       tkn_dieResult: dieResult.result,
    //     }),
    //     callback: () => this.updateInterfaceConfirm({ dieResult }),
    //   })
    // );

    // const stack: UnitStack =
    //   this.game.gameMap.stacks[this.args.spaceId][this.args.faction];
    // stack.open();
    // this.setUnitsSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({
    dieResult,
  }: {
    dieResult: BTDieResultWithRerollSources;
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Reroll ${tkn_dieResult} ?'),
      args: {
        tkn_dieResult: dieResult.result,
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actEventRoundUpMenAndEquipment',
        args: {
          dieResult: dieResult,
          rerollSource: dieResult.availableRerollSources[0]
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
