/* ------- DEFAULT LOG TOKENS ------- */

const tlpLogTokenBoldText = ({
  text,
  tooltipId,
  italic = false,
}: {
  text: string;
  tooltipId?: string;
  italic?: boolean;
}) =>
  `<span ${tooltipId ? `id="${tooltipId}"` : ''} style="font-weight: 700;${
    italic ? ' font-style: italic;' : ''
  }">${_(text)}</span>`;

const tplLogTokenPlayerName = ({
  name,
  color,
}: {
  name: string;
  color: string;
}) => `<span class="playername" style="color:#${color};">${name}</span>`;

/* ------- GAME SPECIFIC LOG TOKENS ------- */

const tplLogTokenActionPoint = (faction: string, actionPointId: string) => {
  return `<div class="bt_action_point" data-faction="${faction}"><div class="bt_action_point_img" data-ap-id="${actionPointId}"></div></div>`;
};

const tplLogTokenCard = (id: string) => {
  return `<div class="bt_log_card bt_card" data-card-id="${id}"></div>`;
};

const tplLogTokenMarker = (type: string, side?: string,) => {
  return `<div class="bt_log_token bt_marker_side" data-type="${type}"${side ? ` data-side="${side}"` : ''}></div>`;
};

const tplLogTokenRoad = (state: string) => {
  return `<div class="bt_log_token bt_road" data-road="${state}"></div>`;
};

const tplLogTokenUnit = (counterId: string, type: string, reduced: boolean) => {
  return `<div class="bt_token_side bt_log_token" data-counter-id="${counterId}${
    reduced ? '_reduced' : ''
  }"${type === COMMANDER ? ' data-commander="true"' : ''}></div>`;
};

const tplLogDieResult = (dieResult: string) => {
  return `<div class="bt_log_die" data-die-result="${dieResult}"></div>`;
};

const tplLogTokenWieChit = (input: string) => {
  const split = input.split(':');
  const side = split[1] === 'back' ? 'back' : 'front';
  const value = split[1] === 'back' ? '0' : split[1];
  return `<div class="bt_log_token bt_marker_side" data-type="wieChit" data-faction="${split[0]}" data-side="${side}" data-value="${value}"></div>`;
};
