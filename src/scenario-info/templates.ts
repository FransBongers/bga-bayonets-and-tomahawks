const tplScenarioInfoButton = () => {
  return `<div id="scenario_info">
      <div id="clipboard_button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M336 64h-53.88C268.9 26.8 233.7 0 192 0S115.1 26.8 101.9 64H48C21.5 64 0 85.48 0 112v352C0 490.5 21.5 512 48 512h288c26.5 0 48-21.48 48-48v-352C384 85.48 362.5 64 336 64zM192 64c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S160 113.7 160 96C160 78.33 174.3 64 192 64zM272 224h-160C103.2 224 96 216.8 96 208C96 199.2 103.2 192 112 192h160C280.8 192 288 199.2 288 208S280.8 224 272 224z"></path></svg>
      </div>
    </div>`;
};

const tplVictoryTrackDisplay = (type: string) => {
  if (!VICTORY_TRACK_DISPLAY_CONFIG[type]) {
    return type;
  }
  const {
    width,
    height,
    backgroundPositionX,
    backgroundPositionY,
    markerLeft,
  } = VICTORY_TRACK_DISPLAY_CONFIG[type];

  return `
    <div class="bt_victory_track_display" style="width: ${width}px; height: ${height}px; background-position: ${backgroundPositionX}px ${backgroundPositionY}px;">
    <div class="bt_marker_side bt_victory_track_display_marker" data-side="front" data-type="victory_marker" style="left: ${markerLeft}px;"></div>
  </div>
`;
};

const tplScenarioInfoFactions = (
  game: BayonetsAndTomahawksGame,
  scenario: BTScenario
) => {
  return [FRENCH, BRITISH]
    .map(
      (faction) => `
   <div>
    <span class="bt_section_title">${
      faction === FRENCH ? _('French') : _('British')
    }</span>
    <div class="bt_container">
      <span>${_('Year End Bonus:')}</span>
      ${Object.entries(scenario.yearEndBonusDescriptions[faction])
        .map(
          ([year, data]) => `
        <span class="bt_section_title">${year}</span>
        <div class="bt_year_end_bonus_container">
          <div class="bt_year_end_bonus" data-type="${faction}"><span>+${
            data.vpBonus
          }</span></div>
          <div class="bt_year_end_bonus_description">
            <span>${game.format_string_recursive(_(data.log), {
              tkn_boldItalicText: _(data.args.tkn_boldItalicText),
            })}</span>
          </div>
        </div>
        `
        )
        .join('')}
    </div>
    <div class="bt_container" style="margin-top: 16px;">
      <span>${_('Year End Victory Threshold')}</span>
      ${Object.entries(scenario.victoryThreshold[faction])
        .map(
          ([year, threshold]) => `
          <span class="bt_section_title">${year}</span>
          ${tplVictoryTrackDisplay(
            threshold > 0
              ? `${faction}${threshold}`
              : (`british${Math.abs(threshold)}` as VictoryTrackDisplayType)
          )}     
        `
        )
        .join('')}
    </div>
  </div>
  `
    )
    .join('');
};

const tplScenarioModalContent = (
  game: BayonetsAndTomahawksGame,
  scenario: BTScenario
) => {
  return `
<div id="scenario_modal_content">
  <div>
    <span>${
      scenario.duration === 1
        ? game.format_string_recursive(_('${tkn_boldText_duration} 1 year'), {
            tkn_boldText_duration: _('Duration:'),
          })
        : game.format_string_recursive(
            _('${tkn_boldText_duration} ${number} years'),
            {
              tkn_boldText_duration: _('Duration:'),
              number: scenario.duration,
            }
          )
    }</span>
  </div>
  <div class="bt_scenario_info_faction">
      ${tplScenarioInfoFactions(game, scenario)}
  </div>
  <div>
    <div style="margin-top: 8px;">
      <span class="bt_section_title">${_('Reinforcements:')}</span>
      <div class="bt_scenario_info_reinforcements" data-duration="${
        scenario.duration
      }">
        <div></div>
          ${Object.entries(scenario.reinforcements)
            .map(([year, _reinforcements]) => `<div class="bt_year_container"><span class="bt_section_title">${year}</span></div>`)
            .join('')}
        <div class="bt_reinforcement_type">
          <span>${_('Fleets')}</span>
        </div>
        ${Object.entries(scenario.reinforcements)
          .map(
            ([_year, reinforcements]) => `
          <div class="bt_reinforcement_year" data-type="fleets"><span>${reinforcements.poolFleets}</span></div>  
          `
          )
          .join('')}
        <div class="bt_reinforcement_type">
          <span>${_('French Metropolitan')}</span>
        </div>
        ${Object.entries(scenario.reinforcements)
          .map(
            ([_year, reinforcements]) => `
          <div class="bt_reinforcement_year" data-type="french"><span>${reinforcements.poolFrenchMetropolitanVoW}</span></div>`
          )
          .join('')}
        <div class="bt_reinforcement_type">
          <span>${_('British Metropolitan')}</span>
        </div>
        ${Object.entries(scenario.reinforcements)
          .map(
            ([_year, reinforcements]) => `
          <div class="bt_reinforcement_year" data-type="british"><span>${reinforcements.poolBritishMetropolitanVoW}</span></div>`
          )
          .join('')}
        
        <div class="bt_reinforcement_type">
          <span>${_('Colonial')}</span>
        </div>
        ${Object.entries(scenario.reinforcements)
          .map(
            ([_year, reinforcements]) => `
          <div class="bt_reinforcement_year" data-type="colonial"><span>${reinforcements.poolBritishColonialVoW}</span></div>`
          )
          .join('')}
      </div>
    </div>
  </div>
</div>`;
};
