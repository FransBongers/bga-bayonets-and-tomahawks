<?php
require_once 'gameoptions.inc.php';
/**
 * State ids / names
 */

const ST_GAME_SETUP = 1;
const ST_GAME_SETUP_NAME = 'gameSetup';
const ST_CHANGE_ACTIVE_PLAYER = 95;
const ST_CHANGE_ACTIVE_PLAYER_NAME = 'changeActivePlayer';
const ST_END_GAME = 99;
const ST_END_GAME_NAME = 'gameEnd';

/*
 * Units types
 */
const LIGHT = 'light';

/**
 * Units classes
 */
const LANGIS = 'langis';

/**
 * Factions / control
 */
const BRITISH = 'british';
const FRENCH = 'french';
const INDIAN = 'indian';
const NEUTRAL = 'neutral';

/**
 * Spaces
 */
const ANNAPOLIS_ROYAL = 'AnnapolisRoyal';
const CAPE_SABLE = 'CapeSable';
const CHIGNECTOU = 'Chignectou';
const COTE_DE_BEAUPRE = 'CoteDeBeaupre';
const COTE_DU_SUD = 'CoteDuSud';
const GOASEK = 'Goasek';
const GRAND_SAULT = 'GrandSault';
const HALIFAX = 'Halifax';
const ISLE_AUX_NOIX = 'IsleAuxNoix';
const JACQUES_CARTIER = 'JacquesCartier';
const KADESQUIT = 'Kadesquit';
const KWANOSKWANCOK = 'Kwanoskwamcok';
const LES_TROIS_RIVIERES = 'LesTroisRivieres';
const LOUISBOURG = 'Louisbourg';
const MAMHLAWBAGOK = 'Mamhlawbagok';
const MATAWASKIYAK = 'Matawaskiyak';
const MIRAMICHY = 'Miramichy';
const MOLOJOAK = 'Molojoak';
const MONTREAL = 'Montreal';
const MOZODEBINEBESEK = 'Mozodebinebesek';
const MTAN = 'Mtan';
const NAMASKONKIK = 'Namaskonkik';
const NEWFOUNDLAND = 'Newfoundland';
const NUMBER_FOUR = 'NumberFour';
const POINTE_SAINTE_ANNE = 'PointeSainteAnne';
const PORT_DAUPHIN = 'PortDauphin';
const PORT_LA_JOYE = 'PortLaJoye';
const QUEBEC = 'Quebec';
const RIVIERE_DU_LOUP = 'RiviereDuLoup';
const RIVIERE_RISTIGOUCHE = 'RiviereRistigouche';
const RUMFORD = 'Rumford';
const ST_GEORGE = 'StGeorge';
const TACONNET = 'Taconnet';
const TADOUSSAC = 'Tadoussac';
const WOLASTOKUK = 'Wolastokuk';
const YORK = 'York';
const ZAWAKWTEGOK = 'Zawakwtegok';

const SPACES = [
  ANNAPOLIS_ROYAL,
  CAPE_SABLE,
  CHIGNECTOU,
  COTE_DE_BEAUPRE,
  COTE_DU_SUD,
  GOASEK,
  GRAND_SAULT,
  HALIFAX,
  ISLE_AUX_NOIX,
  JACQUES_CARTIER,
  KADESQUIT,
  KWANOSKWANCOK,
  LES_TROIS_RIVIERES,
  LOUISBOURG,
  MATAWASKIYAK,
  MIRAMICHY,
  MOLOJOAK,
  MONTREAL,
  MOZODEBINEBESEK,
  MTAN,
  NAMASKONKIK,
  NEWFOUNDLAND,
  NUMBER_FOUR,
  POINTE_SAINTE_ANNE,
  PORT_DAUPHIN,
  PORT_LA_JOYE,
  QUEBEC,
  RIVIERE_DU_LOUP,
  RIVIERE_RISTIGOUCHE,
  RUMFORD,
  TACONNET,
  TADOUSSAC,
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
 * Unit to class name map
 */
const UNIT_CLASSES = [
  LANGIS => 'Langis',
];