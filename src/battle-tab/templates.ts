const tplActiveBattleCounters = (side: 'attacker' | 'defender') => `
<div class="bt_counter_container">
  <div class="bt_counter">
    <div id="bt_active_battle_${side}_battle_victory_marker" class="bt_log_token bt_marker_side"></div>
    <span id="bt_active_battle_${side}_score" class="bt_active_battle_score_counter"></span>
  </div>
  <div id="bt_active_battle_${side}_commander_container" class="bt_counter">
    <div id="bt_active_battle_${side}_commander" class="bt_active_battle_commander"></div>
    <span id="bt_active_battle_${side}_rerolls"></span>
  </div>
</div>
`;


const tplActiveBattleStep = (stepId: string, side: 'attacker' | 'defender') => `
<div id="bt_active_battle_sequence_${stepId}_${side}_container" class="bt_active_battle_sequence_container">
  <div class="bt_active_battle_sequence_title_container">
    <span class="bt_active_battle_sequence_name">${getBattleRollSequenceName(
      stepId
    )}</span>
  </div>
  <div class="bt_active_battle_sequence_inner_container">
    <div id="bt_active_battle_sequence_${stepId}_${side}_units" class="bt_active_battle_sequence_units">
    </div>
    <div id="bt_active_battle_sequence_${stepId}_${side}_rolls" class="bt_active_battle_sequence_rolls">
    </div>
  </div>
</div>
`;

const tplActiveBattleDieResult = (dieResult: string) => `
<div class="bt_die_result_container">
  ${tplLogDieResult(dieResult)}
</div>
`;

const tplActiveBattleLog = (game: BayonetsAndTomahawksGame) => `
<div id="bt_active_battle_log">
  <div id="bt_active_battle_log_map_detail"></div>
  <div class="bt_active_battle_title_container"><span id="bt_active_battle_title"></span></div>  
  <div class="bt_active_battle_log_content_container">
  <div id="bt_active_battle_attacker_banner" class="bt_active_battle_faction_header">
    <div class="bt_active_battle_faction_banner" data-faction="">
      ${_('Attacker')}
    </div>
    ${tplActiveBattleCounters(ATTACKER)}
  </div>
  <div id="bt_active_battle_defender_banner" class="bt_active_battle_faction_header">
    <div class="bt_active_battle_faction_banner" data-faction="">
    ${_('Defender')}
    </div>
    ${tplActiveBattleCounters(DEFENDER)}
  </div>
    <div id="bt_active_battle_attacker_faction_container" class="bt_active_battle_faction_container" data-faction="british">
          ${BATTLE_ROLL_SEQUENCE.map((stepId) =>
            tplActiveBattleStep(stepId, 'attacker')
          ).join('')}
      ${[MILITIA, COMMANDER]
        .map((stepId) => tplActiveBattleStep(stepId, 'attacker'))
        .join('')}
    </div>
    <div id="bt_active_battle_defender_faction_container" class="bt_active_battle_faction_container" data-faction="french">
    
      ${BATTLE_ROLL_SEQUENCE.map((stepId) =>
        tplActiveBattleStep(stepId, 'defender')
      ).join('')}
      ${[MILITIA, COMMANDER]
        .map((stepId) => tplActiveBattleStep(stepId, 'defender'))
        .join('')}
    </div>
  </div>
</div>
`;

const tplBattleInfo = (game: BayonetsAndTomahawksGame) =>
  `<div class="bt_battle_info_container">
<div class="bt_battle_info">
<div class="bt_grid_cell bt_center">${_('Unit type')}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(
    HIT_TRIANGLE_CIRCLE
  )}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(
    HIT_SQUARE_CIRCLE
  )}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(B_AND_T)}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(FLAG)}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(MISS)}</div>
  <div class="bt_grid_cell bt_center">${_('Unit Hit')}</div>
  ${Object.entries(getBattleOrderConfig())
    .map(
      ([type, config]) => `
    <div class="bt_battle_info_${type} bt_grid_cell">
      <div>${config.title}</div>
      <div>
        ${config.counterIds
          .map((counterId: string) =>
            type === MILITIA
              ? game.format_string_recursive('${tkn_marker}', {
                  tkn_marker: counterId,
                })
              : game.format_string_recursive('${tkn_unit}', {
                  tkn_unit: counterId,
                })
          )
          .join('')}
      </div>
    </div>
    `
    )
    .join('')}
  ${getBattleInfoDieResultConfig(game)
    .map(
      ({ column, content, row, extraClasses }) => `
      <div class="bt_grid_cell ${extraClasses}" style="grid-column: ${column.from} / ${column.to}; grid-row: ${row.from} / ${row.to};">
        ${content}
      </div>
    `
    )
    .join('')}
</div>
</div>`;
