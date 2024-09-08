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
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattlePenalties extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_PENALTIES;
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

  public function stBattlePenalties()
  {
    $parentInfo = $this->ctx->getParent()->getInfo();
    $spaceId = $parentInfo['spaceId'];
    $space = Spaces::get($spaceId);

    $attackingFaction = $parentInfo['attacker'];
    $defendingFaction = $parentInfo['defender'];

    $playersPerFaction = Players::getPlayersForFactions();

    $attackingPlayer = $playersPerFaction[$attackingFaction];
    $defendingPlayer = $playersPerFaction[$defendingFaction];


    $this->battlePenalties($space, $attackingPlayer, $attackingFaction, $defendingPlayer, $defendingFaction);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattlePenalties()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattlePenalties()
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

  public function actPassBattlePenalties()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattlePenalties($args)
  {
    self::checkAction('actBattlePenalties');

    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function battlePenalties($space, $attackingPlayer, $attackingFaction, $defendingPlayer, $defendingFaction)
  {
    $markers = Markers::getInLocationLike($space->getId());
    $units = $space->getUnits();

    $attackingUnits = Utils::filter($units, function ($unit) use ($attackingFaction) {
      return !$unit->isCommander() && $unit->getFaction() === $attackingFaction;
    });
    $defendingUnits = Utils::filter($units, function ($unit) use ($defendingFaction) {
      return !$unit->isCommander() && $unit->getFaction() === $defendingFaction;
    });

    $unitCount = [
      BRITISH => 0,
      FRENCH => 0,
    ];
    $unitCount[$attackingFaction] = count($attackingUnits);
    $unitCount[$defendingFaction] = count($defendingUnits);

    $markersPerFaction = [
      BRITISH => [],
      FRENCH => [],
    ];

    $penalties = [
      BRITISH => 0,
      FRENCH => 0,
    ];

    foreach ($markers as $marker) {
      $markerType = $marker->getType();
      if (!in_array($markerType, [LANDING_MARKER, OUT_OF_SUPPLY_MARKER, MARSHAL_TROOPS_MARKER, ROUT_MARKER])) {
        continue;
      }

      $faction = explode('_', $marker->getLocation())[1];
      $markersPerFaction[$faction][] = $marker;
      if ($markerType === OUT_OF_SUPPLY_MARKER && $unitCount[$faction] >= 8) {
        $penalties[$faction] = $penalties[$faction] - 2;
      } else {
        $penalties[$faction] = $penalties[$faction] - 1;
      }
    }

    $defenderHasFort = Utils::array_some($defendingUnits, function ($unit) {
      return $unit->isFort();
    });
    $attackerHasArtillery = Utils::array_some($attackingUnits, function ($unit) {
      return $unit->isArtillery();
    });

    if ($defenderHasFort) {
      $penalty = $attackerHasArtillery ? 1 : 2;
      $penalties[$attackingFaction] = $penalties[$attackingFaction] - $penalty;
    }

    foreach ([BRITISH, FRENCH] as $faction) {
      if ($penalties[$faction] < -5) {
        $penalties[$faction] = -5;
      }
    }

    foreach ([$attackingFaction, $defendingFaction] as $index => $faction) {
      $penalty = abs($penalties[$faction]);
      if ($penalty > 0) {
        $text = $penalty === 1 ? clienttranslate('${player_name} receives 1 Battle Penalty') : clienttranslate('${player_name} receives ${penaltyCount} Battle Penalties');
        $player = $index === 0 ? $attackingPlayer : $defendingPlayer;
        Notifications::message($text, [
          'player' => $player,
          'penaltyCount' => $penalty,
        ]);
        $this->moveBattleVictoryMarker($player, $faction, $penalties[$faction]);
      }
    }
  }
}
