/* ------- DEFAULT LOG TOKENS ------- */

const tlpLogTokenBoldText = ({ text }) =>
  `<span style="font-weight: 700;">${_(text)}</span>`;

const tplLogTokenPlayerName = ({
  name,
  color,
}: {
  name: string;
  color: string;
}) => `<span class="playername" style="color:#${color};">${name}</span>`;

/* ------- GAME SPECIFIC LOG TOKENS ------- */

const tplLogTokenCard = (id: string) => {
  return `<div class="bt_log_card bt_card" data-card-id="${id}"></div>`;
};

const tplLogTokenMarker = (type: string) => {
  return `<div class="bt_marker_side" data-type="${type}"></div>`;
};

const tplLogTokenUnit = (counterId: string, type: string, reduced: boolean) => {
  return `<div class="bt_token_side" data-counter-id="${counterId}${
    reduced ? '_reduced' : ''
  }"${type === COMMANDER ? ' data-commander="true"' : ''}></div>`;
};

const tplLogDieResult = (dieResult: string) => {
  return `<div class="bt_log_die" data-die-result="${dieResult}"></div>`;
};
