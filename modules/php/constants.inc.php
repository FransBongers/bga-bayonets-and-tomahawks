<?php
require_once 'gameoptions.inc.php';

/**
 * STATS
 */

const STAT_TURN = 12;

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
const ST_SELECT_RESERVE_CARD = 20;
const ST_PLAYER_ACTION = 21;


/**
 * Atomic actions
 */
const PLAYER_ACTION = 'PLAYER_ACTION';
const SELECT_RESERVE_CARD = 'SELECT_RESERVE_CARD';

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
const KANISTIOH = 'Kanistioh';
const KENINSHEKA = 'Keninsheka';
const KEOWEE = 'Keowee';
const KINGSTON = 'Kingston';
const KITHANINK = 'Kithanink';
const KWANOSKWANCOK = 'Kwanoskwamcok';
const LA_PRESQU_ISLE = 'LaPresquIsle';
const LA_PRESENTATION = 'LaPresentation';
const LAKE_GEORGE = 'LakeGeorge';
const LE_BARIL = 'LeBaril';
const LE_DETROIT = 'LeDetroit';
const LES_ILLINOIS = 'LesIllinois';
const LES_TROIS_RIVIERES = 'LesTroisRivieres';
const LOUISBOURG = 'Louisbourg';
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
  KANISTIOH,
  KENINSHEKA,
  KEOWEE,
  KINGSTON,
  KITHANINK,
  KWANOSKWANCOK,
  LA_PRESQU_ISLE,
  LA_PRESENTATION,
  LAKE_GEORGE,
  LE_BARIL,
  LE_DETROIT,
  LES_ILLINOIS,
  LES_TROIS_RIVIERES,
  LOUISBOURG,
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
