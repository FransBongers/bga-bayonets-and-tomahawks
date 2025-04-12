<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Cards;
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
    $parent = $this->ctx->getParent();
    $parentInfo = $parent->getInfo();
    $spaceId = $parentInfo['spaceId'];
    $space = Spaces::get($spaceId);

    /**
     * There can be cases where we have added the battle to the engine but
     * the battle marker has been removed in the meantime.
     * Ie, overwhelm during retreat has triggered and killed all units of one side,
     * so we need to skip the battle.
     */
    if ($space->getBattle() === 0) {
      $this->ctx->updateInfo('resolveParent', true);
      $this->resolveAction([
        'automatic' => true,
      ]);
      return;
    }


    $defendingFaction = $space->getDefender();
    $attackingFaction = Players::otherFaction($defendingFaction);

    Globals::setActiveBattleSpaceId($parentInfo['spaceId']);
    Globals::setActiveBattleAttackerFaction($attackingFaction);
    Globals::setActiveBattleDefenderFaction($defendingFaction);
    Globals::setActiveBattleHighlandBrigadeHit(false);
    Globals::setActiveBattleCoupDeMain(false);
    Globals::setActiveBattleLastBastionEliminated(false);

    $this->ctx->getParent()->updateInfo('attacker', $attackingFaction);
    $this->ctx->getParent()->updateInfo('defender', $defendingFaction);

    $playersPerFaction = Players::getPlayersForFactions();

    $unitsPerFaction = $this->getUnitsPerFaction($space);

    $markers = $this->placeMarkers($attackingFaction, $defendingFaction);
    Notifications::battleStart($space, $markers[0], $markers[1], $unitsPerFaction);

    // Add militia markers
    $militiaPerFaction = $this->placeMilitia($space, $playersPerFaction);


    // Check combining reduced units
    foreach ([$defendingFaction, $attackingFaction] as $faction) {
      $this->checkIfReducedUnitsCanBeCombined($space->getId(), $faction, $playersPerFaction[$faction]);
    }

    $this->checkWildernessAmbush($space);
    $this->checkCoupDeMain($space);
    $this->checkIndomitableAbbatis($space);



    $battleLog = [
      'spaceId' => $spaceId,
      'attacker' => $attackingFaction,
      'defender' => $defendingFaction,
    ];

    foreach ([$attackingFaction, $defendingFaction] as $faction) {
      $battleLog[$faction] = [
        'faction' => $faction,
        'militiaIds' => isset($militiaPerFaction[$faction]) ? BTHelpers::returnIds($militiaPerFaction[$faction]) : [],
        'penalties' => 0,
        'rolls' => [],
        'unitIds' => BTHelpers::returnIds($unitsPerFaction[$faction]),
      ];
    }

    Globals::setActiveBattleLog($battleLog);


    $this->resolveAction([
      'automatic' => true,
      'unitIds' => BTHelpers::returnIds(array_merge($unitsPerFaction[BRITISH], $unitsPerFaction[FRENCH])),
    ]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattlePreparation() {}


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

  private function getUnitsPerFaction($space)
  {

    $resolvedBattlePreparation = Engine::getResolvedActions([BATTLE_PREPARATION]);

    $unitIdsThatAlreadyFought = Globals::getUnitsThatCannotFight([]);;

    foreach ($resolvedBattlePreparation as $node) {
      $resArgs = $node->getActionResolutionArgs();
      if (isset($resArgs['unitIds'])) {
        $unitIdsThatAlreadyFought = array_merge($unitIdsThatAlreadyFought, $resArgs['unitIds']);
      }
    }

    $units = $space->getUnits();

    $unitsPerFaction = [];

    foreach ([BRITISH, FRENCH] as $faction) {
      $unitsPerFaction[$faction] = Utils::filter($units, function ($unit) use ($faction, $unitIdsThatAlreadyFought) {
        return $unit->getFaction() === $faction && !in_array($unit->getId(), $unitIdsThatAlreadyFought);
      });
    }
    return $unitsPerFaction;
  }

  private function checkCoupDeMain($space)
  {
    $coupDeMainInPlay = Cards::isCardInPlay(FRENCH, COUP_DE_MAIN_CARD_ID);
    if (!$coupDeMainInPlay || ($coupDeMainInPlay && Globals::getUsedEventCount(FRENCH) === 1)) {
      return;
    }


    $hasBritishFort = Utils::array_some($space->getUnits(BRITISH), function ($unit) {
      return $unit->isFort();
    });
    if (!$hasBritishFort) {
      return;
    }

    $this->ctx->insertAsBrother(
      Engine::buildTree([
        'playerId' => Players::getPlayerForFaction(FRENCH)->getId(),
        'action' => USE_EVENT,
        'spaceId' => $space->getId(),
        'cardId' => COUP_DE_MAIN_CARD_ID,
        'optional' => true,
      ])
    );
  }

  private function checkIndomitableAbbatis($space)
  {
    $indomitableAbbatisInPlay = Cards::isCardInPlay(FRENCH, INDOMITABLE_ABBATIS_CARD_ID);
    if (!$indomitableAbbatisInPlay || ($indomitableAbbatisInPlay && Globals::getUsedEventCount(FRENCH) === 1)) {
      return;
    }

    if ($space->getDefender() === FRENCH) {
      $this->ctx->insertAsBrother(
        Engine::buildTree([
          'playerId' => Players::getPlayerForFaction(FRENCH)->getId(),
          'action' => USE_EVENT,
          'spaceId' => $space->getId(),
          'cardId' => INDOMITABLE_ABBATIS_CARD_ID,
          'optional' => true,
        ])
      );
    }
  }

  private function checkWildernessAmbush($space)
  {
    $wildernessWarInPlay = Cards::isCardInPlay(FRENCH, WILDERNESS_AMBUSH_CARD_ID);
    if (!$wildernessWarInPlay || ($wildernessWarInPlay && Globals::getUsedEventCount(FRENCH) === 1)) {
      return;
    }

    if (!($space->isOutpost() || $space->isWildernessSpace())) {
      return;
    }

    $lightUnits = Utils::array_some($space->getUnits(FRENCH), function ($unit) {
      return $unit->isLight();
    });

    if (!$lightUnits) {
      return;
    }

    $this->ctx->insertAsBrother(
      Engine::buildTree([
        'playerId' => Players::getPlayerForFaction(FRENCH)->getId(),
        'action' => EVENT_WILDERNESS_AMBUSH,
        'spaceId' => $space->getId(),
        'optional' => true,
      ])
    );
  }


  private function placeMarkers($attackingFaction, $defendingFaction)
  {
    $attackerMarker = Markers::get($this->factionBattleMarkerMap[$attackingFaction]);
    $attackerMarker->setLocation(Locations::battleTrack(true, 0));
    $defenderMarker = Markers::get($this->factionBattleMarkerMap[$defendingFaction]);
    $defenderMarker->setLocation(Locations::battleTrack(false, 0));

    return [$attackerMarker, $defenderMarker];
  }

  private function placeMilitia($space, $playersPerFaction)
  {
    $numberOfMilitia = $space->getMilitia();
    $militiaFaction = $space->getDefaultControl();

    if ($space->getControl() !== $militiaFaction) {
      $numberOfMilitia--;
    }
    if ($numberOfMilitia <= 0) {
      return [];
    }

    $markerLocation = Locations::stackMarker($space->getId(), $militiaFaction);

    $type = $militiaFaction === BRITISH ? BRITISH_MILITIA_MARKER : FRENCH_MILITIA_MARKER;
    $markers = Markers::getMarkersFromSupply($type, $numberOfMilitia);

    foreach ($markers as $marker) {
      $marker->setLocation($markerLocation);
    }

    Notifications::placeStackMarker($playersPerFaction[$militiaFaction], $markers, $space);

    return [
      $militiaFaction => $markers
    ];
  }

  // private function battlePenalties($space, $attackingPlayer, $attackingFaction, $defendingPlayer, $defendingFaction)
  // {
  //   $markers = Markers::getInLocationLike($space->getId());
  //   $units = $space->getUnits();

  //   $attackingUnits = Utils::filter($units, function ($unit) use ($attackingFaction) {
  //     return !$unit->isCommander() && $unit->getFaction() === $attackingFaction;
  //   });
  //   $defendingUnits = Utils::filter($units, function ($unit) use ($defendingFaction) {
  //     return !$unit->isCommander() && $unit->getFaction() === $defendingFaction;
  //   });

  //   $unitCount = [
  //     BRITISH => 0,
  //     FRENCH => 0,
  //   ];
  //   $unitCount[$attackingFaction] = count($attackingUnits);
  //   $unitCount[$defendingFaction] = count($defendingUnits);

  //   $markersPerFaction = [
  //     BRITISH => [],
  //     FRENCH => [],
  //   ];

  //   $penalties = [
  //     BRITISH => 0,
  //     FRENCH => 0,
  //   ];

  //   foreach ($markers as $marker) {
  //     $markerType = $marker->getType();
  //     if (!in_array($markerType, [LANDING_MARKER, OUT_OF_SUPPLY_MARKER, MARSHAL_TROOPS_MARKER, ROUT_MARKER])) {
  //       continue;
  //     }

  //     $faction = explode('_', $marker->getLocation())[1];
  //     $markersPerFaction[$faction][] = $marker;
  //     if ($markerType === OUT_OF_SUPPLY_MARKER && $unitCount[$faction] >= 8) {
  //       $penalties[$faction] = $penalties[$faction] - 2;
  //     } else {
  //       $penalties[$faction] = $penalties[$faction] - 1;
  //     }
  //   }

  //   $defenderHasFort = Utils::array_some($defendingUnits, function ($unit) {
  //     return $unit->isFort();
  //   });
  //   $attackerHasArtillery = Utils::array_some($attackingUnits, function ($unit) {
  //     return $unit->isArtillery();
  //   });

  //   if ($defenderHasFort) {
  //     $penalty = $attackerHasArtillery ? 1 : 2;
  //     $penalties[$attackingFaction] = $penalties[$attackingFaction] - $penalty;
  //   }

  //   foreach ([BRITISH, FRENCH] as $faction) {
  //     if ($penalties[$faction] < -5) {
  //       $penalties[$faction] = -5;
  //     }
  //   }

  //   if ($penalties[$attackingFaction] < 0) {
  //     Notifications::message('${player_name} receives ${penaltyCount} Battle Penalties', [
  //       'player' => $attackingPlayer,
  //       'penaltyCount' => abs($penalties[$attackingFaction]),
  //     ]);
  //     $this->moveBattleVictoryMarker($attackingPlayer, $attackingFaction, $penalties[$attackingFaction]);
  //   }
  //   if ($penalties[$defendingFaction] < 0) {
  //     Notifications::message('${player_name} receives ${penaltyCount} Battle Penalties', [
  //       'player' => $defendingPlayer,
  //       'penaltyCount' => abs($penalties[$defendingFaction]),
  //     ]);
  //     $this->moveBattleVictoryMarker($defendingPlayer, $defendingFaction, $penalties[$attackingFaction]);
  //   }
  // }
}
