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
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\StackActions;
use BayonetsAndTomahawks\Models\Player;

class UnitMovement extends \BayonetsAndTomahawks\Actions\StackAction
{

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  // /**
  //  * Check if there are enemy units.
  //  * If so check if player owe
  //  */
  // public function checkEnemyUnitsAndOverwhelm($space, $player)
  // {
  //   $playerFaction = $player->getFaction();
  //   $units = $space->getUnits();

  //   $enemyHasFort = false;
  //   $enemyUnits = [];
  //   $playerUnits = [];
  //   foreach ($units as $unit) {
  //     if ($unit->getType() === COMMANDER) {
  //       continue;
  //     }
  //     if ($unit->getFaction() === $playerFaction) {
  //       $playerUnits[] = $unit;
  //     } else {
  //       $enemyUnits[] = $unit;
  //       if ($unit->getType() === FORT) {
  //         $enemyHasFort = true;
  //       }
  //     }
  //   }

  //   $enemyHasBastion = $playerFaction === BRITISH && $space->hasBastion();

  //   $militia = $space->getControl() !== $playerFaction ? $space->getMilitia() : 0;

  //   $numberOfEnemyUnits = count($enemyUnits) + $militia;

  //   $hasEnemyUnits = $numberOfEnemyUnits > 0;
  //   $overwhelm = !($enemyHasFort || $enemyHasBastion) && $hasEnemyUnits && count($playerUnits) / $numberOfEnemyUnits > 3;
  //   $battleOccurs = $hasEnemyUnits && !$overwhelm;
  //   // Battle notif
  //   if ($battleOccurs && $space->getBattle() === 0) {
  //     $space->setBattle(1);
  //     $space->setDefender(Players::otherFaction($playerFaction));
  //     Notifications::battle($player, $space);
  //   }

  //   if ($overwhelm) {
  //     // insert as brother retreat move for opponent
  //   }

  //   return [
  //     'hasEnemyUnits' => $hasEnemyUnits,
  //     'overwhelm' => $overwhelm,
  //     'battleOccurs' => $battleOccurs,
  //   ];
  // }

  /**
   * If all units leave enemy settled space:
   * - Remove control marker
   * - Adjust victory points
   */
  // public function loseControlCheck($player, $origin)
  // {

  //   $playerFaction = $player->getFaction();

  //   $playerLosesControl = $origin->getSettledSpace() &&
  //     $origin->getControl() !== $origin->getHomeSpace() &&
  //     count($origin->getUnits($playerFaction)) === 0;

  //   if (!$playerLosesControl) {
  //     return;
  //   }

  //   $origin->setControl($playerFaction === BRITISH ? FRENCH : BRITISH);
  //   Notifications::loseControl($player, $origin);

  //   if ($origin->getVictorySpace()) {
  //     Players::scoreVictoryPoints($player, -1 * $origin->getValue());
  //   }
  // }

  /**
   * If enenmy controlled outpost or Indian village take control:
   * - Place control marker
   * - Adjust victory points
   */
  public function takeControlCheck($player, $destination)
  {
    $playerFaction = $player->getFaction();
    $playerTakesControl = $destination->getOutpost() &&
      $destination->getControl() !== $playerFaction &&
      count($destination->getUnits($playerFaction === BRITISH ? FRENCH : BRITISH)) === 0;

    if (!$playerTakesControl) {
      return;
    }

    $destination->setControl($playerFaction);
    Notifications::takeControl($player, $destination);

    if ($destination->getVictorySpace()) {
      Players::scoreVictoryPoints($player, $destination->getValue());
    }
  }
}
