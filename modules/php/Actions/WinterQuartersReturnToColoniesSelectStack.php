<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\GameMap;
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

class WinterQuartersReturnToColoniesSelectStack extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK;
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

  public function stWinterQuartersReturnToColoniesSelectStack()
  {
    $options = $this->getOptions();

    if (count($options) === 0) {
      $this->resolveAction(['automatic' => true, 'unitIds' => []]);
    }
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersReturnToColoniesSelectStack() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersReturnToColoniesSelectStack()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    return [
      'options' => $this->getOptions(),
      'faction' => $faction,
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

  public function actPassWinterQuartersReturnToColoniesSelectStack()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actWinterQuartersReturnToColoniesSelectStack($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesSelectStack');
    $originId = $args['originId'];
    $destinationId = $args['destinationId'];
    $path = $args['path'];

    $stateArgs = $this->argsWinterQuartersReturnToColoniesSelectStack();

    if (!isset($stateArgs['options'][$originId])) {
      throw new \feException("ERROR 078");
    }

    $option = $stateArgs['options'][$originId];

    if (!isset($option['destinations'][$destinationId])) {
      throw new \feException("ERROR 079");
    }
    $destinationOption = $option['destinations'][$destinationId];

    $destinationPath = $destinationOption['path'];

    if (count($path) !== count($destinationPath)) {
      throw new \feException("ERROR 080");
    }

    //path matches
    foreach ($path as $index => $spaceId) {
      if ($spaceId !== $destinationPath[$index]) {
        throw new \feException("ERROR 081");
      }
    }

    $faction = $this->ctx->getInfo()['faction'];
    $playerId = $this->ctx->getPlayerId();

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $option['units']);

    $this->ctx->getParent()->pushChild(Engine::buildTree([
      'children' => [
        [
          'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK,
          'faction' => $faction,
          'playerId' => $playerId,
          'originId' => $originId,
          'destinationId' => $destinationId,
          'path' => $path,
          'unitIds' => $unitIds,
        ]
      ]
    ]));

    $this->ctx->getParent()->pushChild(new LeafNode([
      'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK,
      'faction' => $faction,
      'playerId' => $playerId,
    ]));

    $this->resolveAction([
      'unitIds' => $unitIds,
    ]);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getOptions()
  {
    $faction = $this->ctx->getInfo()['faction'];
    $units = Units::getAll()->toArray();

    $alreadyResolved = $this->ctx->getParent()->getResolvedActions([WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK]);

    $resolvedUnitIds = [];

    foreach ($alreadyResolved as $node) {
      $resArgs = $node->getActionResolutionArgs();
      $unitIds = $resArgs['unitIds'];
      $resolvedUnitIds = array_merge($resolvedUnitIds, $unitIds);
    }

    $unitsThatMightNeedToReturn = Utils::filter($units, function ($unit) use ($resolvedUnitIds) {
      // Forts don't move. Indians and Colonial Brigades have already been resolved.
      return !$unit->isFort() && !$unit->isIndian() && !$unit->isColonialBrigade() && !in_array($unit->getId(), $resolvedUnitIds);
    });
    $spaces = Spaces::getAll();
    $connections = Connections::getAll();

    $stacks = GameMap::getStacks(null, $unitsThatMightNeedToReturn)[$faction];

    $options = [];

    foreach ($stacks as $spaceId => $stack) {
      $space = $stack['space'];
      if ($space->isFriendlyColonyHomeSpace($faction)) {
        unset($stacks[$spaceId]);
        continue;
      }
      if (Utils::array_some($stack['units'], function ($unit) {
        return $unit->isFleet();
      })) {
        unset($stacks[$spaceId]);
        continue;
      }

      $stackOptions = $this->getOptionsForStack($spaces, $connections, $units, $stack, $faction);

      if (count($stackOptions) > 0) {
        $options[$spaceId] = [
          'space' => $stack['space'],
          'units' => $stack['units'],
          'destinations' => $stackOptions,
        ];
      }
    }

    return $options;
  }

  private function hasFleet($space, $units, $faction)
  {
    return Utils::array_some($units, function ($unit) use ($space, $faction) {
      return $unit->getLocation() === $space->getId() && $unit->isFleet() && $unit->getFaction() === $faction;
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

  public function getOptionsForStack($spaces, $connections, $units, $stack, $faction)
  {
    $sourceSpace =  $stack['space'];
    $sourceSpaceId = $sourceSpace->getId();

    $options = [];
    $shortestDistance = null;

    $canUsePaths = count($stack['units']) === count(Utils::filter($stack['units'], function ($unit) {
      return $unit->isLight();
    }));
    $indianNationControl = [
      CHEROKEE => Globals::getControlCherokee(),
      IROQUOIS => Globals::getControlIroquois(),
    ];

    $visited = [
      $sourceSpaceId => [
        'level' => 0,
        'parent' => null,
        'space' => $sourceSpace,
      ],
    ];
    $queue = [$sourceSpaceId];
    // $nextLevelQueue = [];
    // $level = 1;

    // First get all spaces within range
    while (count($queue) > 0) {
      $currentSpaceId = array_shift($queue);

      $currentSpace = $spaces[$currentSpaceId];
      $currentSpaceIsTarget = $currentSpace->isFriendlyColonyHomeSpace($faction) || $this->hasFleet($currentSpace, $units, $faction);

      if ($shortestDistance !== null && $visited[$currentSpaceId]['level'] > $shortestDistance) {
        continue;
      } else if ($shortestDistance !== null && $visited[$currentSpaceId]['level'] === $shortestDistance && $currentSpaceIsTarget) {
        $options[$currentSpaceId] = $visited[$currentSpaceId];
        continue;
      }

      if ($currentSpaceIsTarget) {
        $options[$currentSpaceId] = $visited[$currentSpaceId];
        $shortestDistance = $visited[$currentSpaceId]['level'];
        continue;
      }

      $adjacentSpaces = $currentSpace->getAdjacentSpaces();

      foreach ($adjacentSpaces as $spaceId => $connectionId) {
        if (isset($visited[$spaceId])) {
          continue;
        }
        if ($faction === FRENCH && $spaces[$spaceId]->getBritishBase()) {
          continue;
        }
        $connection = $connections[$connectionId];

        // TODO: check how this interactions with Indian Nation Control
        $indianPath = $connection->getIndianNationPath();
        if ($indianPath !== null && $indianNationControl[$indianPath] === NEUTRAL) {
          continue;
        }

        if ($connection->isPath() && !$canUsePaths) {
          continue;
        }

        $queue[] = $spaceId;

        $visited[$spaceId] = [
          'level' => $visited[$currentSpaceId]['level'] + 1,
          'parent' => $currentSpaceId,
          'space' => $spaces[$spaceId],
        ];
      }
    }

    $result = [];
    foreach ($options as $spaceId => $data) {
      $result[$spaceId] = [
        'space' => $data['space'],
        'path' => $this->getPath($spaceId, $visited)
      ];
    }

    return $result;
  }
}
