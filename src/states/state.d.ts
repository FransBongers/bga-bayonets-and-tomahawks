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

interface OnEnteringActionRoundActionPhaseStateArgs extends CommonArgs {

}

interface OnEnteringActionRoundChooseCardStateArgs {
  _private: BTCard[];
}

interface OnEnteringActionRoundChooseFirstPlayerStateArgs extends CommonArgs {

}

interface OnEnteringActionRoundChooseReactionStateArgs extends CommonArgs {
  actionPoints: BTActionPoint[];
}

interface OnEnteringActionRoundSailBoxLandingStateArgs extends CommonArgs {

}

interface OnEnteringSelectReserveCardStateArgs {
  _private: BTCard[];
}