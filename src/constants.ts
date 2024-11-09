const MIN_PLAY_AREA_WIDTH = 1500; // Is this still used?
const MIN_NOTIFICATION_MS = 1000;

const ENABLED = 'enabled';
/**
 * Class names
 */
const DISABLED = 'disabled';
const BT_SELECTABLE = 'bt_selectable';
const BT_SELECTED = 'bt_selected';

/**
 * Card locations
 */
const DISCARD = 'discard';

/**
 * Operations on Action points
 */
const REMOVE_AP = 'REMOVE_AP';
const ADD_AP = 'ADD_AP';

/**
 * Setting ids
 */
// const CARD_SIZE_IN_LOG = 'cardSizeInLog';
// const CARD_INFO_IN_TOOLTIP = 'cardInfoInTooltip';
const PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY =
  'confirmEndOfTurnPlayerSwitchOnly';
const PREF_SHOW_ANIMATIONS = 'showAnimations';
const PREF_ANIMATION_SPEED = 'animationSpeed';
const PREF_CARD_INFO_IN_TOOLTIP = 'cardInfoInTooltip';
const PREF_CARD_SIZE_IN_LOG = 'cardSizeInLog';
const PREF_DISABLED = 'disabled';
const PREF_ENABLED = 'enabled';
const PREF_SINGLE_COLUMN_MAP_SIZE = 'singleColumnMapSize';

/**
 * Factions / control
 */
const BRITISH = 'british';
const FRENCH = 'french';
const INDIAN = 'indian';
const NEUTRAL = 'neutral';
const FACTIONS: Faction[] = [BRITISH, FRENCH, INDIAN];

/*
 * Units types
 */
const ARTILLERY = 'artillery';
const BASTION_UNIT_TYPE = 'bastion';
// const BASTION = 'Bastion';
const BRIGADE = 'brigade';
const COMMANDER = 'commander';
const FLEET = 'fleet';
const FORT = 'fort';
const LIGHT = 'light';
const VAGARIES_OF_WAR = 'vagariesOfWar';

const REMOVED_FROM_PLAY = 'removedFromPlay';

/**
 * Connection types
 */
const ROAD = 'road';
const PATH = 'path';
const HIGHWAY = 'highway';

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

const POOLS = [
  POOL_FLEETS,
  POOL_BRITISH_COMMANDERS,
  POOL_BRITISH_LIGHT,
  POOL_BRITISH_COLONIAL_LIGHT,
  POOL_BRITISH_ARTILLERY,
  POOL_BRITISH_FORTS,
  POOL_BRITISH_METROPOLITAN_VOW,
  POOL_BRITISH_COLONIAL_VOW,
  POOL_BRITISH_COLONIAL_VOW_BONUS,
  POOL_FRENCH_COMMANDERS,
  POOL_FRENCH_LIGHT,
  POOL_FRENCH_ARTILLERY,
  POOL_FRENCH_FORTS,
  POOL_FRENCH_METROPOLITAN_VOW,
  POOL_NEUTRAL_INDIANS,
  // Drawn reinforcements
  REINFORCEMENTS_FLEETS,
  REINFORCEMENTS_BRITISH,
  REINFORCEMENTS_FRENCH,
  REINFORCEMENTS_COLONIAL,
];

