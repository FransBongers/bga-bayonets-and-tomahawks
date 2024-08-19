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
    $spaceId = $parentInfo['spaceId'];
    $space = Spaces::get($spaceId);
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

    // $players = Players::getAll()->toArray();
    $playersPerFaction = Players::getPlayersForFactions();

    $attackingPlayer = $playersPerFaction[$attackingFaction];
    $defendingPlayer = $playersPerFaction[$defendingFaction];

    $this->placeMarkers($space, $attackingFaction, $defendingFaction);

    // Add militia markers
    $this->placeMilitia($space, $playersPerFaction);

    
    $this->battlePenalties($space, $attackingPlayer, $attackingFaction, $defendingPlayer, $defendingFaction);

    $this->selectCommanders($units, [$attackingPlayer, $defendingPlayer], $space);

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

  private function placeMilitia($space, $playersPerFaction)
  {
    $numberOfMilitia = $space->getMilitia();
    $militiaFaction = $space->getDefaultControl();

    if ($space->getControl() !== $militiaFaction) {
      $numberOfMilitia--;
    }
    if ($numberOfMilitia <= 0) {
      return;
    }

    $markerLocation = Locations::stackMarker($space->getId(), $militiaFaction);

    $type = $militiaFaction === BRITISH ? BRITISH_MILITIA_MARKER : FRENCH_MILITIA_MARKER;
    $markers = Markers::getMarkersFromSupply($type, $numberOfMilitia);
    
    foreach($markers as $marker) {
      $marker->setLocation($markerLocation);
    }

    Notifications::placeStackMarker($playersPerFaction[$militiaFaction], $markers, $space);
  }

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

    if ($penalties[$attackingFaction] < 0) {
      Notifications::message('${player_name} receives ${penaltyCount} Battle Penalties', [
        'player' => $attackingPlayer,
        'penaltyCount' => abs($penalties[$attackingFaction]),
      ]);
      $this->moveBattleVictoryMarker($attackingPlayer, $attackingFaction, $penalties[$attackingFaction]);
    }
    if ($penalties[$defendingFaction] < 0) {
      Notifications::message('${player_name} receives ${penaltyCount} Battle Penalties', [
        'player' => $defendingPlayer,
        'penaltyCount' => abs($penalties[$defendingFaction]),
      ]);
      $this->moveBattleVictoryMarker($defendingPlayer, $defendingFaction, $penalties[$attackingFaction]);
    }
  }
}
