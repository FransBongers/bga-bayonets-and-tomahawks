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

trait DebugTrait
{
  // function debugLoadScenario($scenarioId)
  // {
  //   Scenario::loadId($scenarioId);
  //   Scenario::setup();
  // }

  // function getStacksAndSupplySources()
  // {
  //   $spaces = Spaces::getAll();
  //   $units = Units::getAll()->toArray();

  //   $stacks = [
  //     BRITISH => [],
  //     FRENCH => [],
  //   ];

  //   foreach ($units as $unit) {
  //     $location = $unit->getLocation();
  //     if (!(in_array($location, SPACES) && !in_array($location, BASTIONS))) {
  //       continue;
  //     }
  //     $faction = $unit->getFaction();
  //     // location is a Space
  //     if (isset($stacks[$faction][$location])) {
  //       $stacks[$faction][$location]['units'][] = $unit;
  //     } else {
  //       $stacks[$faction][$location] = [
  //         'units' => [$unit],
  //         'space' => $spaces[$location]
  //       ];
  //     }
  //   }

  //   $supplySources = [
  //     BRITISH => [],
  //     FRENCH => [],
  //   ];

  //   // Friendly colony homespaces
  //   foreach ($spaces as $spaceId => $space) {
  //     $isColonyHomeSpace = $space->getColony() !== null && $space->getHomeSpace() !== null;
  //     if (!$isColonyHomeSpace) {
  //       continue;
  //     }
  //     foreach ([BRITISH, FRENCH] as $faction) {
  //       if ($space->getControl() === $faction) {
  //         $supplySources[$faction][] = $spaceId;
  //       }
  //     }
  //   }

  //   // Spaces with friendly fleets
  //   foreach ([BRITISH, FRENCH] as $faction) {
  //     foreach ($stacks[$faction] as $spaceId => $data) {
  //       if (Utils::array_some($data['units'], function ($unit) {
  //         return $unit->isFleet();
  //       }) && !in_array($spaceId, $supplySources[$faction])) {
  //         $supplySources[$faction][] = $spaceId;
  //       };
  //     }
  //   }

  //   return [
  //     'stacks' => $stacks,
  //     'supplySources' => $supplySources,
  //   ];
  // }



  function debug_getUnit($unitId)
  {
    Notifications::log('unit', Units::get($unitId));
  }

  // function checkSupplyForFaction($faction, $stacks, $supplySources, $enemyStacks)
  // {
  //   $spaces = Spaces::getAll();
  //   $connections = Connections::getAll();
  //   $enemyFaction = BTHelpers::getOtherFaction($faction);
  //   $indianNationControl = [
  //     CHEROKEE => Globals::getControlCherokee(),
  //     IROQUOIS => Globals::getControlIroquois(),
  //   ];

  //   foreach ($stacks as $spaceId => $stackInSpaceData) {
  //     $canUsePaths = !Utils::array_some($stackInSpaceData['units'], function ($unit) {
  //       return !$unit->isLight();
  //     });

  //     // Check supply
  //     $visited = [];
  //     $queue = [$spaceId];
  //     $inSupply = false;

  //     while (count($queue) > 0) {
  //       $currentSpaceId = array_shift($queue);
  //       if (isset($visited[$currentSpaceId]) && $visited[$currentSpaceId]) {
  //         continue;
  //       }
  //       $visited[$currentSpaceId] = true;

  //       if (in_array($spaceId, $supplySources)) {
  //         $inSupply = true;
  //         break;
  //       }

  //       $currentSpace = $spaces[$currentSpaceId];
  //       $adjacentSpaces = $currentSpace->getAdjacentSpaces();

  //       foreach ($adjacentSpaces as $adjacentSpaceId => $connectionId) {
  //         if (isset($visited[$adjacentSpaceId]) && $visited[$currentSpaceId]) {
  //           continue;
  //         }

