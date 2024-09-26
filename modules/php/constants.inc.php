<?php
require_once 'gameoptions.inc.php';

/**
 * STATS
 */

const STAT_TURN = 12;

/**
 * Card locations -
 */
const DISCARD = 'discard';

/*
  * ENGINE
  */
const NODE_SEQ = 'seq';
const NODE_OR = 'or';
const NODE_XOR = 'xor';
const NODE_PARALLEL = 'parallel';
const NODE_LEAF = 'leaf';

const ZOMBIE = 98;
const PASS = 99;

const AFTER_FINISHING_ACTION = 'afterFinishing';

/**
 * State ids / names
 */

const ST_GAME_SETUP = 1;
const ST_GAME_SETUP_NAME = 'gameSetup';
// Boiler plate
const ST_BEFORE_START_OF_TURN = 6;
const ST_TURNACTION = 7;
const ST_RESOLVE_STACK = 90;
const ST_RESOLVE_CHOICE = 91;
const ST_IMPOSSIBLE_MANDATORY_ACTION = 92;
const ST_CONFIRM_TURN = 93;
const ST_CONFIRM_PARTIAL_TURN = 94;
const ST_GENERIC_NEXT_PLAYER = 95;
const ST_END_GAME = 99;
const ST_END_GAME_NAME = 'gameEnd';

const ST_CLEANUP = 88; // TODO: replace

// Game
const ST_SETUP_YEAR = 19;
const ST_SETUP_ACTION_ROUND = 20;
const ST_SELECT_RESERVE_CARD = 21;
const ST_PLAYER_ACTION = 22;

const ST_ACTION_ROUND_CHOOSE_CARD = 30;
const ST_ACTION_ROUND_CHOOSE_FIRST_PLAYER = 31;
const ST_ACTION_ROUND_SAIL_BOX_LANDING = 32;
const ST_ACTION_ROUND_CHOOSE_REACTION = 33;
const ST_ACTION_ROUND_ACTION_PHASE = 34;
const ST_ACTION_ROUND_RESOLVE_BATTLES = 35;
const ST_ACTION_ROUND_END = 36;
const ST_ACTION_ACTIVATE_STACK = 37;

const ST_ACTION_ROUND_RESOLVE_AR_START_EVENT = 38;

const ST_DRAW_REINFORCEMENTS = 50;
const ST_VAGARIES_OF_WAR_PICK_UNITS = 51;
const ST_VAGARIES_OF_WAR_PUT_BACK_IN_POOL = 52;

const ST_FLEETS_ARRIVE_COMMANDER_DRAW = 53;
const ST_FLEETS_ARRIVE_UNIT_PLACEMENT = 54;

const ST_LOGISTICS_ROUND_END = 55;
const ST_COLONIALS_ENLIST_UNIT_PLACEMENT = 56;

const ST_WINTER_QUARTERS_GAME_END_CHECK = 70;
const ST_WINTER_QUARTERS_ROUND_END = 71;
const ST_WINTER_QUARTERS_REMOVE_MARKERS = 72;
const ST_WINTER_QUARTERS_MOVE_STACK_ON_SAIL_BOX = 73;
const ST_WINTER_QUARTERS_PLACE_INDIAN_UNITS = 74;
const ST_WINTER_QUARTERS_DISBAND_COLONIAL_BRIGADES = 75;
const ST_WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES = 76;
const ST_WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK = 77;
const ST_WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK = 78;
const ST_WINTER_QUARTERS_RETURN_TO_COLONIES_LEAVE_UNITS = 79;

const ST_RAID_SELECT_TARGET = 80;
const ST_RAID_MOVE = 81;
const ST_RAID_RESOLUTION = 82;
const ST_RAID_INTERCEPTION = 83;
const ST_RAID_REROLL = 84;


const ST_MARSHAL_TROOPS = 110;
const ST_CONSTRUCTION = 111;

const ST_MOVEMENT = 120;
const ST_MOVEMENT_LOSE_CONTROL_CHECK = 121;
const ST_MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK = 122;
const ST_MOVEMENT_OVERWHELM_CHECK = 123;
const ST_MOVEMENT_PLACE_SPENT_MARKERS = 124;
const ST_MOVE_STACK = 125;
const ST_PLACE_MARKER_ON_STACK = 126;
const ST_SAIL_MOVEMENT = 127;

const ST_BATTLE_PREPARATION = 140;
const ST_BATTLE_SELECT_COMMANDER = 141;
const ST_BATTLE_ROLLS = 142;
const ST_BATTLE_ROLLS_REROLLS = 143;
const ST_BATTLE_ROLLS_ROLL_DICE = 144;
const ST_BATTLE_ROLLS_EFFECTS = 145;
const ST_BATTLE_APPLY_HITS = 146;
const ST_BATTLE_OUTCOME = 147;
const ST_BATTLE_ROUT = 148;
const ST_BATTLE_RETREAT = 149;
const ST_BATTLE_CLEANUP = 150;
const ST_BATTLE_RETREAT_CHECK_OPTIONS = 151;
const ST_BATTLE_MILITIA_ROLLS = 152;
const ST_BATTLE_COMBINE_REDUCED_UNITS = 153;
const ST_BATTLE_PENALTIES = 154;
const ST_BATTLE_PRE_SELECT_COMMANDER = 155;
const ST_BATTLE_FORT_ELIMINATION = 156;

const ST_EVENT_ARMED_BATTOEMEN = 170;
const ST_EVENT_BRITISH_ENCROACHMENT = 171;
const ST_EVENT_DELAYED_SUPPLIES_FROM_FRANCE = 172;
const ST_EVENT_DISEASE_IN_BRITISH_CAMP = 173;
const ST_EVENT_DISEASE_IN_FRENCH_CAMP = 174;
const ST_EVENT_HESITANT_BRITISH_GENERAL = 175;
const ST_EVENT_ROUND_UP_MEN_AND_EQUIPMENT = 176;
const ST_EVENT_PENNSYLVANIAS_PEACE_PROMISES = 177;
const ST_EVENT_SMALLPOX_EPIDEMIC = 178;
const ST_EVENT_SMALLPOX_INFECTED_BLANKETS = 179;
const ST_EVENT_STAGED_LACROSSE_GAME = 180;
const ST_EVENT_WILDERNESS_AMBUSH = 181;
const ST_USE_EVENT = 182;
const ST_EVENT_WINTERING_REAR_ADMIRAL = 183;
const ST_EVENT_FRENCH_LAKE_WARSHIPS = 184;

const ST_WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK = 200;
const ST_WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS = 201;
const ST_WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS = 202;
const ST_WINTER_QUARTERS_RETURN_FLEETS = 203;


/**
 * Scenario Ids
 */
const VaudreuilsPetiteGuerre1755 = 'VaudreuilsPetiteGuerre1755';
const LoudounsGamble1757 = 'LoudounsGamble1757';
const AmherstsJuggernaut1758_1759 = 'AmherstsJuggernaut1758_1759';

/**
 * Atomic actions
 */
