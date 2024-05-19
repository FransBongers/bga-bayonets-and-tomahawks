interface State {
  onEnteringState: (args: any) => void;
  onLeavingState: () => void;
}

interface CommonArgs {
  optionalAction: boolean;
  previousEngineChoices: number;
  previousSteps: number[];
}

interface OnEnteringConfirmTurnArgs extends CommonArgs {}

interface OnEnteringActionActivateStackStateArgs extends CommonArgs {
  // actionsAllowed: BTStackAction[];
  stacks: Record<string, BTStackAction[]>;
  faction: BRITISH_FACTION | FRENCH_FACTION;
}

interface OnEnteringActionRoundActionPhaseStateArgs extends CommonArgs {
  action: string;
  card: BTCard;
  availableActionPoints: string[];
}

interface OnEnteringActionRoundChooseCardStateArgs {
  _private: {
    cards: BTCard[];
    indianCard?: BTCard | null;
    selectedCard: BTCard | null;
  };
}

interface OnEnteringActionRoundChooseFirstPlayerStateArgs extends CommonArgs {

}

interface OnEnteringActionRoundChooseReactionStateArgs extends CommonArgs {
  actionPoints: BTActionPoint[];
}

interface OnEnteringActionRoundSailBoxLandingStateArgs extends CommonArgs {

}

interface OnEnteringLightMovementStateArgs extends CommonArgs {
  commanders: BTUnit[];
  lightUnits: BTUnit[];
  destinations: Record<string, {
    space: BTSpace;
    remainingConnectionLimit: number;
  }>
  isIndianAP: boolean;
  origin: BTSpace;
  faction: Faction;
}

interface OnEnteringRaidStateArgs extends CommonArgs {
  raidTargets: Record<string, {
    space: BTSpace,
    paths: string[][],
  }>;
  units: BTUnit[];
}

interface OnEnteringSelectReserveCardStateArgs {
  _private: BTCard[];
}