  //         // Cannot use paths if stack is not entirely composed of Light Units
  //         $connection = $connections[$connectionId];
  //         if ($connection->isPath() && !$canUsePaths) {
  //           continue;
  //         }

  //         // Cannot use paths of Neutral Indian Nations
  //         $indianPath = $connection->getIndianNationPath();
  //         if ($canUsePaths && $indianPath !== null && $indianNationControl[$indianPath] === NEUTRAL) {
  //           continue;
  //         }

  //         $adjacentSpace = $spaces[$adjacentSpaceId];

  //         // Cannot trace through enemy controlled spaces, unless Outpost with no enemy units
  //         if ($adjacentSpace->getControl() === $enemyFaction && !($adjacentSpace->isOutpost() && !isset($enemyStacks[$adjacentSpaceId]))) {
  //           continue;
  //         }

  //         // Can use Wilderness Spaces unless they contain enemy units
  //         if ($adjacentSpace->getControl() === NEUTRAL && isset($enemyStacks[$adjacentSpaceId])) {
  //           continue;
  //         }

  //         if (in_array($adjacentSpaceId, $supplySources)) {
  //           $inSupply = true;
  //           break;
  //         }
  //         $queue[] = $adjacentSpaceId;
  //       }
  //     }

  //     if (!$inSupply) {
  //       Notifications::log('stack not in supply', $stackInSpaceData);
  //     }
  //   }
  // }

  function debug_test()
  {
    // $pathCalculator = new PathCalculator(3);
    // $set = [CHIGNECTOU, ANNAPOLIS_ROYAL, HALIFAX, CAPE_SABLE, PORT_LA_JOYE, LOUISBOURG, PORT_DAUPHIN, MIRAMICHY, KWANOSKWAMCOK, ST_GEORGE. POINTE_SAINTE_ANNE, GRAND_SAULT];
    // $paths = $pathCalculator->findAllPathsBetweenSpaces(CHIGNECTOU, CAPE_SABLE, $set);

    $result = AtomicActions::get(RAID_SELECT_TARGET)->getAllRaidPaths(KITHANINK, 6, FRENCH);
    Notifications::log('result', $result);

    // Notifications::log('oos', GameMap::getMarkersOnMap(ROUT_MARKER, BRITISH));
    // GameMap::placeMarkerOnStack(Players::getPlayerForFaction(BRITISH), OUT_OF_SUPPLY_MARKER, Spaces::get(HALIFAX), BRITISH);

    // Notifications::log('globals', Globals::getUsedEventCount(BRITISH));

    // WarInEuropeChits::drawChit(BRITISH);
    // WarInEuropeChits::drawChit(FRENCH);

    // GameMap::placeMarkerOnStack(Players::get(), ROUT_MARKER, Spaces::get(BOSTON), BRITISH);
    // GameMap::placeMarkerOnStack(Players::get(), ROUT_MARKER, Spaces::get(NEW_YORK), BRITISH);
    // GameMap::placeMarkerOnStack(Players::get(), ROUT_MARKER, Spaces::get(ALBANY), BRITISH);

    // Units::get('unit_36')->setLocation(ALBANY);
    // Units::get('unit_35')->setLocation(ALBANY);






    // Cards::get('Card27')->insertOnTop(Locations::buildUpDeck(FRENCH));
    // Cards::get('Card14')->insertOnTop(Locations::buildUpDeck(BRITISH));
    // Cards::get('Card36')->insertOnTop(Locations::campaignDeck(FRENCH));
    // Cards::get('Card09')->insertOnTop(Locations::campaignDeck(BRITISH));


    // Units::get('unit_51')->setLocation(Locations::lossesBox(FRENCH));

    // // Round up men and equipment
    // Units::get('unit_45')->setLocation(Locations::lossesBox(BRITISH));
    // Units::get('unit_108')->setLocation(Locations::lossesBox(BRITISH));
    // Units::get('unit_94')->setLocation(Locations::lossesBox(BRITISH));

    // $player = Players::get();
    // Units::get('unit_43')->reduce($player);
    // Units::get('unit_47')->reduce($player);
    // Units::get('unit_52')->reduce($player);
    // Units::get('unit_42')->reduce($player);

    // British encroachment

    // Units::get('unit_12')->setLocation(Locations::lossesBox(FRENCH));
    // Units::get('unit_2')->setLocation(Locations::lossesBox(FRENCH));
    // Units::get('unit_3')->setLocation(Locations::lossesBox(FRENCH));
    // Spaces::get(GRAND_SAULT)->setControl(BRITISH);
    // Spaces::get(GENNISHEYO)->setControl(BRITISH);

    ///////
    // Notifications::log('players',Players::getPlayersForFactions());
    // Notifications::log('units', Scenarios::get(LoudounsGamble1757)->getYearEndBonus(BRITISH, 1757));
    // Notifications::log('units', Scenarios::get(LoudounsGamble1757)->getYearEndBonus(FRENCH, 1757));
    // Units::create($units, null);

    // Spaces::get(CHIGNECTOU)->setControl(BRITISH);
    // $tokens = [];
    // $tokens[OPEN_SEAS_MARKER] = [
    //   'id' => OPEN_SEAS_MARKER,
    //   'location' => OPEN_SEAS_MARKER
    // ];

  }