/**
 * Tokens / markers
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
const LANDING_MARKER = 'landingMarker';
const MARSHAL_TROOPS_MARKER = 'marshalTroopsMarker';
const OUT_OF_SUPPLY_MARKER = 'outOfSupplyMarker';
const ROUT_MARKER = 'routMarker';
const BRITISH_MILITIA_MARKER = 'britishMilitiaMarker';
const FRENCH_MILITIA_MARKER = 'frenchMilitiaMarker';

const STACK_MARKERS = [
  LANDING_MARKER,
  MARSHAL_TROOPS_MARKER,
  OUT_OF_SUPPLY_MARKER,
  ROUT_MARKER,
  BRITISH_MILITIA_MARKER,
  FRENCH_MILITIA_MARKER,
];

const FORT_CONSTRUCTION_MARKER = 'fortConstructionMarker';
const ROAD_CONSTRUCTION_MARKER = 'roadConstructionMarker';
const ROAD_MARKER = 'roadMarker';

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

// Battle track
const BATTLE_TRACK_ATTACKER_MINUS_5 = 'battle_track_attacker_minus_5';
const BATTLE_TRACK_ATTACKER_MINUS_4 = 'battle_track_attacker_minus_4';
const BATTLE_TRACK_ATTACKER_MINUS_3 = 'battle_track_attacker_minus_3';
const BATTLE_TRACK_ATTACKER_MINUS_2 = 'battle_track_attacker_minus_2';
const BATTLE_TRACK_ATTACKER_MINUS_1 = 'battle_track_attacker_minus_1';
const BATTLE_TRACK_ATTACKER_PLUS_0 = 'battle_track_attacker_plus_0';
const BATTLE_TRACK_ATTACKER_PLUS_1 = 'battle_track_attacker_plus_1';
const BATTLE_TRACK_ATTACKER_PLUS_2 = 'battle_track_attacker_plus_2';
const BATTLE_TRACK_ATTACKER_PLUS_3 = 'battle_track_attacker_plus_3';
const BATTLE_TRACK_ATTACKER_PLUS_4 = 'battle_track_attacker_plus_4';
const BATTLE_TRACK_ATTACKER_PLUS_5 = 'battle_track_attacker_plus_5';
const BATTLE_TRACK_ATTACKER_PLUS_6 = 'battle_track_attacker_plus_6';
const BATTLE_TRACK_ATTACKER_PLUS_7 = 'battle_track_attacker_plus_7';
const BATTLE_TRACK_ATTACKER_PLUS_8 = 'battle_track_attacker_plus_8';
const BATTLE_TRACK_ATTACKER_PLUS_9 = 'battle_track_attacker_plus_9';
const BATTLE_TRACK_ATTACKER_PLUS_10 = 'battle_track_attacker_plus_10';

const BATTLE_TRACK_DEFENDER_MINUS_5 = 'battle_track_defender_minus_5';
const BATTLE_TRACK_DEFENDER_MINUS_4 = 'battle_track_defender_minus_4';
const BATTLE_TRACK_DEFENDER_MINUS_3 = 'battle_track_defender_minus_3';
const BATTLE_TRACK_DEFENDER_MINUS_2 = 'battle_track_defender_minus_2';
const BATTLE_TRACK_DEFENDER_MINUS_1 = 'battle_track_defender_minus_1';
const BATTLE_TRACK_DEFENDER_PLUS_0 = 'battle_track_defender_plus_0';
const BATTLE_TRACK_DEFENDER_PLUS_1 = 'battle_track_defender_plus_1';
const BATTLE_TRACK_DEFENDER_PLUS_2 = 'battle_track_defender_plus_2';
const BATTLE_TRACK_DEFENDER_PLUS_3 = 'battle_track_defender_plus_3';
const BATTLE_TRACK_DEFENDER_PLUS_4 = 'battle_track_defender_plus_4';
const BATTLE_TRACK_DEFENDER_PLUS_5 = 'battle_track_defender_plus_5';
const BATTLE_TRACK_DEFENDER_PLUS_6 = 'battle_track_defender_plus_6';
const BATTLE_TRACK_DEFENDER_PLUS_7 = 'battle_track_defender_plus_7';
const BATTLE_TRACK_DEFENDER_PLUS_8 = 'battle_track_defender_plus_8';
const BATTLE_TRACK_DEFENDER_PLUS_9 = 'battle_track_defender_plus_9';
const BATTLE_TRACK_DEFENDER_PLUS_10 = 'battle_track_defender_plus_10';

const BATTLE_MARKERS_POOL = 'battle_markers_pool';

const COMMANDER_REROLLS_TRACK_ATTACKER_0 = 'commander_rerolls_track_attacker_0';
const COMMANDER_REROLLS_TRACK_ATTACKER_1 = 'commander_rerolls_track_attacker_1';
const COMMANDER_REROLLS_TRACK_ATTACKER_2 = 'commander_rerolls_track_attacker_2';
const COMMANDER_REROLLS_TRACK_ATTACKER_3 = 'commander_rerolls_track_attacker_3';

const COMMANDER_REROLLS_TRACK_DEFENDER_0 = 'commander_rerolls_track_defender_0';
const COMMANDER_REROLLS_TRACK_DEFENDER_1 = 'commander_rerolls_track_defender_1';
const COMMANDER_REROLLS_TRACK_DEFENDER_2 = 'commander_rerolls_track_defender_2';
const COMMANDER_REROLLS_TRACK_DEFENDER_3 = 'commander_rerolls_track_defender_3';

const OPEN_SEAS_MARKER_SAIL_BOX = 'openSeasMarkerSailBox';
const CHEROKEE_CONTROL = 'cherokeeControl';
const IROQUOIS_CONTROL = 'iroquoisControl';

const ATTACKER = 'attacker';
const DEFENDER = 'defender';
const COMMANDER_IN_PLAY = 'commanderInPlay';

const BATTLE_SIDES: Array<'attacker' | 'defender'> = [
  ATTACKER,
  DEFENDER,
];

// const CHEROKEE = 'Cherokee';
// const IROQUOIS = 'Iroquois';

// Losses Boxes
const LOSSES_BOX_BRITISH = 'lossesBox_british';
const LOSSES_BOX_FRENCH = 'lossesBox_french';
const DISBANDED_COLONIAL_BRIGADES = 'disbandedColonialBrigades';
const SAIL_BOX = 'sailBox';

const MARKERS = 'markers';
const UNITS = 'units';

const BOXES = [
  DISBANDED_COLONIAL_BRIGADES,
  LOSSES_BOX_BRITISH,
  LOSSES_BOX_FRENCH,
];

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
const VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH =
  'VOWPickTwoArtilleryOrLightBritish';
// Colonial
const VOW_PICK_ONE_COLONIAL_LIGHT = 'VOWPickOneColonialLight';
const VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK = 'VOWPickOneColonialLightPutBack';
const VOW_FEWER_TROOPS_COLONIAL = 'VOWFewerTroopsColonial';
const VOW_FEWER_TROOPS_PUT_BACK_COLONIAL = 'VOWFewerTroopsPutBackColonial';
const VOW_PENNSYLVANIA_MUSTERS = 'VOWPennsylvaniaMusters';
const VOW_PITT_SUBSIDIES = 'VOWPittSubsidies';

/**
 * Actions
 */
