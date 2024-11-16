<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
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

class WinterQuartersReturnToColoniesStep2SelectStack extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK;
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

  public function stWinterQuartersReturnToColoniesStep2SelectStack()
  {
    $data = $this->getOptions();


    if (count($data['options']) === 0) {
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

  public function stPreWinterQuartersReturnToColoniesStep2SelectStack() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersReturnToColoniesStep2SelectStack()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $data = $this->getOptions();

    return [
      'destinationIds' => $data['destinationIds'],
      'options' => $data['options'],
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

  public function actPassWinterQuartersReturnToColoniesStep2SelectStack()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actWinterQuartersReturnToColoniesStep2SelectStack($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesStep2SelectStack');
    $originId = $args['originId'];
    $destinationId = $args['destinationId'];
    $remainingUnitIds = $args['remainingUnitIds'];

    $stateArgs = $this->argsWinterQuartersReturnToColoniesStep2SelectStack();

    if (!isset($stateArgs['options'][$originId])) {
      throw new \feException("ERROR 084");
    }

    $option = $stateArgs['options'][$originId];

    $unitsThatRemain = Utils::filter($option['units'], function ($unit) use ($remainingUnitIds) {
      return in_array($unit->getId(), $remainingUnitIds);
    });

    if ($option['mayRemain']['maxTotal'] !== null && count($unitsThatRemain) > $option['mayRemain']['maxTotal']) {
      throw new \feException("ERROR 085");
    }

    $remainingBrigades = Utils::filter($unitsThatRemain, function ($unit) {
      return $unit->isBrigade();
    });

    if (count($remainingBrigades) > $option['mayRemain']['maxBrigades']) {
      throw new \feException("ERROR 086");
    }

    $player = self::getPlayer();
    if (count($unitsThatRemain) > 0) {
      Notifications::winterQuartersReturnToColoniesLeaveUnits($player, $unitsThatRemain, $option['space']);
    }

    $unitsThatDoNotRemain = Utils::filter($option['units'], function ($unit) use ($remainingUnitIds) {
      return !in_array($unit->getId(), $remainingUnitIds);
    });

    if (count($unitsThatDoNotRemain) > 0) {
      if (!in_array($destinationId, $stateArgs['destinationIds'])) {
        throw new \feException("ERROR 087");
      }
      $destination = $destinationId !== SAIL_BOX ? Spaces::get($destinationId) : null;

      Units::move(BTHelpers::returnIds($unitsThatDoNotRemain), $destinationId);
      Notifications::moveStack($player, $unitsThatDoNotRemain, [], $option['space'], $destination, null, false, $destinationId === SAIL_BOX);
    }

    $origin = Spaces::get($originId);
    GameMap::loseControlCheck($player, $origin);
    $this->removeBattleMarkerCheck($player, $origin);

    $faction = $this->ctx->getInfo()['faction'];
    $playerId = $this->ctx->getPlayerId();

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $option['units']);

    $this->ctx->getParent()->pushChild(new LeafNode([
      'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK,
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

  private function removeBattleMarkerCheck($player, $space)
  {
    if ($space->getBattle() === 1) {
      $space->setBattle(1);
      Notifications::battleRemoveMarker($player, $space);
    }
  }

  private function getDestinationIds($spaces, $faction)
  {
    $destinations = [];

    foreach ($spaces as $spaceId => $space) {
      if (
        $space->isHomeSpace($faction) &&
        $space->isCoastal() &&
        $space->getControl() === $faction &&
        $space->getColony() !== null
      ) {
        $destinations[] = $space;
      }
    }

    if (count($destinations) > 0) {
      return BTHelpers::returnSpaceIds($destinations);
    }

    return BTHelpers::getSpacesBasedOnFleetRetreatPriorities($faction)['spaceIds'];
  }

  public function getOptions()
  {
    $faction = $this->ctx->getInfo()['faction'];
    $units = Units::getAll()->toArray();

    $alreadyResolved = $this->ctx->getParent()->getResolvedActions([WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK]);

    $resolvedUnitIds = [];

    foreach ($alreadyResolved as $node) {
      $resArgs = $node->getActionResolutionArgs();
      $unitIds = $resArgs['unitIds'];
      $resolvedUnitIds = array_merge($resolvedUnitIds, $unitIds);
    }

    $unitsThatMightNeedToReturn = Utils::filter($units, function ($unit) use ($resolvedUnitIds, $faction) {
      // Forts don't move. Indians and Colonial Brigades have already been resolved.
      return $unit->getFaction() === $faction &&
        !$unit->isFort() &&
        !$unit->isIndian() &&
        !$unit->isColonialBrigade() &&
        !in_array($unit->getId(), $resolvedUnitIds);
    });
    $spaces = Spaces::getAll();

    $stacks = GameMap::getStacks($spaces, $unitsThatMightNeedToReturn)[$faction];

    $options = [];

    $destinationIds = $this->getDestinationIds($spaces, $faction);

    foreach ($stacks as $spaceId => $stack) {
      $space = $stack['space'];
      if (!$space->isHomeSpace($faction) && $space->isCoastal()) {
        $options[$spaceId] = [
          'space' => $stack['space'],
          'units' => $stack['units'],
          'mayRemain' => $this->getUnitsThatCanRemainOnSpace($stack['space'], $units, $stack['units'], $faction)
        ];
      }
    }

    return [
      'destinationIds' => $destinationIds,
      'options' => $options,
    ];
  }
}
