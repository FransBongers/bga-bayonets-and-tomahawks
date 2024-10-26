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
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class EventSmallpoxInfectedBlankets extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_SMALLPOX_INFECTED_BLANKETS;
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

  public function stEventSmallpoxInfectedBlankets()
  {
    $options = $this->getOptions();
    if (count($options) > 2) {
      return;
    }
    if (count($options) === 0) {
      Notifications::message(clienttranslate('No French-controlled Indian units available on the map'),[]);
    } else {
      $player = self::getPlayer();
      foreach($options as $unit) {
        $unit->placeInLosses($player, FRENCH);
      }
    }
    
    $this->resolveAction(['automatic' => true]);
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventSmallpoxInfectedBlankets()
  {

    return [
      'units' => $this->getOptions(),
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

  public function actPassEventSmallpoxInfectedBlankets()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actEventSmallpoxInfectedBlankets($args)
  {
    self::checkAction('actEventSmallpoxInfectedBlankets');

    $selectedUnitIds = $args['selectedUnitIds'];

    $units = $this->getOptions();

    $requiredUnits = min(2, count($units));
    if (count($selectedUnitIds) !== $requiredUnits) {
      throw new \feException("ERROR 041");
    }

    $player = self::getPlayer();
    foreach ($selectedUnitIds as $unitId) {
      $unit = Utils::array_find($units, function ($optionUnit) use ($unitId) {
        return $unitId === $optionUnit->getId();
      });
      if ($unit === null) {
        throw new \feException("ERROR 042");
      }
      $unit->placeInLosses($player, FRENCH);
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

  private function getOptions()
  {
    $units = Utils::filter(Units::getAll()->toArray(), function ($unit) {
      return $unit->isIndian() && $unit->getFaction() === FRENCH && in_array($unit->getLocation(), SPACES);
    });
    return $units;
  }
}
