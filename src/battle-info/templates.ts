const tplBattleInfo = (game: BayonetsAndTomahawksGame) =>
  `<div class="bt_battle_info_container">
<div class="bt_battle_info">
<div class="bt_grid_cell bt_center">${_('Unit type')}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(HIT_TRIANGLE_CIRCLE)}</div>
  <div class="bt_grid_cell bt_center">${tplLogDieResult(HIT_SQUARE_CIRCLE)}</div>
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
          .map((counterId: string) => type === MILITIA ? game.format_string_recursive('${tkn_marker}', { tkn_marker: counterId })  :
            game.format_string_recursive('${tkn_unit}', { tkn_unit: counterId })
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
