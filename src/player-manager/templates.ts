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
        ? `<div id="indian_action_points" class="bt_action_points" data-faction="indian"></div>`
        : ''
    }
    <div id="${faction}_action_points" class="bt_action_points" data-faction="${faction}">
    </div>
  </div>`;
};

// const tplPlayerPanel = ({
//   playerId,
//   faction,
// }: {
//   faction: BRITISH_FACTION | FRENCH_FACTION;
//   playerId: number;
// }) => {
//   return `
//   <div id="bt_player_panel_${playerId}" class="bt_player_panel">
//     <div class="bt_cards_in_play_container">
//       <div id="${faction}_card_in_play" class="bt_card_in_play">
//         <div class="bt_card_in_play_border"></div>
//       </div>
//       ${
//         faction === 'french'
//           ? `<div id="indian_card_in_play" class="bt_card_in_play">
//         <div class="bt_card_in_play_border"></div>
//       </div>`
//           : ''
//       }
//     </div>
//   </div>`;
// };
