<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Models\Player;

class EventDiseaseInBritishCamp extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_DISEASE_IN_BRITISH_CAMP;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventDiseaseInBritishCamp()
  {
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

  public function stEventDiseaseInFrenchCamp()
  {
    $result = $this->getOptions();

    if (count(array_merge($result['brigades'], $result['colonialBrigades'], $result['metropolitanBrigades'])) === 0) {
      Notifications::message(_('No British Brigade to eliminate'),[]);
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

  public function argsEventDiseaseInBritishCamp()
  {

    return  $this->getOptions();
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

  public function actPassEventDiseaseInBritishCamp()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actEventDiseaseInBritishCamp($args)
  {
    self::checkAction('actEventDiseaseInBritishCamp');
    $selectedUnitIds = $args['selectedUnitIds'];

    $options = $this->getOptions();

    $year = $options['year'];

    if ($year <= 1756 && count($selectedUnitIds) !== 1) {
      throw new \feException("ERROR 043");
    } else if ($year >= 1757 && count($selectedUnitIds) !== 2) {
      throw new \feException("ERROR 044");
    }

    $units = $year <= 1756 ?
      $options['brigades'] :
      array_merge($options['colonialBrigades'], $options['metropolitanBrigades']);

    $player = self::getPlayer();

    $eliminatedUnits = [];

    foreach ($selectedUnitIds as $unitId) {
      $unit = Utils::array_find($units, function ($possibleunit) use ($unitId) {
        return $unitId === $possibleunit->getId();
      });
      if ($unit === null) {
        throw new \feException("ERROR 045");
      }
      $unit->eliminate($player);
      $eliminatedUnits[] = $unit;
    }

    if ($year >= 1757 && !(Utils::array_some($eliminatedUnits, function ($unit) {
      return $unit->isColonialBrigade();
    }) && Utils::array_some($eliminatedUnits, function ($unit) {
      return $unit->isMetropolitanBrigade();
    }))) {
      throw new \feException("ERROR 046");
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
    $year = BTHelpers::getYear();
    $result = [
      'year' => $year,
      'brigades' => [],
      'colonialBrigades' => [],
      'metropolitanBrigades' => [],
    ];

    $britishBrigades = Utils::filter(Units::getAll()->toArray(), function ($unit) {
      return $unit->isBrigade() && $unit->getFaction() === BRITISH && ($unit->getLocation() === SAIL_BOX || in_array($unit->getLocation(), SPACES));
    });

    if (in_array($year, [1755, 1756])) {
      $result['brigades'] = $britishBrigades;
    } else {
      $result['colonialBrigades'] = Utils::filter($britishBrigades, function ($unit) {
        return $unit->isColonialBrigade();
      });
      $result['metropolitanBrigades'] = Utils::filter($britishBrigades, function ($unit) {
        return $unit->isMetropolitanBrigade();
      });
    }

    return $result;
  }
}
