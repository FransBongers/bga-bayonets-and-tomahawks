const MIN_PLAY_AREA_WIDTH = 1500;

/**
 * Class names
 */
const DISABLED = "disabled";
const BT_SELECTABLE = "bt_selectable";
const BT_SELECTED = "bt_selected";

/**
 * Setting ids
 */
// const CARD_SIZE_IN_LOG = 'cardSizeInLog';
// const CARD_INFO_IN_TOOLTIP = 'cardInfoInTooltip';
const PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY =
  "confirmEndOfTurnPlayerSwitchOnly";
const PREF_SHOW_ANIMATIONS = "showAnimations";
const PREF_ANIMATION_SPEED = "animationSpeed";
const PREF_DISABLED = "disabled";
const PREF_ENABLED = "enabled";

/**
 * Factions / control
 */
const BRITISH = "british";
const FRENCH = "french";
const INDIAN = "indian";
const NEUTRAL = "neutral";
const FACTIONS: Faction[] = [BRITISH, FRENCH, INDIAN];

/**
 * Pools
 */
const POOL_FLEETS = "poolFleets";
const POOL_BRITISH_COMMANDERS = "poolBritishCommanders";
const POOL_BRITISH_LIGHT = "poolBritishLight";
const POOL_BRITISH_ARTILLERY = "poolBritishArtillery";
const POOL_BRITISH_FORTS = "poolBritishForts";
const POOL_BRITISH_METROPOLITAN_VOW = "poolBritishMetropolitanVoW";
const POOL_BRITISH_COLONIAL_VOW = "poolBritishColonialVoW";

const POOL_FRENCH_COMMANDERS = "poolFrenchCommanders";
const POOL_FRENCH_LIGHT = "poolFrenchLight";
const POOL_FRENCH_ARTILLERY = "poolFrenchArtillery";
const POOL_FRENCH_FORTS = "poolFrenchForts";
const POOL_FRENCH_METROPOLITAN_VOW = "poolFrenchMetropolitanVoW";

const POOL_NEUTRAL_INDIANS = "poolNeutralIndians";

const POOLS = [
  POOL_FLEETS,
  POOL_BRITISH_COMMANDERS,
  POOL_BRITISH_LIGHT,
  POOL_BRITISH_ARTILLERY,
  POOL_BRITISH_FORTS,
  POOL_BRITISH_METROPOLITAN_VOW,
  POOL_BRITISH_COLONIAL_VOW,
  POOL_FRENCH_COMMANDERS,
  POOL_FRENCH_LIGHT,
  POOL_FRENCH_ARTILLERY,
  POOL_FRENCH_FORTS,
  POOL_FRENCH_METROPOLITAN_VOW,
  POOL_NEUTRAL_INDIANS,
];

/**
 * Tokens / markers
 */
const YEAR_MARKER = "year_marker";
const ROUND_MARKER = "round_marker";
const VICTORY_MARKER = "victory_marker";
const OPEN_SEAS_MARKER = "open_seas_marker";
const FRENCH_RAID_MARKER = "french_raid_marker";
const BRITISH_RAID_MARKER = "british_raid_marker";
