const LOG_TOKEN_BOLD_TEXT = 'boldText';
const LOG_TOKEN_BOLD_ITALIC_TEXT = 'boldItalicText';
const LOG_TOKEN_ITALIC_TEXT = 'italicText';
const LOG_TOKEN_NEW_LINE = 'newLine';
// const LOG_TOKEN_PLAYER_NAME = "playerName";
// Game specific
const LOG_TOKEN_ACTION_POINT = 'actionPoint'
const LOG_TOKEN_CARD = 'card';
const LOG_TOKEN_CARD_NAME = 'cardName';
const LOG_TOKEN_MARKER = 'marker';
const LOG_TOKEN_WIE_CHIT = 'wieChit';
const LOG_TOKEN_ROAD = 'road';
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
    case LOG_TOKEN_BOLD_ITALIC_TEXT:
      return tlpLogTokenBoldText({ text: value, italic: true });
    case LOG_TOKEN_ITALIC_TEXT:
      return tlpLogTokenBoldText({ text: value, italic: true, bold: false });
    case LOG_TOKEN_ACTION_POINT:
      const [faction, actionPointId] = value.split(':');
      return tplLogTokenActionPoint(faction, actionPointId);
    case LOG_TOKEN_CARD:
      return tplLogTokenCard(value);
    case LOG_TOKEN_MARKER:
      const [tokenType, tokenSide] = value.split(':');
      return tplLogTokenMarker(tokenType, tokenSide);
    case LOG_TOKEN_CARD_NAME:
      let cardNameTooltipId = undefined;
      const withTooltip = value.includes(':');
      if (withTooltip) {
        cardNameTooltipId = `bt_tooltip_${game._last_tooltip_id}`;
        game.tooltipsToMap.push([game._last_tooltip_id, value.split(':')[0]]);
        game._last_tooltip_id++;
      }
      return tlpLogTokenBoldText({
        text: withTooltip ? value.split(':')[1] : value,
        tooltipId: cardNameTooltipId,
      });
    case LOG_TOKEN_NEW_LINE:
      return '<br>';
    case LOG_TOKEN_DIE_RESULT:
      return tplLogDieResult(value);
    case LOG_TOKEN_ROAD:
      return tplLogTokenRoad(value);
    case LOG_TOKEN_UNIT:
      const splitCounterId = value.split(':');
      const counterId = splitCounterId[0];
      const reduced = splitCounterId?.[1] === 'reduced';
      return tplLogTokenUnit(
        counterId,
        game.gamedatas.staticData.units[counterId]?.type,
        reduced
      );
    case LOG_TOKEN_WIE_CHIT:
      return tplLogTokenWieChit(value);
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