const ACTION_ROUND_INDIAN_ACTIONS = 'ACTION_ROUND_INDIAN_ACTIONS';

/**
 * Road status
 */
const NO_ROAD = 0;
const ROAD_UNDER_CONTRUCTION = 1;
const HAS_ROAD = 2;

/**
 * Raid
 */
const RAID_INTERCEPTION = 'RAID_INTERCEPTION';
const RAID_RESOLUTION = 'RAID_RESOLUTION';

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


// Die faces
const FLAG = 'flag';
const HIT_TRIANGLE_CIRCLE = 'hit_triangle_circle';
const HIT_SQUARE_CIRCLE = 'hit_square_circle';
const B_AND_T = 'b_and_t';
const MISS = 'miss';

/**
 * Action Points
 */
const ARMY_AP = 'ARMY_AP';
const ARMY_AP_2X = 'ARMY_AP_2X';
const LIGHT_AP = 'LIGHT_AP';
const LIGHT_AP_2X = 'LIGHT_AP_2X';
const INDIAN_AP = 'INDIAN_AP';
const INDIAN_AP_2X = 'INDIAN_AP_2X';
const SAIL_ARMY_AP = 'SAIL_ARMY_AP';
const SAIL_ARMY_AP_2X = 'SAIL_ARMY_AP_2X';
const FRENCH_LIGHT_ARMY_AP = 'FRENCH_LIGHT_ARMY_AP';

/**
 * Events
 */
