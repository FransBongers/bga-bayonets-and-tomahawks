const isDebug =
  window.location.host == 'studio.boardgamearena.com' ||
  window.location.hash.indexOf('debug') > -1;
const debug = isDebug ? console.info.bind(window.console) : () => {};

const capitalizeFirstLetter = (string: string) => {
  return string.charAt(0).toUpperCase() + string.slice(1);
};

const otherFaction = (
  faction: BRITISH_FACTION | FRENCH_FACTION
): BRITISH_FACTION | FRENCH_FACTION => {
  return faction === BRITISH ? FRENCH : BRITISH;
};

const createUnitsLog = (units: BTUnit[]) => {
  let unitsLog = '';
  const unitsLogArgs = {};

  units.forEach((unit, index) => {
    const key = `tkn_unit_${index}`;
    unitsLog += '${' + key + '}';
    unitsLogArgs[key] = `${unit.counterId}:${
      unit.reduced ? 'reduced' : 'full'
    }`;
  });

  return {
    log: unitsLog,
    args: unitsLogArgs,
  };
};

const getFactionClass = (
  faction: BRITISH_FACTION | FRENCH_FACTION | 'indian'
) => {
  switch (faction) {
    case BRITISH:
      return 'bt_british';
    case FRENCH:
      return 'bt_french';
    case INDIAN:
      return 'bt_indian';
  }
};

const tknActionPointLog = (faction: string, actionPointId: string) =>
  `${faction}:${actionPointId}`;

const tknUnitLog = (unit: BTUnit) =>
  `${unit.counterId}:${unit.reduced ? 'reduced' : 'full'}`;

const getBattleRollSequenceName = (stepId: string) => {
  switch (stepId) {
    case NON_INDIAN_LIGHT:
      return _('Non-Indian Light');
    case INDIAN:
      return _('Indian');
    case HIGHLAND_BRIGADES:
      return _('Highland Brigades');
    case METROPOLITAN_BRIGADES:
      return _('Metropolitan Brigades');
    case NON_METROPOLITAN_BRIGADES:
      return _('Non-Metropolitan Brigades');
    case FLEETS:
      return _('Fleets');
    case BASTIONS_OR_FORT:
      return _('Bastion or Fort');
    case ARTILLERY:
      return _('Artillery');
    case MILITIA:
      return _('Militia');
    case COMMANDER:
      return _('Other Commanders')
    default:
      return _('');
  }
};

const getBattleSideFromLocation = (input: BTUnit | string) => {
  const location = typeof input === 'string' ? input : input.location;
  if (location.includes(ATTACKER)) {
    return ATTACKER;
  } else {
    return DEFENDER;
  }
}

const getUnitIdForBattleInfo = (unit: BTUnit | BTMarker) => {
  return `${unit.id}_battle`;
}

const updateUnitIdForBattleInfo = (unit: BTUnit | BTMarker) => {
  unit.id = getUnitIdForBattleInfo(unit);
  return unit;
}