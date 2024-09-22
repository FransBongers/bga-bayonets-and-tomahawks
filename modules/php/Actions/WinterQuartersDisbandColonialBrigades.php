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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Marker;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenario;

class WinterQuartersDisbandColonialBrigades extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_DISBAND_COLONIAL_BRIGADES;
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

  public function stWinterQuartersDisbandColonialBrigades()
  {
    $options = $this->getOptions();

    $disbanded = $options['disbanded'];
    $mayRemain = $options['mayRemain'];

    $player = self::getPlayer();

    Units::move(array_map(function ($unit) {
      return $unit->getId();
    }, $disbanded), DISBANDED_COLONIAL_BRIGADES);
    Notifications::winterQuartersDisbandColonialBrigades($player, $disbanded);

    if (count($mayRemain) > 0) {
      $node = [
        'action' => WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES,
        'playerId' => $player->getId(),
      ];

      $this->ctx->insertAsBrother(Engine::buildTree($node));
    }

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersDisbandColonialBrigades() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersDisbandColonialBrigades()
  {


    return [];
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

  public function actPassWinterQuartersDisbandColonialBrigades()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actWinterQuartersDisbandColonialBrigades($args)
  {
    self::checkAction('actWinterQuartersDisbandColonialBrigades');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function canBeDisbanded($unit, $spaces, $units)
  {
    $location = $unit->getLocation();
    // is on a non-Settled Space with a Friendly Fort
    if (!$spaces[$location]->isSettledSpace() && Utils::array_some($units, function ($otherUnit) use ($location) {
      return $otherUnit->isFort() && $otherUnit->getLocation() === $location;
    })) {
      return false;
    }
    // Is on a friendly controlled enemy Settled Space
    if ($spaces[$location]->isSettledSpace(FRENCH)) {
      return false;
    }

    return true;
  }

  private function getOptions()
  {
    $units = Units::getAll()->toArray();
    $spaces = Spaces::getAll();

    $colonialBrigadesOnMap = Utils::filter($units, function ($unit) use ($spaces, $units) {
      if (!$unit->isColonialBrigade()) {
        return false;
      };
      $location = $unit->getLocation();
      if (!in_array($location, SPACES) || in_array($location, BASTIONS)) {
        return false;
      }
      return true;
    });

    $disbanded = [];
    $mayRemain = [];

    foreach ($colonialBrigadesOnMap as $unit) {
      $disbandBrigade = $this->canBeDisbanded($unit, $spaces, $units);
      if ($disbandBrigade) {
        $disbanded[] = $unit;
      } else {
        $mayRemain[] = $unit;
      }
    }

    return [
      'disbanded' => $disbanded,
      'mayRemain' => $mayRemain,
    ];
  }
}
