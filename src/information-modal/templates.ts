const tplInformationButton =
  () => `<button id="information_button" type="button" class="information_modal_button">
<div class="information_modal_icon">
  <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" /></svg>
</div>
</button>`;

const tplInfoModalTab = ({ id, text }: { id: string; text: string }) => `
  <div id="information_modal_tab_${id}" class="information_modal_tab">
    <span>${_(text)}</span>
  </div>`;
  

const tplGameMapInfo = (game: BayonetsAndTomahawksGame) => `
  ${spacesInfoConfig(game).map(({images, text, title}) => `
    <div class="bt_space_info_row">
      <div class="bt_space_info_images">
        ${images.map(({width, height, top, left}) => `<div class="bt_space_image" style="width: ${width}px; height: ${height}px; background-position: ${left}px ${top}px;"></div>`).join('')}
      </div>
      <div class="bt_space_text">
        <span class="title">${title}</span>
        <span class="text">${text}</span>
      </div>
    </div>
    `).join('')}
  `

const tplWinterQuartersRow = (text: string, index: number) => `
<tr>
  <td class="bt_winterQuarters_details">
    <span class="bt_procedure_number">${index + 1}.</span><span>${text}</span>
  </td>
</tr>
`

  const tplWinterQuartersInfo = (game: BayonetsAndTomahawksGame) => `
  <table id="bt_winter_quarters_procedure_info">
    <tr>
      <th><h3 class="section_title">${_('Winter Quarters Procedure')}</h3></th>
    </tr>
    ${winterQuartersProcedureConfig(game).map((text, index) => tplWinterQuartersRow(text, index)).join('')}
  </table>

  <table id="bt_return_to_colonies_procedure_info">
    <tr>
      <th><h3 class="section_title">${_('Return to Colonies')}</h3>
    <span>${_('No AP required. Ignore enemy units / enemy-controlled spaces / Connection Limits / Fleet transport capacity / MP Limits')}</span>
    </th>
    </tr>
    ${returnToColoniesProcedureConfig(game).map((text, index) => tplWinterQuartersRow(text, index)).join('')}
  </table>
  `

const tplInformationModalContent = ({
  tabs,
  game,
}: {
  tabs: Record<string, { text: string }>;
  game: BayonetsAndTomahawksGame;
}) => {
  

  return `
  <div id="information_modal_content">
    <div class="information_modal_tabs">
      ${Object.entries(tabs)
        .map(([id, info]) => tplInfoModalTab({ id, text: info.text }))
        .join('')}
    </div>
    <div id="bt_winterQuarters" class="information_modal_tab_content" style="display: none;">
      ${tplWinterQuartersInfo(game)}
    </div>
    <div id="bt_gameMap" class="information_modal_tab_content" style="display: none;">
      ${tplGameMapInfo(game)}
    </div>
  </div>`;
};
