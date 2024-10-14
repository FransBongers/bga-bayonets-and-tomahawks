class ActionRoundActionPhaseState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringActionRoundActionPhaseStateArgs;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: OnEnteringActionRoundActionPhaseStateArgs) {
    debug('Entering ActionRoundActionPhaseState');
    this.args = args;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving ActionRoundActionPhaseState');
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

    // this.game.setCardSelected({ id: this.args.card.id });

    this.game.clientUpdatePageTitle({
      text: this.args.isIndianActions
        ? _('${you} may use the Indian card for actions')
        : _('${you} may perform actions'),
      args: {
        you: '${you}',
      },
    });

    this.addActionButtons();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm({ actionPointId }: { actionPointId: string }) {
    this.game.clearPossible();
    this.game.setCardSelected({ id: this.args.card.id });

    this.game.clientUpdatePageTitle({
      text: _('Use ${tkn_actionPoint} to perform an Action?'),
      args: {
        tkn_actionPoint: tknActionPointLog(
          this.args.isIndianActions ? INDIAN : this.args.faction,
          actionPointId
        ),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actActionRoundActionPhase',
        args: {
          actionPointId: actionPointId,
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

  private addActionButtons() {
    const faction = this.args.isIndianActions ? INDIAN : this.args.faction;

    this.args.availableActionPoints.forEach((actionPointId, index) => {
      this.game.addSecondaryActionButton({
        id: `ap_${actionPointId}_${index}`,
        text: this.game.format_string_recursive('${tkn_actionPoint}', {
          tkn_actionPoint: tknActionPointLog(faction, actionPointId),
        }),
        callback: () => this.updateInterfaceConfirm({ actionPointId }),
        extraClasses: getFactionClass(faction),
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
