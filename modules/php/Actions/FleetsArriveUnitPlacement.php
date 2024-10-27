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

class FleetsArriveUnitPlacement extends \BayonetsAndTomahawks\Actions\LogisticsRounds
{
  public function getState()
  {
    return ST_FLEETS_ARRIVE_UNIT_PLACEMENT;
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

  public function stFleetsArriveUnitPlacement()
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

  public function stPreFleetsArriveUnitPlacement()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsFleetsArriveUnitPlacement()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $fleets = Utils::filter(Units::getInLocation(REINFORCEMENTS_FLEETS)->toArray(), function ($unit) use ($faction) {
      return $unit->getFaction() === $faction;
    });
    $units = Utils::filter(Units::getInLocation($faction === BRITISH ? REINFORCEMENTS_BRITISH : REINFORCEMENTS_FRENCH)->toArray(), function ($unit) {
      return !$unit->isCommander();
    });

    $friendlySeaZones = GameMap::getFriendlySeaZones($faction);

    $possibleSpaces = Utils::filter(Spaces::getControlledBy($faction), function ($space) use ($friendlySeaZones) {
      return $space->getHomeSpace() !== null && $space->isCoastal() && Utils::array_some($space->getAdjacentSeaZones(), function ($seaZone) use ($friendlySeaZones) {
        return in_array($seaZone, $friendlySeaZones);
      });
    });

    if (count($possibleSpaces) === 0) {
      // Follow Fleet Retreat Priorities
    }

    $commanderDrawAction = Utils::filter(Engine::getResolvedActions([FLEETS_ARRIVE_COMMANDER_DRAW]), function ($action) use ($faction) {
      return $action->getInfo()['faction'] === $faction;
    })[0];

    $commandersPerUnit = isset($commanderDrawAction->getInfo()['commanders']) ? $commanderDrawAction->getInfo()['commanders'] : [];

    $commanders = Units::getMany(array_values($commandersPerUnit));

    return [
      'fleets' => $fleets,
      'spaces' => $possibleSpaces,
      'units' => $units,
      'commandersPerUnit' => $commandersPerUnit,
      'commanders' => $commanders,
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

  public function actPassFleetsArriveUnitPlacement()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actFleetsArriveUnitPlacement($args)
  {
    self::checkAction('actFleetsArriveUnitPlacement');

    $placedFleets = $args['placedFleets'];
    $placedUnits = $args['placedUnits'];

    $stateArgs = $this->argsFleetsArriveUnitPlacement();

    $fleets = $stateArgs['fleets'];
    $spaces = $stateArgs['spaces'];


    // Check if all fleets have been placed
    foreach ($fleets as $fleet) {
      if (!isset($placedFleets[$fleet->getId()])) {
        throw new \feException("ERROR 020");
      }
    }

    $fleetsPerSpace = [];

    // Place fleets
    foreach ($placedFleets as $fleetId => $spaceId) {
      $fleet = Utils::array_find($fleets, function ($unit) use ($fleetId) {
        return $unit->getId() === $fleetId;
      });
      if ($fleet === null) {
        throw new \feException("ERROR 021");
      }
      if (isset($fleetsPerSpace[$spaceId])) {
        $fleetsPerSpace[$spaceId]['units'][] = $fleet;
        continue;
      }

      $space = Utils::array_find($spaces, function ($optionSpace) use ($spaceId) {
        return $optionSpace->getId() === $spaceId;
      });
      if ($space === null) {
        throw new \feException("ERROR 022");
      }
      $fleetsPerSpace[$spaceId] = [
        'space' => $space,
        'units' => [$fleet]
      ];
    }


    $faction = $this->ctx->getInfo()['faction'];
    $player = self::getPlayer();

    $this->placeUnits($fleetsPerSpace, $player, $faction);

    $units = $stateArgs['units'];
    $commandersPerUnit = $stateArgs['commandersPerUnit'];
    $commanders = $stateArgs['commanders'];

    // Check if all units have been placed
    foreach ($units as $unit) {
      if (!isset($placedUnits[$unit->getId()])) {
        throw new \feException("ERROR 023");
      }
    }

    $allowedSpaceIds = [];
    // Fleets have been placed. All units need to be placed with a fleet
    if (count($fleetsPerSpace) > 0) {
      $allowedSpaceIds = array_map(function ($data) {
        return $data['space']->getId();
      }, $fleetsPerSpace);
    } else {
      $allowedSpaceIds = array_unique(array_values($placedUnits));
    }

    $unitsPerSpace = [];
    // Place units
    foreach ($placedUnits as $unitId => $spaceId) {
      $unit = Utils::array_find($units, function ($optionUnit) use ($unitId) {
        return $optionUnit->getId() === $unitId;
      });
      if ($unit === null) {
        throw new \feException("ERROR 024");
      }
      $commanderId = isset($commandersPerUnit[$unitId]) ? $commandersPerUnit[$unitId] : null;
      $commander = $commanderId !== null ? $commanders[$commanderId] : null;
      if (isset($unitsPerSpace[$spaceId])) {
        $unitsPerSpace[$spaceId]['units'][] = $unit;
        if ($commander !== null) {
          $unitsPerSpace[$spaceId]['units'][] = $commander;
        }
        continue;
      }

      $space = Utils::array_find($spaces, function ($optionSpace) use ($spaceId) {
        return $optionSpace->getId() === $spaceId;
      });
      if ($space === null) {
        throw new \feException("ERROR 025");
      }
      if (!in_array($space->getId(), $allowedSpaceIds)) {
        throw new \feException("ERROR 026");
      }
      $unitsForSpace = [$unit];
      if ($commander !== null) {
        $unitsForSpace[] = $commander;
      }
      $unitsPerSpace[$spaceId] = [
        'space' => $space,
        'units' => $unitsForSpace,
      ];
    }

    $this->placeUnits($unitsPerSpace, $player, $faction);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function placeUnits($unitsPerSpace, $player, $faction)
  {
    $unitsPerSpace = array_values($unitsPerSpace);

    usort($unitsPerSpace, function ($a, $b) {
      return $a['space']->getBattlePriority() - $b['space']->getBattlePriority();
    });

    foreach ($unitsPerSpace as $data) {
      $space = $data['space'];
      $units = $data['units'];
      Units::move(array_map(function ($unit) {
        return $unit->getId();
      }, $units), $space->getId());

      Notifications::placeUnits($player, $units, $space, $faction);
    }
  }
}
