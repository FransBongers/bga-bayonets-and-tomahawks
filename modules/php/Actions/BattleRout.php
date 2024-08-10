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

    Notifications::battleRout($faction);

    $markerLocation = Locations::stackMarker($space->getId(), $faction);
    $existingMarker = Markers::getOfTypeInLocation(ROUT_MARKER, $markerLocation);
    if (count($existingMarker) === 0) {
      $marker = Markers::getMarkerFromSupply(ROUT_MARKER);
      $marker->setLocation($markerLocation);
  
      Notifications::placeStackMarker($player, $marker, $space);
    }

    $unitsToEliminate = $this->getUnitsToEliminate($space, $faction);

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
    // REPLACE frienly fort

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
    // Stats::incPassActionCount($player->getId(), 1);
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

  private function getUnitsToEliminate($space, $faction)
  {
    $units = $space->getUnits();
    /**
     * Priority:
     * 1. Artillery
     * 2. Non indian units
     * 4. Indian units
     * // Not fort
     */

    $artillery = Utils::filter($units, function ($unit) use ($faction) {
      return $unit->getFaction() === $faction && $unit->isArtillery();
    });
    if (count($artillery) > 0) {
      return $artillery;
    }
    $nonIndian = Utils::filter($units, function ($unit) use ($faction) {
      return $unit->getFaction() === $faction && !$unit->isIndian() && !$unit->isFort();
    });
    if (count($nonIndian) > 0) {
      return $nonIndian;
    }
    return Utils::filter($units, function ($unit) use ($faction) {
      return $unit->getFaction() === $faction && $unit->isIndian();
    });
  }
}