const AR_START = 'arStart';
const ARMED_BATTOEMEN = 'armedBattoemen';
const A_RIGHT_TO_PLUNDER_AND_CAPTIVES = 'aRightToPlunderCaptives';
const BRITISH_ENCROACHMENT = 'britishEncroachment';
const CHEROKEE_DIPLOMACY = 'cherokeeDiplomacy';
const CONSTRUCTION_FRENZY = 'constructionFrenzy';
const COUP_DE_MAIN = 'coupDeMain';
const DELAYED_SUPPLIES_FROM_FRANCE = 'delayedSuppliesFromFrance';
const DISEASE_IN_BRITISH_CAMP = 'diseaseInBritishCamp';
const DISEASE_IN_FRENCH_CAMP = 'diseaseInFrenchCamp';
const FORCED_MARCH = 'forcedMarch';
const FRENCH_LAKE_WARSHIPS = 'frenchLakeWarships';
const FRENCH_TRADE_GOODS_DESTROYED = 'frenchTradeGoodsDestroyed';
const FRONTIERS_ABLAZE = 'frontiersAblaze';
const HESITANT_BRITISH_GENERAL = 'hesitantBritishGeneral';
const INDOMITABLE_ABBATIS = 'indomitableAbbatis';
const IROQUOIS_DIPLOMACY = 'iroquoisDiplomacy';
const LETS_SEE_HOW_THE_FRENCH_FIGHT = 'letsSeeHowTheFrenchFight';
const LUCKY_CANNONBALL = 'luckyCannonball';
const PENNSYLVANIAS_PEACE_PROMISES = 'pennsylvaniasPeacePromises';
const PERFECT_VOLLEYS = 'perfectVolleys';
const PURSUIT_OF_ELEVATED_STATUS = 'pursuitOfElevatedStatus';
const RELUCTANT_WAGONEERS = 'reluctantWagoneers';
const ROUGH_SEAS = 'roughSeas';
const ROUND_UP_MEN_AND_EQUIPMENT = 'roundUpMenAndEquipment';
const SMALLPOX_EPIDEMIC = 'smallpoxEpidemic';
const SMALLPOX_INFECTED_BLANKETS = 'smallpoxInfectedBlankets';
const STAGED_LACROSSE_GAME = 'stagedLacrosseGame';
const SURPRISE_LANDING = 'surpriseLanding';
const WILDERNESS_AMBUSH = 'wildernessAmbush';
const WINTERING_REAR_ADMIRAL = 'winteringRearAdmiral';

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

const NEW_ENGLAND = 'NewEngland';

/**
 * Action and Logistics rounds
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

const LOGISTICS_ROUNDS = [
  FLEETS_ARRIVE, COLONIALS_ENLIST, WINTER_QUARTERS
];

/**
 * Steps in Action and Logistics round
 */
const SELECT_RESERVE_CARD_STEP = 'selectReserveCardStep';
const SELECT_CARD_TO_PLAY_STEP = 'selectCardToPlayStep';
const SELECT_FIRST_PLAYER_STEP = 'selectFirstPlayerStep';
const RESOLVE_AR_START_EVENTS_STEP = 'resolveARStartEventsStep';
const FIRST_PLAYER_ACTIONS_STEP = 'firstPlayerActionsStep';
const SECOND_PLAYER_ACTIONS_STEP = 'secondPlayerActionsStep';
const FIRST_PLAYER_REACTION_STEP = 'firstPlayerReactionStep';
const RESOLVE_BATTLES_STEP = 'resolveBattlesStep';
const END_OF_AR_STEPS = 'endOfARSteps';

// Fleets Arrive
const DRAW_FLEETS_STEP = 'drawFleetsStep';
const DRAW_BRITISH_UNITS_STEP = 'drawBritishUnitsStep';
const DRAW_FRENCH_UNITS_STEP = 'drawFrenchUnitsStep';
const PLACE_BRITISH_UNITS_STEP = 'placeBritishUnitsStep';
const PLACE_FRENCH_UNITS_STEP = 'placeFrenchUnitsStep';

// Colonials Enlist
const DRAW_COLONIAL_REINFORCEMENTS_STEP = 'drawColonialReinforcementsStep';
const PLACE_COLONIAL_UNITS_STEP = 'placeColonialUnitsStep';

// Winter Quarters
const PERFORM_VICTORY_CHECK_STEP = 'performVictoryCheckStep';
const REMOVE_MARKERS_STEP = 'removeMarkersStep';
const MOVE_STACKS_ON_SAIL_BOX_STEP = 'moveStacksOnSailBoxStep';
const PLACE_INDIAN_UNITS_STEP = 'placeIndianUnitsStep';
const MOVE_COLONIAL_BRIGADES_TO_DISBANDED_STEP = 'moveColonialBrigadesToDisbandedStep';
const RETURN_TO_COLONIES_STEP = 'returnToColoniesStep';
const RETURN_FLEETS_TO_FLEET_POOL_STEP = 'returnFleetsToFleetPoolStep';
const PLACE_UNITS_FROM_LOSSES_BOX_STEP = 'placeUnitsFromLossesBoxStep';
const END_OF_YEAR_STEP = 'endOfYearStep';