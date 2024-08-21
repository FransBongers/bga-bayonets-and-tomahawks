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
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class Construction extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_CONSTRUCTION;
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

  public function stConstruction()
  {
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreConstruction()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsConstruction()
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

  public function actPassConstruction()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actConstruction($args)
  {
    self::checkAction('actConstruction');
    $activatedUnitId = $args['activatedUnitId'];
    $connectionId = $args['connectionId'];
    $fortOption = $args['fortOption'];

    if ($connectionId === null && $fortOption === null) {
      throw new \feException("ERROR 056");
    } else if ($connectionId !== null && $fortOption !== null) {
      throw new \feException("ERROR 057");
    }

    $stateArgs = $this->argsConstruction();

    $activatedUnit = Utils::array_find($stateArgs['activate'], function ($unit) use ($activatedUnitId) {
      return $unit->getId() === $activatedUnitId;
    });

    if ($activatedUnit === null) {
      throw new \feException("ERROR 058");
    }

    $player = self::getPlayer();
    $space = $stateArgs['space'];
    Notifications::construction($player, $activatedUnit, $space);

    if ($fortOption !== null) {
      $this->fortConstruction($player, $stateArgs, $space, $fortOption, $activatedUnit);
    } else {
      $this->roadConstruction($player, $stateArgs, $space, $connectionId, $activatedUnit);
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

  private function updatePlacedConstructionMarkers($locationId)
  {
    $current = Globals::getPlacedConstructionMarkers();
    $current[] = $locationId;
    Globals::setPlacedConstructionMarkers($current);
  }

  private function fortConstruction($player, $stateArgs, $space, $option, $activatedUnit)
  {
    $fortOptions = $stateArgs['fortOptions'];
    if (!in_array($option, $fortOptions)) {
      throw new \feException("ERROR 060");
    }
    if ($option === PLACE_FORT_CONSTRUCTION_MARKER) {
      $space->setFortConstruction(1);
    } else {
      $space->setFortConstruction(0);
    }
    $faction = $player->getFaction();
    $fort = Utils::array_find($space->getUnits($faction), function ($unit) {
      return $unit->isFort();
    });
    if ($option === REPAIR_FORT) {
      $fort->setReduced(0);
    }
    if ($option === REMOVE_FORT) {
      $fort->setLocation(REMOVED_FROM_PLAY);
    }
    if ($option === REPLACE_FORT_CONSTRUCTION_MARKER) {
      $fort = Units::getTopOf($faction === BRITISH ? POOL_BRITISH_FORTS : POOL_FRENCH_FORTS);
      $fort->setLocation($space->getId());
    }
    Notifications::constructionFort($player, $space, $fort, $faction, $option);
    $activatedUnit->setSpent(1);
    Notifications::addSpentMarkerToUnits($player, [$activatedUnit]);
  }

  private function roadConstruction($player, $stateArgs, $space, $connectionId, $activatedUnit)
  {
    $roadOptions = $stateArgs['roadOptions'];
    $activatedUnitId = $activatedUnit->getId();

    if (!isset($roadOptions[$connectionId])) {
      throw new \feException("ERROR 059");
    }

    $option = $roadOptions[$connectionId];
    $connection = $option['connection'];
    $destinationSpace = $option['space'];

    if ($option['roadOption'] === PLACE_ROAD_CONSTRUCTION_MARKER) {
      $connection->setRoad(ROAD_UNDER_CONTRUCTION);
      $this->updatePlacedConstructionMarkers($connectionId);
    } else {
      $connection->setRoad(HAS_ROAD);
      $this->ctx->insertAsBrother(Engine::buildTree(AtomicActions::get(MOVEMENT)->getFlow(
        CONSTRUCTION,
        $player->getId(),
        $space->getId(),
        $destinationSpace->getId(),
        [$activatedUnitId]
      )));
    }
    Notifications::constructionRoad($player, $connection, $space, $destinationSpace);
    if ($option['roadOption']) {
      $activatedUnit->setSpent(1);
      Notifications::addSpentMarkerToUnits($player, [$activatedUnit]);
    }
  }

  public function getUiData()
  {
    return [
      'id' => CONSTRUCTION,
      'name' => clienttranslate("Construction"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    $markers = Markers::getInLocation(Locations::stackMarker($space->getId(), $playerFaction))->toArray();

    if (Utils::array_some($markers, function ($marker) {
      return in_array($marker->getType(), [ROUT_MARKER, OUT_OF_SUPPLY_MARKER]);
    })) {
      return false;
    }

    $options = $this->getOptions($units, $space, $playerFaction);
    return count($options['activate']) > 0 && count($options['fortOptions']) + count($options['roadOptions']) > 0;
  }

  public function getFlow($actionPointId, $playerId, $originId)
  {
    return [
      'originId' => $originId,
      'children' => [
        [
          'action' => CONSTRUCTION,
          'spaceId' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }

  public function getOptions($unitsInSpace, $space, $playerFaction)
  {
    // Perform supply check at moment of action?
    $spaceInSupply = !$space->hasStackMarker(OUT_OF_SUPPLY_MARKER, $playerFaction);
    $unitsToActivate = Utils::filter($unitsInSpace, function ($unit) use ($spaceInSupply) {
      return $unit->isBrigade() && !$unit->isSpent() && $spaceInSupply;
    });

    // Options
    $fort = Utils::array_find($unitsInSpace, function ($unit) {
      return $unit->isFort();
    });
    $spaceHasFort = $fort !== null;
    $spaceHasFortConstructionMarker = $space->getFortConstruction() === 1;

    $fortOptions = [];
    if (!($spaceHasFort || $spaceHasFortConstructionMarker || $space->hasBastion())) {
      $fortOptions[] = PLACE_FORT_CONSTRUCTION_MARKER;
    }
    if (
      $spaceHasFortConstructionMarker &&
      !in_array($space->getId(), Globals::getPlacedConstructionMarkers()) &&
      Units::countInLocation($playerFaction === BRITISH ? POOL_BRITISH_FORTS : POOL_FRENCH_FORTS) > 0
    ) {
      $fortOptions[] = REPLACE_FORT_CONSTRUCTION_MARKER;
    }
    if ($spaceHasFort && $fort->isReduced()) {
      $fortOptions[] = REPAIR_FORT;
    }
    if ($spaceHasFort) {
      $fortOptions[] = REMOVE_FORT;
    }
    if ($spaceHasFortConstructionMarker) {
      $fortOptions[] = REMOVE_FORT_CONSTRUCTION_MARKER;
    }

    $roadOptions = [];
    $adjacent = $space->getAdjacentConnectionsAndSpaces();

    foreach ($adjacent as $data) {
      $connection = $data['connection'];
      if ($connection->getType() !== PATH) {
        continue;
      }
      if ($connection->getRoad() === 1) {
        $roadOptions[$connection->getId()] = array_merge($data, [
          'roadOption' => FLIP_ROAD_CONSTRUCTION_MARKER
        ]);
      } else {
        $roadOptions[$connection->getId()] = array_merge($data, [
          'roadOption' => PLACE_ROAD_CONSTRUCTION_MARKER
        ]);
      }
    }


    return [
      'activate' => $unitsToActivate,
      'fortOptions' => $fortOptions,
      'fort' => $fort,
      'roadOptions' => $roadOptions,
      // 'units' => $unitsInSpace,
      // 'spaceInSupply' => $spaceInSupply,
    ];
  }
}
