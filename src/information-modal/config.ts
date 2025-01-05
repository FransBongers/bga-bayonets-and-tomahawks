const getActionsConfig = () => [
  
]

const winterQuartersProcedureConfig = (game: BayonetsAndTomahawksGame) => [
  game.format_string_recursive(_('Remove ${tkn_boldText_raided} markers ${tkn_italicText_raidTrackUnaffected}.'),{
    tkn_boldText_raided: _('Raided'),
    tkn_italicText_raidTrackUnaffected: _('(Raid track unaffected)')
  }),
  game.format_string_recursive(_('Remove ${tkn_boldText_routOOS} markers.'),{
    tkn_boldText_routOOS: _('Rout / OOS'),
  }),
  game.format_string_recursive(_('Stack on ${tkn_boldText_sailBox}: to a ${tkn_italicText_friendlyCHS}.'),{
    tkn_boldText_sailBox: _('Sail box'),
    tkn_italicText_friendlyCHS: _('friendly Coastal Home Space')
  }),
  game.format_string_recursive(_('${tkn_boldText_indianUnits} ${tkn_italicText_incLossesBox}: to their villages ${tkn_italicText_noEnemyControl}. Go to / remain on Losses box otherwise.'),{
    tkn_boldText_indianUnits: _('Indian units'),
    tkn_italicText_incLossesBox: _('(including Losses box)'),
    tkn_italicText_noEnemyControl: _('if not enemy-controlled'),
  }),
  game.format_string_recursive(_('Colonial Brigades on spaces: to ${tkn_boldText_disbandedBox} ${tkn_italicText_keepStatus}. May remain as ${tkn_italicText_winterGarrisonOnly}.'),{
    tkn_boldText_disbandedBox: _('Disbanded Colonial Brigades box'),
    tkn_italicText_keepStatus: _('(keep Full/Reduced status)'),
    tkn_italicText_winterGarrisonOnly: _('Winter Garrison only')
  }),
  game.format_string_recursive(_('${tkn_boldText_stacksNotOnFriendlyCol}: Return to Colonies.'),{
    tkn_boldText_stacksNotOnFriendlyCol: _('Stacks not on a friendly Colony'),
  }),
  game.format_string_recursive(_('${tkn_boldText_fleets}: to Fleets pool.'),{
    tkn_boldText_fleets: _('Fleets'),
  }),
  game.format_string_recursive(_('${tkn_boldText_forEachLossesBox}: 1/3 of ${tkn_italicText_sameType} non-Indian units (rounded down) return to ${tkn_italicText_friendly} Settled Home Spaces (Highland: 1 max. per Year / Colonial Brigade: to ${tkn_italicText_disbandedBox}).'),{
    tkn_boldText_forEachLossesBox: _('For each Losses box'),
    tkn_italicText_sameType: _('same type'),
    tkn_italicText_friendly: _('friendly'),
    tkn_italicText_disbandedBox: _('Disbanded Colonial Brigades box'),
  }),
  game.format_string_recursive(_('Discard Reserve cards. If Scenario 4/4B: also replace ${tkn_italicText_yearSpecific} cards. Played/discarded/newly added cards: shuffled in their respective decks.'),{
    tkn_italicText_yearSpecific: _('Year-specific'),
  }),
  game.format_string_recursive(_('${tkn_boldText_yearMarker}: advanced to next Year. ${tkn_boldText_roundMarker}: to AR1.'),{
    tkn_boldText_yearMarker: _('Year marker'),
    tkn_boldText_roundMarker: _('Round marker')
  }),
]