const PLAYER_ACTION = 'PLAYER_ACTION';
const SELECT_RESERVE_CARD = 'SELECT_RESERVE_CARD';
const ACTION_ROUND_ACTION_PHASE = 'ACTION_ROUND_ACTION_PHASE';
const ACTION_ACTIVATE_STACK = 'ACTION_ACTIVATE_STACK';
const ACTION_ROUND_CHOOSE_CARD = 'ACTION_ROUND_CHOOSE_CARD';
const ACTION_ROUND_CHOOSE_FIRST_PLAYER = 'ACTION_ROUND_CHOOSE_FIRST_PLAYER';
const ACTION_ROUND_FIRST_PLAYER_ACTIONS = 'ACTION_ROUND_FIRST_PLAYER_ACTIONS';
const ACTION_ROUND_SECOND_PLAYER_ACTIONS = 'ACTION_ROUND_SECOND_PLAYER_ACTIONS';
const ACTION_ROUND_INDIAN_ACTIONS = 'ACTION_ROUND_INDIAN_ACTIONS';
const ACTION_ROUND_REACTION = 'ACTION_ROUND_REACTION';
const ACTION_ROUND_RESOLVE_AR_START_EVENT = 'ACTION_ROUND_RESOLVE_AR_START_EVENT';
const ACTION_ROUND_RESOLVE_BATTLES = 'ACTION_ROUND_RESOLVE_BATTLES';
const ACTION_ROUND_END = 'ACTION_ROUND_END';
const ACTION_ROUND_SAIL_BOX_LANDING = 'ACTION_ROUND_SAIL_BOX_LANDING';
const ACTION_ROUND_CHOOSE_REACTION = 'ACTION_ROUND_CHOOSE_REACTION';
const BATTLE_APPLY_HITS = 'BATTLE_APPLY_HITS';
const BATTLE_CLEANUP = 'BATTLE_CLEANUP';
const BATTLE_COMBINE_REDUCED_UNITS = 'BATTLE_COMBINE_REDUCED_UNITS';
const BATTLE_FORT_ELIMINATION = 'BATTLE_FORT_ELIMINATION';
const BATTLE_MILITIA_ROLLS = 'BATTLE_MILITIA_ROLLS';
const BATTLE_OUTCOME = 'BATTLE_OUTCOME';
const BATTLE_PENALTIES = 'BATTLE_PENALTIES';
const BATTLE_PRE_SELECT_COMMANDER = 'BATTLE_PRE_SELECT_COMMANDER';
const BATTLE_PREPARATION = 'BATTLE_PREPARATION';
const BATTLE_RETREAT = 'BATTLE_RETREAT';
const BATTLE_RETREAT_CHECK_OPTIONS = 'BATTLE_RETREAT_CHECK_OPTIONS';
const BATTLE_ROLLS = 'BATTLE_ROLLS';
const BATTLE_ROLLS_EFFECTS = 'BATTLE_ROLLS_EFFECTS';
const BATTLE_ROLLS_ROLL_DICE = 'BATTLE_ROLLS_ROLL_DICE';
const BATTLE_ROLLS_REROLLS = 'BATTLE_ROLLS_REROLLS';
const BATTLE_ROUT = 'BATTLE_ROUT';
const BATTLE_SELECT_COMMANDER = 'BATTLE_SELECT_COMMANDER';
const COLONIALS_ENLIST_UNIT_PLACEMENT = 'COLONIALS_ENLIST_UNIT_PLACEMENT';
const DRAW_REINFORCEMENTS = 'DRAW_REINFORCEMENTS';
const EVENT_ARMED_BATTOEMEN = 'EVENT_ARMED_BATTOEMEN';
const EVENT_BRITISH_ENCROACHMENT = 'EVENT_BRITISH_ENCROACHMENT';
const EVENT_COUP_DE_MAIN = 'EVENT_COUP_DE_MAIN';
const EVENT_DELAYED_SUPPLIES_FROM_FRANCE = 'EVENT_DELAYED_SUPPLIES_FROM_FRANCE';
const EVENT_DISEASE_IN_BRITISH_CAMP = 'EVENT_DISEASE_IN_BRITISH_CAMP';
const EVENT_DISEASE_IN_FRENCH_CAMP = 'EVENT_DISEASE_IN_FRENCH_CAMP';
const EVENT_FRENCH_LAKE_WARSHIPS = 'EVENT_FRENCH_LAKE_WARSHIPS';
const EVENT_HESITANT_BRITISH_GENERAL = 'EVENT_HESITANT_BRITISH_GENERAL';
const EVENT_PENNSYLVANIAS_PEACE_PROMISES = 'EVENT_PENNSYLVANIAS_PEACE_PROMISES';
const EVENT_ROUND_UP_MEN_AND_EQUIPMENT = 'EVENT_ROUND_UP_MEN_AND_EQUIPMENT';
const EVENT_SMALLPOX_EPIDEMIC = 'EVENT_SMALLPOX_EPIDEMIC';
const EVENT_SMALLPOX_INFECTED_BLANKETS = 'EVENT_SMALLPOX_INFECTED_BLANKETS';
const EVENT_STAGED_LACROSSE_GAME = 'EVENT_STAGED_LACROSSE_GAME';
const EVENT_WILDERNESS_AMBUSH = 'EVENT_WILDERNESS_AMBUSH';
const EVENT_WINTERING_REAR_ADMIRAL = 'EVENT_WINTERING_REAR_ADMIRAL';
const FLEETS_ARRIVE_COMMANDER_DRAW = 'FLEETS_ARRIVE_COMMANDER_DRAW';
const FLEETS_ARRIVE_UNIT_PLACEMENT = 'FLEETS_ARRIVE_UNIT_PLACEMENT';
const LOGISTICS_ROUND_END = 'LOGISTICS_ROUND_END';
const MOVEMENT = 'MOVEMENT';
const MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK = 'MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK';
const MOVEMENT_LOSE_CONTROL_CHECK = 'MOVEMENT_LOSE_CONTROL_CHECK';
const MOVEMENT_OVERWHELM_CHECK = 'MOVEMENT_OVERWHELM_CHECK';
const MOVEMENT_PLACE_SPENT_MARKERS = 'MOVEMENT_PLACE_SPENT_MARKERS';
const MOVE_STACK = 'MOVE_STACK';
const PLACE_MARKER_ON_STACK = 'PLACE_MARKER_ON_STACK';
const SAIL_MOVEMENT = 'SAIL_MOVEMENT';
const VAGARIES_OF_WAR_PICK_UNITS = 'VAGARIES_OF_WAR_PICK_UNITS';
const VAGARIES_OF_WAR_PUT_BACK_IN_POOL = 'VAGARIES_OF_WAR_PUT_BACK_IN_POOL';
const WINTER_QUARTERS_DISBAND_COLONIAL_BRIGADES = 'WINTER_QUARTERS_DISBAND_COLONIAL_BRIGADES';
const WINTER_QUARTERS_MOVE_STACK_ON_SAIL_BOX = 'WINTER_QUARTERS_MOVE_STACK_ON_SAIL_BOX';
const WINTER_QUARTERS_GAME_END_CHECK = 'WINTER_QUARTERS_GAME_END_CHECK';
const WINTER_QUARTERS_ROUND_END = 'WINTER_QUARTERS_ROUND_END';
const WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES = 'WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES';
const WINTER_QUARTERS_REMOVE_MARKERS = 'WINTER_QUARTERS_REMOVE_MARKERS';
const WINTER_QUARTERS_PLACE_INDIAN_UNITS = 'WINTER_QUARTERS_PLACE_INDIAN_UNITS';
const WINTER_QUARTERS_RETURN_FLEETS = 'WINTER_QUARTERS_RETURN_FLEETS';
const WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS = 'WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS';
const WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK = 'WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK';
const WINTER_QUARTERS_RETURN_TO_COLONIES_LEAVE_UNITS = 'WINTER_QUARTERS_RETURN_TO_COLONIES_LEAVE_UNITS';
const WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK = 'WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK';
const WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS = 'WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS';
const WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK = 'WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK';
const USE_EVENT = 'USE_EVENT';

