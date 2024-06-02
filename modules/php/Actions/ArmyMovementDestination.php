<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class ArmyMovementDestination extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_ARMY_MOVEMENT_DESTINATION;
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

  public function stArmyMovementDestination()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreArmyMovementDestination()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsArmyMovementDestination()
  {
    $info = $this->ctx->getInfo();
    // // $parent = $this->ctx->getParent();
    // // $parentInfo = $parent->getInfo();
    // // $actionPointId = $parent->getParent()->getInfo()['actionPointId'];

    // // $resolved = $parent->getResolvedActions([ARMY_MOVEMENT]);
    $player = self::getPlayer();

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();
    // $unitsOnSpace = $space->getUnits($playerFaction);
    $adjacentSpaces = $space->getAdjacentSpaces();
    $unitIds = $info['unitIds'];

    $units = Units::getMany($unitIds)->toArray();

    // foreach ($unitsOnSpace as $unit) {
    //   // TODO filter units that cannot move
    //   if ($unit->getType() === FORT || $unit->getType() === BASTION) {
    //     continue;
    //   }

    //   $units[] = $unit;
    // }

    $destinations = [];

    $requiresCoastalConnection = Utils::array_some($units, function ($unit) {
      return $unit->getType() === FLEET;
    });

    $requiresRoadOrHighway = Utils::array_some($units, function ($unit) {
      return in_array($unit->getType(), [ARTILLERY, BRIGADE, COMMANDER]);
    });

    foreach ($adjacentSpaces as $targetSpaceId => $connection) {
      if ($requiresRoadOrHighway && $connection->getType() === PATH) {
        continue;
      }
      if ($requiresCoastalConnection && !$connection->isCoastalConnection()) {
        continue;
      }

      $remainingConnectionLimit = $connection->getLimit() - $connection->getLimitUsed($playerFaction);

      if ($remainingConnectionLimit < count($unitIds)) {
        // TODO: check roads and artillery limit
        continue;
      }

      $destinations[$targetSpaceId] = [
        'space' => Spaces::get($targetSpaceId),
        'connection' => $connection,
      ];
    }

    return [
      'units' => $units,
      // 'origin' => $space,
      'destinations' => $destinations,
      'faction' => $playerFaction,
      // 'numberResolved' => count($resolved),
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

  public function actPassArmyMovementDestination()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actArmyMovementDestination($args)
  {
    self::checkAction('actArmyMovementDestination');

    // $unitIds = $args['unitIds'];
    $destinationId = $args['spaceId'];
    $destination = Spaces::get($destinationId);
    Notifications::log('args', $args);
    $player = self::getPlayer();


    $stateArgs = $this->argsArmyMovementDestination();
    if (!isset($stateArgs['destinations'][$destinationId])) {
      throw new \feException("ERROR 009");
    }
  
    $info = $this->ctx->getInfo();
    $origin = Spaces::get($info['spaceId']);
    $unitIds = $info['unitIds'];
    $units = Units::getMany($unitIds)->toArray();

    Units::move($unitIds, $destinationId);


    Notifications::moveStack(self::getPlayer(), $units, $origin, $destination);

    // Check if origin was Settled space and is now empty of enemy units
    $this->loseControlCheck($player, $origin);

    // Check if players takes control of empty enemy controlled outpost
    $this->takeControlCheck($player, $destination);

    $enemyUnitsAndOverwhelm = $this->checkEnemyUnitsAndOverwhelm($destination, $player);
    // Notifications::log('enemyUnitsAndOverwhelm', $enemyUnitsAndOverwhelm);
    // $hasEnemyUnits = $enemyUnitsAndOverwhelm['hasEnemyUnits'];
    // $overwhelm = $enemyUnitsAndOverwhelm['overwhelm'];
    $battleOccurs = $enemyUnitsAndOverwhelm['battleOccurs'];

    // // $battle = $hasEnemyUnits && !$overwhelm;
    // // Add battle marker


    $resolvedMoves = count($this->ctx->getParent()->getResolvedActions([ARMY_MOVEMENT_DESTINATION]));
    if (!$battleOccurs && $resolvedMoves < 2) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => ARMY_MOVEMENT,
        'spaceId' => $destinationId,
        'playerId' => self::getPlayer()->getId(),
        'optional' => true,
      ]));
    }

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getUnitsThatCanMove()
  {
    $info = $this->ctx->getInfo();
    $player = self::getPlayer();

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $playerFaction = $player->getFaction();

    $unitsOnSpace = $space->getUnits($playerFaction);
    $units = [];

    foreach ($unitsOnSpace as $unit) {
      // TODO filter units that cannot move
      if ($unit->getType() === FORT || $unit->getType() === BASTION) {
        continue;
      }

      $units[] = $unit;
    }

    return $units;
  }

  // public function getUiData()
  // {
  //   return [
  //     'id' => ARMY_MOVEMENT,
  //     'name' => clienttranslate("Army Movement"),
  //   ];
  // }

  // public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  // {
  //   return count($units) > 0 && Utils::array_some($units, function ($unit) {
  //     return $unit->getType() !== FORT && $unit->getType() !== BASTION;
  //   });
  // }

  // public function getFlow($playerId, $originId)
  // {
  //   return [
  //     'originId' => $originId,
  //     'children' => [
  //       [
  //         'action' => ARMY_MOVEMENT,
  //         'spaceId' => $originId,
  //         'playerId' => $playerId,
  //       ],
  //     ],
  //   ];
  // }
}
