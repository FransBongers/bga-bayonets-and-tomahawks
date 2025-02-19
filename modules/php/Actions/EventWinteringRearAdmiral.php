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
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class EventWinteringRearAdmiral extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_EVENT_WINTERING_REAR_ADMIRAL;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventWinteringRearAdmiral() {}

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

  public function stEventWinteringRearAdmiral() {
    $fleets = Utils::filter(Units::getInLocation(POOL_FLEETS)->toArray(), function ($unit) {
      return $unit->getFaction() === BRITISH;
    });
    if (count($fleets) === 0) {
      $this->resolveAction(['automatic' => true]);
    }
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventWinteringRearAdmiral()
  {

    $friendlySeaZones = GameMap::getFriendlySeaZones(BRITISH);

    $possibleSpaces = Utils::filter(Spaces::getControlledBy(BRITISH), function ($space) use ($friendlySeaZones) {
      return $space->isCoastal() && Utils::array_some($space->getAdjacentSeaZones(), function ($seaZone) use ($friendlySeaZones) {
        return in_array($seaZone, $friendlySeaZones);
      });
    });

    return [
      'fleets' => Utils::filter(Units::getInLocation(POOL_FLEETS)->toArray(), function ($unit) {
        return $unit->getFaction() === BRITISH;
      }),
      'spaces' => $possibleSpaces,
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

  public function actPassEventWinteringRearAdmiral()
  {
    $player = self::getPlayer();

    $this->resolveAction(PASS);
  }

  public function actEventWinteringRearAdmiral($args)
  {
    self::checkAction('actEventWinteringRearAdmiral');
    $spaceId = $args['spaceId'];
    $unitId = $args['unitId'];

    $stateArgs = $this->argsEventWinteringRearAdmiral();

    // if($args['skip'] && count($stateArgs['fleets']) === 0) {
    //   $this->resolveAction($args);
    //   return;
    // }

    $unit = Utils::array_find($stateArgs['fleets'], function ($unit) use ($unitId) {
      return $unitId === $unit->getId();
    });

    if ($unit === null) {
      throw new \feException("ERROR 072");
    }

    $space = Utils::array_find($stateArgs['spaces'], function ($space) use ($spaceId) {
      return $spaceId === $space->getId();
    });

    if ($space === null) {
      throw new \feException("ERROR 073");
    }

    $unit->setLocation($space->getId());
    Globals::setWinteringRearAdmiralPlayed(true);
    
    $player = self::getPlayer();

    Notifications::placeUnits($player, [$unit], $space, BRITISH);
    
    $markers = Markers::getInLocation(Locations::stackMarker($space->getId(), BRITISH))->toArray();
    foreach($markers as $marker) {
      if (in_array($marker->getType(), [ROUT_MARKER, OUT_OF_SUPPLY_MARKER])) {
        $marker->remove($player);
      }
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

}