// Actions that can be performed by stack
const CONSTRUCTION = 'CONSTRUCTION';
const MARSHAL_TROOPS = 'MARSHAL_TROOPS';
const RAID_SELECT_TARGET = 'RAID_SELECT_TARGET';
const RAID_MOVE = 'RAID_MOVE';
const RAID_INTERCEPTION = 'RAID_INTERCEPTION';
const RAID_REROLL = 'RAID_REROLL';
const RAID_RESOLUTION = 'RAID_RESOLUTION';

/**
 * Action rounds / steps in year
 */

const ACTION_ROUND_1 = 'action_round_track_ar1';
const ACTION_ROUND_2 = 'action_round_track_ar2';
const ACTION_ROUND_3 = 'action_round_track_ar3';
const ACTION_ROUND_4 = 'action_round_track_ar4';
const ACTION_ROUND_5 = 'action_round_track_ar5';
const ACTION_ROUND_6 = 'action_round_track_ar6';
const ACTION_ROUND_7 = 'action_round_track_ar7';
const ACTION_ROUND_8 = 'action_round_track_ar8';
const ACTION_ROUND_9 = 'action_round_track_ar9';
const FLEETS_ARRIVE = 'action_round_track_fleetsArrive';
const COLONIALS_ENLIST = 'action_round_track_colonialsEnlist';
const WINTER_QUARTERS = 'action_round_track_winterQuarters';

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

const BATTLE_MARKERS_POOL = 'battle_markers_pool';

const OPEN_SEAS_MARKER_SAIL_BOX = 'openSeasMarkerSailBox';

// Die faces
const FLAG = 'flag';
const HIT_TRIANGLE_CIRCLE = 'hit_triangle_circle';
const HIT_SQUARE_CIRCLE = 'hit_square_circle';
const B_AND_T = 'b_and_t';
const MISS = 'miss';

const DIE_FACES = [
  FLAG,
  FLAG,
  HIT_TRIANGLE_CIRCLE,
  HIT_SQUARE_CIRCLE,
  B_AND_T,
  MISS,
];

// unit shapes

const TRIANGLE = 'triangle';
const SQUARE = 'square';
const CIRCLE = 'circle';

/**
 * Action Points
 */
// const ARMY_AP = 'armyActionPoint';
// const ARMY_AP_2X = 'armyActionPoint2x';
// const LIGHT_AP = 'lightActionPoint';
// const LIGHT_AP_2X = 'lightActionPoint2x';
// const INDIAN_AP = 'indianActionPoint';
// const INDIAN_AP_2X = 'indianActionPoint2x';
// const SAIL_ARMY_AP = 'sailArmyActionPoint';
// const SAIL_ARMY_AP_2X = 'sailArmyActionPoint2x';
// const FRENCH_LIGHT_ARMY_AP = 'frenchLightArmyActionPoint';

const ARMY_AP = 'ARMY_AP';
const ARMY_AP_2X = 'ARMY_AP_2X';
const LIGHT_AP = 'LIGHT_AP';
const LIGHT_AP_2X = 'LIGHT_AP_2X';
const INDIAN_AP = 'INDIAN_AP';
const INDIAN_AP_2X = 'INDIAN_AP_2X';
const SAIL_ARMY_AP = 'SAIL_ARMY_AP';
const SAIL_ARMY_AP_2X = 'SAIL_ARMY_AP_2X';
const FRENCH_LIGHT_ARMY_AP = 'FRENCH_LIGHT_ARMY_AP';

/*
 * Units types
 */
const ARTILLERY = 'artillery';
const BASTION_UNIT_TYPE = 'bastion';
const BRIGADE = 'brigade';
const COMMANDER = 'commander';
const FLEET = 'fleet';
const FORT = 'fort';
const LIGHT = 'light';
const VAGARIES_OF_WAR = 'vagariesOfWar';

/**
 * Factions / control
 */
const BRITISH = 'british';
const FRENCH = 'french';
const INDIAN = 'indian';
const NEUTRAL = 'neutral';

/**
 * Tokens / markers
 * TODO: rename these to yearMarker, etc..
 */
const YEAR_MARKER = 'year_marker';
const ROUND_MARKER = 'round_marker';
const VICTORY_MARKER = 'victory_marker';
const OPEN_SEAS_MARKER = 'openSeasMarker';
const FRENCH_RAID_MARKER = 'french_raid_marker';
const BRITISH_RAID_MARKER = 'british_raid_marker';
const FRENCH_BATTLE_MARKER = 'french_battle_marker';
const BRITISH_BATTLE_MARKER = 'british_battle_marker';
// Stack markers
const MARSHAL_TROOPS_MARKER = 'marshalTroopsMarker';
const ROUT_MARKER = 'routMarker';
const OUT_OF_SUPPLY_MARKER = 'outOfSupplyMarker';
const LANDING_MARKER = 'landingMarker';
const BRITISH_MILITIA_MARKER = 'britishMilitiaMarker';
const FRENCH_MILITIA_MARKER = 'frenchMilitiaMarker';

// Construction markers
const FORT_CONSTRUCTION_MARKER = 'fortConstructionMarker';
const ROAD_CONSTRUCTION_MARKER = 'roadConstructionMarker';
const ROAD_MARKER = 'roadMarker';


const NAMED_MARKERS = [
  YEAR_MARKER,
  ROUND_MARKER,
  VICTORY_MARKER,
  OPEN_SEAS_MARKER,
  FRENCH_RAID_MARKER,
  BRITISH_RAID_MARKER,
  FRENCH_BATTLE_MARKER,
  BRITISH_BATTLE_MARKER,
];

const MARKERS = 'markers';
const UNITS = 'units';


// Used during scenario setup
const BRITISH_CONTROL_MARKER = 'britishControlMarker';
const FRENCH_CONTROL_MARKER = 'frenchControlMarker';

/**
 * Pools
 */
const POOL_FLEETS = 'poolFleets';
const POOL_BRITISH_COMMANDERS = 'poolBritishCommanders';
const POOL_BRITISH_LIGHT = 'poolBritishLight';
const POOL_BRITISH_ARTILLERY = 'poolBritishArtillery';
const POOL_BRITISH_FORTS = 'poolBritishForts';
const POOL_BRITISH_METROPOLITAN_VOW = 'poolBritishMetropolitanVoW';
const POOL_BRITISH_COLONIAL_LIGHT = 'poolBritishColonialLight';
const POOL_BRITISH_COLONIAL_VOW = 'poolBritishColonialVoW';
const POOL_BRITISH_COLONIAL_VOW_BONUS = 'poolBritishColonialVoWBonus';

const POOL_FRENCH_COMMANDERS = 'poolFrenchCommanders';
const POOL_FRENCH_LIGHT = 'poolFrenchLight';
const POOL_FRENCH_ARTILLERY = 'poolFrenchArtillery';
const POOL_FRENCH_FORTS = 'poolFrenchForts';
const POOL_FRENCH_METROPOLITAN_VOW = 'poolFrenchMetropolitanVoW';

const POOL_NEUTRAL_INDIANS = 'poolNeutralIndians';

/**
 * Reinforcements
 */
const REINFORCEMENTS_FLEETS = 'reinforcementsFleets';
const REINFORCEMENTS_BRITISH = 'reinforcementsBritish';
const REINFORCEMENTS_FRENCH = 'reinforcementsFrench';
const REINFORCEMENTS_COLONIAL = 'reinforcementsColonial';

const DISBANDED_COLONIAL_BRIGADES = 'disbandedColonialBrigades';

/**
 * Colonies
 */
// British
const NEW_ENGLAND = 'NewEngland';
const NEW_YORK_AND_NEW_JERSEY = 'NewYorkAndNewJersey';
const PENNSYLVANIA_AND_DELAWARE = 'PennsylvaniaAndDelaware';
const VIRGINIA_AND_SOUTH = 'VirginiaAndSouth';
const NOVA_SCOTIA = 'NovaScotia';
// French
const ACADIE = 'Acadie';
const CANADA = 'Canada';
const PAYS_D_EN_HAUT = 'PaysDEnHaut';

