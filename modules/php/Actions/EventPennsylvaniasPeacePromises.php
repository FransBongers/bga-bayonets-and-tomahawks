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

class EventPennsylvaniasPeacePromises extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_PENNSYLVANIAS_PEACE_PROMISES;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventPennsylvaniasPeacePromises()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventPennsylvaniasPeacePromises()
  {

    // Notifications::log('argsEventPennsylvaniasPeacePromises',[]);
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

  public function actPassEventPennsylvaniasPeacePromises()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actEventPennsylvaniasPeacePromises($args)
  {
    self::checkAction('actEventPennsylvaniasPeacePromises');

    $selectedUnitIds = $args['selectedUnitIds'];

    $units = $this->getOptions();

    $requiredUnits = min(2, count($units));
    if (count($selectedUnitIds) !== $requiredUnits) {
      throw new \feException("ERROR 032");
    }

    $player = self::getPlayer();
    foreach ($selectedUnitIds as $unitId) {
      $unit = Utils::array_find($units, function ($optionUnit) use ($unitId) {
        return $unitId === $optionUnit->getId();
      });
      if ($unit === null) {
        throw new \feException("ERROR 033");
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

  public function canBeResolved()
  {
    $spaces = Spaces::getMany([DIIOHAGE, FORKS_OF_THE_OHIO, KITHANINK])->toArray();

    if (Utils::array_some($spaces, function ($space) {
      return $space->getControl() === BRITISH || $space->getRaided() === BRITISH;
    })) {
      return false;
    }
    return true;
  }

  private function getOptions()
  {
    $units = Utils::filter(Units::getAll()->toArray(), function ($unit) {
      return in_array($unit->getCounterId(), [MINGO, DELAWARE, CHAOUANON]) && $unit->getLocation() !== Locations::lossesBox(FRENCH);
    });
    return $units;
  }
}
