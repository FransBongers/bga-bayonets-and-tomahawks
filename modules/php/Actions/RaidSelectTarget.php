<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class RaidSelectTarget extends \BayonetsAndTomahawks\Actions\Raid
{
  public function getState()
  {
    return ST_RAID_SELECT_TARGET;
  }

  // ..######..########....###....########.########
  // .##....##....##......##.##......##....##......
  // .##..........##.....##...##.....##....##......
  // ..######.....##....##.....##....##....######..
  // .......##....##....#########....##....##......
  // .##....##....##....##.....##....##....##......
  // ..######.....##....##.....##....##....########

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function stRaidSelectTarget()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreRaidSelectTargetd()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsRaidSelectTarget()
  {
    $info = $this->ctx->getInfo();

    $spaceId = $info['spaceId'];
    $player = self::getPlayer();
    $playerFaction = $player->getFaction();
    $actionPointId = $this->ctx->getParent()->getParent()->getInfo()['actionPointId'];

    $maxDistance = $this->getMaxDistance($actionPointId);

    $raidTargets = $this->getAllRaidPaths($spaceId, $maxDistance, $playerFaction);

    $allUnits = Spaces::get($spaceId)->getUnits();

    $units = Utils::filter($allUnits, function ($unit) use ($actionPointId, $playerFaction) {
      if (!$unit->isLight() || $unit->isSpent()) {
        return false;
      }
      $unitFaction = $unit->getFaction();
      if (($actionPointId === INDIAN_AP || $actionPointId === INDIAN_AP_2X) && !$unit->isIndian()) {
        return false;
      }
      return $playerFaction === $unitFaction;
    });

    return [
      // 'info' => $info,
      // 'parentInfo' => $parentInfo,
      // 'actionPointId' => $actionPointId,
      'originId' => $spaceId,
      'units' => $units,
      'raidTargets' => $raidTargets,
      'faction' => $playerFaction,
    ];
  }

  //  .########..##..........###....##....##.########.########.
  //  .##.....##.##.........##.##....##..##..##.......##.....##
  //  .##.....##.##........##...##....####...##.......##.....##
  //  .########..##.......##.....##....##....######...########.
  //  .##........##.......#########....##....##.......##...##..
  //  .##........##.......##.....##....##....##.......##....##.
  //  .##........########.##.....##....##....########.##.....##

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function actPassRaidSelectTarget()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actRaidSelectTarget($args)
  {
    self::checkAction('actRaidSelectTarget');
    $path = $args['path'];
    $spaceId = $args['spaceId'];
    $unitId = $args['unitId'];

    $stateArgs = $this->argsRaidSelectTarget();


    /**
     * Get data and validate input
     */
    $unit = Utils::array_find($stateArgs['units'], function ($possibleUnit) use ($unitId) {
      return $unitId === $possibleUnit->getId();
    });

    if ($unit === null) {
      throw new \feException("ERROR 003");
    }

    if (!isset($stateArgs['raidTargets'][$spaceId])) {
      throw new \feException("ERROR 004");
    }

    $raidTarget = $stateArgs['raidTargets'][$spaceId];

    $path = Utils::array_find($raidTarget['paths'], function ($targetPath) use ($path) {
      if (count($targetPath) !== count($path)) {
        return false;
      }
      $pathMatches = true;
      for ($i = 0; $i < count($path); $i++) {
        if ($path[$i] !== $targetPath[$i]) {
          $pathMatches = false;
          break;
        }
      }
      return $pathMatches;
    });

    if ($path === null) {
      throw new \feException("ERROR 005");
    }

    $player = self::getPlayer();
    $playerId = $player->getId();

    $flow = [
      'children' => []
    ];

    foreach ($path as $index => $spaceId) {
      if ($index !== 0) {
        $flow['children'][] = [
          'action' => RAID_MOVE,
          'playerId' => $playerId,
          'unitId' => $unitId,
          'toSpaceId' => $spaceId,
          'startSpaceId' => $path[0],
        ];
      }
    }
    $flow['children'][] = [
      'action' => RAID_RESOLUTION,
      'playerId' => $playerId,
      'unitId' => $unitId,
      'spaceId' => $path[count($path) - 1],
      'startSpaceId' => $path[0],
    ];

    $this->ctx->insertAsBrother(Engine::buildTree($flow));

    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getMaxDistance($actionPointId)
  {
    $maxDistance = 3;
    if ($actionPointId === INDIAN_AP_2X || $actionPointId === LIGHT_AP_2X) {
      $maxDistance = $maxDistance * 2;
    }
    return $maxDistance;
  }

  public function getUiData()
  {
    return [
      'id' => RAID_SELECT_TARGET,
      'name' => clienttranslate("Raid"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    $routMarkers = Markers::getOfTypeInLocation(ROUT_MARKER, Locations::stackMarker($space->getId(), $playerFaction));
    if (count($routMarkers) > 0) {
      return false;
    }

    $hasLightUnit = Utils::array_some($units, function ($unit) {
      return $unit->isLight() && !$unit->isSpent();
    });
    if (!$hasLightUnit) {
      return false;
    }

    $maxDistance = $this->getMaxDistance($actionPoint->getId());

    $destinations = $this->getAllRaidPaths($space->getId(), $maxDistance, $playerFaction);

    // Notifications::log('destinations', $destinations);

    return count($destinations) > 0;
  }

  public function getFlow($actionPointId, $playerId, $originId)
  {
    return [
      // 'stackAction' => LIGHT_MOVEMENT,
      // 'actionPointId' => $actionPointId,
      'originId' => $originId,
      'children' => [
        [
          'action' => RAID_SELECT_TARGET,
          'spaceId' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }

  function spaceHasEnemyUnits($space, $playerFaction)
  {
    $units = $space->getUnits();
    return Utils::array_some($units, function ($unit) use ($playerFaction) {
      return $unit->getFaction() !== $playerFaction;
    });
  }

  function getPath($destinationId, $visited)
  {
    $path = [$destinationId];
    $parentId = $visited[$destinationId]['parent'];
    while ($parentId !== null) {
      array_unshift($path, $parentId);
      $parentId = $visited[$parentId]['parent'];
    }
    return $path;
  }

  function getAllRaidPaths($source, $maxLevel, $playerFaction)
  {
    $allSpaces = Spaces::getAll();
    $sourceHasEnemeyUnits = $this->spaceHasEnemyUnits($allSpaces[$source], $playerFaction);

    $visited = [
      $source => [
        'level' => 0,
        'parent' => null,
        'space' => $allSpaces[$source],
        'weight' => 0,
        'spaceHasEnemeyUnits' => $sourceHasEnemeyUnits,
      ],
    ];
    $queue = [$source];
    // $nextLevelQueue = [];
    // $level = 1;

    while (count($queue) > 0) {
      $currentSpaceId = array_shift($queue);

      if ($visited[$currentSpaceId]['level'] === $maxLevel) {
        continue;
      }

      $currentSpace = $allSpaces[$currentSpaceId];

      $adjacentIds = $currentSpace->getAdjacentSpacesIds();

      foreach ($adjacentIds as $spaceId) {
        if (isset($visited[$spaceId])) {
          continue;
        }
        if ($playerFaction === FRENCH && $allSpaces[$spaceId]->getBritishBase()) {
          continue;
        }
        $queue[] = $spaceId;

        $weight = 1; // No units in target

        // For now just check if enemy units or not
        if ($this->spaceHasEnemyUnits($allSpaces[$spaceId], $playerFaction)) {
          $weight = 100;
        }

        $visited[$spaceId] = [
          'level' => $visited[$currentSpaceId]['level'] + 1,
          'parent' => $currentSpaceId,
          'space' => $allSpaces[$spaceId],
          'weight' => $visited[$currentSpaceId]['weight'] + $weight,
          'spaceHasEnemeyUnits' => $weight === 100,
        ];
      }
    }

    $destinations = [];
    $set = array_keys($visited);
    // Notifications::log('set', $set);
    $enemyFaction = $playerFaction === BRITISH ? FRENCH : BRITISH;

    foreach ($visited as $spaceId => $data) {
      $space = $allSpaces[$spaceId];
      // Space has already been raided
      if ($space->getRaided() !== null) {
        continue;
      }
      $homeSpace = $space->getHomeSpace();
      if ($homeSpace === null && !Utils::array_some($space->getUnits(), function ($unit) use ($playerFaction) {
        return $unit->getType() === FORT && $unit->getFaction() !== $playerFaction;
      })) {
        continue;
      }
      if (($homeSpace !== null && $homeSpace !== $enemyFaction)) {
        continue;
      }
      // We found shortest route without enemy units
      if (
        $data['weight'] < 100 ||
        ($sourceHasEnemeyUnits && $data['weight'] < 200) ||
        ($data['spaceHasEnemeyUnits'] && $data['weight'] < 200) ||
        ($sourceHasEnemeyUnits && $data['spaceHasEnemeyUnits'] && $data['weight'] < 300)
      ) {
        $destinations[$spaceId] =
          [
            'space' => $visited[$spaceId]['space'],
            'paths' => [$this->getPath($spaceId, $visited)],
          ];
      } else {
        $pathCalculator = new PathCalculator($maxLevel);

        $paths = $pathCalculator->findAllPathsBetweenSpaces($source, $spaceId, $set);
        // Notifications::log('paths',[
        //   'spaceId' => $spaceId,
        //   'paths' => $paths,
        // ]);
        $destinations[$spaceId] =
          [
            'space' => $visited[$spaceId]['space'],
            // 'paths' => Utils::filter($paths, function ($path) use ($maxLevel) {
            //   return count($path) <= $maxLevel + 1;
            // }),
            'paths' => $paths
          ];
      }
    }

    return $destinations;
  }
}