const COLONIES = [
  NEW_ENGLAND,
  NEW_YORK_AND_NEW_JERSEY,
  NOVA_SCOTIA,
  PENNSYLVANIA_AND_DELAWARE,
  VIRGINIA_AND_SOUTH
];

const FRENCH_COLONIES = [
  ACADIE,
  CANADA,
  PAYS_D_EN_HAUT
];

/**
 * Spaces
 */
const ALBANY = 'Albany';
const ALEXANDRIA = 'Alexandria';
const ANNAPOLIS_ROYAL = 'AnnapolisRoyal';
const ASSUNEPACHLA = 'Assunepachla';
const BAYE_DE_CATARACOUY = 'BayeDeCataracouy';
const BEVERLEY = 'Beverley';
const BOSTON = 'Boston';
const CAPE_SABLE = 'CapeSable';
const CARLISLE = 'Carlisle';
const CAWICHNOWANE = 'Cawichnowane';
const CHARLES_TOWN = 'CharlesTown';
const CHIGNECTOU = 'Chignectou';
const CHOTE = 'Chote';
const COTE_DE_BEAUPRE = 'CoteDeBeaupre';
const COTE_DU_SUD = 'CoteDuSud';
const DIIOHAGE = 'Diiohage';
const EASTON = 'Easton';
const FORKS_OF_THE_OHIO = 'ForksOfTheOhio';
const FORT_OUIATENON = 'FortOuiatenon';
const GENNISHEYO = 'Gennisheyo';
const GNADENHUTTEN = 'Gnadenhutten';
const GOASEK = 'Goasek';
const GRAND_SAULT = 'GrandSault';
const HALIFAX = 'Halifax';
const ISLE_AUX_NOIX = 'IsleAuxNoix';
const JACQUES_CARTIER = 'JacquesCartier'; // doubles with unit
const KADESQUIT = 'Kadesquit';
const KAHUAHGO = 'Kahuahgo';
const KAHNISTIOH = 'Kahnistioh';
const KENINSHEKA = 'Keninsheka';
const KEOWEE = 'Keowee';
const KINGSTON = 'Kingston';
const KITHANINK = 'Kithanink';
const KWANOSKWAMCOK = 'Kwanoskwamcok';
const LA_PRESQU_ISLE = 'LaPresquIsle';
const LA_PRESENTATION = 'LaPresentation';
const LAKE_GEORGE = 'LakeGeorge';
const LE_BARIL = 'LeBaril';
const LE_DETROIT = 'LeDetroit';
const LES_ILLINOIS = 'LesIllinois';
const LES_TROIS_RIVIERES = 'LesTroisRivieres';
const LOUISBOURG = 'Louisbourg';
const LOUISBOURG_BASTION_1 = 'LouisbourgBastion1';
const LOUISBOURG_BASTION_2 = 'LouisbourgBastion2';
const LOYALHANNA = 'Loyalhanna';
const MAMHLAWBAGOK = 'Mamhlawbagok';
const MATAWASKIYAK = 'Matawaskiyak';
const MATSCHEDASH = 'Matschedash';
const MEKEKASINK = 'Mekekasink';
const MIKAZAWITEGOK = 'Mikazawitegok';
const MINISINK = 'Minisink';
const MIRAMICHY = 'Miramichy';
const MOLOJOAK = 'Molojoak';
const MONTREAL = 'Montreal';
const MOZODEBINEBESEK = 'Mozodebinebesek';
const MTAN = 'Mtan';
const NAMASKONKIK = 'Namaskonkik';
const NEW_LONDON = 'NewLondon';
const NEW_YORK = 'NewYork';
const NEWFOUNDLAND = 'Newfoundland';
const NIAGARA = 'Niagara'; // doubles with unit
const NIHANAWATE = 'Nihanawate';
const NINETY_SIX = 'NinetySix';
const NORTHFIELD = 'Northfield';
const NUMBER_FOUR = 'NumberFour';
const ONEIDA_LAKE = 'OneidaLake';
const ONONTAKE = 'Onontake';
const ONYIUDAONDAGWAT = 'Onyiudaondagwat';
const OQUAGA = 'Oquaga';
const OSWEGO = 'Oswego';
const OUENTIRONK = 'Ouentironk';
const PHILADELPHIA = 'Philadelphia';
const POINTE_SAINTE_ANNE = 'PointeSainteAnne';
const PORT_DAUPHIN = 'PortDauphin';
const PORT_LA_JOYE = 'PortLaJoye';
const QUEBEC = 'Quebec';
const QUEBEC_BASTION_1 = 'QuebecBastion1';
const QUEBEC_BASTION_2 = 'QuebecBastion2';
const RAYS_TOWN = 'RaysTown';
const RIVIERE_DU_LOUP = 'RiviereDuLoup';
const RIVIERE_OUABACHE = 'RiviereOuabache';
const RIVIERE_RISTIGOUCHE = 'RiviereRistigouche';
const RUMFORD = 'Rumford';
const SACHENDAGA = 'Sachendaga';
const SARANAC = 'Saranac';
const SAUGINK = 'Saugink';
const SHAMOKIN = 'Shamokin';
const ST_GEORGE = 'StGeorge';
const TACONNET = 'Taconnet';
const TADOUSSAC = 'Tadoussac';
const TICONDEROGA = 'Ticonderoga'; // Doubles with unit
const TORONTO = 'Toronto';
const TU_ENDIE_WEI = 'TuEndieWei';
const WAABISHKIIGOO_GICHIGAMI = 'WaabishkiigooGichigami';
const WILLS_CREEK = 'WillsCreek';
const WINCHESTER = 'Winchester';
const WOLASTOKUK = 'Wolastokuk';
const YORK = 'York';
const ZAWAKWTEGOK = 'Zawakwtegok';

const BASTIONS = [
  LOUISBOURG_BASTION_1,
  LOUISBOURG_BASTION_2,
  QUEBEC_BASTION_1,
  QUEBEC_BASTION_2,
];

const SPACES = [
  ALBANY,
  ALEXANDRIA,
  ANNAPOLIS_ROYAL,
  ASSUNEPACHLA,
  BAYE_DE_CATARACOUY,
  BEVERLEY,
  BOSTON,
  CAPE_SABLE,
  CARLISLE,
  CAWICHNOWANE,
  CHARLES_TOWN,
  CHIGNECTOU,
  CHOTE,
  COTE_DE_BEAUPRE,
  COTE_DU_SUD,
  DIIOHAGE,
  EASTON,
  FORKS_OF_THE_OHIO,
  FORT_OUIATENON,
  GENNISHEYO,
  GNADENHUTTEN,
  GOASEK,
  GRAND_SAULT,
  HALIFAX,
  ISLE_AUX_NOIX,
  JACQUES_CARTIER,
  KADESQUIT,
  KAHUAHGO,
  KAHNISTIOH,
  KENINSHEKA,
  KEOWEE,
  KINGSTON,
  KITHANINK,
  KWANOSKWAMCOK,
  LA_PRESQU_ISLE,
  LA_PRESENTATION,
  LAKE_GEORGE,
  LE_BARIL,
  LE_DETROIT,
  LES_ILLINOIS,
  LES_TROIS_RIVIERES,
  LOUISBOURG,
  LOUISBOURG_BASTION_1,
  LOUISBOURG_BASTION_2,
  LOYALHANNA,
  MAMHLAWBAGOK,
  MATAWASKIYAK,
  MATSCHEDASH,
  MEKEKASINK,
  MIKAZAWITEGOK,
  MINISINK,
  MIRAMICHY,
  MOLOJOAK,
  MONTREAL,
  MOZODEBINEBESEK,
  MTAN,
  NAMASKONKIK,
  NEW_LONDON,
  NEW_YORK,
  NEWFOUNDLAND,
  NIAGARA,
  NIHANAWATE,
  NINETY_SIX,
  NORTHFIELD,
  NUMBER_FOUR,
  ONEIDA_LAKE,
  ONONTAKE,
  ONYIUDAONDAGWAT,
  OQUAGA,
  OSWEGO,
  OUENTIRONK,
  PHILADELPHIA,
  POINTE_SAINTE_ANNE,
  PORT_DAUPHIN,
  PORT_LA_JOYE,
  QUEBEC,
  QUEBEC_BASTION_1,
  QUEBEC_BASTION_2,
  RAYS_TOWN,
  RIVIERE_DU_LOUP,
  RIVIERE_OUABACHE,
  RIVIERE_RISTIGOUCHE,
  RUMFORD,
  SACHENDAGA,
  SARANAC,
  SAUGINK,
  SHAMOKIN,
  ST_GEORGE,
  TACONNET,
  TADOUSSAC,
  TICONDEROGA,
  TORONTO,
  TU_ENDIE_WEI,
  WAABISHKIIGOO_GICHIGAMI,
  WILLS_CREEK,
  WINCHESTER,
  WOLASTOKUK,
  YORK,
  ZAWAKWTEGOK,
];

