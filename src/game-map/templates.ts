const tplUnit = ({faction, counterId}: {faction: 'british' | 'french' | 'indian'; counterId: string;}) => `
  <div class="bt_token" data-faction="${faction}" data-counter-id="${counterId}"></div>
`

const tplSpaces = ({spaces}: {spaces: BayonetsAndTomahawksGamedatas['spaces']}): string => {
  const filteredSpaces = spaces.filter((space) => space.top && space.left );
  const mappedSpaces = filteredSpaces.map((space) => `<div data-space-id="${space.id}" class="bt_space" style="top: ${space.top - 26}px; left: ${space.left - 26}px;"></div>`);
  const result = mappedSpaces.join('');
  return result;
}

const tplGameMap = ({
  gamedatas,
}: {
  gamedatas: BayonetsAndTomahawksGamedatas;
}) => {
  const {spaces} = gamedatas;

  return `
<div id="bt_game_map_containter">
  <div class="bt_game_map_zoom_buttons">
    <button id="bt_game_map_zoom_out_button" type="button" class="bga-zoom-button bga-zoom-out-icon" style="margin-bottom: -5px;"></button>
    <button id="bt_game_map_zoom_in_button" type="button" class="bga-zoom-button bga-zoom-in-icon" style="margin-bottom: -5px;"></button>
  </div>
  <div id="bt_game_map">
    <div class="bt_marker" data-marker-type="victory_point"></div>
    ${ tplSpaces({spaces}) }
  </div>
</div>`;
};
// <i class="fa-regular fa-magnifying-glass-plus"></i>
// <div class="bt_token" data-faction="french" data-unit-type="bastion"></div>
// <div class="bt_token" data-faction="indian" data-unit-type="micmac"></div>