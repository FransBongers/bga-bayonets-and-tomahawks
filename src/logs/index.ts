const LOG_TOKEN_BOLD_TEXT = 'boldText';
const LOG_TOKEN_NEW_LINE = 'newLine';
// const LOG_TOKEN_PLAYER_NAME = "playerName";
// Game specific
const LOG_TOKEN_CARD = 'card';
const LOG_TOKEN_MARKER = 'marker';
const LOG_TOKEN_UNIT = 'unit';
const LOG_TOKEN_DIE_RESULT = 'dieResult';

let tooltipIdCounter = 0;

const getTokenDiv = ({
  key,
  value,
  game,
}: {
  key: string;
  value: string;
  game: BayonetsAndTomahawksGame;
}) => {
  const splitKey = key.split('_');
  const type = splitKey[1];
  switch (type) {
    case LOG_TOKEN_BOLD_TEXT:
      return tlpLogTokenBoldText({ text: value });
    case LOG_TOKEN_CARD:
      return tplLogTokenCard(value);
    case LOG_TOKEN_MARKER:
      return tplLogTokenMarker(value);
    case LOG_TOKEN_NEW_LINE:
      return '<br>';
    case LOG_TOKEN_DIE_RESULT:
      return tplLogDieResult(value);
    case LOG_TOKEN_UNIT:
      return tplLogTokenUnit(value);
    // case LOG_TOKEN_PLAYER_NAME:
    //   const player = game.playerManager
    //     .getPlayers()
    //     .find((player) => player.getName() === value);
    //   return player
    //     ? tplLogTokenPlayerName({
    //         name: player.getName(),
    //         color: player.getHexColor(),
    //       })
    //     : value;
    default:
      return value;
  }
};
