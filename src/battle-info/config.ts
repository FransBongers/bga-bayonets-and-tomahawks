const getBattleOrderTitle = (step: string): string => {
  const nameMap = {
    [NON_INDIAN_LIGHT]: _('Non-Indian Light'),
    [INDIAN]: _('Indian Light'),
    [HIGHLAND_BRIGADES]: _('Highland Brigades'),
    [METROPOLITAN_BRIGADES]: _('Metropolitan Brigades'),
    [NON_METROPOLITAN_BRIGADES]: _('Non-Metropolitan Brigades'),
    [FLEETS]: _('Fleets'),
    [BASTIONS_OR_FORT]: _('Bastion or Fort'),
    [ARTILLERY]: _('Artillery'),
    [MILITIA]: _('Militia'),
  };
  return nameMap[step] || '';
};

const getBattleHitPriorityTitle = (step: string): string => {
  const nameMap = {
    [NON_INDIAN_LIGHT]: _('Non-Indian Light'),
    [INDIAN]: _('Indian Light'),
    [HIGHLAND_BRIGADES]: _('Highland Brigade'),
    [METROPOLITAN_BRIGADES]: _('Metropolitan Brigade'),
    [NON_METROPOLITAN_BRIGADES]: _('Non-Metropolitan Brigade'),
    [FLEETS]: _('Fleet'),
    [FORT]: _('Fort'),
    [BASTIONS_OR_FORT]: _('Bastion or Fort'),
    [ARTILLERY]: _('Artillery'),
    [BATTLE_INFO_FIRST_HIT_TO_HIGHLAND]: _('First Hit to Highland'),
  };
  return nameMap[step] || '';
};

const getBattleOrderConfig = () => ({
  [NON_INDIAN_LIGHT]: {
    title: getBattleOrderTitle(NON_INDIAN_LIGHT),
    counterIds: [LACORNE, WASHINGTON, L_HOWE],
  },
  [INDIAN]: {
    title: getBattleOrderTitle(INDIAN),
    counterIds: [DELAWARE, MOHAWK],
  },
  [HIGHLAND_BRIGADES]: {
    title: getBattleOrderTitle(HIGHLAND_BRIGADES),
    counterIds: [FRASER],
  },
  [METROPOLITAN_BRIGADES]: {
    title: getBattleOrderTitle(METROPOLITAN_BRIGADES),
    counterIds: [BOULONNOIS_ROYAL_BARROIS, B_22ND_28TH],
  },
  [NON_METROPOLITAN_BRIGADES]: {
    title: getBattleOrderTitle(NON_METROPOLITAN_BRIGADES),
    counterIds: [CANADIENS, NEW_ENGLAND],
  },
  [FLEETS]: {
    title: getBattleOrderTitle(FLEETS),
    counterIds: [DE_LA_MOTTE, HOLBURNE],
  },
  [BASTIONS_OR_FORT]: {
    title: getBattleOrderTitle(BASTIONS_OR_FORT),
    counterIds: [CARILLON, BASTION, CUMBERLAND],
  },
  [ARTILLERY]: {
    title: getBattleOrderTitle(ARTILLERY),
    counterIds: [CANONNIERS_BOMBARDIERS, ROYAL_ARTILLERY],
  },
  [MILITIA]: {
    title: getBattleOrderTitle(MILITIA),
    counterIds: [FRENCH_MILITIA_MARKER, BRITISH_MILITIA_MARKER],
  },
});

const BATTLE_INFO_ADVANCE_BATTLE_MARKER = 'BATTLE_INFO_ADVANCE_BATTLE_MARKER';
const BATTLE_INFO_FIRST_HIT_TO_HIGHLAND = 'BATTLE_INFO_FIRST_HIT_TO_HIGHLAND';
const BATTLE_INFO_MAY_MOVE_TO_COASTAL_HOME_SPACE =
  'BATTLE_INFO_MAY_MOVE_TO_COASTAL_HOME_SPACE';
