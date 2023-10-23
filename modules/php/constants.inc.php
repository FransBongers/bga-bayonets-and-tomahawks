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
 * Factions
 */
const FRENCH = 'french';

/**
 * Spaces
 */
const CHIGNECTOU = 'Chignectou';
const LOUISBOURG = 'Louisbourg';

const SPACES = [
  CHIGNECTOU,
  LOUISBOURG
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