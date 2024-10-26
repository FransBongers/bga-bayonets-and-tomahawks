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
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleCleanup extends \BayonetsAndTomahawks\Actions\Battle
{

  public function getState()
  {
    return ST_BATTLE_CLEANUP;
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

  public function stBattleCleanup()
  {
    $parentInfo = $this->ctx->getParent()->getInfo();
    $space = Spaces::get($parentInfo['spaceId']);

    $defender = $space->getDefender();
    $attacker = Players::otherFaction($defender);

    // Return battle markers
    $attackerMarker = Markers::get($this->factionBattleMarkerMap[$attacker]);
    $defenderMarker = Markers::get($this->factionBattleMarkerMap[$defender]);

    $attackerMarker->setSide(0);
    $attackerMarker->setLocation(BATTLE_MARKERS_POOL);
    $defenderMarker->setSide(0);
    $defenderMarker->setLocation(BATTLE_MARKERS_POOL);

    $unitsOnSpace = $space->getUnits();
    $battleContinues = Utils::array_some($unitsOnSpace, function ($unit) {
      return $unit->getFaction() === BRITISH;
    }) && Utils::array_some($unitsOnSpace, function ($unit) {
      return $unit->getFaction() === FRENCH;
    });

    // Do not remove battle marker if defeated defender on Fortress
    if (!$battleContinues) {
      $space->setBattle(0);
      $space->setDefender(null);
    }

    // Flip Open Seas marker if applicable

    $playersPerFaction = Players::getPlayersForFactions();
    $outcomeResolutionArgs = $this->ctx->getParent()->getResolvedActions([BATTLE_OUTCOME])[0]->getActionResolutionArgs();
    $winningFaction = $outcomeResolutionArgs['winner'];

    $this->removeMarkers($space, $playersPerFaction);

    $this->updateControl($space, $winningFaction, $playersPerFaction, $battleContinues);

    $this->updateLuckyCannonballAndPerfectVolleysEventAbilities();

    Notifications::battleCleanup($space, $attackerMarker, $defenderMarker, $battleContinues);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleCleanup()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleCleanup()
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

  public function actPassBattleCleanup()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleCleanup($args)
  {
    self::checkAction('actBattleCleanup');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  /**
   * These abilities can only be used in one battle so if they have been used
   * set them to their limit so they will not be available in another battle
   */
  private function updateLuckyCannonballAndPerfectVolleysEventAbilities()
  {
    foreach ([BRITISH, FRENCH] as $faction) {
      $cardInPlay = Cards::getTopOf(Locations::cardInPlay($faction));
      $event = $cardInPlay->getEvent();
      if ($event !== null && $event['id'] === PERFECT_VOLLEYS && Globals::getUsedEventCount($faction) > 0 && Globals::getUsedEventCount($faction) < 3) {
        Globals::setUsedEventCount($faction, 3);
      } else if ($event !== null && $event['id'] === LUCKY_CANNONBALL && Globals::getUsedEventCount($faction) > 0 && Globals::getUsedEventCount($faction) < 3) {
        Globals::setUsedEventCount($faction, 3);
      }
    }
  }

  private function removeMarkers($space, $playersPerFaction)
  {
    $markers = Markers::getInLocationLike($space->getId());
    $markersToRemove = [BRITISH_MILITIA_MARKER, FRENCH_MILITIA_MARKER, LANDING_MARKER, MARSHAL_TROOPS_MARKER];
    foreach ($markers as $marker) {
      if (!in_array($marker->getType(), $markersToRemove)) {
        continue;
      }
      $owner = explode('_', $marker->getLocation())[1];
      $marker->remove($playersPerFaction[$owner]);
    }
  }

  private function updateControl($space, $winningFaction, $playersPerFaction, $battleContinues)
  {
    $control = $space->getControl();
    if ($control === $winningFaction || $control === NEUTRAL || $battleContinues) {
      return;
    }
    GameMap::updateControl($playersPerFaction[$winningFaction], $space);

    if (!($winningFaction === BRITISH && $space->getId() === LOUISBOURG)) {
      return;
    }

    // Flip SZ
    $marker = Markers::get(OPEN_SEAS_MARKER);
    if ($marker->getSide() === 1) {
      // Marker already flipped
      return;
    }
    $marker->setSide(1);
    Notifications::flipMarker($playersPerFaction[BRITISH], $marker);
  }
}