/**
 * Sea zones
 */
const ATLANTIC_OCEAN = 'atlanticOcean';
const GULF_OF_SAINT_LAWRENCE = 'gulfOfSaintLawrence';

/**
 * Other locations
 */
const REMOVED_FROM_PLAY = 'removedFromPlay';

/**
 * Log tokens
 */
const LOG_TOKEN_BOLD_TEXT = 'boldText';
const LOG_TOKEN_NEW_LINE = 'newLine';
const LOG_TOKEN_PLAYER_NAME = 'playerName';


/**
 * Dispatch actions
 */

const DISPATCH_TRANSITION = 'dispatchTransition';

/**
 * Counters
 */
// British
const ANNE = 'Anne';
const ARMSTRONG = 'Armstrong';
const AUGUSTA = 'Augusta';
const B_1ST_ROYAL_AMERICAN = 'B1stRoyalAmerican';
const B_2ND_ROYAL_AMERICAN = 'B2ndRoyalAmerican';
const B_15TH_58TH = 'B15th58th';
const B_22ND_28TH = 'B22nd28th';
const B_27TH_55TH = 'B27th55th';
const B_35TH_NEW_YORK_COMPANIES = 'B35thNewYorkCompanies';
const B_40TH_45TH_47TH = 'B40th45th47th';
const B_43RD_46TH = 'B43rd46th';
const B_44TH_48TH = 'B44th48th';
const B_50TH_51ST = 'B50th51st';
const B_61ST_63RD = 'B61st63rd';
const B_94TH_95TH = 'B94th95th';
const BEDFORD = 'Bedford';
const BOSCAWEN = 'Boscawen';
const BRADSTREET = 'Bradstreet';
const CAMPBELL = 'Campbell';
const COLVILL = 'Colvill';
const CROWN_POINT = 'CrownPoint';
const CUMBERLAND = 'Cumberland';
const DUNN = 'Dunn';
const DURELL = 'Durell';
const EDWARD = 'Edward';
const FRASER = 'Fraser';
const FORBES = 'Forbes';
const FREDERICK = 'Frederick';
const GAGE = 'Gage';
const GOREHAM = 'Goreham';
const HARDY = 'Hardy';
const HERKIMER = 'Herkimer';
const HOLBURNE = 'Holburne';
const HOLMES = 'Holmes';
const HOWARDS_BUFFS_KINGS_OWN = 'HowardsBuffsKingsOwn';
const C_HOWE = 'CHowe';
const L_HOWE = 'LHowe';
const JOHNSON = 'Johnson';
const LIGONIER = 'Ligonier';
const MONTGOMERY = 'Montgomery';
const MORGAN = 'Morgan';
// const NEW_ENGLAND = 'NewEngland'; // Doubles with Colony
const NYORK_NJ = 'NYorkNJ';
const ONTARIO = 'Ontario';
const PENN_DEL = 'PennDel';
const PITT = 'Pitt';
const POWNALL = 'Pownall';
const PUTNAM = 'Putnam';
const ROGERS = 'Rogers';
const ROYAL_ARTILLERY = 'RoyalArtillery';
const ROYAL_HIGHLAND = 'RoyalHighland';
const ROYAL_NAVY = 'RoyalNavy';
const ROYAL_SCOTS_17TH = 'RoyalScots17th';
const SCOTT = 'Scott';
const STANWIX = 'Stanwix';
const SAUNDERS = 'Saunders';
// TICONDEROGA: doubles with space 
const VIRGINIA_S = 'VirginiaS';
const WASHINGTON = 'Washington';
const WILLIAM_HENRY = 'WilliamHenry';
const WOLFE = 'Wolfe';
// French
const AUBRY = 'Aubry';
const ANGOUMOIS_BEAUVOISIS = 'AngoumoisBeauvoisis';
const ARTOIS_BOURGOGNE = 'ArtoisBourgogne';
const BASTION = 'Bastion';
const BEARN_GUYENNE = 'BearnGuyenne';
const BERRY = 'Berry';
const BEAUFFREMONT = 'Beauffremont';
const BEAUJEU = 'Beaujeu';
const BEAUSEJOUR = 'Beausejour';
const BELESTRE = 'Belestre';
const BOULONNOIS_ROYAL_BARROIS = 'BoulonnoisRoyalBarrois';
const BOISHEBERT = 'Boishebert';
const CANADIENS = 'Canadiens';
const CANONNIERS_BOMBARDIERS = 'CanonniersBombardiers';
const CARILLON = 'Carillon';
const DE_L_ISLE = 'DeLIsle';
const DE_LA_MOTTE = 'DeLaMotte';
const DE_LA_MARINE = 'DeLaMarine';
const DUQUESNE = 'Duquesne';
const FOIX_QUERCY = 'FoixQuercy';
const FRONTENAC = 'Frontenac';
// JACQUES_CARTIER doubles with space
const LA_SARRE_ROYAL_ROUSSILLON = 'LaSarreRoyalRoussillon';
const LACORNE = 'Lacorne';
const LANGIS = 'Langis';
const LANGLADE = 'Langlade';
const LANGUEDOC_LA_REINE = 'LanguedocLaReine';
const LERY = 'Lery';
const C_LEVIS = 'CLevis'; // Commander
const F_LEVIS = 'FLevis'; // Fort
const LIGNERY = 'Lignery';
const MARINE_ROYALE = 'MarineRoyale';
const MASSIAC = 'Massiac';
const MONTCALM = 'Montcalm';
// NIAGARA doubles with space
const POUCHOT = 'Pouchot';
const RIGAUD = 'Rigaud';
const SAINT_FREDERIC = 'SaintFrederic';
const VILLIERS = 'Villiers';
const VOLONT_ETRANGERS_CAMBIS = 'VolontEtrangersCambis';
// Indian
const ABENAKI = 'Abenaki';
const CHAOUANON = 'Chaouanon';
const CHEROKEE = 'Cherokee';
const BRITISH_CHEROKEE = 'BritishCherokee';
const FRENCH_CHEROKEE = 'FrenchCherokee';
const DELAWARE = 'Delaware';
const IROQUOIS = 'Iroquois';
const BRITISH_IROQUOIS = 'BritishIroquois';
const FRENCH_IROQUOIS = 'FrenchIroquois';
const KAHNAWAKE = 'Kahnawake';
const MALECITE = 'Malecite';
const MICMAC = 'Micmac';
const MINGO = 'Mingo';
const MISSISSAGUE = 'Mississague';
const MOHAWK = 'Mohawk';
const OUTAOUAIS = 'Outaouais';
const SENECA = 'Seneca';

