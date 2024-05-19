<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
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
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Models\ActionPoint;

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



  function test()
  {
    $connection = Spaces::get(GNADENHUTTEN)->getUnits();
    Notifications::log('space', $connection);
    // $result = AtomicActions::get(LIGHT_MOVEMENT)->checkEnemyUnitsAndOverwhelm(Spaces::get(ANNAPOLIS_ROYAL), Players::get());

    // Notifications::log('checkEnemyUnitsAndOverwhelm', $result);
  }

  function ed()
  {
    $this->engineDisplay();
  }

  function engineDisplay()
  {
    Notifications::log('engine', Globals::getEngine());
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
