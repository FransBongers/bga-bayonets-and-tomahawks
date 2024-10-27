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

    $constructionFrenzy = isset($info['constructionFrenzy']) && $info['constructionFrenzy'];

    return [
      'faction' => $faction,
      'options' => $this->getOptions($faction, $constructionFrenzy, $spaceId),
      'constructionFrenzy' => $constructionFrenzy,
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

  public function actPassConstruction()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actConstruction($args)
  {
    self::checkAction('actConstruction');
    $activatedUnitId = $args['activatedUnitId'];
    $connectionId = $args['connectionId'];
    $fortOption = $args['fortOption'];
    $spaceId = $args['spaceId'];

    if ($connectionId === null && $fortOption === null) {
      throw new \feException("ERROR 056");
    } else if ($connectionId !== null && $fortOption !== null) {
      throw new \feException("ERROR 057");
    }

    $stateArgs = $this->argsConstruction();

    if (!isset($stateArgs['options'][$spaceId])) {
      throw new \feException("ERROR 005");
    }

    $option = $stateArgs['options'][$spaceId];

    $activatedUnit = Utils::array_find($option['activate'], function ($unit) use ($activatedUnitId) {
      return $unit->getId() === $activatedUnitId;
    });

    if ($activatedUnit === null) {
      throw new \feException("ERROR 058");
    }

    $player = self::getPlayer();
    $faction = $player->getFaction();
    $space = $option['space'];
    Notifications::construction($player, $activatedUnit, $space);

    if ($fortOption !== null) {
      $this->fortConstruction($player, $option, $space, $fortOption, $activatedUnit);
    } else {
      $this->roadConstruction($player, $option, $space, $connectionId, $activatedUnit);
    }

    $britishConstructionFrenzy = $faction === BRITISH && Cards::isCardInPlay(BRITISH, BRITISH_CONSTRUCTION_FRENZY_CARD_ID) && Globals::getUsedEventCount(BRITISH) === 0;
    $frenchConstructionFrenzy = $faction === FRENCH && Cards::isCardInPlay(FRENCH, FRENCH_CONSTRUCTION_FRENZY_CARD_ID) && Globals::getUsedEventCount(FRENCH) === 0;
    if (!$stateArgs['constructionFrenzy'] && ($britishConstructionFrenzy || $frenchConstructionFrenzy)) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => CONSTRUCTION,
        'playerId' => $player->getId(),
        'activatedUnitId' => $activatedUnit->getId(),
        'spaceId' => $spaceId,
        'optional' => true,
        'constructionFrenzy' => true,

      ]));
    }
    if ($stateArgs['constructionFrenzy']) {
      Globals::setUsedEventCount($faction, 1);
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



  private function updatePlacedConstructionMarkers($locationId, $constructionOption)
  {
    $current = Globals::getPlacedConstructionMarkers();
    if (isset($current[$locationId])) {
      $current[$locationId] = [$constructionOption];
    } else {
      $current[$locationId][] = $constructionOption;
    }

    Globals::setPlacedConstructionMarkers($current);
  }

  private function fortConstruction($player, $option, $space, $fortOption, $activatedUnit)
  {
    $fortOptions = $option['fortOptions'];
    if (!in_array($fortOption, $fortOptions)) {
      throw new \feException("ERROR 060");
    }

    if ($fortOption === PLACE_FORT_CONSTRUCTION_MARKER) {
      $this->updatePlacedConstructionMarkers($space->getId(), PLACE_FORT_CONSTRUCTION_MARKER);
      $space->setFortConstruction(1);
    } else {
      $space->setFortConstruction(0);
    }
    $faction = $player->getFaction();
    $fort = Utils::array_find($space->getUnits($faction), function ($unit) {
      return $unit->isFort();
    });
    if ($fortOption === REPAIR_FORT) {
      $fort->setReduced(0);
    }
    if ($fortOption === REMOVE_FORT) {
      $fort->setLocation(REMOVED_FROM_PLAY);
    }
    if ($fortOption === REPLACE_FORT_CONSTRUCTION_MARKER) {
      $fort = Units::getTopOf($faction === BRITISH ? POOL_BRITISH_FORTS : POOL_FRENCH_FORTS);
      $fort->setLocation($space->getId());
    }
    Notifications::constructionFort($player, $space, $fort, $faction, $fortOption);
    $activatedUnit->setSpent(1);
    Notifications::addSpentMarkerToUnits($player, [$activatedUnit]);
  }

  private function roadConstruction($player, $option, $space, $connectionId, $activatedUnit)
  {
    $roadOptions = $option['roadOptions'];
    $activatedUnitId = $activatedUnit->getId();

    if (!isset($roadOptions[$connectionId])) {
      throw new \feException("ERROR 059");
    }

    $option = $roadOptions[$connectionId];
    $connection = $option['connection'];
    $destinationSpace = $option['space'];

    if ($option['roadOption'] === PLACE_ROAD_CONSTRUCTION_MARKER) {
      $connection->setRoad(ROAD_UNDER_CONTRUCTION);
      $this->updatePlacedConstructionMarkers($connectionId, PLACE_ROAD_CONSTRUCTION_MARKER);
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
    if ($option['roadOption'] === PLACE_ROAD_CONSTRUCTION_MARKER) {
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

    $options = $this->getOptions($playerFaction, false, $space->getId(), $units, $markers);
    $canBePerformed = false;
    foreach ($options as $spaceId => $option) {
      if (count($option['activate']) > 0 && count($option['fortOptions']) + count($option['roadOptions']) > 0) {
        $canBePerformed = true;
        break;
      };
    }

    return $canBePerformed;
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

  public function getOptions($faction, $constructionFrenzy, $spaceId, $units = null, $markers = null)
  {
    // Construction Frenzy can activate a unit twice
    $constructionFrenzyUnitId = null;
    if ($constructionFrenzy) {
      $constructionFrenzyUnitId = $this->ctx->getInfo()['activatedUnitId'];
    }
    $placedConstructionMarkers = Globals::getPlacedConstructionMarkers();

    $units = $units === null ? Units::getAll()->toArray() : $units;
    $spaceIds = [$spaceId];
    $spaces = [];
    if ($constructionFrenzy) {
      $stacks = GameMap::getStacks(null, $units);
      $spaceIds = array_keys($stacks[$faction]);
    }
    $spaces = Spaces::getMany($spaceIds);

    $markers = $markers === null ? Markers::getAll()->toArray() : $markers;

    $result = [];

    foreach ($spaces as $spaceId => $space) {
      // Perform supply check at moment of action?
      $spaceHasRoutOrOOSMarker = Utils::array_some($markers, function ($marker) use ($spaceId, $faction) {
        return $marker->getLocation() === Locations::stackMarker($spaceId, $faction) && in_array($marker->getType(), [ROUT_MARKER, OUT_OF_SUPPLY_MARKER]);
      });
      if ($spaceHasRoutOrOOSMarker) {
        continue;
      }
      $unitsToActivate = Utils::filter($units, function ($unit) use ($spaceId, $constructionFrenzyUnitId) {
        return $unit->getLocation() === $spaceId && $unit->isBrigade() && (!$unit->isSpent() || $unit->getId() === $constructionFrenzyUnitId);
      });
      $canDoubleConstruct = $constructionFrenzyUnitId !== null && Utils::array_some($unitsToActivate, function ($unit) use ($constructionFrenzyUnitId) {
        return $unit->getId() === $constructionFrenzyUnitId;
      });

      if (count($unitsToActivate) === 0) {
        continue;
      }
      $outnumberInfo = GameMap::factionOutnumbersEnemyInSpace($space, $faction);
      if ($outnumberInfo['hasEnemyUnitsExcludingMilitia'] && !$outnumberInfo['outnumbers']) {
        continue;
      }

      // Options
      $fort = Utils::array_find($units, function ($unit) use ($spaceId) {
        return $unit->getLocation() === $spaceId && $unit->isFort();
      });
      $spaceHasFort = $fort !== null;
      $spaceHasFortConstructionMarker = $space->getFortConstruction() === 1;

      $fortOptions = [];
      if (!($spaceHasFort || $spaceHasFortConstructionMarker || $space->hasBastion())) {
        $fortOptions[] = PLACE_FORT_CONSTRUCTION_MARKER;
      }
      if (
        $spaceHasFortConstructionMarker &&
        (!in_array(PLACE_FORT_CONSTRUCTION_MARKER, isset($placedConstructionMarkers[$space->getId()]) ? $placedConstructionMarkers[$space->getId()] : []) ||
          $canDoubleConstruct) &&
        Units::countInLocation($faction === BRITISH ? POOL_BRITISH_FORTS : POOL_FRENCH_FORTS) > 0
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
        if (
          $connection->getRoad() === 1 &&
          (!in_array(PLACE_ROAD_CONSTRUCTION_MARKER, isset($placedConstructionMarkers[$connection->getId()]) ? $placedConstructionMarkers[$connection->getId()] : []) || $canDoubleConstruct)
        ) {
          $roadOptions[$connection->getId()] = array_merge($data, [
            'roadOption' => FLIP_ROAD_CONSTRUCTION_MARKER
          ]);
        }
        if ($connection->getRoad() === 0) {
          $roadOptions[$connection->getId()] = array_merge($data, [
            'roadOption' => PLACE_ROAD_CONSTRUCTION_MARKER
          ]);
        }
      }

      $result[$spaceId] = [
        'activate' => $unitsToActivate,
        'fortOptions' => $fortOptions,
        'fort' => $fort,
        'roadOptions' => $roadOptions,
        'space' => $space,
      ];
    }

    return $result;
  }
}