/**
 * Vagaries of War tokens
 */
// French
const VOW_FRENCH_NAVY_LOSSES_PUT_BACK = 'VOWFrenchNavyLossedPutBack';
const VOW_FEWER_TROOPS_FRENCH = 'VOWFewerTroopsFrench';
const VOW_FEWER_TROOPS_PUT_BACK_FRENCH = 'VOWFewerTroopsPutBackFrench';
const VOW_PICK_ONE_ARTILLERY_FRENCH = 'VOWPickOneArtilleryFrench';
// British
const VOW_FEWER_TROOPS_BRITISH = 'VOWFewerTroopsBritish';
const VOW_FEWER_TROOPS_PUT_BACK_BRITISH = 'VOWFewerTroopsPutBackBritish';
const VOW_PICK_TWO_ARTILLERY_BRITISH = 'VOWPickTwoArtilleryBritish';
const VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH = 'VOWPickTwoArtilleryOrLightBritish';
// Colonial
const VOW_PICK_ONE_COLONIAL_LIGHT = 'VOWPickOneColonialLight';
const VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK = 'VOWPickOneColonialLightPutBack';
const VOW_FEWER_TROOPS_COLONIAL = 'VOWFewerTroopsColonial';
const VOW_FEWER_TROOPS_PUT_BACK_COLONIAL = 'VOWFewerTroopsPutBackColonial';
const VOW_PENNSYLVANIA_MUSTERS = 'VOWPennsylvaniaMusters';
const VOW_PITT_SUBSIDIES = 'VOWPittSubsidies';

/**
 * Connection types
 */
const ROAD = 'road';
const PATH = 'path';
const HIGHWAY = 'highway';

/**
 * ConnectionIds
 */
