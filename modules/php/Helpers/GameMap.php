<?php

namespace BayonetsAndTomahawks\Helpers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

class GameMap extends \APP_DbObject
{
  public static function getFriendlySeaZones($faction)
  {
    if ($faction === FRENCH) {
      return [ATLANTIC_OCEAN, GULF_OF_SAINT_LAWRENCE];
    }
    $openSeasMarker = Markers::get(OPEN_SEAS_MARKER);
    if ($openSeasMarker->getState() === 0) {
      return [ATLANTIC_OCEAN];
    } else {
      return [ATLANTIC_OCEAN, GULF_OF_SAINT_LAWRENCE];
    }
  }

  public static function performIndianNationControlProcedure($indianNation, $faction)
  {
    if ($indianNation === CHEROKEE) {
      Globals::setControlCherokee($faction);
    } else {
      Globals::setControlIroquois($faction);
    }

    $player = Players::getPlayerForFaction($faction);
    Notifications::indianNationControl($player, $indianNation, $faction);


    $units = Utils::filter(Units::getInLocation(POOL_NEUTRAL_INDIANS)->toArray(), function ($unit) use ($indianNation) {
      return $unit->getCounterId() === $indianNation;
    });
    $indianNationVillages = $units[0]->getVillages();

    foreach ($units as $index => $unit) {
      $space = Spaces::get($indianNationVillages[$index]);
      $unit->setLocation($space->getId());
      if ($faction === BRITISH) {
        $unit->setState(1);
      }
      Notifications::placeUnits($player, [$unit], $space, $faction);
    }
  }
}
