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

  function getStacks()
  {
    $spaces = Spaces::getAll();

    $stacks = [];
    foreach ($spaces as $space) {
      $units = $space->getUnits();
      // if (count($units) > 0) {
      //   Notifications::log('units '.$space->getId(),$units);
      // }

      $hasUnitToActivate = Utils::array_some($units, function ($unit) {
        $faction = $unit->getFaction();
        Notifications::log('faction', $faction);
        return $faction === INDIAN;
      });
      if ($hasUnitToActivate) {
        $stacks[] = $space->getId();
      }
    }
    Notifications::log('stacks', $stacks);
  }



  private function getRemainingActionPoints($usedActionPoints, $card)
  {
    $cardActionPoints = $card->getActionPoints();

    $result = [];
    foreach ($cardActionPoints as $cIndex => $actionPoint) {
      $uIndex = Utils::array_find_index($usedActionPoints, function ($uActionPointId) use ($actionPoint) {
        return $uActionPointId === $actionPoint['id'];
      });
      if ($uIndex === null) {
        $result[] = $actionPoint;
      } else {
        unset($usedActionPoints[$uIndex]);
        $usedActionPoints = array_values($usedActionPoints);
      }
    }

    return $result;
  }

  function debug_getUnit($unitId)
  {
    Notifications::log('unit', Units::get($unitId));
  }

 

  function debug_test()
  {
    $this->battlePenalties(Spaces::get(LOUISBOURG), BRITISH, FRENCH);

    // Units::get('unit_48')->setState(1);
    // Connections::get('Loyalhanna_RaysTown')->setRoad(1);
    // Connections::get('RaysTown_Shamokin')->setRoad(2);
    // Globals::setPlacedConstructionMarkers([]);
    // Notifications::log('marker', Markers::get('marshalTroopsMarker_1'));
    // Notifications::log('hasStackMarker', Spaces::get(ALBANY)->hasStackMarker(OUT_OF_SUPPLY_MARKER, BRITISH));



    // $next = Engine::getNextUnresolved();
    // Notifications::log('index', GameMap::factionOutnumbersEnemyInSpace(Spaces::get(TICONDEROGA), FRENCH));

    // Notifications::log('markers', GameMap::getMarkersOnMap(OUT_OF_SUPPLY_MARKER,BRITISH));

    // GameMap::placeMarkerOnStack(Players::getPlayerForFaction(BRITISH), ROUT_MARKER, Spaces::get(ALBANY), BRITISH);
    // GameMap::placeMarkerOnStack(Players::getPlayerForFaction(BRITISH), OUT_OF_SUPPLY_MARKER, Spaces::get(BOSTON), BRITISH);
    // GameMap::placeMarkerOnStack(Players::getPlayerForFaction(BRITISH), OUT_OF_SUPPLY_MARKER, Spaces::get(CARLISLE), BRITISH);

    // Players::scoreVictoryPoints(Players::getPlayerForFaction(FRENCH),3);
    // GameMap::performIndianNationControlProcedure(IROQUOIS, BRITISH);
    // GameMap::performIndianNationControlProcedure(CHEROKEE, FRENCH);
    // Globals::setControlCherokee(NEUTRAL);
    // Globals::setControlIroquois(NEUTRAL);

    // Globals::setLostAPIndian([]);
    // Globals::setLostAPBritish([]);
    // Globals::setLostAPFrench([]);

    // Cards::get('Card33')->insertOnTop(Locations::buildUpDeck(FRENCH));
    // Cards::get('Card21')->insertOnTop(Locations::buildUpDeck(BRITISH));
    // Cards::get('Card38')->insertOnTop(Locations::campaignDeck(FRENCH));
    // Cards::get('Card13')->insertOnTop(Locations::campaignDeck(BRITISH));


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
  //       ($data['spaceHasEnemeyUnits'] && $data['weight'] < 200) ||
  //       ($sourceHasEnemeyUnits && $data['spaceHasEnemeyUnits'] && $data['weight'] < 300)
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
}