  // function ed()
  // {
  //   $this->engineDisplay();
  // }

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

  // /**
  //  * TODO: just make one db call to get all spaces data?
  //  */
  // function raidCalculation($start, $maxLevel)
  // {
  //   $allSpaces = Spaces::getAll();

  //   $visited = [
  //     $start => [
  //       'level' => 0,
  //       'parent' => null,
  //       'space' => $allSpaces[$start],
  //     ],
  //   ];
  //   $currentLevelQueue = [$start];
  //   $nextLevelQueue = [];
  //   $level = 1;

  //   while (count($currentLevelQueue) + count($nextLevelQueue) > 0 && $level <= $maxLevel) {
  //     $currentSpaceId = array_shift($currentLevelQueue);

  //     $currentSpace = $allSpaces[$currentSpaceId];

  //     $adjacentIds = $currentSpace->getAdjacentSpacesIds();

  //     foreach ($adjacentIds as $spaceId) {
  //       if (isset($visited[$spaceId])) {
  //         continue;
  //       }
  //       $nextLevelQueue[] = $spaceId;
  //       $visited[$spaceId] = [
  //         'level' => $level,
  //         'parent' => $currentSpaceId,
  //         'space' => $allSpaces[$spaceId]
  //       ];
  //     }

  //     if (count($currentLevelQueue) === 0) {
  //       $currentLevelQueue = $nextLevelQueue;
  //       $nextLevelQueue = [];
  //       $level += 1;
  //     }
  //   }
  //   return $visited;
  // }

  // function findShortestPath($start, $set)
  // {
  //   Notifications::log('findShortestPath', [
  //     'start' => $start,
  //     'set' => $set,
  //   ]);
  //   $allSpaces = Spaces::getAll();

  //   foreach ($set as $spaceId) {
  //     $distances[$spaceId] = 1000000;
  //   };
  //   $distances[$start] = 0;


  //   $shortestPaths = [];
  //   $minimum = 2000000;
  //   $nextSpaceId = null;
  //   foreach ($distances as $spaceId => $distance) {
  //     if ($distance < $minimum) {
  //       $minimum = $distance;
  //       $nextSpaceId = $spaceId;
  //     }
  //   }
  //   Notifications::log('nextSpaceId', $nextSpaceId);
  //   $currentSpace = $allSpaces[$nextSpaceId];
  //   $shortestPaths[$nextSpaceId] = [
  //     'space' => $currentSpace,
  //     'distance' => $minimum,
  //   ];

