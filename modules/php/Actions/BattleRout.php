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
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleRout extends \BayonetsAndTomahawks\Actions\Battle
{

  public function getState()
  {
    return ST_BATTLE_ROUT;
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

  public function stBattleRout()
  {
    $info = $this->ctx->getInfo();

    $player = self::getPlayer();
    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);
    $eliminateNonLightUnits = $info['eliminateNonLightUnits'];

    Notifications::battleRout($faction);

    if ($eliminateNonLightUnits) {
      $this->eliminateAllNonLightUnits($player, $space, $faction);
    }

    $units = $space->getUnits($faction);


    // TODO: refactor to use GameMap function?
    $markerLocation = Locations::stackMarker($space->getId(), $faction);
    $existingMarker = Markers::getOfTypeInLocation(ROUT_MARKER, $markerLocation);
    if (count($existingMarker) === 0 && count($units) > 0) {
      $marker = Markers::getMarkersFromSupply(ROUT_MARKER)[0];
      $marker->setLocation($markerLocation);
  
      Notifications::placeStackMarker($player, [$marker], $space);
    }

    $unitsToEliminate = $this->getUnitsToEliminate($units, $faction);

    if (count($unitsToEliminate) === 1) {
      $unitsToEliminate[0]->eliminate($player);
    } else if (count($unitsToEliminate) > 1) {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_APPLY_HITS,
        'playerId' => $player->getId(),
        'unitIds' => array_map(function ($unit) {
          return $unit->getId();
        }, $unitsToEliminate),
        'spaceId' => $spaceId,
        'faction' => $faction,
        'eliminate' => true,
      ]));
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

  public function stPreBattleRout()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRout()
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

  public function actPassBattleRout()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleRout($args)
  {
    self::checkAction('actBattleRout');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function eliminateAllNonLightUnits($player, $space, $faction)
  {
    Notifications::message(clienttranslate('All non-Light units are eliminated'),[]);
    $units = $space->getUnits($faction);
    $nonLightUnits = Utils::filter(($units), function ($unit) {
      return !$unit->isLight();
    });
    foreach($nonLightUnits as $unit) {
      $unit->eliminate($player);
    }
  }

  private function getUnitsToEliminate($units, $faction)
  {
    
    /**
     * Priority:
     * 1. Artillery
     * 2. Non indian units
     * 4. Indian units
     * // Not fort
     */

    $artillery = Utils::filter($units, function ($unit) use ($faction) {
      return $unit->isArtillery();
    });
    if (count($artillery) > 0) {
      return $artillery;
    }
    $nonIndian = Utils::filter($units, function ($unit) use ($faction) {
      return !$unit->isIndian() && !$unit->isFort();
    });
    if (count($nonIndian) > 0) {
      return $nonIndian;
    }
    return Utils::filter($units, function ($unit) use ($faction) {
      return $unit->isIndian();
    });
  }
}