const ALBANY_KINGSTON = 'Albany_Kingston';
const ALBANY_LAKE_GEORGE = 'Albany_LakeGeorge';
const ALBANY_NORTHFIELD = 'Albany_Northfield';
const ALBANY_ONEIDA_LAKE = 'Albany_OneidaLake';
const ALBANY_OQUAGA = 'Albany_Oquaga';
const ALEXANDRIA_PHILADELPHIA = 'Alexandria_Philadelphia';
const ALEXANDRIA_WINCHESTER = 'Alexandria_Winchester';
const ANNAPOLIS_ROYAL_CAPE_SABLE = 'AnnapolisRoyal_CapeSable';
const ANNAPOLIS_ROYAL_CHIGNECTOU = 'AnnapolisRoyal_Chignectou';
const ANNAPOLIS_ROYAL_HALIFAX = 'AnnapolisRoyal_Halifax';
const ASSUNEPACHLA_CAWICHNOWANE = 'Assunepachla_Cawichnowane';
const ASSUNEPACHLA_KITHANINK = 'Assunepachla_Kithanink';
const ASSUNEPACHLA_LOYALHANNA = 'Assunepachla_Loyalhanna';
const ASSUNEPACHLA_RAYS_TOWN = 'Assunepachla_RaysTown';
const BAYE_DE_CATARACOUY_LA_PRESENTATION = 'BayeDeCataracouy_LaPresentation';
const BAYE_DE_CATARACOUY_OSWEGO = 'BayeDeCataracouy_Oswego';
const BAYE_DE_CATARACOUY_TORONTO = 'BayeDeCataracouy_Toronto';
const BEVERLEY_CHOTE = 'Beverley_Chote';
const BEVERLEY_WINCHESTER = 'Beverley_Winchester';
const BOSTON_NEW_LONDON = 'Boston_NewLondon';
const BOSTON_NORTHFIELD = 'Boston_Northfield';
const BOSTON_YORK = 'Boston_York';
const CAPE_SABLE_HALIFAX = 'CapeSable_Halifax';
const CARLISLE_EASTON = 'Carlisle_Easton';
const CARLISLE_PHILADELPHIA = 'Carlisle_Philadelphia';
const CARLISLE_RAYS_TOWN = 'Carlisle_RaysTown';
const CARLISLE_SHAMOKIN = 'Carlisle_Shamokin';
const CARLISLE_WINCHESTER = 'Carlisle_Winchester';
const CAWICHNOWANE_GNADENHUTTEN = 'Cawichnowane_Gnadenhutten';
const CAWICHNOWANE_KAHNISTIOH = 'Cawichnowane_Kahnistioh';
const CAWICHNOWANE_KITHANINK = 'Cawichnowane_Kithanink';
const CAWICHNOWANE_OQUAGA = 'Cawichnowane_Oquaga';
const CAWICHNOWANE_SHAMOKIN = 'Cawichnowane_Shamokin';
const CHARLES_TOWN_NINETY_SIX = 'CharlesTown_NinetySix';
const CHIGNECTOU_HALIFAX = 'Chignectou_Halifax';
const CHIGNECTOU_KWANOSKWAMCOK = 'Chignectou_Kwanoskwamcok';
const CHIGNECTOU_MIRAMICHY = 'Chignectou_Miramichy';
const CHIGNECTOU_POINTE_SAINTE_ANNE = 'Chignectou_PointeSainteAnne';
const CHIGNECTOU_PORT_LA_JOYE = 'Chignectou_PortLaJoye';
const CHOTE_KENINSHEKA = 'Chote_Keninsheka';
const CHOTE_KEOWEE = 'Chote_Keowee';
const COTE_DE_BEAUPRE_COTE_DU_SUD = 'CoteDeBeaupre_CoteDuSud';
const COTE_DE_BEAUPRE_JACQUES_CARTIER = 'CoteDeBeaupre_JacquesCartier';
const COTE_DE_BEAUPRE_QUEBEC = 'CoteDeBeaupre_Quebec';
const COTE_DE_BEAUPRE_TADOUSSAC = 'CoteDeBeaupre_Tadoussac';
const COTE_DU_SUD_RIVIERE_DU_LOUP = 'CoteDuSud_RiviereDuLoup';
const COTE_DU_SUD_QUEBEC = 'CoteDuSud_Quebec';
const COTE_DU_SUD_WOLASTOKUK = 'CoteDuSud_Wolastokuk';
const DIIOHAGE_FORKS_OF_THE_OHIO = 'Diiohage_ForksOfTheOhio';
const DIIOHAGE_LA_PRESQU_ISLE = 'Diiohage_LaPresquIsle';
const DIIOHAGE_LE_BARIL = 'Diiohage_LeBaril';
const DIIOHAGE_LE_DETROIT = 'Diiohage_LeDetroit';
const EASTON_GNADENHUTTEN = 'Easton_Gnadenhutten';
const EASTON_MINISINK = 'Easton_Minisink';
const EASTON_PHILADELPHIA = 'Easton_Philadelphia';
const FORKS_OF_THE_OHIO_KITHANINK = 'ForksOfTheOhio_Kithanink';
const FORKS_OF_THE_OHIO_LOYALHANNA = 'ForksOfTheOhio_Loyalhanna';
const FORKS_OF_THE_OHIO_MEKEKASINK = 'ForksOfTheOhio_Mekekasink';
const FORKS_OF_THE_OHIO_TU_ENDIE_WEI = 'ForksOfTheOhio_TuEndieWei';
const FORT_OUIATENON_LE_DETROIT = 'FortOuiatenon_LeDetroit';
const FORT_OUIATENON_RIVIERE_OUABACHE = 'FortOuiatenon_RiviereOuabache';
const GENNISHEYO_KAHNISTIOH = 'Gennisheyo_Kahnistioh';
const GENNISHEYO_LA_PRESQU_ISLE = 'Gennisheyo_LaPresquIsle';
const GENNISHEYO_NIAGARA = 'Gennisheyo_Niagara';
const GENNISHEYO_ONONTAKE = 'Gennisheyo_Onontake';
const GENNISHEYO_ONYIUDAONDAGWAT = 'Gennisheyo_Onyiudaondagwat';
const GNADENHUTTEN_MINISINK = 'Gnadenhutten_Minisink';
const GNADENHUTTEN_OQUAGA = 'Gnadenhutten_Oquaga';
const GNADENHUTTEN_SHAMOKIN = 'Gnadenhutten_Shamokin';
const GOASEK_MAMHLAWBAGOK = 'Goasek_Mamhlawbagok';
const GOASEK_NUMBER_FOUR = 'Goasek_NumberFour';
const GOASEK_TICONDEROGA = 'Goasek_Ticonderoga';
const GOASEK_ZAWAKWTEGOK = 'Goasek_Zawakwtegok';
const GRAND_SAULT_MATAWASKIYAK = 'GrandSault_Matawaskiyak';
const GRAND_SAULT_MIRAMICHY = 'GrandSault_Miramichy';
const GRAND_SAULT_POINTE_SAINTE_ANNE = 'GrandSault_PointeSainteAnne';
const GRAND_SAULT_WOLASTOKUK = 'GrandSault_Wolastokuk';
const ISLE_AUX_NOIX_LES_TROIS_RIVIERES = 'IsleAuxNoix_LesTroisRivieres';
const ISLE_AUX_NOIX_MAMHLAWBAGOK = 'IsleAuxNoix_Mamhlawbagok';
const ISLE_AUX_NOIX_MONTREAL = 'IsleAuxNoix_Montreal';
const ISLE_AUX_NOIX_SARANAC = 'IsleAuxNoix_Saranac';
const ISLE_AUX_NOIX_TICONDEROGA = 'IsleAuxNoix_Ticonderoga';
const JACQUES_CARTIER_LES_TROIS_RIVIERES = 'JacquesCartier_LesTroisRivieres';
const JACQUES_CARTIER_QUEBEC = 'JacquesCartier_Quebec';
const KADESQUIT_MOZODEBINEBESEK = 'Kadesquit_Mozodebinebesek';
const KADESQUIT_POINTE_SAINTE_ANNE = 'Kadesquit_PointeSainteAnne';
const KADESQUIT_ST_GEORGE = 'Kadesquit_StGeorge';
const KADESQUIT_TACONNET = 'Kadesquit_Taconnet';
const KAHNISTIOH_KITHANINK = 'Kahnistioh_Kithanink';
const KAHUAHGO_LA_PRESENTATION = 'Kahnistioh_LaPresentation';
const KAHUAHGO_NIHANAWATE = 'Kahuahgo_Nihanawate';
const KAHUAHGO_ONEIDA_LAKE = 'Kahuahgo_OneidaLake';
const KAHUAHGO_OSWEGO = 'Kahuahgo_Oswego';
const KENINSHEKA_TU_ENDIE_WEI = 'Keninsheka_TuEndieWei';
const KEOWEE_NINETY_SIX = 'Keowee_NinetySix';
const KINGSTON_MINISINK = 'Kingston_Minisink';
const KINGSTON_NEW_YORK = 'Kingston_NewYork';
const KINGSTON_OQUAGA = 'Kingston_Oquaga';
const KITHANINK_LA_PRESQU_ISLE = 'Kithanink_LaPresquIsle';
const KWANOSKWAMCOK_ST_GEORGE = 'Kwanoskwamcok_StGeorge';
const LAKE_GEORGE_MIKAZAWITEGOK = 'LakeGeorge_Mikazawitegok';
const LAKE_GEORGE_ONEIDA_LAKE = 'LakeGeorge_OneidaLake';
const LAKE_GEORGE_SACHENDAGA = 'LakeGeorge_Sachendaga';
const LAKE_GEORGE_TICONDEROGA = 'LakeGeorge_Ticonderoga';
const LA_PRESENTATION_MONTREAL = 'LaPresentation_Montreal';
const LA_PRESENTATION_NIHANAWATE = 'LaPresentation_Nihanawate';
const LA_PRESQU_ISLE_NIAGARA = 'LaPresquIsle_Niagara';
const LE_BARIL_RIVIERE_OUABACHE = 'LeBaril_RiviereOuabache';
const LE_BARIL_TU_ENDIE_WEI = 'LeBaril_TuEndieWei';
const LE_DETROIT_SAUGINK = 'LeDetroit_Saugink';
const LE_DETROIT_WAABISHKIIGOO_GICHIGAMI = 'LeDetroit_WaabishkiigooGichigami';
const LES_ILLINOIS_RIVIERE_OUABACHE = 'LesIllinois_RiviereOuabache';
const LES_TROIS_RIVIERES_MAMHLAWBAGOK = 'LesTroisRivieres_Mamhlawbagok';
const LES_TROIS_RIVIERES_MONTREAL = 'LesTroisRivieres_Montreal';
const LES_TROIS_RIVIERES_QUEBEC = 'LesTroisRivieres_Quebec';
const LOUISBOURG_PORT_DAUPHIN = 'Louisbourg_PortDauphin';
const LOUISBOURG_PORT_LA_JOYE = 'Louisbourg_PortLaJoye';
const LOYALHANNA_RAYS_TOWN = 'Loyalhanna_RaysTown';
const MAMHLAWBAGOK_NAMASKONKIK = 'Mamhlawbagok_Namaskonkik';
const MATAWASKIYAK_RIVIERE_DU_LOUP = 'Matawaskiyak_RiviereDuLoup';
const MATSCHEDASH_OUENTIRONK = 'Matschedash_Ouentironk';
const MATSCHEDASH_SAUGINK = 'Matschedash_Saugink';
const MEKEKASINK_RAYS_TOWN = 'Mekekasink_RaysTown';
const MEKEKASINK_WILLS_CREEK = 'Mekekasink_WillsCreek';
const MIKAZAWITEGOK_NUMBER_FOUR = 'Mikazawitegok_NumberFour';
const MINISINK_OQUAGA = 'Minisink_Oquaga';
const MIRAMICHY_POINTE_SAINTE_ANNE = 'Miramichy_PointeSainteAnne';
const MIRAMICHY_RIVIERE_RISTIGOUCHE = 'Miramichy_RiviereRistigouche';
const MIRAMICHY_PORT_LA_JOYE = 'Miramichy_PortLaJoye';
const MOLOJOAK_MOZODEBINEBESEK = 'Molojoak_Mozodebinebesek';
const MOLOJOAK_NAMASKONKIK = 'Molojoak_Namaskonkik';
const MOLOJOAK_TACONNET = 'Molojoak_Taconnet';
const MOLOJOAK_ZAWAKWTEGOK = 'Molojoak_Zawakwtegok';
const MOZODEBINEBESEK_WOLASTOKUK = 'Mozodebinebesek_Wolastokuk';
const MTAN_RIVIERE_DU_LOUP = 'Mtan_RiviereDuLoup';
const MTAN_RIVIERE_RISTIGOUCHE = 'Mtan_RiviereRistigouche';
const NAMASKONKIK_QUEBEC = 'Namaskonkik_Quebec';
const NEW_LONDON_NEW_YORK = 'NewLondon_NewYork';
const NEW_LONDON_NORTHFIELD = 'NewLondon_Northfield';
const NEW_YORK_PHILADELPHIA = 'NewYork_Philadelphia';
const NIAGARA_ONYIUDAONDAGWAT = 'Niagara_Onyiudaondagwat';
const NIAGARA_TORONTO = 'Niagara_Toronto';
const NIAGARA_WAABISHKIIGOO_GICHIGAMI = 'Niagara_WaabishkiigooGichigami';
const NIHANAWATE_SACHENDAGA = 'Nihanawate_Sachendaga';
const NIHANAWATE_SARANAC = 'Nihanawate_Saranac';
const NORTHFIELD_NUMBER_FOUR = 'Northfield_NumberFour';
const NORTHFIELD_RUMFORD = 'Northfield_Rumford';
const NUMBER_FOUR_TICONDEROGA = 'NumberFour_Ticonderoga';
const NUMBER_FOUR_ZAWAKWTEGOK = 'NumberFour_Zawakwtegok';
const ONEIDA_LAKE_OQUAGA = 'OneidaLake_Oquaga';
const ONEIDA_LAKE_OSWEGO = 'OneidaLake_Oswego';
const ONONTAKE_OQUAGA = 'Onontake_Oquaga';
const ONONTAKE_OSWEGO = 'Onontake_Oswego';
const ONYIUDAONDAGWAT_OSWEGO = 'Onyiudaondagwat_Oswego';
const OUENTIRONK_TORONTO = 'Ouentironk_Toronto';
const RAYS_TOWN_SHAMOKIN = 'RaysTown_Shamokin';
const RUMFORD_YORK = 'Rumford_York';
const RUMFORD_ZAWAKWTEGOK = 'Rumford_Zawakwtegok';
const SARANAC_TICONDEROGA = 'Saranac_Ticonderoga';
const ST_GEORGE_TACONNET = 'StGeorge_Taconnet';
const ST_GEORGE_YORK = 'StGeorge_York';
const TACONNET_YORK = 'Taconnet_York';
const WILLS_CREEK_WINCHESTER = 'WillsCreek_Winchester';
const YORK_ZAWAKWTEGOK = 'York_Zawakwtegok';

