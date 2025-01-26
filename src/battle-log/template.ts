const tplBattleLogButton = () => `<button id="battle_log_button" type="button">
<div class="battle_log_icon">
  <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17.8,20C17.4,21.2 16.3,22 15,22H5C3.3,22 2,20.7 2,19V18H5L14.2,18C14.6,19.2 15.7,20 17,20H17.8M19,2C20.7,2 22,3.3 22,5V6H20V5C20,4.4 19.6,4 19,4C18.4,4 18,4.4 18,5V18H17C16.4,18 16,17.6 16,17V16H5V5C5,3.3 6.3,2 8,2H19M8,6V8H15V6H8M8,10V12H14V10H8Z" /></svg>
</div>
</button>`;

const tplBattleLogUnit = (
  game: BayonetsAndTomahawksGame,
  counterId: string,
  state: 'destroyed' | 'full' | 'reduced'
) => {
  const isCommander = game.getUnitStaticData(counterId).type === COMMANDER;
  return `<div class="bt_token_side" data-counter-id="${counterId}${
    state === 'reduced' ? '_reduced' : ''
  }"${state === 'destroyed' ? 'data-eliminated="true"' : ''}${
    isCommander ? 'data-commander="true"' : ''
  }></div>`;
};

const tplBattleLog = (
  game: BayonetsAndTomahawksGame,
  log: BTCustomLog<BTBattleLog>
) => {
  const { spaceId, attacker, defender } = log.data;

  const scale = 640 / 1500;
  const { x, y } = getBattleLogMapImageBackgroundPosition(
    game,
    spaceId,
    scale,
    100
  );

  console.log(log.data[attacker]);

  return `
  <div class="battle_log">
    <div class="bt_battle_log_map_detail" style="background-position-x: ${x}px; background-position-y: ${y}px; width: ${
    scale * 1500
  }px;"></div>
    <div class="bt_log_data_container">
      <div class="bt_title">
        <span>${game.format_string_recursive(
          _('Battle in ${tkn_boldText_spaceName}'),
          {
            tkn_boldText_spaceName: _(game.getSpaceStaticData(spaceId).name),
          }
        )}<span>
      </div>
      <div class="bt_result"><span>${game.format_string_recursive(
        _('${attackerScore} vs ${defenderScore}'),
        {
          attackerScore: log.data[attacker].result ?? '-',
          defenderScore: log.data[defender].result ?? '-',
        }
      )}</span></div>
      <div class="bt_battle_units_row">
        <div class="bt_faction_banner" data-faction="${attacker}">
          <span>${_('Attacker')}</span>
        </div>

        <div class="bt_faction_banner" data-faction="${defender}">
          <span>${_('Defender')}</span>
        </div>

        <div class="bt_faction_units_container">${(
          log.data[attacker].unitsAfterBattle || []
        )
          .map(({ counterId, state }) =>
            tplBattleLogUnit(game, counterId, state)
          )
          .join('')}</div>
        <div class="bt_faction_units_container">${(
          log.data[defender].unitsAfterBattle || []
        )
          .map(({ counterId, state }) =>
            tplBattleLogUnit(game, counterId, state)
          )
          .join('')}</div>
      </div>
    </div>
  </div>
`;
};

const tplBattleLogSectionTitle = (round: string, year: number) => `
  <h3>${getCurrentRoundName(round)} - ${year}</h3>
`;

const tplBattleLogModalContent = (game: BayonetsAndTomahawksGame) => {
  return `
  <div id="battle_log_content">

  </div>`;
};
