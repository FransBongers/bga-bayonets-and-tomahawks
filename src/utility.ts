const isDebug =
  window.location.host == 'studio.boardgamearena.com' ||
  window.location.hash.indexOf('debug') > -1;
const debug = isDebug ? console.info.bind(window.console) : () => {};

const capitalizeFirstLetter = (string: string) => {
  return string.charAt(0).toUpperCase() + string.slice(1);
};

const createUnitsLog = (units: BTUnit[]) => {
  let unitsLog = '';
  const unitsLogArgs = {};

  units.forEach((unit, index) => {
    const key = `tkn_unit_${index}`;
    unitsLog += '${' + key + '}';
    unitsLogArgs[key] = `${unit.counterId}:${unit.reduced ? 'reduced' : 'full'}`;
  });

  return {
    log: unitsLog,
    args: unitsLogArgs,
  };
};
