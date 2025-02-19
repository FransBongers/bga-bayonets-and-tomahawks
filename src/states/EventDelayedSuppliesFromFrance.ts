class EventDelayedSuppliesFromFranceState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: EventDelayedSuppliesFromFranceStateArgs;
  private frenchAP: string = null;
  private indianAP: string = null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: EventDelayedSuppliesFromFranceStateArgs) {
    debug('Entering EventDelayedSuppliesFromFranceState');
    this.args = args;
    this.frenchAP = null;
    this.indianAP = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving EventDelayedSuppliesFromFranceState');
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
    if (this.args.indianAP.length === 0) {
      this.updateInterfaceSelectFrenchAP();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select an Indian AP to lose'),
      args: {
        you: '${you}',
      },
    });

    this.args.indianAP.forEach((actionPoint, index) => {
      this.game.addSecondaryActionButton({
        id: `ap_${actionPoint.id}_${index}`,
        text: this.game.format_string_recursive('${tkn_actionPoint}', {
          tkn_actionPoint: tknActionPointLog(INDIAN, actionPoint.id),
        }),
        callback: () => {
          this.indianAP = actionPoint.id;
          this.updateInterfaceSelectFrenchAP();
        },
        extraClasses: getFactionClass(INDIAN),
      });
    });

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectFrenchAP() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a French AP to lose'),
      args: {
        you: '${you}',
      },
    });

    this.args.frenchAP.forEach((actionPoint, index) => {
      this.game.addSecondaryActionButton({
        id: `ap_${actionPoint.id}_${index}`,
        text: this.game.format_string_recursive('${tkn_actionPoint}', {
          tkn_actionPoint: tknActionPointLog(FRENCH, actionPoint.id),
        }),
        callback: () => {
          this.frenchAP = actionPoint.id;
          this.updateInterfaceConfirm();
        },
        extraClasses: getFactionClass(FRENCH),
      });
      // this.game.addPrimaryActionButton({
      //   id: `ap_${actionPoint}_${index}`,
      //   text: actionPoint.id,
      //   callback: () => {
      //     this.frenchAP = actionPoint.id;
      //     this.updateInterfaceConfirm();
      //   },
      // });
    });
  }

  private updateInterfaceConfirm() {
    this.game.clearPossible();

    const text =
      this.indianAP === null
        ? _('Lose ${tkn_actionPoint_french}?')
        : _('Lose ${tkn_actionPoint_indian} and ${tkn_actionPoint_french}?');
    this.game.clientUpdatePageTitle({
      text,
      args: {
        tkn_actionPoint_indian: this.indianAP
          ? tknActionPointLog(INDIAN, this.indianAP)
          : '',
        tkn_actionPoint_french: tknActionPointLog(FRENCH, this.frenchAP),
      },
    });

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actEventDelayedSuppliesFromFrance',
        args: {
          frenchAP: this.frenchAP,
          indianAP: this.indianAP,
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
