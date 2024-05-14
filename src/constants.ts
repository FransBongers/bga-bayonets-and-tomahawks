const MIN_PLAY_AREA_WIDTH = 1500; // Is this still used?
const MIN_NOTIFICATION_MS = 1200;

/**
 * Class names
 */
const DISABLED = "disabled";
const BT_SELECTABLE = "bt_selectable";
const BT_SELECTED = "bt_selected";

/**
 * Card locations
 */
const DISCARD = "discard";

/**
 * Setting ids
 */
// const CARD_SIZE_IN_LOG = 'cardSizeInLog';
// const CARD_INFO_IN_TOOLTIP = 'cardInfoInTooltip';
const PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY =
  "confirmEndOfTurnPlayerSwitchOnly";
const PREF_SHOW_ANIMATIONS = "showAnimations";
const PREF_ANIMATION_SPEED = "animationSpeed";
const PREF_CARD_SIZE_IN_LOG = "cardSizeInLog";
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

// Raid track
const RAID_TRACK_0 = 'raid_track_0';
const RAID_TRACK_1 = 'raid_track_1';
const RAID_TRACK_2 = 'raid_track_2';
const RAID_TRACK_3 = 'raid_track_3';
const RAID_TRACK_4 = 'raid_track_4';
const RAID_TRACK_5 = 'raid_track_5';
const RAID_TRACK_6 = 'raid_track_6';
const RAID_TRACK_7 = 'raid_track_7';
const RAID_TRACK_8 = 'raid_track_8';

// Victory points track
const VICTORY_POINTS_FRENCH_10 = 'victory_points_french_10';
const VICTORY_POINTS_FRENCH_9 = 'victory_points_french_9';
const VICTORY_POINTS_FRENCH_8 = 'victory_points_french_8';
const VICTORY_POINTS_FRENCH_7 = 'victory_points_french_7';
const VICTORY_POINTS_FRENCH_6 = 'victory_points_french_6';
const VICTORY_POINTS_FRENCH_5 = 'victory_points_french_5';
const VICTORY_POINTS_FRENCH_4 = 'victory_points_french_4';
const VICTORY_POINTS_FRENCH_3 = 'victory_points_french_3';
const VICTORY_POINTS_FRENCH_2 = 'victory_points_french_2';
const VICTORY_POINTS_FRENCH_1 = 'victory_points_french_1';

const VICTORY_POINTS_BRITISH_1 = 'victory_points_british_1';
const VICTORY_POINTS_BRITISH_2 = 'victory_points_british_2';
const VICTORY_POINTS_BRITISH_3 = 'victory_points_british_3';
const VICTORY_POINTS_BRITISH_4 = 'victory_points_british_4';
const VICTORY_POINTS_BRITISH_5 = 'victory_points_british_5';
const VICTORY_POINTS_BRITISH_6 = 'victory_points_british_6';
const VICTORY_POINTS_BRITISH_7 = 'victory_points_british_7';
const VICTORY_POINTS_BRITISH_8 = 'victory_points_british_8';
const VICTORY_POINTS_BRITISH_9 = 'victory_points_british_9';
const VICTORY_POINTS_BRITISH_10 = 'victory_points_british_10';


// Losses Boxes
const LOSSES_BOX_BRITISH = 'lossesBox_british';
const LOSSES_BOX_FRENCH = 'lossesBox_french';

/**
 * Actions
 */
const ACTION_ROUND_INDIAN_ACTIONS = 'ACTION_ROUND_INDIAN_ACTIONS';