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
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleFortElimination extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_FORT_ELIMINATION;
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

  public function stBattleFortElimination()
  {
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $faction = $info['faction'];
    $isRouted = $info['isRouted'];
    $space = Spaces::get($spaceId);
    $player = self::getPlayer();

    $enemyFort = Units::getTopOf($faction === BRITISH ? POOL_FRENCH_FORTS : POOL_BRITISH_FORTS);
    $fort = Utils::array_find($space->getUnits($faction), function ($unit) {
      return $unit->isFort();
    });

    if ($fort !== null && $enemyFort === null) {
      $fort->eliminate($player);
      $this->resolveAction(['automatic' => true]);
    }

    if (!$isRouted) {
      return;
    }

    if ($fort !== null) {
      $this->replaceFort($space, $fort, $player);
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

  public function stPreBattleFortElimination() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleFortElimination()
  {
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $faction = $info['faction'];
    $space = Spaces::get($spaceId);

    $fort = Utils::array_find($space->getUnits($faction), function ($unit) {
      return $unit->isFort();
    });

    return [
      'fort' => $fort,
      'enemyFort' => Units::getTopOf($faction === BRITISH ? POOL_FRENCH_FORTS : POOL_BRITISH_FORTS),
      'space' => $space,
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

  public function actPassBattleFortElimination()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleFortElimination($args)
  {
    self::checkAction('actBattleFortElimination');

    $choice = $args['choice'];

    if (!in_array($choice, ['eliminate', 'replace'])) {
      throw new \feException("ERROR 067");
    }

    $stateArgs = $this->argsBattleFortElimination();
    $fort = $stateArgs['fort'];

    $player = self::getPlayer();
    if ($choice === 'eliminate') {
      $fort->eliminate($player);
    } else {
      $this->replaceFort($stateArgs['space'], $fort, $player);
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


  private function replaceFort($space, $fort, $player)
  {
    $faction = $fort->getFaction();
    $reducedState = $fort->getReduced();

    $fort->eliminate($player);

    $newFort = Units::getTopOf($faction === BRITISH ? POOL_FRENCH_FORTS : POOL_BRITISH_FORTS);

    // What should happen if there are no forts left and cannot be replaced?
    if ($newFort === null) {
      return;
    }

    $newFort->setLocation($space->getId());
    $newFort->setReduced($reducedState);

    Notifications::placeUnits(Players::getOther($player->getId()), [$newFort], $space, BTHelpers::getOtherFaction($faction));
  }
}
