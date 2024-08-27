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
  enemyFort: BTUnit;
  space: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
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
  }[];
  units: BTUnit[];
  fromSpace: BTSpace;
  faction: BRITISH_FACTION | FRENCH_FACTION;
  destination: BTSpace;
  requiredUnitIds: string[];
  source: string;
  forcedMarchAvailable: boolean;
  count: number;
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

interface OnEnteringConstructionStateArgs extends CommonArgs {
  options: Record<string, BTConstructionOptions>;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface EventArmedBattoemenStateArgs extends CommonArgs {
  markers: BTMarker[];
}

interface EventConstructionFrenzyStateArgs extends CommonArgs {
  
}

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

interface EventHesitantBritishGeneralStateArgs extends CommonArgs {
  stacks: BTSpace[];
}

interface EventPennsylvaniasPeacePromisesStateArgs extends CommonArgs {
  units: BTUnit[];
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

interface EventWildernessAmbushStateArgs extends CommonArgs {
  positions: number;
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

interface OnEnteringVagariesOfWarPickUnitsStateArgs extends CommonArgs {
  options: Record<string, BTUnit[]>;
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
