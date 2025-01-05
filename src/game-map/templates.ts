const tplUnitVisibilityButton = () => {
  return `<div id="bt_unit_visibility_info" class="scrollmap_button_wrapper">
      <div id="eye_button" class="scrollmap_icon" data-units-visible="true">
        <svg  id="bt_eye_on_button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" /></svg>
        <svg id="bt_eye_off_button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.83,9L15,12.16C15,12.11 15,12.05 15,12A3,3 0 0,0 12,9C11.94,9 11.89,9 11.83,9M7.53,9.8L9.08,11.35C9.03,11.56 9,11.77 9,12A3,3 0 0,0 12,15C12.22,15 12.44,14.97 12.65,14.92L14.2,16.47C13.53,16.8 12.79,17 12,17A5,5 0 0,1 7,12C7,11.21 7.2,10.47 7.53,9.8M2,4.27L4.28,6.55L4.73,7C3.08,8.3 1.78,10 1,12C2.73,16.39 7,19.5 12,19.5C13.55,19.5 15.03,19.2 16.38,18.66L16.81,19.08L19.73,22L21,20.73L3.27,3M12,7A5,5 0 0,1 17,12C17,12.64 16.87,13.26 16.64,13.82L19.57,16.75C21.07,15.5 22.27,13.86 23,12C21.27,7.61 17,4.5 12,4.5C10.6,4.5 9.26,4.75 8,5.2L10.17,7.35C10.74,7.13 11.35,7 12,7Z" /></svg>
      </div>
    </div>`;
};
// <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M336 64h-53.88C268.9 26.8 233.7 0 192 0S115.1 26.8 101.9 64H48C21.5 64 0 85.48 0 112v352C0 490.5 21.5 512 48 512h288c26.5 0 48-21.48 48-48v-352C384 85.48 362.5 64 336 64zM192 64c17.67 0 32 14.33 32 32c0 17.67-14.33 32-32 32S160 113.7 160 96C160 78.33 174.3 64 192 64zM272 224h-160C103.2 224 96 216.8 96 208C96 199.2 103.2 192 112 192h160C280.8 192 288 199.2 288 208S280.8 224 272 224z"></path></svg>

const tplMarker = ({ id }: { id: string }) =>
  `<div id="${id}" class="bt_marker"></div>`;

// const tplCommonMarker = ({type}: {type: string;}) => `<div class="bt_marker" data-type="${type}"></div>`

const tplMarkerSide = ({ id }: { id: string }) =>
  `<div id="${id}" class="bt_marker_side" data-type="${id}" data-side="front"></div>`;

const tplMarkerOfType = ({ id, type }: { id?: string; type: string }) =>
  `<div ${
    id ? `id="${id}"` : ''
  } class="bt_marker_side" data-type="${type}" data-side="front"></div>`;

const tplUnit = ({
  faction,
  counterId,
  style,
}: {
  faction?: 'british' | 'french' | 'indian';
  counterId: string;
  style?: string;
}) => `
  <div class="bt_token_side" data-counter-id="${counterId}"${
  style ? ` style="${style}"` : ''
}></div>
`;

const tplSpaces = ({
  spaces,
}: {
  spaces: BayonetsAndTomahawksGamedatas['spaces'];
}): string => {
  const filteredSpaces = spaces.filter((space) => space.top && space.left);
  // const mappedSpaces = filteredSpaces.map((space) => `<div data-space-id="${space.id}" class="bt_space" style="top: ${space.top - 26}px; left: ${space.left - 26}px;"></div>`);
  // TO Check: why -26px?
  const mappedSpaces = filteredSpaces.map(
    (space) =>
      `<div id="${
        space.id
      }" class="bt_space" style="top: calc(var(--btMapScale) * ${
        space.top - 26
      }px); left: calc(var(--btMapScale) * ${space.left - 26}px);">
        <div id="${space.id}_french_stack"></div>
        <div id="${space.id}_markers"></div>
        <div id="${space.id}_british_stack"></div>
      </div>`
  );
  const result = mappedSpaces.join('');
  return result;
};

const tplConnection = ({
  id,
  top,
  left,
}: {
  id: string;
  top: number;
  left: number;
}) => {
  return `<div id="${id}" class="bt_connection" style="top: calc(var(--btMapScale) * ${top}px); left: calc(var(--btMapScale) * ${left}px);">
          <div id="${id}_french_limit" class="bt_connection_limit_counter">
            <span id="${id}_frenchLimit_counter" data-faction="french">4</span>
          </div>
          <div id="${id}_road" class="bt_marker_side" data-type="none"></div>
          <div id="${id}_british_limit" class="bt_connection_limit_counter">
            <span id="${id}_britishLimit_counter" data-faction="british">14</span>
          </div>
      </div>`;
};

const tplMarkerSpace = ({
  id,
  top,
  left,
  extraClasses,
}: {
  id: string;
  top: number;
  left: number;
  extraClasses?: string;
}) => {
  return `<div id="${id}" class="bt_marker_space${
    extraClasses ? ` ${extraClasses}` : ''
  }" style="top: calc(var(--btMapScale) * ${top}px); left: calc(var(--btMapScale) * ${left}px);"></div>`;
};

