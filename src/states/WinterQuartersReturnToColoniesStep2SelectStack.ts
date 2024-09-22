class WinterQuartersReturnToColoniesStep2SelectStackState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: OnEnteringWinterQuartersReturnToColoniesStep2SelectStackStateArgs;
  private unitsThatRemain: BTUnit[] = [];
  private selectedOption: WinterQuartersReturnToColoniesStep2SelectStackOption =
    null;

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(
    args: OnEnteringWinterQuartersReturnToColoniesStep2SelectStackStateArgs
  ) {
    debug('Entering WinterQuartersReturnToColoniesStep2SelectStackState');
    this.args = args;
    this.unitsThatRemain = [];
    this.selectedOption = null;
    this.updateInterfaceInitialStep();
  }

  onLeavingState() {
    debug('Leaving WinterQuartersReturnToColoniesStep2SelectStackState');
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
      text: _('${you} must select a stack to move (${number} remaining)'),
      args: {
        you: '${you}',
        number: Object.keys(this.args.options).length,
      },
    });

    this.setStacksSelectable();

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceSelectUnits() {
    const unitsThatCanBeSelected = this.getUnitsThatCanBeSelected();
    if (
      unitsThatCanBeSelected.length === 0 ||
      (this.selectedOption.mayRemain.maxTotal !== null &&
        this.unitsThatRemain.length === this.selectedOption.mayRemain.maxTotal)
    ) {
      this.updateInterfaceSelectDestination();
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text:
        this.selectedOption.mayRemain.maxTotal === 1
          ? _('${you} may select a unit to remain on ${spaceName}')
          : _('${you} may select units to remain on ${spaceName}'),
      args: {
        you: '${you}',
        spaceName: _(this.selectedOption.space.name),
      },
    });

    const stack: UnitStack =
      this.game.gameMap.stacks[this.selectedOption.space.id][this.args.faction];
    stack.open();

    this.setUnitsSelectable(unitsThatCanBeSelected);
    this.game.setElementsSelected(this.unitsThatRemain);

    this.game.addPrimaryActionButton({
      id: 'done_btn',
      text: _('Done'),
      callback: () => this.updateInterfaceSelectDestination(),
    });

    this.game.addCancelButton();
  }

  private updateInterfaceSelectDestination() {
    const unitsThatMove = this.selectedOption.units.filter(
      (unit) =>
        !this.unitsThatRemain.some(
          (remainingUnit) => remainingUnit.id === unit.id
        )
    );
    if (unitsThatMove.length === 0) {
      this.updateInterfaceConfirm({
        origin: this.selectedOption.space,
        destinationId: null,
      });
      return;
    }
    if (this.args.destinationIds.length === 1) {
      this.updateInterfaceConfirm({
        origin: this.selectedOption.space,
        destinationId: this.args.destinationIds[0],
      });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select Space to move your stack to'),
      args: {
        you: '${you}',
      },
    });

    this.game.setStackSelected({
      spaceId: this.selectedOption.space.id,
      faction: this.args.faction,
    });

    this.args.destinationIds.forEach((destinationId) => {
      this.game.setLocationSelectable({
        id: destinationId,
        callback: () =>
          this.updateInterfaceConfirm({
            origin: this.selectedOption.space,
            destinationId,
          }),
      });
    });

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    origin,
    destinationId,
  }: {
    origin: BTSpace;
    destinationId: string | null;
  }) {
    this.game.clearPossible();

    if (destinationId === null) {
      this.game.clientUpdatePageTitle({
        text: _('Leave ${unitsLog} on ${originSpaceName}?'),
        args: {
          originSpaceName: _(origin.name),
          unitsLog: createUnitsLog(this.unitsThatRemain)
        },
      });
    } else {
      this.game.clientUpdatePageTitle({
        text: _(
          'Move stack from ${originSpaceName} to ${destinationSpaceName}?'
        ),
        args: {
          originSpaceName: _(origin.name),
          destinationSpaceName:
            destinationId === SAIL_BOX
              ? _('the Sail Box')
              : _(
                  this.game.getSpaceStaticData({ id: destinationId } as BTSpace)
                    .name
                ),
        },
      });
    }

    const remainingUnitIds = this.unitsThatRemain.map((unit) => unit.id);
    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actWinterQuartersReturnToColoniesStep2SelectStack',
        args: {
          originId: origin.id,
          remainingUnitIds,
          destinationId,
        },
      });
    };

    if (this.unitsThatRemain.length > 0) {
      this.game.setElementsSelected(
        this.selectedOption.units.filter(
          (unit) => !remainingUnitIds.includes(unit.id)
        )
      );
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

  private getUnitsThatCanBeSelected() {
    const selectedBrigades = this.unitsThatRemain.filter(
      (unit) => this.game.getUnitStaticData(unit).type === BRIGADE
    ).length;
    return this.selectedOption.units.filter((unit) => {
      if (
        this.unitsThatRemain.some((selectedUnit) => selectedUnit.id === unit.id)
      ) {
        return false;
      }
      if (
        this.game.getUnitStaticData(unit).type === BRIGADE &&
        selectedBrigades === this.selectedOption.mayRemain.maxBrigades
      ) {
        return false;
      }
      return true;
    });
  }

  private setStacksSelectable() {
    Object.entries(this.args.options).forEach(([spaceId, option]) =>
      this.game.setLocationSelectable({
        id: `${spaceId}_${this.args.faction}_stack`,
        callback: () => {
          this.selectedOption = option;
          if (
            option.mayRemain.maxTotal === null ||
            (option.mayRemain.maxTotal !== null &&
              option.mayRemain.maxTotal > 0)
          ) {
            this.updateInterfaceSelectUnits();
          } else {
            this.updateInterfaceSelectDestination();
          }
        },
      })
    );
  }

  private setUnitsSelectable(unitsThatCanBeSelected: BTUnit[]) {
    unitsThatCanBeSelected.forEach((unit) => {
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          if (
            this.unitsThatRemain.some(
              (selectedUnit) => selectedUnit.id === unit.id
            )
          ) {
            this.unitsThatRemain = this.unitsThatRemain.filter(
              (selectedUnit) => selectedUnit.id !== unit.id
            );
          } else {
            this.unitsThatRemain.push(unit);
          }

          this.updateInterfaceSelectUnits();
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
}