const returnToColoniesProcedureConfig = (game: BayonetsAndTomahawksGame) => [
  game.format_string_recursive(_('${tkn_boldText_stacksNotOnFriendlyCHS} Move to ${tkn_italicText_nearest} Fleet / friendly Colony Home Space (count Connections). ${tkn_italicText_connectionRestrictions}.${tkn_newLine}${tkn_boldText_stackIncFleets}: no move until step 2.'),{
    tkn_boldText_stacksNotOnFriendlyCHS: _('Stacks not on a friendly Colony Home Space:'),
    tkn_italicText_nearest: _('nearest'),
    tkn_newLine: '',
    tkn_italicText_connectionRestrictions: _('Connection type restrictions apply'),
    tkn_boldText_stackIncFleets: ('Stacks including Fleet(s)')
  }),
  game.format_string_recursive(_('${tkn_boldText_stacksOnNonHomeCS} (including stacks that moved in step 1): To friendly Coastal Home Spaces on ${tkn_italicText_friendlyCols}. If none available: Fleet Retreat priorities.'),{
    tkn_boldText_stacksOnNonHomeCS: _('Stacks still on non-Home Coastal Spaces'),
    tkn_italicText_friendlyCols: _('friendly Colonies')
  }),
  game.format_string_recursive(_('${tkn_boldText_commanders}: May redeploy to stacks on ${tkn_italicText_friendlyCHS}. Mandatory for Commanders with no other unit types.'),{
    tkn_boldText_commanders: _('Commanders'),
    tkn_italicText_friendlyCHS: _('friendly Colonies Home Spaces')
  }),
  game.format_string_recursive(_('${tkn_boldText_combineReduced} on spaces + ${tkn_italicText_disbandedBox}. Odd Reduced Colonial Brigade: flipped to Full.'),{
    tkn_boldText_combineReduced: _('Combine Reduced units'),
    tkn_italicText_disbandedBox: _('Disbanded Colonial Brigades box'),
  }),
];

const spacesInfoConfig = (game: BayonetsAndTomahawksGame) => [
  {
    title: _('Victory spaces = yellow outline'),
    text: game.format_string_recursive(_('${tkn_italicText_fortresses} and some ${tkn_italicText_outpostSettles} that are worth Victory Points (VPs).'), {
      tkn_italicText_fortresses: _('Fortresses'),
      tkn_italicText_outpostSettles: _('Outposts/Settled Spaces')
    }),
    images: [
      {
        width: 50,
        height: 50,
        left: -603,
        top: -876,
      },
      {
        width: 50,
        height: 50,
        left: -212,
        top: -1014,
      },
    ]
  },
  {
    title: _('Settled'),
    text: _('Has Militia. Number = Raid points, VPs if Victory Space, # of enemy Brigades that may winter (other types unlimited). Enemy must remain to keep VPs.'),
    images: [
      {
        width: 50,
        height: 50,
        left: -603,
        top: -762,
      },
      {
        width: 50,
        height: 50,
        left: -201,
        top: -794,
      },
    ]
  },
  {
    title: _('Outpost'),
    text: _('1 Raid point. 1 VP if Victory Space. Supply tracing allowed by own faction unless enemy units occupy it.'),
    images: [
      {
        width: 50,
        height: 50,
        left: -531,
        top: -597,
      },
      {
        width: 50,
        height: 50,
        left: -153,
        top: -1130,
      },
    ]
  },
  {
    title: _('Indian Village'),
    text: _('Space OR icon on British/ French Home Space. Each linked to indicated Indian unit'),
    images: [
      {
        width: 50,
        height: 50,
        left: -382,
        top: -1120,
      },
      {
        width: 50,
        height: 50,
        left: -308,
        top: -1143,
      },
    ]
  },
  {
    title: _('Coastal'),
    text: _('Space of any type bordering a Sea Zone. Allows Sail Movement or Landing (check Open Seas marker). Landing attacker: Battle penalty'),
    images: [
      {
        width: 102,
        height: 50,
        left: -631,
        top: -581,
      },
    ]
  },
  {
    title: _('Wilderness'),
    text: _('No control. 1 Raid point if Fort on it'),
    images: [
      {
        width: 50,
        height: 50,
        left: -546,
        top: -500,
      },
    ]
  },
  {
    title: _('Fortress (also a Settled Space)'),
    text: _('British stack: no Overwhelm required to leave space. No defender Retreat if Bastion remains. Last Bastion eliminated: Rout. Non-light defending units may be eliminated. No Bastions: Settled Space only'),
    images: [
      {
        width: 138,
        height: 55,
        left: -689,
        top: -194,
      },
    ]
  },
  {
    title: _('Base'),
    text: _('No French unit allowed.'),
    images: [
      {
        width: 50,
        height: 50,
        left: -743,
        top: -798,
      },
    ]
  },
]
