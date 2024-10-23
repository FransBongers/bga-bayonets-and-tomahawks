const getPoolConfig = (): Array<{ id: string; title: string; faction: string; }> => [
  {
    id: POOL_FLEETS,
    title: _('Fleets'),
    faction: NEUTRAL,
  },
  {
    id: POOL_NEUTRAL_INDIANS,
    title: _('Neutral Indians'),
    faction: NEUTRAL,
  },
  // British
  {
    id: POOL_BRITISH_COMMANDERS,
    title: _('Commanders'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_LIGHT,
    title: _('Light'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_COLONIAL_LIGHT,
    title: _('Colonial Light'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_ARTILLERY,
    title: _('Artillery'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_FORTS,
    title: _('Forts'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_METROPOLITAN_VOW,
    title: _('Metropolitan Brigades & VoW'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_COLONIAL_VOW,
    title: _('Colonial Brigades & VoW'),
    faction: BRITISH,
  },
  {
    id: POOL_BRITISH_COLONIAL_VOW_BONUS,
    title: _('Colonial VoW Bonus'),
    faction: BRITISH,
  },
  // French
  {
    id: POOL_FRENCH_COMMANDERS,
    title: _('Commanders'),
    faction: FRENCH,
  },
  {
    id: POOL_FRENCH_LIGHT,
    title: _('Light'),
    faction: FRENCH,
  },
  {
    id: POOL_FRENCH_ARTILLERY,
    title: _('Artillery'),
    faction: FRENCH,
  },
  {
    id: POOL_FRENCH_FORTS,
    title: _('Forts'),
    faction: FRENCH,
  },
  {
    id: POOL_FRENCH_METROPOLITAN_VOW,
    title: _('Metropolitan Brigades & VoW'),
    faction: FRENCH,
  },
];
