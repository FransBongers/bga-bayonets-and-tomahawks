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

class ColonialsEnlistUnitPlacement extends \BayonetsAndTomahawks\Actions\LogisticsRounds
{
  public function getState()
  {
    return ST_COLONIALS_ENLIST_UNIT_PLACEMENT;
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

  public function stColonialsEnlistUnitPlacement()
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

  public function stPreColonialsEnlistUnitPlacement()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsColonialsEnlistUnitPlacement()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $units = array_merge(Units::getInLocation(REINFORCEMENTS_COLONIAL)->toArray(), Units::getInLocation(DISBANDED_COLONIAL_BRIGADES)->toArray());

    $spaces = Utils::filter(Spaces::getControlledBy($faction), function ($space) {
      return $space->getHomeSpace() === BRITISH && $space->getColony() !== null;
    });


    return [
      'spaces' => $spaces,
      'units' => $units,
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

  public function actPassColonialsEnlistUnitPlacement()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actColonialsEnlistUnitPlacement($args)
  {
    self::checkAction('actColonialsEnlistUnitPlacement');

    $placedUnits = $args['placedUnits'];

    $stateArgs = $this->argsColonialsEnlistUnitPlacement();


    $faction = $this->ctx->getInfo()['faction'];
    $player = self::getPlayer();

    $spaces = $stateArgs['spaces'];
    $units = $stateArgs['units'];

    // Check if all units have been placed
    foreach ($units as $unit) {
      if (!isset($placedUnits[$unit->getId()])) {
        throw new \feException("ERROR 027");
      }
    }


    $unitsPerSpace = [];
    foreach ($placedUnits as $unitId => $spaceId) {
      $unit = Utils::array_find($units, function ($optionUnit) use ($unitId) {
        return $optionUnit->getId() === $unitId;
      });
      if ($unit === null) {
        throw new \feException("ERROR 028");
      }

      $space = Utils::array_find($spaces, function ($optionSpace) use ($spaceId) {
        return $optionSpace->getId() === $spaceId;
      });
      if ($space === null) {
        throw new \feException("ERROR 029");
      }
      if ($unit->isBrigade() && $space->getColony() !== $unit->getColony()) {
        throw new \feException("ERROR 030");
      }

      if (isset($unitsPerSpace[$spaceId])) {
        $unitsPerSpace[$spaceId]['units'][] = $unit;
      } else {
        $unitsPerSpace[$spaceId] = [
          'space' => $space,
          'units' => [$unit],
        ];
      }
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
