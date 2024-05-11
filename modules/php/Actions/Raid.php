<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Tokens;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class Raid extends \BayonetsAndTomahawks\Actions\StackAction
{
  public function getState()
  {
    return ST_RAID;
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

  public function stRaid()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreRaid()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsRaid()
  {
    $info = $this->ctx->getInfo();
    $parentInfo = $this->ctx->getParent()->getInfo();

    $spaceId = $info['spaceId'];
    $player = self::getPlayer();
    $playerFaction = $player->getFaction();
    $actionPointId = $this->ctx->getParent()->getParent()->getInfo()['actionPointId'];

    $maxDistance = $this->getMaxDistance($actionPointId);

    $raidTargets = $this->getAllRaidPaths($spaceId, $maxDistance, $playerFaction);

    $allUnits = Spaces::get($spaceId)->getUnits();

    $units = Utils::filter($allUnits, function ($unit) use ($actionPointId, $playerFaction) {
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
      'units' => $units,
      'raidTargets' => $raidTargets,
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

  public function actPassRaid()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actRaid($args)
  {
    self::checkAction('actRaid');

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getMaxDistance($actionPointId) {
    $maxDistance = 3;
    if ($actionPointId === INDIAN_AP_2X || $actionPointId === LIGHT_AP_2X) {
      $maxDistance = $maxDistance * 2;
    }
    return $maxDistance;
  }

  public function getUiData() {
    return [
      'id' => RAID,
      'name' => clienttranslate("Raid"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    $hasLightUnit = Utils::array_some($units, function ($unit) {
      $unitType = $unit->getType();
      return $unitType === LIGHT;
      // TODO: unit may not have moved already?
      // non-routed
    });
    if (!$hasLightUnit) {
      return false;
    }

    $maxDistance = $this->getMaxDistance($actionPoint->getId());

    $destinations = $this->getAllRaidPaths($space->getId(), $maxDistance, $playerFaction);

    Notifications::log('destinations', $destinations);

    return count($destinations) > 0;
  }

  public function getFlow($playerId, $originId)
  {
    return [
      // 'stackAction' => LIGHT_MOVEMENT,
      // 'actionPointId' => $actionPointId,
      'originId' => $originId,
      'children' => [
        [
          'action' => RAID,
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
      if ($space->getHomeSpace() !== $enemyFaction || $space->getValue() === 0) {
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