  //   $adjacentIds = $currentSpace->getAdjacentSpacesIds();
  //   foreach ($adjacentIds as $spaceId) {
  //     if (!isset($distances[$spaceId])) {
  //       continue;
  //     }
  //     $weight = 1; // No units in target
  //     $units = $allSpaces[$spaceId]->getUnits();
  //     if (Utils::array_some($units, function ($unit) {
  //       return $unit->getFaction() === BRITISH && $unit->gettype() === LIGHT;
  //     })) {
  //       $weight = 1000;
  //     } else if (Utils::array_some($units, function ($unit) {
  //       return $unit->getFaction() === BRITISH;
  //     })) {
  //       $weight = 100;
  //     }
  //     $distances[$spaceId] = $weight;
  //   }
  //   Notifications::log('distances', $distances);
  // }

  // function minDistance($distances, $shortPathSet)
  // {
  //   $minimum = 2000000;
  //   $nextSpaceId = null;

  //   foreach ($distances as $spaceId => $distance) {
  //     if (!$shortPathSet[$spaceId] && $distance < $minimum) {
  //       $minimum = $distance;
  //       $nextSpaceId = $spaceId;
  //     }
  //   };
  //   return $nextSpaceId;
  // }

  // function dijkstra($source, $set)
  // {
  //   Notifications::log('source', [
  //     'source' => $source,
  //     'set' => $set,
  //   ]);
  //   $allSpaces = Spaces::getAll();

  //   $distances = [];
  //   $shortestPaths = [];
  //   $parents = [];
  //   foreach ($set as $spaceId) {
  //     $distances[$spaceId] = 1000000;
  //     $shortestPaths[$spaceId] = false;
  //     $parents[$spaceId] = null;
  //   };
  //   $distances[$source] = 0;

  //   Notifications::log('distances', $distances);
  //   Notifications::log('shortestPaths', $shortestPaths);

  //   // Find shortest path for all Spaces
  //   $numberOfSpaces = count($set);
  //   for ($i = 0; $i < $numberOfSpaces - 1; $i++) {
  //     // Get minimum distance from Spaces not yet processed
  //     $currentSpaceId = $this->minDistance($distances, $shortestPaths);
  //     Notifications::log('currentSpaceId', $currentSpaceId);
  //     // Mark as processed
  //     $shortestPaths[$currentSpaceId] = true;

  //     // Update distance of adjacent
  //     $currentSpace = $allSpaces[$currentSpaceId];
  //     $adjacentIds = $currentSpace->getAdjacentSpacesIds();
  //     Notifications::log('adjacentIds', $adjacentIds);
  //     foreach ($adjacentIds as $spaceId) {
  //       // Do not update if not set in distances (ie, not in set)
  //       // or if already in shortest path
  //       if (!isset($distances[$spaceId]) || $shortestPaths[$spaceId]) {
  //         continue;
  //       }
  //       $weight = 1; // No units in target
  //       $units = $allSpaces[$spaceId]->getUnits();
  //       if (Utils::array_some($units, function ($unit) {
  //         return $unit->getFaction() === BRITISH && $unit->gettype() === LIGHT;
  //       })) {
  //         $weight = 1000;
  //       } else if (Utils::array_some($units, function ($unit) {
  //         return $unit->getFaction() === BRITISH;
  //       })) {
  //         $weight = 100;
  //       }

  //       if ($weight + $distances[$currentSpaceId] < $distances[$spaceId]) {
  //         $distances[$spaceId] = $weight + $distances[$currentSpaceId];
  //         $parents[$spaceId] = $currentSpaceId;
  //       }
  //     }
  //   }

  //   return [
  //     'distances' => $distances,
  //     'parents' => $parents,
  //   ];
  //   Notifications::log('distances', $distances);
  // }

  // function spaceHasEnemyUnits($space)
  // {
  //   $units = $space->getUnits();
  //   return Utils::array_some($units, function ($unit) {
  //     return $unit->getFaction() === BRITISH;
  //   });
  // }

