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

const tplGameMap = ({
  gamedatas,
}: {
  gamedatas: BayonetsAndTomahawksGamedatas;
}) => {
  const { spaces } = gamedatas;

  return `
  <div id="bt_game_map">
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
    ${tplMarkerSpace({ id: `${CHEROKEE_CONTROL}_markers`, top: 2120, left: 863.5 })}
    ${tplMarkerSpace({ id: `${IROQUOIS_CONTROL}_markers`, top: 1711.5, left: 585.5 })}
  </div>`;
};
// <div class="bt_marker_test" data-marker-type="victory_point"></div>

// <i class="fa-regular fa-magnifying-glass-plus"></i>
// <div class="bt_token" data-faction="french" data-unit-type="bastion"></div>
// <div class="bt_token" data-faction="indian" data-unit-type="micmac"></div>
