<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class SailMovement extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_SAIL_MOVEMENT;
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

  public function stSailMovement()
  {

    // $this->resolveAction(['automatic' => true], true);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreSailMovement()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsSailMovement()
  {
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $player = self::getPlayer();
    $faction = $player->getFaction();
    $space = Spaces::get($spaceId);
    $units = $space->getUnits($faction);

    return [
      'faction' => $faction,
      'space' => $space,
      'units' => $this->getUnitsThatCanMove($space, $faction, $units),
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

  public function actPassSailMovement()
  {
    $player = self::getPlayer();

    $this->resolveAction(PASS);
  }

  public function actSailMovement($args)
  {
    self::checkAction('actSailMovement');
    $selectedUnitIds = array_values(array_unique($args['selectedUnitIds']));

    $stateArgs = $this->argsSailMovement();

    $units = Utils::filter($stateArgs['units'], function ($unit) use ($selectedUnitIds) {
      return in_array($unit->getId(), $selectedUnitIds);
    });

    if (count($units) !== count($selectedUnitIds)) {
      throw new \feException("ERROR 061");
    }

    $fleetCount = count(Utils::filter($units, function ($unit) {
      return $unit->isFleet();
    }));

    $transportedUnitsCount = count(Utils::filter($units, function ($unit) {
      return !$unit->isFleet() && !$unit->isCommander();
    }));
    if ($fleetCount === 0 || $transportedUnitsCount / $fleetCount > 4) {
      throw new \feException("ERROR 062");
    }

    // Units::move($selectedUnitIds, SAIL_BOX);
    $playerId = $this->ctx->getPlayerId();
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];

    $flow = [
      'children' => [
        [
          'action' => MOVE_STACK,
          'playerId' => $playerId,
          'fromSpaceId' => $spaceId,
          'toSpaceId' => SAIL_BOX,
          'unitIds' => $selectedUnitIds,
        ],
        [
          'action' => MOVEMENT_LOSE_CONTROL_CHECK,
          'playerId' => $playerId,
          'spaceId' => $spaceId,
        ],
      ]
    ];

    if ($info['source'] === SAIL_ARMY_AP_2X) {
      $flow['children'][] =     [
        'action' => ACTION_ROUND_SAIL_BOX_LANDING,
        'playerId' => $playerId,
        'optional' => true,
        'source' => $info['source'],
      ];
    }

    $this->ctx->insertAsBrother(Engine::buildTree($flow));

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getUnitsThatCanMove($space, $faction, $units)
  {
    // Requires fleet
    if (!Utils::array_some($units, function ($unit) {
      return $unit->isFleet();
    })) {
      return [];
    }

    $battleInSpace = $space->getBattle() === 1;

    if ($battleInSpace) {
      $data = GameMap::factionOutnumbersEnemyInSpace($space, $faction);
      if (!$data['outnumbers'] && !($faction === BRITISH && $data['enemyHasBastion'])) {
        return [];
      }
    }

    // TODO: filter units that are locked in battle?
    $unitsThatCanMove = Utils::filter($units, function ($unit) {
      if ($unit->isFort() || $unit->isBastion()) {
        return false;
      }

      return !$unit->isSpent();
    });

    return $unitsThatCanMove;
  }

  public function getUiData()
  {
    return [
      'id' => SAIL_MOVEMENT,
      'name' => clienttranslate("Sail Movement"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    if (Cards::isCardInPlay(FRENCH, ROUGH_SEAS_CARD_ID)) {
      return false;
    }

    return count($this->getUnitsThatCanMove($space, $playerFaction, $units)) > 0;
  }

  public function getFlow($source, $playerId, $originId, $destinationId = null, $requiredUnitIds = [])
  {
    return [
      'originId' => $originId,
      'children' => [
        [
          'action' => SAIL_MOVEMENT,
          'spaceId' => $originId,
          'source' => $source,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
