<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * bayonetsandtomahawks implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * gameoptions.inc.php
 *
 * bayonetsandtomahawks game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in bayonetsandtomahawks.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

namespace BayonetsAndTomahawks;

require_once 'modules/php/gameoptions.inc.php';

$game_options = [
  OPTION_SCENARIO => [
    'name' => totranslate('Scenario'),
    'values' => [
      OPTION_SCENARIO_1 => [
        'name' => totranslate("Vaudreil's Petite Guerre 1755"),
        'tmdisplay' => totranslate("Vaudreil's Petite Guerre 1755"),
      ],
      OPTION_SCENARIO_2 => [
        'name' => totranslate("Loudoun's Gamble 1757"),
        'tmdisplay' => totranslate("Loudoun's Gamble 1757"),
      ]
    ],
  ]
];

$game_preferences = [];