  // function getPath($destinationId, $visited)
  // {
  //   $path = [$destinationId];
  //   $parentId = $visited[$destinationId]['parent'];
  //   while ($parentId !== null) {
  //     array_unshift($path, $parentId);
  //     $parentId = $visited[$parentId]['parent'];
  //   }
  //   return $path;
  // }


  // function getAllRaidPaths($source, $maxLevel)
  // {
  //   $allSpaces = Spaces::getAll();
  //   $sourceHasEnemeyUnits = $this->spaceHasEnemyUnits($allSpaces[$source]);

  //   $visited = [
  //     $source => [
  //       'level' => 0,
  //       'parent' => null,
  //       'space' => $allSpaces[$source],
  //       'weight' => 0,
  //       'spaceHasEnemeyUnits' => $sourceHasEnemeyUnits,
  //     ],
  //   ];
  //   $queue = [$source];
  //   // $nextLevelQueue = [];
  //   // $level = 1;

  //   while (count($queue) > 0) {
  //     $currentSpaceId = array_shift($queue);

  //     if ($visited[$currentSpaceId]['level'] === $maxLevel) {
  //       continue;
  //     }

  //     $currentSpace = $allSpaces[$currentSpaceId];

  //     $adjacentIds = $currentSpace->getAdjacentSpacesIds();

  //     foreach ($adjacentIds as $spaceId) {
  //       if (isset($visited[$spaceId])) {
  //         continue;
  //       }
  //       $queue[] = $spaceId;

  //       $weight = 1; // No units in target
  //       // $units = $allSpaces[$spaceId]->getUnits();

  //       // For now just check if enemy units or not
  //       if ($this->spaceHasEnemyUnits($allSpaces[$spaceId])) {
  //         $weight = 100;
  //       }

  //       $visited[$spaceId] = [
  //         'level' => $visited[$currentSpaceId]['level'] + 1,
  //         'parent' => $currentSpaceId,
  //         'space' => $allSpaces[$spaceId],
  //         'weight' => $visited[$currentSpaceId]['weight'] + $weight,
  //         'spaceHasEnemeyUnits' => $weight === 100,
  //       ];
  //     }

  //     // if (count($currentLevelQueue) === 0) {
  //     //   $currentLevelQueue = $nextLevelQueue;
  //     //   $nextLevelQueue = [];
  //     //   $level += 1;
  //     // }
  //   }


  //   Notifications::log('visisted 2', $visited);

  //   $destinations = [];
  //   $set = array_keys($visited);
  //   foreach ($visited as $spaceId => $data) {
  //     $space = $allSpaces[$spaceId];
  //     if ($space->getHomeSpace() !== BRITISH || $space->getValue() === 0) {
  //       continue;
  //     }
  //     // We found shortest route without enemy units
  //     if (
  //       $data['weight'] < 100 ||
  //       ($sourceHasEnemeyUnits && $data['weight'] < 200) ||
  //       ($data['spaceHasEnemyUnits'] && $data['weight'] < 200) ||
  //       ($sourceHasEnemeyUnits && $data['spaceHasEnemyUnits'] && $data['weight'] < 300)
  //     ) {
  //       $destinations[$spaceId] =
  //         [
  //           'space' => $visited[$spaceId]['space'],
  //           'paths' => [$this->getPath($spaceId, $visited)],
  //         ];
  //     } else {
  //       $pathCalculator = new PathCalculator($maxLevel);

  //       $paths = $pathCalculator->findAllPathsBetweenSpaces($source, $spaceId, $set);
  //       $destinations[$spaceId] =
  //         [
  //           'space' => $visited[$spaceId]['space'],
  //           'paths' => Utils::filter($paths, function ($path) use ($maxLevel) {
  //             return count($path) <= $maxLevel;
  //           }),
  //         ];
  //       // Notifications::log('needs extra check', $spaceId);
  //     }
  //   }

  //   return $destinations;
  // }

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
