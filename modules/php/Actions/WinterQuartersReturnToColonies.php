<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Marker;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenario;

class WinterQuartersReturnToColonies extends \BayonetsAndTomahawks\Models\AtomicAction
{

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  protected function canUnitsRemainOnSpace($space, $units, $stackUnits, $stackUnitIds, $faction)
  {
    $data = $this->getUnitsThatCanRemainOnSpace($space, $units, $stackUnits, $stackUnitIds, $faction);

    return $data['maxTotal'] === null || $data['maxTotal'] > 0;
  }

  protected function getUnitsThatCanRemainOnSpace($space, $units, $stackUnits, $stackUnitIds, $faction)
  {
    $cannotRemain = [
      'maxBrigades' => 0,
      'maxTotal' => 0,
    ];

    $spaceId = $space->getId();
    $friendlyUnitsOnSpace = Utils::filter($units, function ($unit) use ($faction, $spaceId) {
      return $unit->getLocation() === $spaceId && $unit->getFaction() === $faction;
    });
    $isNonSettledSpaceWithFriendlyFort = !$space->isSettledSpace() && Utils::array_some($friendlyUnitsOnSpace, function ($unit) {
      return $unit->isFort();
    });

    // Can leave 1 unit of there is not already a unit there not belonging to the stack
    if ($isNonSettledSpaceWithFriendlyFort) {

      $numberOfNonStackFriendlyUnitsOnSpace = count(Utils::filter($friendlyUnitsOnSpace, function ($unit) use ($stackUnitIds) {
        return !$unit->isFort() && !in_array($unit->getId(), $stackUnitIds);
      }));

      if ($numberOfNonStackFriendlyUnitsOnSpace === 0) {
        return [
          'maxBrigades' => 1,
          'maxTotal' => 1,
        ];
      } else {
        return $cannotRemain;
      }
    }

    $isFrienlyControlledEnemySettledSpace = $space->isSettledSpace(BTHelpers::getOtherFaction($faction)) && $space->getControl() === $faction;

    if ($isFrienlyControlledEnemySettledSpace) {
      // TODO: check logic for commanders
      $stackHasNonBrigadeUnit = Utils::array_some($stackUnits, function ($unit) {
        return !$unit->isBrigade();
      });
      $otherFriendlyBrigadesOnSpace = Utils::filter($friendlyUnitsOnSpace, function ($unit) use ($stackUnitIds) {
        return !in_array($unit->getId(), $stackUnitIds) && $unit->isBrigade();
      });
      if ($stackHasNonBrigadeUnit || count($otherFriendlyBrigadesOnSpace) < $space->getValue()) {
        $maxBrigades = $space->getValue() - count($otherFriendlyBrigadesOnSpace);
        return [
          'maxBrigades' => max($maxBrigades, 0),
          'maxTotal' => null,
        ];
      } else {
        return $cannotRemain;
      }
    }

    return $cannotRemain;
  }

  // public function getOptions()
  // {
  //   $faction = $this->ctx->getInfo()['faction'];
  //   $units = Units::getAll()->toArray();
  //   $unitsThatMightNeedToReturn = Utils::filter($units, function ($unit) {
  //     // Forts don't move. Indians and Colonial Brigades have already been resolved.
  //     return !$unit->isFort() && !$unit->isIndian() && !$unit->isColonialBrigade();
  //   });
  //   $spaces = Spaces::getAll();
  //   $connections = Connections::getAll();

  //   $stacks = GameMap::getStacks(null, $unitsThatMightNeedToReturn)[$faction];

  //   // Notifications::log('stacks before', $stacks);
  //   $options = [];

