const getARStepConfig = (isAR1: boolean): ActionRoundStep[] => {
  const steps = isAR1
    ? [
        {
          id: SELECT_RESERVE_CARD_STEP,
          stepNumber: 0,
          text: _('Select Reserve card'),
        },
      ]
    : [];

  return steps.concat([
    {
      id: SELECT_CARD_TO_PLAY_STEP,
      text: _('Select card to Play'),
      stepNumber: 1,
    },
    {
      id: SELECT_FIRST_PLAYER_STEP,
      text: _('Select First Player'),
      stepNumber: 2,
    },
    {
      id: RESOLVE_AR_START_EVENTS_STEP,
      text: _('Resolve "AR Start" Events'),
      stepNumber: 3,
    },
    {
      id: FIRST_PLAYER_ACTIONS_STEP,
      text: _('First Player Action Phase'),
      stepNumber: 4,
    },
    {
      id: SECOND_PLAYER_ACTIONS_STEP,
      text: _('Second Player Action Phase'),
      stepNumber: 5,
    },
    {
      id: FIRST_PLAYER_REACTION_STEP,
      text: _('First Player Reaction'),
      stepNumber: 6,
    },
    { id: RESOLVE_BATTLES_STEP, text: _('Resolve Battles'), stepNumber: 7 },
    { id: END_OF_AR_STEPS, text: _('End of Action Round'), stepNumber: 8 },
  ]);
};

const getCurrentRoundName = (currentRound: string) => {
  switch (currentRound) {
    case ACTION_ROUND_1:
    case ACTION_ROUND_2:
    case ACTION_ROUND_3:
    case ACTION_ROUND_4:
    case ACTION_ROUND_5:
    case ACTION_ROUND_6:
    case ACTION_ROUND_7:
    case ACTION_ROUND_8:
    case ACTION_ROUND_9:
      return _('Action Round ${number}').replace(
        '${number}',
        currentRound.slice(-1)
      );
    case FLEETS_ARRIVE:
      return _('Fleets Arrive');
    case COLONIALS_ENLIST:
      return _('Colonials Enlist');
    case WINTER_QUARTERS:
      return _('Winter Quarters');
    default:
      return '';
  }
};

const getFleetsArriveConfig = (): ActionRoundStep[] => [
  { id: DRAW_FLEETS_STEP, text: _('Draw Fleets'), stepNumber: 1 },
  {
    id: DRAW_BRITISH_UNITS_STEP,
    text: _('Draw British Units and VoW'),
    stepNumber: 2,
  },
  {
    id: DRAW_FRENCH_UNITS_STEP,
    text: _('Draw French Units and VoW'),
    stepNumber: 3,
  },
  {
    id: PLACE_BRITISH_UNITS_STEP,
    text: _('Place British Units'),
    stepNumber: 4,
  },
  { id: PLACE_FRENCH_UNITS_STEP, text: _('Place French Units'), stepNumber: 5 },
];

const getColonialsEnlistConfig = (): ActionRoundStep[] => [
  {
    id: DRAW_COLONIAL_REINFORCEMENTS_STEP,
    text: _('Draw Colonial Units and VoW'),
    stepNumber: 1,
  },
  {
    id: PLACE_COLONIAL_UNITS_STEP,
    text: _('Place Colonial Units'),
    stepNumber: 2,
  },
];

const getWinterQuartersConfig = (): ActionRoundStep[] => [
  { id: PERFORM_VICTORY_CHECK_STEP, text: _('Victory Check'), stepNumber: 1 },
  { id: REMOVE_MARKERS_STEP, text: _('Remove Markers on map'), stepNumber: 2 },
  {
    id: MOVE_STACKS_ON_SAIL_BOX_STEP,
    text: _('Move stacks on Sail box'),
    stepNumber: 3,
  },
  {
    id: PLACE_INDIAN_UNITS_STEP,
    text: _('Place Indian units on villages'),
    stepNumber: 4,
  },
  {
    id: MOVE_COLONIAL_BRIGADES_TO_DISBANDED_STEP,
    text: _('Disband Colonial Brigades'),
    stepNumber: 5,
  },
  { id: RETURN_TO_COLONIES_STEP, text: _('Return to Colonies'), stepNumber: 6 },
  {
    id: RETURN_FLEETS_TO_FLEET_POOL_STEP,
    text: _('Return Fleets to Fleets pool'),
    stepNumber: 7,
  },
  {
    id: PLACE_UNITS_FROM_LOSSES_BOX_STEP,
    text: _('Place units from Losses box'),
    stepNumber: 8,
  },
  {
    id: END_OF_YEAR_STEP,
    text: _('End of Year'),
    stepNumber: 9,
  },
];

const getStepConfig = (currentRound: string): ActionRoundStep[] => {
  switch (currentRound) {
    case ACTION_ROUND_1:
      return getARStepConfig(true);
    case ACTION_ROUND_2:
    case ACTION_ROUND_3:
    case ACTION_ROUND_4:
    case ACTION_ROUND_5:
    case ACTION_ROUND_6:
    case ACTION_ROUND_7:
    case ACTION_ROUND_8:
    case ACTION_ROUND_9:
      return getARStepConfig(false);
    case FLEETS_ARRIVE:
      return getFleetsArriveConfig();
    case COLONIALS_ENLIST:
      return getColonialsEnlistConfig();
    case WINTER_QUARTERS:
      return getWinterQuartersConfig();
    default:
      return [];
  }
};
