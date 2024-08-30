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
 * states.inc.php
 *
 * bayonetsandtomahawks game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

require_once 'modules/php/constants.inc.php';


$machinestates = array(

    // The initial state. Please do not modify.
    ST_GAME_SETUP => [
        "name" => ST_GAME_SETUP_NAME,
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["" => ST_SETUP_YEAR]
    ],

    ST_GENERIC_NEXT_PLAYER => [
        'name' => 'genericNextPlayer',
        'type' => 'game',
    ],
    // Note: ID=2 => your first state

    2 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must end the game or pass'),
        "descriptionmyturn" => clienttranslate('${you} must end the game or pass'),
        "type" => "activeplayer",
        "possibleactions" => array("playCard", "passTurn", "endGame"),
        "transitions" => [
            'playerTurn' => 2,
            'endGame' => ST_END_GAME
        ]
    ),

    // .########.##.....##.########..##....##
    // ....##....##.....##.##.....##.###...##
    // ....##....##.....##.##.....##.####..##
    // ....##....##.....##.########..##.##.##
    // ....##....##.....##.##...##...##..####
    // ....##....##.....##.##....##..##...###
    // ....##.....#######..##.....##.##....##

    ST_BEFORE_START_OF_TURN => [
        'name' => 'beforeStartOfTurn',
        'description' => '',
        'type' => 'game',
        'action' => 'stBeforeStartOfTurn',
    ],

    ST_SETUP_YEAR => [
        'name' => 'setupYear',
        'description' => '',
        'type' => 'game',
        'action' => 'stSetupYear',
    ],

    ST_SETUP_ACTION_ROUND => [
        'name' => 'setupActionRound',
        'description' => '',
        'type' => 'game',
        'action' => 'stSetupActionRound',
    ],

    ST_TURNACTION => [
        'name' => 'turnAction',
        'description' => '',
        'type' => 'game',
        'action' => 'stTurnAction',
        'transitions' => [
            'done' => ST_CLEANUP,
        ],
        'updateGameProgression' => true,
    ],

    // .########.##....##..######...####.##....##.########
    // .##.......###...##.##....##...##..###...##.##......
    // .##.......####..##.##.........##..####..##.##......
    // .######...##.##.##.##...####..##..##.##.##.######..
    // .##.......##..####.##....##...##..##..####.##......
    // .##.......##...###.##....##...##..##...###.##......
    // .########.##....##..######...####.##....##.########

    ST_RESOLVE_STACK => [
        'name' => 'resolveStack',
        'type' => 'game',
        'action' => 'stResolveStack',
        'transitions' => [],
    ],

    ST_CONFIRM_TURN => [
        'name' => 'confirmTurn',
        'description' => clienttranslate('${actplayer} must confirm or restart their turn'),
        'descriptionmyturn' => clienttranslate('${you} must confirm or restart your turn'),
        'type' => 'activeplayer',
        'args' => 'argsConfirmTurn',
        'action' => 'stConfirmTurn',
        'possibleactions' => ['actConfirmTurn', 'actRestart'],
        'transitions' => [
            // 'breakStart' => ST_BREAK_MULTIACTIVE
        ],
    ],

    ST_CONFIRM_PARTIAL_TURN => [
        'name' => 'confirmPartialTurn',
        'description' => clienttranslate('${actplayer} must confirm the switch of player'),
        'descriptionmyturn' => clienttranslate('${you} must confirm the switch of player. You will not be able to restart turn'),
        'type' => 'activeplayer',
        'args' => 'argsConfirmTurn',
        // 'action' => 'stConfirmPartialTurn',
        'possibleactions' => ['actConfirmPartialTurn', 'actRestart'],
    ],

    // .########.##....##.########......#######..########
    // .##.......###...##.##.....##....##.....##.##......
    // .##.......####..##.##.....##....##.....##.##......
    // .######...##.##.##.##.....##....##.....##.######..
    // .##.......##..####.##.....##....##.....##.##......
    // .##.......##...###.##.....##....##.....##.##......
    // .########.##....##.########......#######..##......

    // ..######......###....##.....##.########
    // .##....##....##.##...###...###.##......
    // .##.........##...##..####.####.##......
    // .##...####.##.....##.##.###.##.######..
    // .##....##..#########.##.....##.##......
    // .##....##..##.....##.##.....##.##......
    // ..######...##.....##.##.....##.########

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    ST_END_GAME => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ],

    // ....###....########..#######..##.....##.####..######.
    // ...##.##......##....##.....##.###...###..##..##....##
    // ..##...##.....##....##.....##.####.####..##..##......
    // .##.....##....##....##.....##.##.###.##..##..##......
    // .#########....##....##.....##.##.....##..##..##......
    // .##.....##....##....##.....##.##.....##..##..##....##
    // .##.....##....##.....#######..##.....##.####..######.

    // ....###.....######..########.####..#######..##....##..######.
    // ...##.##...##....##....##.....##..##.....##.###...##.##....##
    // ..##...##..##..........##.....##..##.....##.####..##.##......
    // .##.....##.##..........##.....##..##.....##.##.##.##..######.
    // .#########.##..........##.....##..##.....##.##..####.......##
    // .##.....##.##....##....##.....##..##.....##.##...###.##....##
    // .##.....##..######.....##....####..#######..##....##..######.

    ST_SELECT_RESERVE_CARD => [
        'name' => 'selectReserveCard',
        'type' => 'multipleactiveplayer',
        'description' => clienttranslate('Both players must select a Reserve card'),
        'descriptionmyturn' => clienttranslate('${you} must select a Reserve card'),
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actSelectReserveCard'],
        'transitions' => ['next' => ST_RESOLVE_STACK],
    ],

    ST_PLAYER_ACTION => [
        'name' => 'playerAction',
        'description' => clienttranslate('${actplayer} may perform actions'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        // 'transitions' => [],
        'possibleactions' => ['actPlayerAction', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_ACTION_ROUND_CHOOSE_CARD => [
        'name' => 'actionRoundChooseCard',
        'type' => 'multipleactiveplayer',
        'description' => clienttranslate('Both players must choose a card to play'),
        'descriptionmyturn' => clienttranslate('${you} must choose a card to play'),
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actActionRoundChooseCard'],
        'transitions' => ['next' => ST_RESOLVE_STACK],
    ],

    ST_ACTION_ROUND_CHOOSE_FIRST_PLAYER => [
        'name' => 'actionRoundChooseFirstPlayer',
        'description' => clienttranslate('${actplayer} must choose who will be First Player for this Action Round'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actActionRoundChooseFirstPlayer', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_ACTION_ROUND_ACTION_PHASE => [
        'name' => 'actionRoundActionPhase',
        'description' => clienttranslate('${actplayer} may perform actions'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actActionRoundActionPhase', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_ACTION_ROUND_RESOLVE_AR_START_EVENT => [
        'name' => 'actionRoundResolveARStartEvent',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_ACTION_ROUND_RESOLVE_BATTLES => [
        'name' => 'actionRoundResolveBattles',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_ACTION_ROUND_END => [
        'name' => 'actionRoundEnd',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_ACTION_ROUND_SAIL_BOX_LANDING => [
        'name' => 'actionRoundSailBoxLanding',
        'description' => clienttranslate('${actplayer} must perform Landing'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actActionRoundSailBoxLanding', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_ACTION_ROUND_CHOOSE_REACTION => [
        'name' => 'actionRoundChooseReaction',
        'description' => clienttranslate('${actplayer} may hold an Action Point for Reaction'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actActionRoundChooseReaction', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_DRAW_REINFORCEMENTS => [
        'name' => 'drawReinforcements',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_VAGARIES_OF_WAR_PICK_UNITS => [
        'name' => 'vagariesOfWarPickUnits',
        'description' => clienttranslate('${actplayer} must resolve Vagaries of War effects'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actVagariesOfWarPickUnits', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_VAGARIES_OF_WAR_PUT_BACK_IN_POOL => [
        'name' => 'vagariesOfWarPutBackInPool',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_FLEETS_ARRIVE_COMMANDER_DRAW => [
        'name' => 'fleetsArriveCommanderDraw',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_FLEETS_ARRIVE_UNIT_PLACEMENT => [
        'name' => 'fleetsArriveUnitPlacement',
        'description' => clienttranslate('${actplayer} must place units'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actFleetsArriveUnitPlacement', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_LOGISTICS_ROUND_END => [
        'name' => 'logisticsRoundEnd',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_COLONIALS_ENLIST_UNIT_PLACEMENT => [
        'name' => 'colonialsEnlistUnitPlacement',
        'description' => clienttranslate('${actplayer} must place Colonial units'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actColonialsEnlistUnitPlacement', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_WINTER_QUARTERS_GAME_END_CHECK => [
        'name' => 'winterQuartersGameEndCheck',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_WINTER_QUARTERS_ROUND_END => [
        'name' => 'winterQuartersRoundEnd',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_ACTION_ACTIVATE_STACK => [
        'name' => 'actionActivateStack',
        'description' => clienttranslate('${actplayer} may activate stack'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actActionActivateStack', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_RAID_SELECT_TARGET => [
        'name' => 'raidSelectTarget',
        'description' => clienttranslate('${actplayer} must select a Space to RaidSelectTarget'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actRaidSelectTarget', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_RAID_INTERCEPTION => [
        'name' => 'raidInterception',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_RAID_MOVE => [
        'name' => 'raidMove',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_RAID_REROLL => [
        'name' => 'raidReroll',
        'description' => clienttranslate('${actplayer} may reroll'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actRaidReroll', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_RAID_RESOLUTION => [
        'name' => 'raidResolution',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_MOVEMENT => [
        'name' => 'movement',
        'description' => clienttranslate('${actplayer} may move units'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actMovement', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK => [
        'name' => 'movementBattleTakeControlCheck',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_MOVEMENT_LOSE_CONTROL_CHECK => [
        'name' => 'movementLoseControlCheck',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_MOVEMENT_OVERWHELM_CHECK => [
        'name' => 'movementOverwhelmCheck',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_MOVEMENT_PLACE_SPENT_MARKERS => [
        'name' => 'movementPlaceSpentMarkers',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_MOVE_STACK => [
        'name' => 'moveStack',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_SAIL_MOVEMENT => [
        'name' => 'sailMovement',
        'description' => clienttranslate('${actplayer} must select units'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actSailMovement', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_MARSHAL_TROOPS => [
        'name' => 'marshalTroops',
        'description' => clienttranslate('${actplayer} may marshal troops'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actMarshalTroops', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_CONSTRUCTION => [
        'name' => 'construction',
        'description' => clienttranslate('${actplayer} may construct'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actConstruction', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_BATTLE_APPLY_HITS => [
        'name' => 'battleApplyHits',
        'description' => clienttranslate('${actplayer} must apply Hit'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actBattleApplyHits', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_BATTLE_CLEANUP => [
        'name' => 'battleCleanup',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_COMBINE_REDUCED_UNITS => [
        'name' => 'battleCombineReducedUnits',
        'description' => clienttranslate('${actplayer} must combine Reduced units'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actBattleCombineReducedUnits', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_BATTLE_FORT_ELIMINATION => [
        'name' => 'battleFortElimination',
        'description' => clienttranslate('${actplayer} may eliminate their Fort'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actBattleFortElimination', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_BATTLE_MILITIA_ROLLS => [
        'name' => 'battleMilitiaRolls',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_PENALTIES => [
        'name' => 'battlePenalties',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_PRE_SELECT_COMMANDER => [
        'name' => 'battlePreSelectCommander',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_PREPARATION => [
        'name' => 'battlePreparation',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_ROLLS => [
        'name' => 'battleRolls',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_SELECT_COMMANDER => [
        'name' => 'battleSelectCommander',
        'description' => clienttranslate('${actplayer} must select a Commander'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actBattleSelectCommander', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_BATTLE_ROLLS_ROLL_DICE => [
        'name' => 'battleRollsRollDice',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_OUTCOME => [
        'name' => 'battleOutcome',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    
    ST_BATTLE_ROUT => [
        'name' => 'battleRout',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_ROLLS_EFFECTS => [
        'name' => 'battleRollsEffects',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_RETREAT => [
        'name' => 'battleRetreat',
        'description' => clienttranslate('${actplayer} must retreat their force'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actBattleRetreat', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_BATTLE_RETREAT_CHECK_OPTIONS => [
        'name' => 'battleRetreatCheckOptions',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_BATTLE_ROLLS_REROLLS => [
        'name' => 'battleRollsRerolls',
        'description' => clienttranslate('${actplayer} may reroll dice'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actBattleRollsRerolls', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_ARMED_BATTOEMEN => [
        'name' => 'eventArmedBattoemen',
        'description' => clienttranslate('${actplayer} must select a marker to remove'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventArmedBattoemen', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_BRITISH_ENCROACHMENT => [
        'name' => 'eventBritishEncroachment',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_EVENT_DISEASE_IN_BRITISH_CAMP => [
        'name' => 'eventDiseaseInBritishCamp',
        'description' => clienttranslate('${actplayer} must select a unit'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventDiseaseInBritishCamp', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_DISEASE_IN_FRENCH_CAMP => [
        'name' => 'eventDiseaseInFrenchCamp',
        'description' => clienttranslate('${actplayer} must select a unit'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventDiseaseInFrenchCamp', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_ROUND_UP_MEN_AND_EQUIPMENT => [
        'name' => 'eventRoundUpMenAndEquipment',
        'description' => clienttranslate('${actplayer} may select reduced units or a unit in their Losses Box'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventRoundUpMenAndEquipment', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_PENNSYLVANIAS_PEACE_PROMISES => [
        'name' => 'eventPennsylvaniasPeacePromises',
        'description' => clienttranslate('${actplayer} must select 2 Indian units'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventPennsylvaniasPeacePromises', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_DELAYED_SUPPLIES_FROM_FRANCE => [
        'name' => 'eventDelayedSuppliesFromFrance',
        'description' => clienttranslate('${actplayer} must choose AP to lose'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventDelayedSuppliesFromFrance', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_HESITANT_BRITISH_GENERAL => [
        'name' => 'eventHesitantBritishGeneral',
        'description' => clienttranslate('${actplayer} must select a British stack'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventHesitantBritishGeneral', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_SMALLPOX_EPIDEMIC => [
        'name' => 'eventSmallpoxEpidemic',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],

    ST_EVENT_SMALLPOX_INFECTED_BLANKETS => [
        'name' => 'eventSmallpoxInfectedBlankets',
        'description' => clienttranslate('${actplayer} must choose 2 French-controlled Indian units to place on the Losses Box'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventSmallpoxInfectedBlankets', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_STAGED_LACROSSE_GAME => [
        'name' => 'eventStagedLacrosseGame',
        'description' => clienttranslate('${actplayer} may declare to use Staged Lacrosse Game'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventStagedLacrosseGame', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_EVENT_WILDERNESS_AMBUSH => [
        'name' => 'eventWildernessAmbush',
        'description' => clienttranslate('${actplayer} may use Wilderness Ambush'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actEventWildernessAmbush', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_USE_EVENT => [
        'name' => 'useEvent',
        'description' => clienttranslate('${actplayer} may use Event'),
        'descriptionmyturn' => clienttranslate('${you}'),
        'type' => 'activeplayer',
        'args' => 'argsAtomicAction',
        'action' => 'stAtomicAction',
        'possibleactions' => ['actUseEvent', 'actPassOptionalAction', 'actRestart'],
    ],

    ST_PLACE_MARKER_ON_STACK => [
        'name' => 'placeMarkerOnStack',
        'description' => '',
        'type' => 'game',
        'action' => 'stAtomicAction',
        'transitions' => [],
    ],
);
