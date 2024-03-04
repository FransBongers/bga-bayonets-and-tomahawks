const tplMarker = ({ id }: { id: string }) =>
  `<div id="${id}" class="bt_marker"></div>`;

const tplUnit = ({
  faction,
  counterId,
  style,
}: {
  faction?: "british" | "french" | "indian";
  counterId: string;
  style?: string;
}) => `
  <div class="bt_token" data-counter-id="${counterId}"${
  style ? ` style="${style}"` : ""
}></div>
`;

const tplSpaces = ({
  spaces,
}: {
  spaces: BayonetsAndTomahawksGamedatas["spaces"];
}): string => {
  const filteredSpaces = spaces.filter((space) => space.top && space.left);
  // const mappedSpaces = filteredSpaces.map((space) => `<div data-space-id="${space.id}" class="bt_space" style="top: ${space.top - 26}px; left: ${space.left - 26}px;"></div>`);
  // TO Check: why -26px?
  const mappedSpaces = filteredSpaces.map(
    (space) =>
      `<div data-space-id="${
        space.id
      }" class="bt_space" style="top: calc(var(--btMapScale) * ${
        space.top - 26
      }px); left: calc(var(--btMapScale) * ${space.left - 26}px);"></div>`
  );
  const result = mappedSpaces.join("");
  return result;
};

const tplMarkerSpace = ({
  id,
  top,
  left,
}: {
  id: string;
  top: number;
  left: number;
}) => {
  return `<div id="${id}" class="bt_marker_space" style="top: calc(var(--btMapScale) * ${top}px); left: calc(var(--btMapScale) * ${left}px);"></div>`;
};

const tplActionRoundTrack = () => ACTION_ROUND_TRACK_CONFIG.map((markerSpace) =>
tplMarkerSpace({
  id: `action_round_track_${markerSpace.id}`,
  top: markerSpace.top,
  left: markerSpace.left,
})
).join("");

const tplYearTrack = () => YEAR_TRACK_CONFIG.map((markerSpace) =>
tplMarkerSpace({
  id: `year_track_${markerSpace.year}`,
  top: markerSpace.top,
  left: markerSpace.left,
})
).join("");

const tplGameMap = ({
  gamedatas,
}: {
  gamedatas: BayonetsAndTomahawksGamedatas;
}) => {
  const { spaces } = gamedatas;

  return `
  <div id="bt_game_map">
    <div class="bt_marker_test" data-marker-type="victory_point"></div>
    ${tplSpaces({ spaces })}
    ${tplYearTrack()}
    ${tplActionRoundTrack()}

  </div>`;
};
// <i class="fa-regular fa-magnifying-glass-plus"></i>
// <div class="bt_token" data-faction="french" data-unit-type="bastion"></div>
// <div class="bt_token" data-faction="indian" data-unit-type="micmac"></div>
