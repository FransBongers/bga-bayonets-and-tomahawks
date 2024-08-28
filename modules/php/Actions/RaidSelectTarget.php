<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
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
      if ($unit->isIndian() && Globals::getNoIndianUnitMayBeActivated()) {
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
    // $path = $args['path'];
    $targetSpaceId = $args['spaceId'];
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

    if (!isset($stateArgs['raidTargets'][$targetSpaceId])) {
      throw new \feException("ERROR 004");
    }

    $raidTarget = $stateArgs['raidTargets'][$targetSpaceId];
    $path = $raidTarget['path'];

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
        $flow['children'][] = [
          'action' => RAID_INTERCEPTION,
          'playerId' => $playerId,
          'unitId' => $unitId,
          'spaceId' => $spaceId,
          'startSpaceId' => $path[0],
        ];
      }
    }

    $canUseStagedLacrosseGame = $player->getFaction() === FRENCH &&
      $unit->isIndian() &&
      Cards::getTopOf(Locations::cardInPlay(INDIAN))->getId() === STAGED_LACROSSE_GAME_CARD_ID &&
      Globals::getUsedEventCount(INDIAN) === 0;

    if ($canUseStagedLacrosseGame) {
      $units = Spaces::get($targetSpaceId)->getUnits(BRITISH);
      if (count($units) === 1 && $units[0]->isFort()) {
        $flow['children'][] = [
          'action' => EVENT_STAGED_LACROSSE_GAME,
          'playerId' => $playerId,
          'spaceId' => $targetSpaceId,
          'optional' => true,
        ];
      }
    }

    $flow['children'][] = [
      'action' => RAID_RESOLUTION,
      'playerId' => $playerId,
      'unitId' => $unitId,
      'spaceId' => $targetSpaceId,
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


    return count($destinations) > 0;
  }

  public function getFlow($actionPointId, $playerId, $originId)
  {
    return [
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

  function getShortestHighestSuccessProbabilityPath($paths, $visited)
  {
    $data = [];
    foreach ($paths as $path) {
      $accruedWeight = 1;
      foreach ($path as $spaceId) {
        $accruedWeight = $accruedWeight * $visited[$spaceId]['weight'];
      }
      $data[] = [
        'path' => $path,
        'accruedWeight' => $accruedWeight,
      ];
    }

    usort($data, function ($a, $b) {
      $weightDifference = $b['accruedWeight'] - $a['accruedWeight'];
      if ($weightDifference < 0) {
        return -1;
      } else if ($weightDifference > 0) {
        return 1;
      } else {
        return count($a['path']) - count($b['path']);
      }
    });

    return $data[0]['path'];
  }

  function getAllRaidPaths($sourceSpaceId, $maxLevel, $playerFaction)
  {
    $allSpaces = Spaces::getAll();
    $connections = Connections::getAll();
    $units = Units::getAll()->toArray();
    $indianNationControl = [
      CHEROKEE => Globals::getControlCherokee(),
      IROQUOIS => Globals::getControlIroquois(),
    ];

    $sourceWeight = $this->getSpaceWeight($units, $sourceSpaceId, $playerFaction);

    $visited = [
      $sourceSpaceId => [
        'level' => 0,
        'parent' => null,
        'space' => $allSpaces[$sourceSpaceId],
        'weight' => $sourceWeight,
        'accruedWeight' => $sourceWeight
        // 'spaceHasEnemyUnits' => $sourceHasEnemyUnits,
      ],
    ];
    $queue = [$sourceSpaceId];
    // $nextLevelQueue = [];
    // $level = 1;

    // First get all spaces within range
    while (count($queue) > 0) {
      $currentSpaceId = array_shift($queue);

      if ($visited[$currentSpaceId]['level'] === $maxLevel) {
        continue;
      }

      $currentSpace = $allSpaces[$currentSpaceId];

      $adjacentSpaces = $currentSpace->getAdjacentSpaces();

      foreach ($adjacentSpaces as $spaceId => $connectionId) {
        if (isset($visited[$spaceId])) {
          continue;
        }
        if ($playerFaction === FRENCH && $allSpaces[$spaceId]->getBritishBase()) {
          continue;
        }
        $connection = $connections[$connectionId];
        $indianPath = $connection->getIndianNationPath();
        if ($indianPath !== null && $indianNationControl[$indianPath] === NEUTRAL) {
          continue;
        }

        $queue[] = $spaceId;

        $weight = $this->getSpaceWeight($units, $spaceId, $playerFaction);

        $visited[$spaceId] = [
          'level' => $visited[$currentSpaceId]['level'] + 1,
          'parent' => $currentSpaceId,
          'space' => $allSpaces[$spaceId],
          'weight' => $weight,
          'accruedWeight' => $weight * $visited[$currentSpaceId]['accruedWeight'],
        ];
      }
    }

    $destinations = [];
    $set = array_keys($visited);

    $enemyFaction = BTHelpers::getOtherFaction($playerFaction);

    foreach ($visited as $spaceId => $data) {
      $space = $allSpaces[$spaceId];
      // Space has already been raided
      if ($space->getRaided() !== null) {
        continue;
      }
      $homeSpace = $space->getHomeSpace();
      // Can raid home spaces or wilderness space with Fort
      if ($homeSpace === null && !Utils::array_some($units, function ($unit) use ($playerFaction, $spaceId) {
        return $unit->getLocation() === $spaceId && $unit->isFort() && $unit->getFaction() !== $playerFaction;
      })) {
        continue;
      }
      if (($homeSpace !== $enemyFaction)) {
        continue;
      }


      // We found shortest route without enemy units othert then possibly in starting space
      if (
        $data['accruedWeight'] === $sourceWeight
      ) {
        $destinations[$spaceId] =
          [
            'space' => $visited[$spaceId]['space'],
            'path' => $this->getPath($spaceId, $visited),
          ];
      } else {
        $pathCalculator = new PathCalculator($maxLevel);
        $paths = $pathCalculator->findAllPathsBetweenSpaces($allSpaces, $connections, $sourceSpaceId, $spaceId, $set);
        $destinations[$spaceId] =
          [
            'space' => $visited[$spaceId]['space'],
            'path' => $this->getShortestHighestSuccessProbabilityPath($paths, $visited)
          ];
      }
    }

    return $destinations;
  }
}
