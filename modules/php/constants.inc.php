<?php
require_once 'gameoptions.inc.php';

/**
 * STATS
 */

const STAT_TURN = 12;

/**
 * Carc locations
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
const ST_ACTION_ROUND_CHOOSE_CARD = 23;
const ST_ACTION_ROUND_CHOOSE_FIRST_PLAYER = 24;
const ST_ACTION_ROUND_ACTION_PHASE = 25;
const ST_ACTION_ROUND_RESOLVE_BATTLES = 26;
const ST_ACTION_ROUND_END = 27;
const ST_ACTION_ROUND_SAIL_BOX_LANDING = 28;
const ST_ACTION_ROUND_CHOOSE_REACTION = 29;
const ST_FLEETS_ARRIVE_DRAW_REINFORCEMENTS = 30;
const ST_COLONIALS_ENLIST_DRAW_REINFORCEMENTS = 40;
const ST_WINTER_QUARTERS_GAME_END_CHECK = 50;
const ST_ACTION_ACTIVATE_STACK = 60;
const ST_MOVEMENT_SELECT_DESTINATION_AND_UNITS = 61;

/**
 * Scenario Ids
 */
const VaudreuilsPetiteGuerre1755 = 'VaudreuilsPetiteGuerre1755';
const LoudounsGamble1757 = 'LoudounsGamble1757';

/**
 * Atomic actions
 */
const PLAYER_ACTION = 'PLAYER_ACTION';
const SELECT_RESERVE_CARD = 'SELECT_RESERVE_CARD';
const ACTION_ACTIVATE_STACK = 'ACTION_ACTIVATE_STACK';
const ACTION_ROUND_CHOOSE_CARD = 'ACTION_ROUND_CHOOSE_CARD';
const ACTION_ROUND_CHOOSE_FIRST_PLAYER = 'ACTION_ROUND_CHOOSE_FIRST_PLAYER';
const ACTION_ROUND_FIRST_PLAYER_ACTIONS = 'ACTION_ROUND_FIRST_PLAYER_ACTIONS';
const ACTION_ROUND_SECOND_PLAYER_ACTIONS = 'ACTION_ROUND_SECOND_PLAYER_ACTIONS';
const ACTION_ROUND_INDIAN_ACTIONS = 'ACTION_ROUND_INDIAN_ACTIONS';
const ACTION_ROUND_REACTION = 'ACTION_ROUND_REACTION';
const ACTION_ROUND_RESOLVE_BATTLES = 'ACTION_ROUND_RESOLVE_BATTLES';
const ACTION_ROUND_END = 'ACTION_ROUND_END';
const ACTION_ROUND_SAIL_BOX_LANDING = 'ACTION_ROUND_SAIL_BOX_LANDING';
const ACTION_ROUND_CHOOSE_REACTION = 'ACTION_ROUND_CHOOSE_REACTION';
const COLONIALS_ENLIST_DRAW_REINFORCEMENTS = 'COLONIALS_ENLIST_DRAW_REINFORCEMENTS';
const FLEETS_ARRIVE_DRAW_REINFORCEMENTS = 'FLEETS_ARRIVE_DRAW_REINFORCEMENTS';
const FLEETS_ARRIVE_ACTION = 'FLEETS_ARRIVE_ACTION';
const WINTER_QUARTERS_GAME_END_CHECK = 'WINTER_QUARTERS_GAME_END_CHECK';
const MOVEMENT_SELECT_DESTINATION_AND_UNITS = 'MOVEMENT_SELECT_DESTINATION_AND_UNITS';

/**
 * Action rounds / steps in year
 */

const ACTION_ROUND_1 = 'ar1';
const ACTION_ROUND_2 = 'ar2';
const ACTION_ROUND_3 = 'ar3';
const ACTION_ROUND_4 = 'ar4';
const ACTION_ROUND_5 = 'ar5';
const ACTION_ROUND_6 = 'ar6';
const ACTION_ROUND_7 = 'ar7';
const ACTION_ROUND_8 = 'ar8';
const ACTION_ROUND_9 = 'ar9';
const FLEETS_ARRIVE = 'fleetsArrive';
const COLONIALS_ENLIST = 'colonialsEnlist';
const WINTER_QUARTERS = 'winterQuarters';

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

/**
 * Actions that can be performed by a stack
 */
const ARMY_MOVEMENT = 'ARMY_MOVEMENT';
const CONSTRUCTION = 'CONSTRUCTION';
const LIGHT_MOVEMENT = 'LIGHT_MOVEMENT';
const MARSHAL_TROOPS = 'MARSHAL_TROOPS';
const RAID = 'RAID';
const SAIL_MOVEMENT = 'SAIL_MOVEMENT';

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

/**
 * Factions / control
 */
const BRITISH = 'british';
const FRENCH = 'french';
const INDIAN = 'indian';
const NEUTRAL = 'neutral';

/**
 * Tokens / markers
 */
const YEAR_MARKER = 'year_marker';
const ROUND_MARKER = 'round_marker';
const VICTORY_MARKER = 'victory_marker';
const OPEN_SEAS_MARKER = 'open_seas_marker';
const FRENCH_RAID_MARKER = 'french_raid_marker';
const BRITISH_RAID_MARKER = 'british_raid_marker';

/**
 * Pools
 */
const POOL_FLEETS = 'poolFleets';
const POOL_BRITISH_COMMANDERS = 'poolBritishCommanders';
const POOL_BRITISH_LIGHT = 'poolBritishLight';
const POOL_BRITISH_ARTILLERY = 'poolBritishArtillery';
const POOL_BRITISH_FORTS = 'poolBritishForts';
const POOL_BRITISH_METROPOLITAN_VOW = 'poolBritishMetropolitanVoW';
const POOL_BRITISH_COLONIAL_VOW = 'poolBritishColonialVoW';

const POOL_FRENCH_COMMANDERS = 'poolFrenchCommanders';
const POOL_FRENCH_LIGHT = 'poolFrenchLight';
const POOL_FRENCH_ARTILLERY = 'poolFrenchArtillery';
const POOL_FRENCH_FORTS = 'poolFrenchForts';
const POOL_FRENCH_METROPOLITAN_VOW = 'poolFrenchMetropolitanVoW';

const POOL_NEUTRAL_INDIANS = 'poolNeutralIndians';
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
const NEW_ENGLAND = 'NewEngland';
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
const DELAWARE = 'Delaware';
const IROQUOIS = 'Iroquois';
const KAHNAWAKE = 'Kahnawake';
const MALECITE = 'Malecite';
const MICMAC = 'Micmac';
const MINGO = 'Mingo';
const MISSISSAGUE = 'Mississague';
const MOHAWK = 'Mohawk';
const OUTAOUAIS = 'Outaouais';
const SENECA = 'Seneca';

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