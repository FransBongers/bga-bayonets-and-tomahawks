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