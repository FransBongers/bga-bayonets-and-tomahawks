const tplScenarioInfoButton = () => {
  return `<div id="scenario_info">
      <div id="clipboard_button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M336 64h-53.88C268.9 26.8 233.7 0 192 0S115.1 26.8 101.9 64H48C21.5 64 0 85.48 0 112v352C0 490.5 21.5 512 48 512h288c26.5 0 48-21.48 48-48v-352C384 85.48 362.5 64 336 64zM192 64c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S160 113.7 160 96C160 78.33 174.3 64 192 64zM272 224h-160C103.2 224 96 216.8 96 208C96 199.2 103.2 192 112 192h160C280.8 192 288 199.2 288 208S280.8 224 272 224z"></path></svg>
      </div>
    </div>`;
};

const tplVictoryTrackDisplay = (type: VictoryTrackDisplayType) => {
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

const tplScenarioInfoFactions = (scenario: BTScenario) => {
  switch (scenario.id) {
    case 'VaudreuilsPetiteGuerre1755':
      return `
      <div>
        <span class="bt_section_title">${_('French')}</span>
        <div>
          <span>${_('Year End Bonus:')}</span>
          <div class="bt_year_end_bonus_container">
            <div class="bt_year_end_bonus" data-type="french"><span>+2</span></div>
            <div class="bt_year_end_bonus_description">
              <span>${_('Control 1 or more British Settled Spaces')}</span>
            </div>
          </div>
        </div>
        <div>
          <span>${_('Year End Victory Threshold')}</span>
          ${tplVictoryTrackDisplay('french1')}
        </div>
      </div>
      <div>
        <span class="bt_section_title">${_('British')}</span>
        <div>
          <span>${_('Year End Bonus:')}</span>
          <div class="bt_year_end_bonus_container">
            <div class="bt_year_end_bonus" data-type="british"><span>+2</span></div>
            <div class="bt_year_end_bonus_description">
              <span>${_('Control 2 or more French Victory Spaces')}</span>
            </div>
          </div>
        </div>
        <div>
          <span>${_('Year End Victory Threshold')}</span>
          ${tplVictoryTrackDisplay('british1')}
        </div>
      </div>
    `;
    case 'LoudounsGamble1757':
      return `
        <div>
          <span class="bt_section_title">${_('French')}</span>
          <div>
            <span>${_('Year End Bonus:')}</span>
            <div class="bt_year_end_bonus_container">
              <div class="bt_year_end_bonus" data-type="french"><span>+2</span></div>
              <div class="bt_year_end_bonus_description">
                <span>${_('Control 3 or more British Victory Spaces')}</span>
              </div>
            </div>
          </div>
          <div>
            <span>${_('Year End Victory Threshold')}</span>
            ${tplVictoryTrackDisplay('french1')}
          </div>
        </div>
        <div>
          <span class="bt_section_title">${_('British')}</span>
          <div>
            <span>${_('Year End Bonus:')}</span>
            <div class="bt_year_end_bonus_container">
              <div class="bt_year_end_bonus" data-type="british"><span>+2</span></div>
              <div class="bt_year_end_bonus_description">
                <span>${_('Control 1 or more French Settled Spaces')}</span>
              </div>
            </div>
          </div>
          <div>
            <span>${_('Year End Victory Threshold')}</span>
            ${tplVictoryTrackDisplay('british1')}
          </div>
        </div>
      `;
    case 'AmherstsJuggernaut1758_1759':
      return `
          <div>
            <span class="bt_section_title">${_('French')}</span>
            <div class="bt_container">
              <span>${_('Year End Bonus:')}</span>
              <span class="bt_section_title">${_('1758')}</span>
              <div class="bt_year_end_bonus_container">
                <div class="bt_year_end_bonus" data-type="french"><span>+2</span></div>
                <div class="bt_year_end_bonus_description">
                  <span>${_('Control 3 or more British Home Spaces')}</span>
                </div>
              </div>
              <span class="bt_section_title">${_('1759')}</span>
              <div class="bt_year_end_bonus_container">
                <div class="bt_year_end_bonus" data-type="french"><span>+2</span></div>
                <div class="bt_year_end_bonus_description">
                  <span>${_('For each 3-VP French space not controlled by the British')}</span>
                </div>
              </div>
            </div>
            <div class="bt_container">
              <span>${_('Year End Victory Threshold')}</span>
              <span class="bt_section_title">${_('1758')}</span>
              ${tplVictoryTrackDisplay('french5')}
              <span class="bt_section_title">${_('1759')}</span>
              ${tplVictoryTrackDisplay('british2')}
            </div>
          </div>
          <div>
            <span class="bt_section_title">${_('British')}</span>
            <div class="bt_container">
              <span>${_('Year End Bonus:')}</span>
              <span class="bt_section_title">${_('1758')}</span>
              <div class="bt_year_end_bonus_container">
                <div class="bt_year_end_bonus" data-type="british"><span>+2</span></div>
                <div class="bt_year_end_bonus_description">
                  <span>${_('Control 2 or more French Settled Spaces')}</span>
                </div>
              </div>
              <span class="bt_section_title">${_('1759')}</span>
              <div class="bt_year_end_bonus_container">
                <div class="bt_year_end_bonus" data-type="british"><span>+2</span></div>
                <div class="bt_year_end_bonus_description">
                  <span>${_('For each French Colony with at least two British-controlled spaces')}</span>
                </div>
              </div>
            </div>
            <div class="bt_container">
              <span>${_('Year End Victory Threshold')}</span>
              <span class="bt_section_title">${_('1758')}</span>
              ${tplVictoryTrackDisplay('british3')}
              <span class="bt_section_title">${_('1759')}</span>
              ${tplVictoryTrackDisplay('british3')}
            </div>
          </div>
        `;
    default:
      return '';
  }
};

const tplScenarioModalContent = (
  game: BayonetsAndTomahawksGame,
  scenario: BTScenario
) => {
  return `
<div id="scenario_modal_content">
  <div>
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
    <div style="margin-top: 8px;">
      <span class="bt_section_title">${_('Reinforcements:')}</span>
      <div class="bt_scenario_info_reinforcements" data-duration="${
        scenario.duration
      }">
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
  <div class="bt_scenario_info_faction">
      ${tplScenarioInfoFactions(scenario)}
  </div>
</div>`;
};

// ${tplVictoryTrackDisplay('french1')}
// ${tplVictoryTrackDisplay('british1')}

// const tplPlayerPrefenceSelectRow = ({
//   setting,
//   currentValue,
//   visible = true,
// }: {
//   setting: PlayerPreferenceSelectConfig;
//   currentValue: string;
//   visible: boolean;
// }) => {
//   let values = setting.options
//     .map(
//       (option) =>
//         `<option value='${option.value}' ${
//           option.value === currentValue ? 'selected="selected"' : ""
//         }>${_(option.label)}</option>`
//     )
//     .join("");

//   return `
//     <div id="setting_row_${setting.id}" class="player_preference_row"${!visible ? ` style="display: none;"` : ''}>
//       <div class="player_preference_row_label">${_(setting.label)}</div>
//       <div class="player_preference_row_value">
//         <select id="setting_${
//           setting.id
//         }" class="" style="display: block;">
//         ${values}
//         </select>
//       </div>
//     </div>
//   `;
// };

// const tplSettingsModalTabContent = ({ id }: { id: string; }) => `
//   <div id="settings_modal_tab_content_${id}" style="display: none;"></div>`;

// const tplSettingsModalTab = ({ id, name }: { id: string; name: string }) => `
//   <div id="settings_modal_tab_${id}" class="settings_modal_tab">
//     <span>${_(name)}</span>
//   </div>`;

// const tplSettingsModalContent = ({tabs}: {tabs: {id: string; name: string;}[]}) => {
//   return `<div id="setting_modal_content">
//     <div class="settings_modal_tabs">
//   ${tabs
//     .map(({id, name}) => tplSettingsModalTab({ id, name }))
//     .join("")}
//     </div>
//   </div>`;
// };

// const tplPlayerPrefenceSliderRow = ({label, id, visible = true}: {label: string; id: string; visible?: boolean}) => {
//   return `
//   <div id="setting_row_${id}" class="player_preference_row"${!visible ? ` style="display: none;"` : ''}>
//     <div class="player_preference_row_label">${_(label)}</div>
//     <div class="player_preference_row_value slider">
//       <div id="setting_${id}"></div>
//     </div>
//   </div>
//   `;
// };
