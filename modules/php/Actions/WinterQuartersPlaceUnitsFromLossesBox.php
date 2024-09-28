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

class WinterQuartersPlaceUnitsFromLossesBox extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_PLACE_UNITS_FROM_LOSSES_BOX;
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

  public function stWinterQuartersPlaceUnitsFromLossesBox()
  {
    $options = $this->getOptions();

    if (count($options) === 0) {
      $this->resolveAction(['automatic' => true]);
    }
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersPlaceUnitsFromLossesBox() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersPlaceUnitsFromLossesBox()
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

  public function actPassWinterQuartersPlaceUnitsFromLossesBox()
  {
    $player = self::getPlayer();

    $this->resolveAction([]);
  }

  public function actWinterQuartersPlaceUnitsFromLossesBox($args)
  {
    self::checkAction('actWinterQuartersPlaceUnitsFromLossesBox');

    $placedUnits = $args['placedUnits'];

    $stateArgs = $this->argsWinterQuartersPlaceUnitsFromLossesBox();

    $unitsPerSpace = [];
    $spaces = Spaces::getAll();

    $highlandBrigadeCount = 0;

    foreach ($stateArgs['options'] as $unitType => $data) {
      if (!isset($placedUnits[$unitType])) {
        throw new \feException("ERROR 091");
      };
      if (count($placedUnits[$unitType]) !== $data['numberToPlace']) {
        throw new \feException("ERROR 092");
      }
      foreach ($placedUnits[$unitType] as $unitId => $spaceId) {
        $unit = Utils::array_find($data['units'], function ($optionUnit) use ($unitId) {
          return $unitId === $optionUnit->getId();
        });
        if ($unit === null) {
          throw new \feException("ERROR 093");
        }
        if (!in_array($spaceId, $data['spaceIds'])) {
          throw new \feException("ERROR 094");
        }
        if ($unitType === METROPOLITAN_BRIGADES && $unit->isHighlandBrigade()) {
          $highlandBrigadeCount++;
        }
        if (isset($unitsPerSpace[$spaceId])) {
          $unitsPerSpace[$spaceId]['units'][] = $unit;
        } else {
          $unitsPerSpace[$spaceId] = [
            'space' => $spaces[$spaceId],
            'units' => [$unit],
          ];
        }
      }
    }

    if ($highlandBrigadeCount > 1) {
      throw new \feException("ERROR 095");
    }

    GameMap::placeUnits($unitsPerSpace, self::getPlayer(), $this->ctx->getInfo()['faction']);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getUnitsOfType($units, $unitType)
  {
    return Utils::filter($units, function ($unit) use ($unitType) {
      if ($unitType === NON_INDIAN_LIGHT) {
        return $unit->isNonIndianLight();
      } else if ($unitType === METROPOLITAN_BRIGADES) {
        return $unit->isMetropolitanBrigade();
      } else if ($unitType === NON_METROPOLITAN_BRIGADES) {
        return $unit->isNonMetropolitanBrigade();
      } else if ($unitType === ARTILLERY) {
        return $unit->isArtillery();
      } else {
        return false;
      }
    });
  }

  private function getOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $units = Utils::filter(Units::getInLocation(Locations::lossesBox($faction))->toArray(), function ($unit) {
      return !$unit->isIndian();
    });

    $friendlySettledHomeSpaceIds = BTHelpers::returnIds(Utils::filter(Spaces::getControlledBy($faction), function ($space) use ($faction) {
      return $space->getHomeSpace() === $faction && $space->getValue() > 1;
    }));

    $options = [];

    foreach ([NON_INDIAN_LIGHT, METROPOLITAN_BRIGADES, NON_METROPOLITAN_BRIGADES, ARTILLERY] as $unitType) {
      $unitsOfType = $this->getUnitsOfType($units, $unitType);
      $numberToPlace = intval(floor(count($unitsOfType) / 3));
      if ($numberToPlace === 0) {
        continue;
      }
      $spaceIds = $unitType === NON_METROPOLITAN_BRIGADES && $faction === BRITISH ?
        [DISBANDED_COLONIAL_BRIGADES] :
        $friendlySettledHomeSpaceIds;

      if (count($spaceIds) === 0) {
        continue;
      }

      $options[$unitType] = [
        'units' => $unitsOfType,
        'numberToPlace' => $numberToPlace,
        'spaceIds' => $spaceIds,
      ];
    }

    return $options;
  }
}