const BATTLE_INFO_NA = 'BATTLE_INFO_NA';

const getBattleInfoCellContent = (type: string) => {
  switch (type) {
    case MISS:
      return `<span>${_('Miss')}</span>`;
    case BATTLE_INFO_ADVANCE_BATTLE_MARKER:
      return `<span>${_('Advance Battle Marker')}</span>`;
    case BATTLE_INFO_MAY_MOVE_TO_COASTAL_HOME_SPACE:
      return `<span>${_(
        'May move to friendly non-Battle Coastal Home space'
      )}</span>`;
    case BATTLE_INFO_NA:
      return _('N/A');
    default:
      return '<div>This should not be here</div>';
  }
};

const getBattlePriorityContent = (
  input: Array<{ index?: number; step: string; classes?: string }>
) => {
  return input
    .map(({ index, step, classes }) => {
      const itemClasses = [];
      if (index === 1) {
        itemClasses.push('bt_battle_priority_1');
      }
      if (classes) {
        itemClasses.push(classes);
      }

      return `
    <div${
      itemClasses.length > 0 ? ` class="${itemClasses.join(' ')}"` : ''
    }><span class="bt_battle_priority_index">${
        index ? `${index}.` : ''
      }</span><span>${getBattleHitPriorityTitle(step)}</span></div>
  `;
    })
    .join('');
};

const BATTLE_DIE_RESULT_IF_ENEMY_TRIANGLE =
  'BATTLE_DIE_RESULT_IF_ENEMY_TRIANGLE';
const BATTLE_DIE_RESULT_IF_ENEMY_SQUARE = 'BATTLE_DIE_RESULT_IF_ENEMY_SQUARE';
const BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE = 'BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE';
const BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE_NO_BASTION =
  'BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE_NO_BASTION';
const BATTLE_DIE_RESULT_APPLY_HIT_TO_ENENMY_SQUARE_IF_MARKER_OVER_0 =
  'BATTLE_DIE_RESULT_APPLY_HIT_TO_ENENMY_SQUARE_IF_MARKER_OVER_0';
const BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0 =
  'BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0';
const BATTLE_DIE_RESULT_REMOVE_ENEMY_MILITIA =
  'BATTLE_DIE_RESULT_REMOVE_ENEMY_MILITIA';
const BATTLE_DIE_RESULT_RESOLVE_SQUARE_CIRCLE =
  'BATTLE_DIE_RESULT_RESOLVE_SQUARE_CIRCLE';

const getDieResultText = (game: BayonetsAndTomahawksGame, textId: string) => {
  switch (textId) {
    case BATTLE_DIE_RESULT_IF_ENEMY_TRIANGLE:
      return game.format_string_recursive(
        _('If there is an enemy ${tkn_shape} unit:'),
        {
          tkn_shape: 'triangle',
        }
      );
    case BATTLE_DIE_RESULT_IF_ENEMY_SQUARE:
      return game.format_string_recursive(
        _('If there is an enemy ${tkn_shape} unit:'),
        {
          tkn_shape: 'square',
        }
      );
    case BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE:
      return game.format_string_recursive(
        _('If there is an enemy ${tkn_shape} unit:'),
        {
          tkn_shape: 'circle',
        }
      );
    case BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE_NO_BASTION:
      return game.format_string_recursive(
        _('If there is an enemy ${tkn_shape} unit (other than ${tkn_unit}):'),
        {
          tkn_shape: 'circle',
          tkn_unit: BASTION,
        }
      );
    case BATTLE_INFO_ADVANCE_BATTLE_MARKER:
      return _('Advance Battle Marker');
    case BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0:
      return _('If Marker > 0: apply Hit');
    case BATTLE_DIE_RESULT_APPLY_HIT_TO_ENENMY_SQUARE_IF_MARKER_OVER_0:
      return game.format_string_recursive(
        _(
          'If Marker > 0: apply Hit to enemy ${tkn_shape} unit ${tkn_newLine}${tkn_italicText}'
        ),
        {
          tkn_shape: 'square',
          tkn_italicText: _('Prioritize Metropolitan Brigade'),
          tkn_newLine: '',
        }
      );
    case BATTLE_DIE_RESULT_REMOVE_ENEMY_MILITIA:
      return _('Remove 1 enemy Militia');
    case BATTLE_DIE_RESULT_RESOLVE_SQUARE_CIRCLE:
      return game.format_string_recursive(
        _('Resolve ${tkn_dieResult} result'),
        {
          tkn_dieResult: HIT_SQUARE_CIRCLE,
        }
      );
    default:
      return 'This should not be here';
  }
};

