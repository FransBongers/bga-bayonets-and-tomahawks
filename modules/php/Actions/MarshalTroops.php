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
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class MarshalTroops extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_MARSHAL_TROOPS;
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

  public function stMarshalTroops()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreMarshalTroops()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsMarshalTroops()
  {
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $player = self::getPlayer();
    $faction = $player->getFaction();

    $space = Spaces::get($spaceId);
    $units = $space->getUnits($faction);

    return array_merge($this->getOptions($units, $space, $faction), [
      'faction' => $faction,
      'space' => $space,
    ]);
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

  public function actPassMarshalTroops()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actMarshalTroops($args)
  {
    self::checkAction('actMarshalTroops');
    $activatedUnitId = $args['activatedUnitId'];
    $marshalledUnitIds = $args['marshalledUnitIds'];

    $stateArgs = $this->argsMarshalTroops();

    $activatedUnit = Utils::array_find($stateArgs['activate'], function ($unit) use ($activatedUnitId) {
      return $unit->getId() === $activatedUnitId;
    });

    if ($activatedUnit === null) {
      throw new \feException("ERROR 052");
    }

    $activatedUnit->setSpent(1);

    $spaces = Spaces::getMany(array_keys($marshalledUnitIds))->toArray();
    $player = self::getPlayer();
    $playerId = $player->getId();

    $targetSpace = Spaces::get($this->ctx->getInfo()['spaceId']);

    $moveActions = [];

    foreach ($marshalledUnitIds as $spaceId => $unitIds) {
      $space = Utils::array_find($spaces, function ($spaceOption) use ($spaceId) {
        return $spaceId === $spaceOption->getId();
      });
      if ($space === null) {
        throw new \feException("ERROR 053");
      }

      // TODO: account for fleets / commanders not counting in limit?
      if (count($unitIds) > $stateArgs['marshal'][$spaceId]['remainingLimit']) {
        throw new \feException("ERROR 054");
      }

      $units = [];
      foreach ($unitIds as $unitId) {
        $unit = Utils::array_find($stateArgs['marshal'][$spaceId]['units'], function ($unitOption) use ($unitId) {
          return $unitId === $unitOption->getId();
        });
        if ($unit === null) {
          throw new \feException("ERROR 055");
        }
        $units[] = $unit;
      }
      $moveActions[] = [
        'action' => MOVE_STACK,
        'playerId' => $playerId,
        'fromSpaceId' => $space->getId(),
        'toSpaceId' => $targetSpace->getId(),
        'unitIds' => $unitIds,
        'connectionId' => $stateArgs['marshal'][$spaceId]['connection']->getId(),
      ];
      $moveActions[] = [
        'action' => MOVEMENT_LOSE_CONTROL_CHECK,
        'playerId' => $player->getId(),
        'spaceId' => $space->getId(),
      ];
    }

    $this->ctx->insertAsBrother(Engine::buildTree([
      'children' => array_merge($moveActions, [
        [
          'action' => MOVEMENT_PLACE_SPENT_MARKERS,
          'playerId' => $playerId,
        ],
        [
          'action' => PLACE_MARKER_ON_STACK,
          'playerId' => $playerId,
          'markerType' => MARSHAL_TROOPS_MARKER,
          'spaceId' => $targetSpace->getId(),
          'faction' => $player->getFaction(),
        ]
      ])
    ]));

    Notifications::marshalTroops($player, $activatedUnit, $targetSpace);



    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getUiData()
  {
    return [
      'id' => MARSHAL_TROOPS,
      'name' => clienttranslate("Marshal Troops"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    $markers = Markers::getInLocation(Locations::stackMarker($space->getId(), $playerFaction))->toArray();

    if (Utils::array_some($markers, function ($marker) {
      return in_array($marker->getType(), [ROUT_MARKER]);
    })) {
      return false;
    }

    $options = $this->getOptions($units, $space, $playerFaction);
    return count($options['activate']) > 0 && count($options['marshal']);
  }

  public function getFlow($actionPointId, $playerId, $originId)
  {
    return [
      'originId' => $originId,
      'children' => [
        [
          'action' => MARSHAL_TROOPS,
          'spaceId' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }

  public function getOptions($unitsInSpace, $space, $playerFaction)
  {
    $roughSeasActive = Cards::isCardInPlay(FRENCH, ROUGH_SEAS_CARD_ID);

    $unitsToActivate = Utils::filter($unitsInSpace, function ($unit) use ($roughSeasActive) {
      if ($roughSeasActive && $unit->isFleet()) {
        return false;
      }
      return $unit->getType() !== LIGHT && !$unit->isSpent();
    });

    $adjacent = $space->getAdjacentConnectionsAndSpaces();
    $marshall = [];

    foreach ($adjacent as $data) {
      $adjacentSpace = $data['space'];
      if ($adjacentSpace->getBattle() === 1) {
        continue;
      }
      $connection = $data['connection'];

      $adjacentUnits = Utils::filter($adjacentSpace->getUnits($playerFaction), function ($unit) use ($connection, $roughSeasActive) {
        if ($roughSeasActive && $unit->isFleet()) {
          return false;
        }
        if ($unit->isIndian() && Globals::getNoIndianUnitMayBeActivated()) {
          return false;
        }
        return !$unit->isSpent() && !$unit->isFort() && $connection->canBeUsedByUnit($unit, true);
      });
      $remainingLimit = $connection->getRemainingLimit($playerFaction);

      // TODO: commanders and fleets do not count for limits
      // What if we have a commander with fleet only and limit = 0?
      if (count($adjacentUnits) > 0 && $remainingLimit > 0) {
        $marshall[$adjacentSpace->getId()] = [
          'units' => $adjacentUnits,
          'connection' => $connection,
          'remainingLimit' => $remainingLimit,
        ];
      }
    }

    return [
      'activate' => $unitsToActivate,
      'marshal' => $marshall,
    ];
  }
}
