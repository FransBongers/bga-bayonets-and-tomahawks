class EventRoundUpMenAndEquipmentState implements State {
  private game: BayonetsAndTomahawksGame;
  private args: EventRoundUpMenAndEquipmentStateArgs;
  private selectedReducedUnits: BTUnit[] = [];

  constructor(game: BayonetsAndTomahawksGame) {
    this.game = game;
  }

  onEnteringState(args: EventRoundUpMenAndEquipmentStateArgs) {
    debug('Entering EventRoundUpMenAndEquipmentState');
    this.args = args;
    this.selectedReducedUnits = [];
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
      text: _('${you} must select an option'),
      args: {
        you: '${you}',
      },
    });

    if (this.args.options.reduced.length > 0) {
      this.game.addPrimaryActionButton({
        id: 'flip_reduced_btn',
        text: _('Flip Reduced units'),
        callback: () => this.updateInterfaceFlipReducedUnits(),
      });
    }

    if (Object.keys(this.args.options.lossesBox).length > 0) {
      this.game.addPrimaryActionButton({
        id: 'place_from_losses_box_btn',
        text: _('Place 1 unit from Losses Box'),
        callback: () => this.updateInterfacePlaceUnitFromLossesBox(),
      });
    }

    this.game.addPassButton({
      optionalAction: this.args.optionalAction,
    });
    this.game.addUndoButtons(this.args);
  }

  private updateInterfaceFlipReducedUnits() {
    if (this.selectedReducedUnits.length === 2) {
      this.updateInterfaceConfirm({ flipReduced: true });
      return;
    }
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _(
        '${you} must select up to 2 Reduced units to flip (${number} remaining)'
      ),
      args: {
        you: '${you}',
        number: 2 - this.selectedReducedUnits.length,
      },
    });

    this.args.options.reduced.forEach((unit) => {
      this.game.openUnitStack(unit);
      this.game.setUnitSelectable({
        id: unit.id,
        callback: () => {
          if (
            this.selectedReducedUnits.some(
              (selectedUnit) => selectedUnit.id === unit.id
            )
          ) {
            this.selectedReducedUnits = this.selectedReducedUnits.filter(
              (selectedUnit) => selectedUnit.id !== unit.id
            );
          } else {
            this.selectedReducedUnits.push(unit);
          }
          this.updateInterfaceFlipReducedUnits();
        },
      });
    });

    this.selectedReducedUnits.forEach(({ id }) =>
      this.game.setUnitSelected({ id })
    );

    this.game.addPrimaryActionButton({
      id: 'done_btn',
      text: _('Done'),
      callback: () => this.updateInterfaceConfirm({ flipReduced: true }),
      extraClasses: this.selectedReducedUnits.length === 0 ? DISABLED : '',
    });

    this.game.addCancelButton();
  }

  private updateInterfacePlaceUnitFromLossesBox() {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select 1 unit from the Losses Box'),
      args: {
        you: '${you}',
      },
    });

    Object.entries(this.args.options.lossesBox).forEach(([id, option]) => {
      this.game.setUnitSelectable({
        id,
        callback: () => this.updateInterfaceSelectSpace(option),
      });
    });

    this.game.addCancelButton();
  }

  private updateInterfaceSelectSpace({
    unit,
    spaceIds,
  }: {
    unit: BTUnit;
    spaceIds: string[];
  }) {
    this.game.clearPossible();

    this.game.clientUpdatePageTitle({
      text: _('${you} must select a friendly Home Space to place ${tkn_unit}'),
      args: {
        you: '${you}',
        tkn_unit: unit.counterId,
      },
    });

    this.game.setUnitSelected({ id: unit.id });

    spaceIds.forEach((spaceId) =>
      this.game.setLocationSelectable({
        id: spaceId,
        callback: () => {
          this.updateInterfaceConfirm({ unit, spaceId });
        },
      })
    );

    this.game.addCancelButton();
  }

  private updateInterfaceConfirm({
    flipReduced = false,
    unit,
    spaceId,
  }: {
    flipReduced?: boolean;
    unit?: BTUnit;
    spaceId?: string;
  }) {
    this.game.clearPossible();

    const text = flipReduced
      ? _('Flip ${unitsLog} ?')
      : _('Place ${tkn_unit} in ${spaceName}?');
    this.game.clientUpdatePageTitle({
      text,
      args: {
        unitsLog: createUnitsLog(this.selectedReducedUnits),
        tkn_unit: unit ? unit.counterId : '',
        spaceName: spaceId
          ? _(this.game.gamedatas.staticData.spaces[spaceId].name)
          : '',
      },
    });

    if (flipReduced) {
      this.selectedReducedUnits.forEach(({ id }) =>
        this.game.setUnitSelected({ id })
      );
    }
    if (unit) {
      this.game.setUnitSelected({ id: unit.id });
    }
    if (spaceId) {
      this.game.setLocationSelected({ id: spaceId });
    }

    const callback = () => {
      this.game.clearPossible();
      this.game.takeAction({
        action: 'actEventRoundUpMenAndEquipment',
        args: {
          selectedReducedUnitIds: this.selectedReducedUnits.map(({ id }) => id),
          placedUnit: unit ? { unitId: unit.id, spaceId } : null,
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