const getBattleResolveDieContent = (
  game: BayonetsAndTomahawksGame,
  input: Array<{ index?: number; textId: string; classes?: string }>
) => {
  return input
    .map(({ index, textId, classes }) => {
      const itemClasses = [];
      if (classes) {
        itemClasses.push(classes);
      }

      return `
    <div${itemClasses.length > 0 ? ` class="${itemClasses.join(' ')}"` : ''}>
    ${index ? `<span class="bt_battle_resolve_die_index">${index}.</span>` : ''}
    <span>${getDieResultText(game, textId)}</span></div>
  `;
    })
    .join('');
};

const getBattleInfoDieResultConfig = (game: BayonetsAndTomahawksGame): Array<{
  content: string;
  column: {
    from: number;
    to: number;
  };
  row: {
    from: number;
    to: number;
  }
  extraClasses?: string;
}> => [
  {
    content: getBattleResolveDieContent(game, [
      {
        textId: BATTLE_DIE_RESULT_IF_ENEMY_TRIANGLE,
      },
      {
        index: 1,
        textId: BATTLE_INFO_ADVANCE_BATTLE_MARKER,
      },
      {
        index: 2,
        textId: BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0,
      },
    ]),
    column: {
      from: 2,
      to: 3,
    },
    row: {
      from: 2,
      to: 4,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 3,
      to: 4,
    },
    row: {
      from: 2,
      to: 4,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 4,
      to: 5,
    },
    row: {
      from: 2,
      to: 4,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(BATTLE_INFO_ADVANCE_BATTLE_MARKER),
    column: {
      from: 5,
      to: 6,
    },
    row: {
      from: 2,
      to: 4,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 6,
      to: 7,
    },
    row: {
      from: 2,
      to: 4,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: NON_INDIAN_LIGHT,
      },
      {
        index: 2,
        step: INDIAN,
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 2,
      to: 3,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: INDIAN,
      },
      {
        index: 2,
        step: NON_INDIAN_LIGHT,
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 3,
      to: 4,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 2,
      to: 3,
    },
    row: {
      from: 4,
      to: 7,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleResolveDieContent(game, [
      {
        textId: BATTLE_DIE_RESULT_IF_ENEMY_SQUARE,
      },
      {
        index: 1,
        textId: BATTLE_INFO_ADVANCE_BATTLE_MARKER,
      },
      {
        index: 2,
        textId: BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0,
      },
    ]),
    column: {
      from: 3,
      to: 4,
    },
    row: {
      from: 4,
      to: 7,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleResolveDieContent(game, [
      {
        index: 1,
        textId: BATTLE_DIE_RESULT_REMOVE_ENEMY_MILITIA,
      },
      {
        index: 2,
        textId: BATTLE_DIE_RESULT_RESOLVE_SQUARE_CIRCLE,
      },
    ]),
    column: {
      from: 4,
      to: 5,
    },
    row: {
      from: 4,
      to: 6,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattleResolveDieContent(game, [
      {
        textId: BATTLE_DIE_RESULT_REMOVE_ENEMY_MILITIA,
      },
    ]),
    column: {
      from: 4,
      to: 5,
    },
    row: {
      from: 6,
      to: 7,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(BATTLE_INFO_ADVANCE_BATTLE_MARKER),
    column: {
      from: 5,
      to: 6,
    },
    row: {
      from: 4,
      to: 7,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 6,
      to: 7,
    },
    row: {
      from: 4,
      to: 7,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: METROPOLITAN_BRIGADES,
      },
      {
        step: BATTLE_INFO_FIRST_HIT_TO_HIGHLAND,
        classes: 'bt_highland_first',
      },
      {
        index: 2,
        step: NON_METROPOLITAN_BRIGADES,
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 4,
      to: 6,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: NON_METROPOLITAN_BRIGADES,
      },
      {
        index: 2,
        step: METROPOLITAN_BRIGADES,
      },
      {
        step: BATTLE_INFO_FIRST_HIT_TO_HIGHLAND,
        classes: 'bt_highland_first',
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 6,
      to: 7,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattleResolveDieContent(game, [
      {
        textId: BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE_NO_BASTION,
      },
      {
        index: 1,
        textId: BATTLE_INFO_ADVANCE_BATTLE_MARKER,
      },
      {
        index: 2,
        textId: BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0,
      },
    ]),
    column: {
      from: 2,
      to: 4,
    },
    row: {
      from: 7,
      to: 8,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(
      BATTLE_INFO_MAY_MOVE_TO_COASTAL_HOME_SPACE
    ),
    column: {
      from: 4,
      to: 5,
    },
    row: {
      from: 7,
      to: 8,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(BATTLE_INFO_ADVANCE_BATTLE_MARKER),
    column: {
      from: 5,
      to: 6,
    },
    row: {
      from: 7,
      to: 10,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 6,
      to: 7,
    },
    row: {
      from: 7,
      to: 10,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: FLEETS,
      },
      {
        index: 2,
        step: ARTILLERY,
      },
      {
        index: 3,
        step: FORT,
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 7,
      to: 8,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattleResolveDieContent(game, [
      {
        textId: BATTLE_DIE_RESULT_IF_ENEMY_CIRCLE,
      },
      {
        index: 1,
        textId: BATTLE_INFO_ADVANCE_BATTLE_MARKER,
      },
      {
        index: 2,
        textId: BATTLE_DIE_RESULT_APPLY_HIT_IF_MARKER_OVER_0,
      },
    ]),
    column: {
      from: 2,
      to: 4,
    },
    row: {
      from: 8,
      to: 10,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleResolveDieContent(game, [
      {
        textId: BATTLE_DIE_RESULT_IF_ENEMY_SQUARE,
      },
      {
        index: 1,
        textId: BATTLE_INFO_ADVANCE_BATTLE_MARKER,
      },
      {
        index: 2,
        textId: BATTLE_DIE_RESULT_APPLY_HIT_TO_ENENMY_SQUARE_IF_MARKER_OVER_0,
      },
    ]),
    column: {
      from: 4,
      to: 5,
    },
    row: {
      from: 8,
      to: 10,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: ARTILLERY,
      },
      {
        index: 2,
        step: FLEETS,
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 8,
      to: 9,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattlePriorityContent([
      {
        index: 1,
        step: ARTILLERY,
      },
      {
        index: 2,
        step: BASTIONS_OR_FORT,
      },
      {
        index: 3,
        step: FLEETS,
      },
    ]),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 9,
      to: 10,
    },
    extraClasses: 'bt_center bt_align_left'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 2,
      to: 5,
    },
    row: {
      from: 10,
      to: 11,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(BATTLE_INFO_ADVANCE_BATTLE_MARKER),
    column: {
      from: 5,
      to: 6,
    },
    row: {
      from: 10,
      to: 11,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(MISS),
    column: {
      from: 6,
      to: 7,
    },
    row: {
      from: 10,
      to: 11,
    },
    extraClasses: 'bt_center'
  },
  {
    content: getBattleInfoCellContent(BATTLE_INFO_NA),
    column: {
      from: 7,
      to: 8,
    },
    row: {
      from: 10,
      to: 11,
    },
    extraClasses: 'bt_center'
  },
];
