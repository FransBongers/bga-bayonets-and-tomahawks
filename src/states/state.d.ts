interface State {
  onEnteringState: (args: any) => void;
  onLeavingState: () => void;
}

interface CommonArgs {
  optionalAction: boolean;
  previousEngineChoices: number;
  previousSteps: number[];
}

interface OnEnteringArmyMovementStateArgs extends CommonArgs {
  // destinations: Record<string, {
  //   space: BTSpace;
  //   remainingConnectionLimit: number;
  // }>
  faction: BRITISH_FACTION | FRENCH_FACTION;
  origin: BTSpace;
  units: BTUnit[];
}

interface OnEnteringArmyMovementDestinationStateArgs extends CommonArgs {
  destinations: Record<
    string,
    {
      space: BTSpace;
    }
  >;
  faction: BRITISH_FACTION | FRENCH_FACTION;
  origin: BTSpace;
  units: BTUnit[];
}

interface OnEnteringBattleApplyHitsStateArgs extends CommonArgs {
  units: BTUnit[];
  // space: BTSpace;
  spaceId: string;
  faction: string;
  eliminate: boolean;
}

interface OnEnteringBattleCombineReducedUnitsStateArgs extends CommonArgs {
  options: Record<string, BTUnit[]>;
  spaceId: string;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringBattleFortEliminationStateArgs extends CommonArgs {
  fort: BTUnit;
  enemyFort: BTUnit | null;
  space: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringBattleMoveFleetStateArgs extends CommonArgs {
  units: BTUnit[];
  space: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
  destinationIds: string[];
}

interface OnEnteringBattleOverwhelmDuringRetreatStateArgs extends CommonArgs {
  enemyFaction: BRITISH_FACTION | FRENCH_FACTION;
  units: BTUnit[];
  numberOfUnitsToEliminate: number;
  space: BTSpace;
}

interface OnEnteringBattleRetreatStateArgs extends CommonArgs {
  retreatOptions: BTSpace[];
}

interface BTDieResultWithRerollSources {
  availableRerollSources: string[];
  result: string;
  usedRerollSources: string[];
  index: number;
}

interface OnEnteringBattleRollsRerollsStateArgs extends CommonArgs {
  diceResults: BTDieResultWithRerollSources[];
}

interface OnEnteringBattleSelectCommanderStateArgs extends CommonArgs {
  space: BTSpace;
  commanders: BTUnit[];
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringBattleSelectSpaceStateArgs extends CommonArgs {
  spaces: BTSpace[];
}

interface OnEnteringColonialsEnlistUnitPlacementStateArgs extends CommonArgs {
  units: BTUnit[];
  spaces: BTSpace[];
}

interface OnEnteringConfirmTurnArgs extends CommonArgs {}

interface OnEnteringActionActivateStackStateArgs extends CommonArgs {
  // actionsAllowed: BTStackAction[];
  stacks: Record<string, BTStackAction[]>;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringActionRoundActionPhaseStateArgs extends CommonArgs {
  // action: string;
  card: BTCard;
  availableActionPoints: string[];
  isIndianActions: boolean;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringActionRoundChooseCardStateArgs {
  _private: {
    cards: BTCard[];
    indianCard?: BTCard | null;
    selectedCard: BTCard | null;
  };
}

interface OnEnteringActionRoundChooseFirstPlayerStateArgs extends CommonArgs {}

interface OnEnteringActionRoundChooseReactionStateArgs extends CommonArgs {
  actionPoints: BTActionPoint[];
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringActionRoundSailBoxLandingStateArgs extends CommonArgs {
  spaces: BTSpace[];
}

interface OnEnteringLightMovementStateArgs extends CommonArgs {
  commanders: BTUnit[];
  lightUnits: BTUnit[];
  // destinations: Record<
  //   string,
  //   {
  //     space: BTSpace;
  //     remainingConnectionLimit: number;
  //   }
  // >;
  isIndianAP: boolean;
  origin: BTSpace;
  faction: Faction;
}

interface OnEnteringMarshalTroopsStateArgs extends CommonArgs {
  activate: BTUnit[];
  space: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
  marshal: Record<
    string,
    {
      units: BTUnit[];
      remainingLimit: number;
    }
  >;
}

interface OnEnteringMovementStateArgs extends CommonArgs {
  adjacent: {
    space: BTSpace;
    connection: BTConnection;
    hasEnemyUnits: boolean;
    requiredForOverwhelm: number;
    requiredToMove: number;
  }[];
  destination: BTSpace;
  units: BTUnit[];
  fromSpace: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
  isArmyMovement: boolean;
  requiredUnitIds: string[];
  source: string;
  forcedMarchAvailable: boolean;
  roughSeasActive: boolean;
  resolvedMoves: number;
  unitsThatCannotMove: BTUnit[];
  unitsThatCannotMoveCount: number;
  previouslyMovedUnitIds: string[];
}

interface BTConstructionOptions {
  activate: BTUnit[];
  fort: BTUnit | null;
  fortOptions: string[];
  roadOptions: Record<
    string,
    {
      connection: BTConnection;
      roadOption: string;
      space: BTSpace;
    }
  >;
  space: BTSpace;
}

interface OnEnteringMovementLoneCommanderStateArgs extends CommonArgs {
  space: BTSpace;
}

interface OnEnteringConstructionStateArgs extends CommonArgs {
  options: Record<string, BTConstructionOptions>;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface EventArmedBattoemenStateArgs extends CommonArgs {
  markers: BTMarker[];
}

interface EventConstructionFrenzyStateArgs extends CommonArgs {}

interface EventDelayedSuppliesFromFranceStateArgs extends CommonArgs {
  indianAP: BTActionPoint[];
  frenchAP: BTActionPoint[];
}

interface EventDiseaseInBritishCampStateArgs extends CommonArgs {
  year: number;
  brigades: BTUnit[];
  colonialBrigades: BTUnit[];
  metropolitanBrigades: BTUnit[];
}

interface EventDiseaseInFrenchCampStateArgs extends CommonArgs {
  options: BTUnit[];
}

interface OnEnteringEventFrenchLakeWarshipsStateArgs extends CommonArgs {
  options: BTConnection[];
}

interface EventHesitantBritishGeneralStateArgs extends CommonArgs {
  stacks: BTSpace[];
}

interface EventPennsylvaniasPeacePromisesStateArgs extends CommonArgs {
  units: BTUnit[];
}

interface OnEnteringEventPlaceIndianNationUnitsStateArgs extends CommonArgs {
  options: Record<string, {
    unit: BTUnit;
    spaces: BTSpace[];
  }>;
}

interface EventRoundUpMenAndEquipmentStateArgs extends CommonArgs {
  options: {
    reduced: BTUnit[];
    lossesBox: Record<
      string,
      {
        unit: BTUnit;
        spaceIds: string[];
      }
    >;
  };
}

interface EventSmallpoxInfectedBlanketsStateArgs extends CommonArgs {
  units: BTUnit[];
}

interface EventStagedLacrosseGameStateArgs extends CommonArgs {}

interface OnEnteringEventWildernessAmbushStateArgs extends CommonArgs {
  positions: number;
}

interface OnEnteringEventWinteringRearAdmiralStateArgs extends CommonArgs {
  fleets: BTUnit[];
  spaces: BTSpace[];
}

interface OnEnteringFleetsArriveUnitPlacementStateArgs extends CommonArgs {
  fleets: BTUnit[];
  units: BTUnit[];
  spaces: BTSpace[];
  commanders: Record<string, BTUnit>;
  commandersPerUnit: Record<string, string>;
}

interface UseEventStateArgs extends CommonArgs {
  eventTitle: string;
  title: string;
  titleOther: string;
}

interface OnEnteringLightMovementDestinationStateArgs extends CommonArgs {
  // commanders: BTUnit[];
  // lightUnits: BTUnit[];
  units: BTUnit[];
  destinations: Record<
    string,
    {
      space: BTSpace;
      remainingConnectionLimit: number;
    }
  >;
  // isIndianAP: boolean;
  // origin: BTSpace;
  faction: Faction;
}

interface OnEnteringSailMovementStateArgs extends CommonArgs {
  units: BTUnit[];
  space: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringRaidRerollStateArgs extends CommonArgs {
  rollType: string;
  source: string;
}

interface OnEnteringRaidSelectTargetStateArgs extends CommonArgs {
  raidTargets: Record<
    string,
    {
      space: BTSpace;
      path: string[];
    }
  >;
  units: BTUnit[];
  originId: string;
  faction: Faction;
}

interface OnEnteringSelectReserveCardStateArgs {
  _private: BTCard[];
}

interface OnEnteringVagariesOfWarPickUnitsStateArgs extends CommonArgs {
  options: Record<string, BTUnit[]>;
}

interface OnEnteringWinterQuartersMoveStackOnSailBoxStateArgs
  extends CommonArgs {
  spaceIds: string[];
}

interface OnEnteringWinterQuartersPlaceUnitsFromLossesBoxStateArgs extends CommonArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  options: Record<string, {
    numberToPlace: number;
    spaceIds: string[];
    units: BTUnit[];
  }>
}

interface WinterQuartersRemainingColonialBrigadesOption {
  space: BTSpace;
  maxRemain: number;
  units: BTUnit[];
}

interface OnEnteringWinterQuartersRemainingColonialBrigadesStateArgs
  extends CommonArgs {
  options: Record<string, WinterQuartersRemainingColonialBrigadesOption>;
}

interface OnEnteringWinterQuartersReturnToColoniesCombineReducedUnitsStateArgs extends CommonArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  options: Record<string, {
    units: BTUnit[];
    space: BTSpace | null;
  }>;
}

interface OnEnteringWinterQuartersReturnToColoniesLeaveUnitsStateArgs
  extends CommonArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  units: BTUnit[];
  space: BTSpace;
  maxBrigades: number;
  maxTotal: number | null; // null if there is no max
}

interface WinterQuartersReturnToColoniesSelectStackDestination {
  path: string[];
  space: BTSpace;
}

interface OnEnteringWinterQuartersReturnToColoniesRedeployCommandersStateArgs
  extends CommonArgs {
  commanders: BTUnit[];
  stacks: Record<
    string,
    {
      units: BTUnit;
      space: BTSpace;
    }
  >;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface WinterQuartersReturnToColoniesSelectStackOption {
  space: BTSpace;
  destinations: Record<
    string,
    WinterQuartersReturnToColoniesSelectStackDestination
  >;
  units: BTUnit[];
}

interface OnEnteringWinterQuartersReturnToColoniesSelectStackStateArgs
  extends CommonArgs {
  options: Record<string, WinterQuartersReturnToColoniesSelectStackOption>;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface WinterQuartersReturnToColoniesStep2SelectStackOption {
  space: BTSpace;
  units: BTUnit[];
  mayRemain: {
    maxBrigades: number;
    maxTotal: number | null; // null if there is no max
  };
}

interface OnEnteringWinterQuartersReturnToColoniesStep2SelectStackStateArgs
  extends CommonArgs {
  destinationIds: string[];
  options: Record<string, WinterQuartersReturnToColoniesStep2SelectStackOption>;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}
