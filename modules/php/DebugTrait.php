<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Managers\ActionPoints;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\Spaces;
// use BayonetsAndTomahawks\Managers\Spaces2;
use BayonetsAndTomahawks\Managers\StackActions;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\WarInEuropeChits;
use BayonetsAndTomahawks\Models\AtomicAction;
use BayonetsAndTomahawks\Models\Space;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Models\ActionPoint;
use BayonetsAndTomahawks\Scenarios\LoudounsGamble1757;
use FTP\Connection;
use Locale;

trait DebugTrait
{
  
  function debug_getUnit($unitId)
  {
    Notifications::log('unit', Units::get($unitId));
  }


  function debug_test()
  {
  
    // Globals::setCurrentStepOfRound(SELECT_CARD_TO_PLAY_STEP);
    // Cards::get('Card47')->setLocation(Locations::cardInPlay(INDIAN));

    // Units::get('unit_121')->setLocation(LOSSES_BOX_BRITISH);
    // Units::get('unit_7')->setLocation(BAYE_DE_CATARACOUY);
    // Units::get('unit_48')->setLocation(NUMBER_FOUR);
    // Units::get('unit_37')->setLocation(LOSSES_BOX_FRENCH);
    // Units::get('unit_3')->setLocation(REMOVED_FROM_PLAY);
    // Units::get('unit_15')->setLocation(WILLS_CREEK);
    // Units::get('unit_38')->setLocation(WILLS_CREEK);
    // Units::get('unit_92')->setSpent(0);

    // GameMap::placeMarkerOnStack(Players::get(), ROUT_MARKER, Spaces::get(BOSTON), BRITISH);
    // GameMap::placeMarkerOnStack(Players::get(), ROUT_MARKER, Spaces::get(NEW_YORK), BRITISH);
    // GameMap::placeMarkerOnStack(Players::get(), ROUT_MARKER, Spaces::get(ALBANY), BRITISH);

    // Cards::get('Card25')->insertOnTop(Locations::buildUpDeck(FRENCH));
    Cards::get('Card07')->insertOnTop(Locations::buildUpDeck(BRITISH));
    // Cards::get('Card06')->insertOnTop(Locations::campaignDeck(FRENCH));
    // Cards::get('Card06')->insertOnTop(Locations::campaignDeck(BRITISH));
    // Cards::get('Card54')->insertOnTop(Locations::campaignDeck(INDIAN));

  }


  function debug_engineDisplay()
  {
    Notifications::log('engine', Globals::getEngine());
  }

  function debug_globalsDisplay()
  {
    Notifications::log('firstPlayerId', Globals::getFirstPlayerId());
    Notifications::log('secondPlayerId', Globals::getSecondPlayerId());
    Notifications::log('reactionActionPointId', Globals::getReactionActionPointId());
  }


  public function loadBugReportSQL(int $reportId, array $studioPlayers): void
  {
    $prodPlayers = $this->getObjectListFromDb("SELECT `player_id` FROM `player`", true);
    $prodCount = count($prodPlayers);
    $studioCount = count($studioPlayers);
    if ($prodCount != $studioCount) {
      throw new BgaVisibleSystemException("Incorrect player count (bug report has $prodCount players, studio table has $studioCount players)");
    }

    // SQL specific to your game
    $sql[] = 'ALTER TABLE `gamelog` ADD `cancel` TINYINT(1) NOT NULL DEFAULT 0;';
    // // For example, reset the current state if it's already game over
    // $sql = [
    //     "UPDATE `global` SET `global_value` = 10 WHERE `global_id` = 1 AND `global_value` = 99"
    // ];
    $map = [];
    foreach ($prodPlayers as $index => $prodId) {
      $studioId = $studioPlayers[$index];
      $map[(int) $prodId] = (int) $studioId;
      // SQL common to all games
      $sql[] = "UPDATE `player` SET `player_id` = $studioId WHERE `player_id` = $prodId";
      $sql[] = "UPDATE `global` SET `global_value` = $studioId WHERE `global_value` = $prodId";
      $sql[] = "UPDATE `stats` SET `stats_player_id` = $studioId WHERE `stats_player_id` = $prodId";

      // SQL specific to your game
      // $sql[] = "UPDATE `player_extra` SET `player_id` = $studioId WHERE `player_id` = $prodId";

      // $sql[] = "UPDATE `card` SET `card_location_arg` = $studioId WHERE `card_location_arg` = $prodId";
      // $sql[] = "UPDATE `my_table` SET `my_column` = REPLACE(`my_column`, $prodId, $studioId)";
    }
    foreach ($sql as $q) {
      $this->DbQuery($q);
    }

    $firstPlayerId = Globals::getFirstPlayerId();
    if ($firstPlayerId !== 0) {
      Globals::setFirstPlayerId($map[$firstPlayerId]);
    }
    $secondPlayerId = Globals::getSecondPlayerId();
    if ($secondPlayerId !== 0) {
      Globals::setSecondPlayerId($map[$secondPlayerId]);
    }


    // Engine
    $engine = Globals::getEngine();
    self::loadDebugUpdateEngine($engine, $map);
    Globals::setEngine($engine);
    Game::get()->reloadPlayersBasicInfos(); // Is this necessary?
  }

  static function loadDebugUpdateEngine(&$node, $map)
  {
    if (isset($node['playerId']) && $node['playerId'] !== 'all') {
      $node['playerId'] = $map[(int) $node['playerId']];
    }

    if (isset($node['children'])) {
      foreach ($node['children'] as &$child) {
        self::loadDebugUpdateEngine($child, $map);
      }
    }
  }
}
