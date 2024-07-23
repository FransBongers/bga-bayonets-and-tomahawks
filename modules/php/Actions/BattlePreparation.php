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

class BattlePreparation extends \BayonetsAndTomahawks\Actions\Battle
{
  // private $factionBattleMarkerMap = [
  //   BRITISH => BRITISH_BATTLE_MARKER,
  //   FRENCH => FRENCH_BATTLE_MARKER
  // ];

  public function getState()
  {
    return ST_BATTLE_PREPARATION;
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

  public function stBattlePreparation()
  {
    $parentInfo = $this->ctx->getParent()->getInfo();
    $space = Spaces::get($parentInfo['spaceId']);
    Notifications::log('stBattlePreparation', $parentInfo);
    $units = $space->getUnits();

    $defendingFaction = $space->getDefender();
    $attackingFaction = Players::otherFaction($defendingFaction);

    Globals::setActiveBattleSpaceId($parentInfo['spaceId']);
    Globals::setActiveBattleAttackerFaction($attackingFaction);
    Globals::setActiveBattleDefenderFaction($defendingFaction);
    Globals::setActiveBattleHighlandBrigadeHit(false);

    $this->ctx->getParent()->updateInfo('attacker', $attackingFaction);
    $this->ctx->getParent()->updateInfo('defender', $defendingFaction);

    $players = Players::getAll()->toArray();
    $attackingPlayer = Utils::array_find($players, function ($player) use ($attackingFaction) {
      return $player->getFaction() === $attackingFaction;
    });
    $defendingPlayer = Utils::array_find($players, function ($player) use ($defendingFaction) {
      return $player->getFaction() === $defendingFaction;
    });

    $this->placeMarkers($space, $attackingFaction, $defendingFaction);

    $this->selectCommanders($units,[$attackingPlayer, $defendingPlayer], $space);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattlePreparation()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattlePreparation()
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

  public function actPassBattlePreparation()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattlePreparation($args)
  {
    self::checkAction('actBattlePreparation');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function placeMarkers($space, $attackingFaction, $defendingFaction)
  {
    $attackerMarker = Markers::get($this->factionBattleMarkerMap[$attackingFaction]);
    $attackerMarker->setLocation(Locations::battleTrack(true, 0));
    $defenderMarker = Markers::get($this->factionBattleMarkerMap[$defendingFaction]);
    $defenderMarker->setLocation(Locations::battleTrack(false, 0));

    Notifications::battleStart($space, $attackerMarker, $defenderMarker);
  }
}