/**
 * Road status
 */
const NO_ROAD = 0;
const ROAD_UNDER_CONTRUCTION = 1;
const HAS_ROAD = 2;

/**
 * Construction options
 */
const PLACE_FORT_CONSTRUCTION_MARKER = 'placeFortConstructionMarker';
const REPLACE_FORT_CONSTRUCTION_MARKER = 'replaceFortConstructionMarker';
const REPAIR_FORT = 'repairFort';
const REMOVE_FORT_CONSTRUCTION_MARKER = 'removeFortConstructionMarkerOrFort';
const REMOVE_FORT = 'removeFort';
const PLACE_ROAD_CONSTRUCTION_MARKER = 'placeRoadConstructionMarker';
const FLIP_ROAD_CONSTRUCTION_MARKER = 'flipRoadConstructionMarker';

/**
 * Battle roll sequence
 */
const NON_INDIAN_LIGHT = 'NON_INDIAN_LIGHT';
// const INDIAN = 'INDIAN'; // Already defined
const HIGHLAND_BRIGADES = 'HIGHLAND_BRIGADES';
const METROPOLITAN_BRIGADES = 'METROPOLITAN_BRIGADES';
const NON_METROPOLITAN_BRIGADES = 'NON_METROPOLITAN_BRIGADES';
const FLEETS = 'FLEETS';
const BASTIONS_OR_FORT = 'BASTIONS_OR_FORT';
// const ARTILLERY = 'ARTILLERY'; // Already defined
const MILITIA = 'MILITIA';

const BATTLE_ROLL_SEQUENCE = [
  NON_INDIAN_LIGHT,
  INDIAN,
  HIGHLAND_BRIGADES,
  METROPOLITAN_BRIGADES,
  NON_METROPOLITAN_BRIGADES,
  FLEETS,
  BASTIONS_OR_FORT,
  ARTILLERY
];


/**
 * Events
 */
const AR_START = 'arStart';
const AR_START_SKIP_MESSAGE = 'arStartSkipMessage';
const ARMED_BATTOEMEN = 'armedBattoemen';
const A_RIGHT_TO_PLUNDER_AND_CAPTIVES = 'aRightToPlunderCaptives';
const A_RIGHT_TO_PLUNDER_AND_CAPTIVES_CARD_ID = 'Card50';
const BRITISH_ENCROACHMENT = 'britishEncroachment';
const CHEROKEE_DIPLOMACY = 'cherokeeDiplomacy';
const CONSTRUCTION_FRENZY = 'constructionFrenzy';
const BRITISH_CONSTRUCTION_FRENZY_CARD_ID = 'Card07';
const FRENCH_CONSTRUCTION_FRENZY_CARD_ID = 'Card28';
const COUP_DE_MAIN = 'coupDeMain';
const COUP_DE_MAIN_CARD_ID = 'Card37';
const DELAYED_SUPPLIES_FROM_FRANCE = 'delayedSuppliesFromFrance';
const DISEASE_IN_BRITISH_CAMP = 'diseaseInBritishCamp';
const DISEASE_IN_FRENCH_CAMP = 'diseaseInFrenchCamp';
const FORCED_MARCH = 'forcedMarch';
const BRITISH_FORCED_MARCH_CARD_ID = 'Card10';
const FRENCH_FORCED_MARCH_CARD_ID = 'Card24';
const FRENCH_LAKE_WARSHIPS = 'frenchLakeWarships';
const FRENCH_TRADE_GOODS_DESTROYED = 'frenchTradeGoodsDestroyed';
const FRONTIERS_ABLAZE = 'frontiersAblaze';
const HESITANT_BRITISH_GENERAL = 'hesitantBritishGeneral';
const INDOMITABLE_ABBATIS = 'indomitableAbbatis';
const INDOMITABLE_ABBATIS_CARD_ID = 'Card41';
const IROQUOIS_DIPLOMACY = 'iroquoisDiplomacy';
const LETS_SEE_HOW_THE_FRENCH_FIGHT = 'letsSeeHowTheFrenchFight';
const LUCKY_CANNONBALL = 'luckyCannonball';
const PENNSYLVANIAS_PEACE_PROMISES = 'pennsylvaniasPeacePromises';
const PERFECT_VOLLEYS = 'perfectVolleys';
const PURSUIT_OF_ELEVATED_STATUS = 'pursuitOfElevatedStatus';
const PURSUIT_OF_ELEVATED_STATUS_CARD_ID = 'Card49';
const RELUCTANT_WAGONEERS = 'reluctantWagoneers';
const ROUGH_SEAS = 'roughSeas';
const ROUGH_SEAS_CARD_ID = 'Card40';
const ROUND_UP_MEN_AND_EQUIPMENT = 'roundUpMenAndEquipment';
const SMALLPOX_EPIDEMIC = 'smallpoxEpidemic';
const SMALLPOX_INFECTED_BLANKETS = 'smallpoxInfectedBlankets';
const STAGED_LACROSSE_GAME = 'stagedLacrosseGame';
const STAGED_LACROSSE_GAME_CARD_ID = 'Card46';
const SURPRISE_LANDING = 'surpriseLanding';
const WILDERNESS_AMBUSH = 'wildernessAmbush';
const WILDERNESS_AMBUSH_CARD_ID = 'Card32';
const WINTERING_REAR_ADMIRAL = 'winteringRearAdmiral';


const SAIL_BOX = 'sailBox';

/**
 * Battle
 */
const FACTION_BATTLE_MARKER_MAP = [
  BRITISH => BRITISH_BATTLE_MARKER,
  FRENCH => FRENCH_BATTLE_MARKER
];
