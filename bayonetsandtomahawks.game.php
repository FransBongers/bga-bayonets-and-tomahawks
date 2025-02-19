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
 * bayonetsandtomahawks.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

$swdNamespaceAutoload = function ($class) {
    $classParts = explode('\\', $class);
    if ($classParts[0] == 'BayonetsAndTomahawks') {
        array_shift($classParts);
        $file = dirname(__FILE__) . '/modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            die('Cannot find file : ' . $file);
        }
    }
};
spl_autoload_register($swdNamespaceAutoload, true, true);


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');

// Generic
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Preferences;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Log;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Players;

// Game specific
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\CustomLogManager;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\WarInEuropeChits;

use const BayonetsAndTomahawks\OPTION_SCENARIO;

class bayonetsandtomahawks extends Table
{
    use BayonetsAndTomahawks\DebugTrait;
    use BayonetsAndTomahawks\States\EngineTrait;
    use BayonetsAndTomahawks\States\TurnTrait;

    // Declare objects from material.inc.php to remove IntelliSense errors
    public $spaces;

    public static $instance = null;
    function __construct()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        self::$instance = $this;
        self::initGameStateLabels(array(
            'logging' => 10,
        ));
        Engine::boot();
        Stats::checkExistence();
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "bayonetsandtomahawks";
    }


    public function getGameOptionValue($optionId)
    {
        $query = new BayonetsAndTomahawks\Helpers\QueryBuilder('global', null, 'global_id');
        $val = $query
            ->where('global_id', $optionId)
            ->get()
            ->first();
        return is_null($val) ? null : $val['global_value'];
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        Globals::setupNewGame($players, $options);
        // Preferences::setupNewGame($players, $options);
        Players::setupNewGame($players, $options);
        Stats::checkExistence();
        Spaces::setupNewGame($players, $options);
        Connections::setupNewGame($players, $options);
        Globals::setTest($options);
        Scenarios::setup($options[OPTION_SCENARIO]);
        Cards::setupNewGame();
        Markers::setupNewGame();
        WarInEuropeChits::setupNewGame();

        Spaces::setStartOfTurnControl();
        Spaces::setStartOfTurnUnits();

        $this->setGameStateInitialValue('logging', false);

        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
    */
    public function getAllDatas($pId = null)
    {

        $pId = $pId ?? Players::getCurrentId();

        $activeBattleLog = Globals::getActiveBattleLog();


        foreach ([BRITISH, FRENCH] as $faction) {
            if (isset($activeBattleLog[$faction]['unitIds'])) {
                $activeBattleLog[$faction]['units'] = Units::getMany($activeBattleLog[$faction]['unitIds'])->toArray();
            }
            if (isset($activeBattleLog[$faction]['militiaIds'])) {
                $activeBattleLog[$faction]['militia'] = Utils::filter(Markers::getMany($activeBattleLog[$faction]['militiaIds'])->toArray(), function ($marker) use ($activeBattleLog) {
                    return in_array($marker->getLocation(), [Locations::stackMarker($activeBattleLog['spaceId'], BRITISH), Locations::stackMarker($activeBattleLog['spaceId'], FRENCH)]);
                });
            }
        }

        if (!isset($activeBattleLog['spaceId'])) {
            $activeBattleLog = null;
        }
        // $activeBattleLog = null;

        $data = [
            'canceledNotifIds' => Log::getCanceledNotifIds(),
            'activeBattleLog' => $activeBattleLog,
            'cardsInPlay' => Cards::getCardsInPlay(),
            'connections' => Connections::getUiData(),
            'currentRound' => [
                'id' => Globals::getActionRound(),
                'step' => Globals::getCurrentStepOfRound(),
                'battleOrder' => Globals::getBattleOrder(),
            ],
            'customLogs' => CustomLogManager::getAll(),
            'playerOrder' => Players::getPlayerOrder(),
            'players' => Players::getUiData($pId),
            'staticData' => [
                'connections' => Connections::getStaticUiData(),
                'units' => Units::getStaticUiData(),
                'spaces' => Spaces::getStaticUiData(),
            ],
            'constrolIndianNations' => [
                CHEROKEE => Globals::getControlCherokee(),
                IROQUOIS => Globals::getControlIroquois(),
            ],
            'markers' => Markers::getAll(),
            'scenario' => Scenarios::get(),
            'spaces' => Spaces::getUiData(),
            'units' => Units::getUiData(),
            'highwayUnusableForBritish' => Globals::getHighwayUnusableForBritish(),
        ];

        return $data;
    }

    function getGameProgression()
    {
        // TODO: compute and return the game progression
        $scenario = Scenarios::get();

        $duration = $scenario->getDuration();
        $totalActionRounds = $duration * 12;

        $currentYear = BTHelpers::getYear();
        $finishedYears = $currentYear - $scenario->getStartYear();

        $actionRoundOrder = [
            ACTION_ROUND_1,
            ACTION_ROUND_2,
            FLEETS_ARRIVE,
            ACTION_ROUND_3,
            COLONIALS_ENLIST,
            ACTION_ROUND_4,
            ACTION_ROUND_5,
            ACTION_ROUND_6,
            ACTION_ROUND_7,
            ACTION_ROUND_8,
            ACTION_ROUND_9,
            WINTER_QUARTERS,
        ];

        $currentActionRound = Markers::get(ROUND_MARKER)->getLocation();
        $actionRoundsFinishedCurrentYear = Utils::array_find_index($actionRoundOrder, function ($actionRound) use ($currentActionRound) {
            return $actionRound === $currentActionRound;
        }) + 1;

        $progression = ($finishedYears * 12 + $actionRoundsFinishedCurrentYear) / $totalActionRounds;

        return $progression * 100;
    }


    public static function get()
    {
        return self::$instance;
    }

    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    ////////////   Custom Turn Order   ////////////
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    public function initCustomTurnOrder($key, $order, $callback, $endCallback, $loop = false, $autoNext = true, $args = [])
    {
        $turnOrders = Globals::getCustomTurnOrders();
        $turnOrders[$key] = [
            'order' => $order ?? Players::getTurnOrder(),
            'index' => -1,
            'callback' => $callback,
            'args' => $args, // Useful mostly for auto card listeners
            'endCallback' => $endCallback,
            'loop' => $loop,
        ];
        Globals::setCustomTurnOrders($turnOrders);

        if ($autoNext) {
            $this->nextPlayerCustomOrder($key);
        }
    }

    public function initCustomDefaultTurnOrder($key, $callback, $endCallback, $loop = false, $autoNext = true)
    {
        $this->initCustomTurnOrder($key, null, $callback, $endCallback, $loop, $autoNext);
    }

    public function nextPlayerCustomOrder($key)
    {
        $turnOrders = Globals::getCustomTurnOrders();
        if (!isset($turnOrders[$key])) {
            throw new BgaVisibleSystemException('Asking for the next player of a custom turn order not initialized : ' . $key);
        }

        // Increase index and save
        $o = $turnOrders[$key];
        $i = $o['index'] + 1;
        if ($i == count($o['order']) && $o['loop']) {
            $i = 0;
        }
        $turnOrders[$key]['index'] = $i;
        Globals::setCustomTurnOrders($turnOrders);

        if ($i < count($o['order'])) {
            $this->gamestate->jumpToState(ST_GENERIC_NEXT_PLAYER);
            $this->gamestate->changeActivePlayer($o['order'][$i]);
            $this->jumpToOrCall($o['callback'], $o['args']);
        } else {
            $this->endCustomOrder($key);
        }
    }

    public function endCustomOrder($key)
    {
        $turnOrders = Globals::getCustomTurnOrders();
        if (!isset($turnOrders[$key])) {
            throw new BgaVisibleSystemException('Asking for ending a custom turn order not initialized : ' . $key);
        }

        $o = $turnOrders[$key];
        $turnOrders[$key]['index'] = count($o['order']);
        Globals::setCustomTurnOrders($turnOrders);
        $callback = $o['endCallback'];
        $this->jumpToOrCall($callback);
    }

    public function jumpToOrCall($mixed, $args = [])
    {
        if (is_int($mixed) && array_key_exists($mixed, $this->gamestate->states)) {
            $this->gamestate->jumpToState($mixed);
        } elseif (method_exists($this, $mixed)) {
            $method = $mixed;
            $this->$method($args);
        } else {
            throw new BgaVisibleSystemException('Failing to jumpToOrCall  : ' . $mixed);
        }
    }


    /////////////////////////////////////////////////////////////
    // Exposing protected methods, please use at your own risk //
    /////////////////////////////////////////////////////////////

    // Exposing protected method getCurrentPlayerId
    public function getCurrentPId()
    {
        return $this->getCurrentPlayerId();
    }

    // Exposing protected method translation
    public static function translate($text)
    {
        return self::_($text);
    }




    // .########..#######..##.....##.########..####.########
    // ......##..##.....##.###...###.##.....##..##..##......
    // .....##...##.....##.####.####.##.....##..##..##......
    // ....##....##.....##.##.###.##.########...##..######..
    // ...##.....##.....##.##.....##.##.....##..##..##......
    // ..##......##.....##.##.....##.##.....##..##..##......
    // .########..#######..##.....##.########..####.########

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    public function zombieTurn($state, $activePlayer)
    {
        $this->gamestate->jumpToState(ST_END_GAME);
        // $stateName = $state['name'];
        // if ($state['type'] == 'activeplayer') {
        //     if ($stateName == 'confirmTurn') {
        //         $this->actConfirmTurn(true);
        //     } else if ($stateName == 'confirmPartialTurn') {
        //         $this->actConfirmPartialTurn(true);
        //     }
        //     // Clear all node of player
        //     else if (Engine::getNextUnresolved() != null) {
        //         Engine::clearZombieNodes($activePlayer);
        //         Engine::proceed();
        //     } else {
        //         // TODO: check if we need this
        //         $this->gamestate->nextState('zombiePass');
        //     }
        // } else if ($state['type'] == 'multipleactiveplayer') {
        //     $this->gamestate->setPlayerNonMultiactive($activePlayer, 'zombiePass');
        // }
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //    $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //    self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //
        if ($from_version <= 2411112151) {
            $sql = 'ALTER TABLE `DBPREFIX_connections` ADD `british_road_used` TINYINT(1) NOT NULL DEFAULT 0';
            self::applyDbUpgradeToAllDB($sql);
            $sql = 'ALTER TABLE `DBPREFIX_connections` ADD `french_road_used` TINYINT(1) NOT NULL DEFAULT 0';
            self::applyDbUpgradeToAllDB($sql);
        }

        if ($from_version <= 2411152208) {
            $sql = 'ALTER TABLE `DBPREFIX_spaces` ADD `units_start_of_turn` VARCHAR(10) NOT NULL DEFAULT "none"';
            self::applyDbUpgradeToAllDB($sql);
        }
    }
}