  //   foreach ($stacks as $spaceId => $stack) {
  //     $space = $stack['space'];
  //     if ($space->isFriendlyColonyHomeSpace($faction)) {
  //       unset($stacks[$spaceId]);
  //       continue;
  //     }
  //     if (Utils::array_some($stack['units'], function ($unit) {
  //       return $unit->isFleet();
  //     })) {
  //       unset($stacks[$spaceId]);
  //       continue;
  //     }
  //     // Notifications::log('stack', $spaceId);
  //     $stackOptions = $this->getOptionsForStack($spaces, $connections, $units, $stack, $faction);
  //     // Notifications::log('options ' . $spaceId, );
  //     $options[$spaceId] = [
  //       'space' => $stack['space'],
  //       'units' => $stack['units'],
  //       'destinations' => $stackOptions,
  //     ];
  //   }

  //   return $options;
  // }

  // private function hasFleet($space, $units, $faction)
  // {
  //   return Utils::array_some($units, function ($unit) use ($space, $faction) {
  //     return $unit->getLocation() === $space->getId() && $unit->isFleet() && $unit->getFaction() === $faction;
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

  // public function getOptionsForStack($spaces, $connections, $units, $stack, $faction)
  // {
  //   $sourceSpace =  $stack['space'];
  //   $sourceSpaceId = $sourceSpace->getId();

  //   $options = [];
  //   $shortestDistance = null;

  //   $canUsePaths = count($stack['units']) === count(Utils::filter($stack['units'], function ($unit) {
  //     return $unit->isLight();
  //   }));
  //   $indianNationControl = [
  //     CHEROKEE => Globals::getControlCherokee(),
  //     IROQUOIS => Globals::getControlIroquois(),
  //   ];

  //   $visited = [
  //     $sourceSpaceId => [
  //       'level' => 0,
  //       'parent' => null,
  //       'space' => $sourceSpace,
  //     ],
  //   ];
  //   $queue = [$sourceSpaceId];
  //   // $nextLevelQueue = [];
  //   // $level = 1;

  //   // First get all spaces within range
  //   while (count($queue) > 0) {
  //     $currentSpaceId = array_shift($queue);

  //     $currentSpace = $spaces[$currentSpaceId];
  //     $currentSpaceIsTarget = $currentSpace->isFriendlyColonyHomeSpace($faction) || $this->hasFleet($currentSpace, $units, $faction);

  //     if ($shortestDistance !== null && $visited[$currentSpaceId]['level'] > $shortestDistance) {
  //       continue;
  //     } else if ($shortestDistance !== null && $visited[$currentSpaceId]['level'] === $shortestDistance && $currentSpaceIsTarget) {
  //       $options[$currentSpaceId] = $visited[$currentSpaceId];
  //       continue;
  //     }

  //     if ($currentSpaceIsTarget) {
  //       $options[$currentSpaceId] = $visited[$currentSpaceId];
  //       $shortestDistance = $visited[$currentSpaceId]['level'];
  //       continue;
  //     }

  //     $adjacentSpaces = $currentSpace->getAdjacentSpaces();

  //     foreach ($adjacentSpaces as $spaceId => $connectionId) {
  //       if (isset($visited[$spaceId])) {
  //         continue;
  //       }
  //       if ($faction === FRENCH && $spaces[$spaceId]->getBritishBase()) {
  //         continue;
  //       }
  //       $connection = $connections[$connectionId];

  //       // TODO: check how this interactions with Indian Nation Control
  //       $indianPath = $connection->getIndianNationPath();
  //       if ($indianPath !== null && $indianNationControl[$indianPath] === NEUTRAL) {
  //         continue;
  //       }

  //       if ($connection->isPath() && !$canUsePaths) {
  //         continue;
  //       }

  //       $queue[] = $spaceId;

  //       $visited[$spaceId] = [
  //         'level' => $visited[$currentSpaceId]['level'] + 1,
  //         'parent' => $currentSpaceId,
  //         'space' => $spaces[$spaceId],
  //       ];
  //     }
  //   }

  //   $result = [];
  //   foreach ($options as $spaceId => $data) {
  //     $result[$spaceId] = [
  //       'space' => $data['space'],
  //       'path' => $this->getPath($spaceId, $visited)
  //     ];
  //   }

  //   return $result;
  // }
}
