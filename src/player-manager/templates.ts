const tplCardInfoContainer = (faction: Faction) => `
<div id="bt_card_info_container_${faction}">
    <div class="bt_event_title_container">
      <span id="${faction}_event_title"></span>
    </div>
    <div id="${faction}_action_points" class="bt_action_points" data-faction="${faction}"></div>
</div>
`

const tplPlayerPanel = ({
  playerId,
  faction,
}: {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  playerId: number;
}) => {
  return `
  <div id="bt_player_panel_${playerId}" class="bt_player_panel">
    ${
      faction === 'french'
        ? tplCardInfoContainer(INDIAN)
        : ''
    }
    ${tplCardInfoContainer(faction)}
  </div>`;
};
