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
}

interface OnEnteringActionRoundActionPhaseStateArgs extends CommonArgs {
  action: string;
  card: BTCard;
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

interface OnEnteringMovementSelectDestinationAndUnitsStateArgs extends CommonArgs {

}

interface OnEnteringSelectReserveCardStateArgs {
  _private: BTCard[];
}