const tplLossesBox = () => {
  return `
    <div id="lossesBox_french" class="bt_losses_box"></div>
    <div id="lossesBox_british" class="bt_losses_box"></div>
    <div id="disbandedColonialBrigades" class="bt_losses_box"></div>
  `;
};

const tplActionRoundTrack = () =>
  ACTION_ROUND_TRACK_CONFIG.map((markerSpace) =>
    tplMarkerSpace({
      id: `action_round_track_${markerSpace.id}`,
      top: markerSpace.top,
      left: markerSpace.left,
    })
  ).join('');

const tplRaidTrack = () =>
  RAID_TRACK_CONFIG.map((markerSpace) =>
    tplMarkerSpace({
      id: `${markerSpace.id}`,
      top: markerSpace.top,
      left: markerSpace.left,
      extraClasses: 'bt_raid_track',
    })
  ).join('');

const tplYearTrack = () =>
  YEAR_TRACK_CONFIG.map((markerSpace) =>
    tplMarkerSpace({
      id: `year_track_${markerSpace.id}`,
      top: markerSpace.top,
      left: markerSpace.left,
    })
  ).join('');

const tplVictoryPointsTrack = () =>
  VICTORY_POINTS_TRACK_CONFIG.map((markerSpace) =>
    tplMarkerSpace({
      id: `${markerSpace.id}`,
      top: markerSpace.top,
      left: markerSpace.left,
    })
  ).join('');

const tplBattleTrack = () =>
  BATTLE_TRACK_CONFIG.map((markerSpace) =>
    tplMarkerSpace({
      id: markerSpace.id as string,
      top: markerSpace.top,
      left: markerSpace.left,
    })
  ).join('');

const tplCommanderTrack = () =>
  COMMANDER_REROLLS_TRACK_CONFIG.map((markerSpace) =>
    tplMarkerSpace({
      id: markerSpace.id as string,
      top: markerSpace.top,
      left: markerSpace.left,
      extraClasses: 'bt_commander_rerolls_track',
    })
  ).join('');

const tplBattleMarkersPool = () => '<div id="battle_markers_pool"></div>';

const tplSailBox = () => `
  <div id="sailBox">
    <div id="${SAIL_BOX}_french_stack"></div>
    <div id="${SAIL_BOX}_british_stack"></div>
  </div>`;

const tplGameMap = ({
  gamedatas,
}: {
  gamedatas: BayonetsAndTomahawksGamedatas;
}) => {
  const { spaces } = gamedatas;

  return `
    <div id="bt_left_column">
      <div id="map_container">
        <div id="map_scrollable" data-units-visible="true">
        </div>
          <div id="map_surface">
            
          </div>
          <div id="map_scrollable_oversurface">
            ${tplMarkerSpace({
              id: OPEN_SEAS_MARKER_SAIL_BOX,
              top: 77.5,
              left: 1374.5,
            })}
            ${tplLossesBox()}
            ${tplSpaces({ spaces })}
            ${tplVictoryPointsTrack()}
            ${tplBattleTrack()}
            ${tplBattleMarkersPool()}
            ${tplCommanderTrack()}
            ${tplRaidTrack()}
            ${tplYearTrack()}
            ${tplActionRoundTrack()}
            ${tplMarkerSpace({
              id: `${CHEROKEE_CONTROL}_markers`,
              top: 2120,
              left: 863.5,
            })}
            ${tplMarkerSpace({
              id: `${IROQUOIS_CONTROL}_markers`,
              top: 1711.5,
              left: 585.5,
            })}
            ${tplSailBox()}
            ${tplMarkerSpace({
              id: `wieChitPlaceholder_french`,
              top: 24.5,
              left: 108.5,
            })}
            ${tplMarkerSpace({
              id: `wieChitPlaceholder_british`,
              top: 24.5,
              left: 1074.5,
            })}
          </div>
      </div>
    </div>`;
};

// const tplGameMap = ({
//   gamedatas,
// }: {
//   gamedatas: BayonetsAndTomahawksGamedatas;
// }) => {
//   const { spaces } = gamedatas;

//   return `
//   <div id="bt_left_column">
//   <div id="bt_game_map" data-units-visible="true">
//     ${tplMarkerSpace({
//       id: OPEN_SEAS_MARKER_SAIL_BOX,
//       top: 77.5,
//       left: 1374.5,
//     })}
//     ${tplLossesBox()}
//     ${tplSpaces({ spaces })}
//     ${tplVictoryPointsTrack()}
//     ${tplBattleTrack()}
//     ${tplBattleMarkersPool()}
//     ${tplCommanderTrack()}
//     ${tplRaidTrack()}
//     ${tplYearTrack()}
//     ${tplActionRoundTrack()}
//     ${tplMarkerSpace({
//       id: `${CHEROKEE_CONTROL}_markers`,
//       top: 2120,
//       left: 863.5,
//     })}
//     ${tplMarkerSpace({
//       id: `${IROQUOIS_CONTROL}_markers`,
//       top: 1711.5,
//       left: 585.5,
//     })}
//     ${tplSailBox()}
//     ${tplMarkerSpace({
//       id: `wieChitPlaceholder_french`,
//       top: 24.5,
//       left: 108.5,
//     })}
//     ${tplMarkerSpace({
//       id: `wieChitPlaceholder_british`,
//       top: 24.5,
//       left: 1074.5,
//     })}
//   </div>
//   </div>`;
// };
