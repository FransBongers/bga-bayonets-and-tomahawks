class BattleRollsRerollsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringBattleRollsRerollsStateArgs;
  private singleSource: boolean;
  private singleDie: boolean;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringBattleRollsRerollsStateArgs) {
    debug('Entering BattleRollsRerollsState');
    this.args = args;
    this.singleSource = false;
    this.singleDie = false;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving BattleRollsRerollsState');
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
    if (this.args.diceResults.length === 1) {
      this.singleDie = true;
      this.updateInterfaceSelectSource({ dieResult: this.args.diceResults[0] });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} may select a die to reroll'),
      args: {
        you: '${you}',
      },
    });

    this.args.diceResults.forEach((dieResult) =>
      this.game.addPrimaryActionButton({
        id: `die_result_${dieResult.index}_btn`,
        text: this.game.format_string_recursive('${tkn_dieResult}', {
          tkn_dieResult: dieResult.result,
        }),
        callback: () => this.updateInterfaceSelectSource({ dieResult }),
      })
    );

    // const stack: UnitStack =
    //   this.game.gameMap.stacks[this.args.spaceId][this.args.faction];
    // stack.open();
    // this.setUnitsSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectSource({
    dieResult,
  }: {
    dieResult: BTDieResultWithRerollSources;
  }) {
    if (dieResult.availableRerollSources.length === 1) {
      this.singleSource = true;
      this.updateInterfaceConfirm({
        dieResult,
        rerollSource: dieResult.availableRerollSources[0],
      });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: this.singleDie
        ? _('${you} may select a source to Reroll ${tkn_dieResult}')
        : _('${you} must select a source to Reroll ${tkn_dieResult}'),
      args: {
        tkn_dieResult: dieResult.result,
        you: '${you}',
      },
    });

    dieResult.availableRerollSources.forEach((source) =>
      this.game.addPrimaryActionButton({
        id: `reroll_${source}_btn`,
        text: this.getSourceText(source),
        callback: () =>
          this.updateInterfaceConfirm({ dieResult, rerollSource: source }),
      })
    );

    if (this.singleDie) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.game.addCancelButton();
    }
  }

  private updateInterfaceConfirm({
    dieResult,
    rerollSource,
  }: {
    dieResult: BTDieResultWithRerollSources;
    rerollSource: string;
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('Use ${source} to Reroll ${tkn_dieResult} ?'),
      args: {
        tkn_dieResult: dieResult.result,
        source: this.getSourceText(rerollSource),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actBattleRollsRerolls',
        args: {
          dieResult: dieResult,
          rerollSource,
        },
      });
    };

    // if (
    //   this.game.settings.get({
    //     id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
    //   }) === PREF_ENABLED
    // ) {
    //   callback();
    // } else {
    this.game.addConfirmButton({
      callback,
    });
    // }

    if (this.singleDie && this.singleSource) {
      this.game.addPassButton({
        optionalAction: this.args.optionalAction,
      });
      this.game.addUndoButtons(this.args);
    } else {
      this.game.addCancelButton();
    }
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private getSourceText(source: string) {
    switch (source) {
      case COMMANDER:
        return _('Commander');
      case HIGHLAND_BRIGADES:
        return _('Highland Brigade');
      case PERFECT_VOLLEYS:
        return _('Perfect Volleys');
      case LUCKY_CANNONBALL:
        return _('Lucky Cannonball');
      default:
        return 'Unknown source';
    }
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
