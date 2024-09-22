class WinterQuartersReturnToColoniesCombineReducedUnitsState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersReturnToColoniesCombineReducedUnitsStateArgs;
  private selectedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(
    args: OnEnteringWinterQuartersReturnToColoniesCombineReducedUnitsStateArgs
  ) {
    debug('Entering WinterQuartersReturnToColoniesCombineReducedUnitsState');
    this.args = args;
    this.selectedUnits = [];
    // this.selectedOption = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersReturnToColoniesCombineReducedUnitsState');
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
      text: _(
        '${you} must select a stack to Combine Reduced units (${number} remaining)'
      ),
      args: {
        you: '${you}',
        number: Object.keys(this.args.options).filter(
          (spaceId) => spaceId !== DISBANDED_COLONIAL_BRIGADES
        ).length,
      },
    });

    this.setStacksSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceConfirm(spaceId: string) {
    this.game.clearPossible();

    // this.game.clientUpdatePageTitle({
    //   text: _('Move stack from ${originSpaceName} to ${destinationSpaceName}?'),
    //   args: {
    //     originSpaceName: _(origin.name),
    //     destinationSpaceName: _(destination.space.name),
    //   },
    // });
    // destination.path.forEach((spaceId) =>
    //   this.game.setLocationSelected({ id: spaceId })
    // );

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actWinterQuartersReturnToColoniesCombineReducedUnits',
        args: {
          spaceId,
          // destinationId: destination.space.id,
          // path: destination.path,
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
    // this.game.addCancelButton();
  }
  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private setStacksSelectable() {
    Object.entries(this.args.options)
      .filter(([spaceId, option]) => spaceId !== DISBANDED_COLONIAL_BRIGADES)
      .forEach(([spaceId, option]) => {
        this.game.setLocationSelectable({
          id: `${spaceId}_${this.args.faction}_stack`,
          callback: () => this.updateInterfaceConfirm(spaceId),
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
