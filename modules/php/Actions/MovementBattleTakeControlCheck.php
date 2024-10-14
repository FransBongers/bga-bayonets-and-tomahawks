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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class MovementBattleTakeControlCheck extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK;
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

  public function stMovementBattleTakeControlCheck()
  {
    $player = self::getPlayer();
    $playerFaction = $player->getFaction();
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];

    $space = Spaces::get($spaceId);

    $battleOccurs = $this->checkBattle($player, $space, $playerFaction);

    if ($battleOccurs) {
      $this->resolveAction(['automatic' => true]);
      return;
    }

    $this->checkTakeControl($player, $space, $playerFaction);

    if (!in_array($info['source'], [CONSTRUCTION, ACTION_ROUND_SAIL_BOX_LANDING])) {
      $this->checkAdditionalMovement($space, $playerFaction, $player, $info);
    }

    $this->resolveAction(['automatic' => true]);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  /**
   * Battle occurs if 
   * - at this point there are still enemy units excluding militia
   * - militia are not overwhelmed and space was not enemy controlled at start of turn
   */
  private function checkBattle($player, $space, $playerFaction)
  {
    // $otherFaction = BTHelpers::getOtherFaction($playerFaction);
    // $enemyUnits = $space->getUnits($otherFaction);
    $enemyMilitia = $space->getHomeSpace() !== $playerFaction ? $space->getMilitia() : 0;

    $data = GameMap::factionOutnumbersEnemyInSpace($space, $playerFaction);
    Notifications::log('OUTNUMBER DATA', $data);
    $overwhelm = $data['overwhelm'];

    $battleOccurs = $data['hasEnemyUnitsExcludingMilitia'] || ($enemyMilitia > 0 && !$overwhelm && $space->getControlStartOfTurn() !== $playerFaction);

    Notifications::log('BATTLE OCCURS', $battleOccurs);

    if ($battleOccurs && $space->getBattle() === 0) {
      $space->setBattle(1);

      // TODO: check defender / attacker
      $space->setDefender(Players::otherFaction($playerFaction));
      Notifications::battle($player, $space);
    } else if (!$battleOccurs && $space->getBattle() === 1) {
      $space->setBattle(0);
      $space->setDefender(null);
      Notifications::battleRemoveMarker($player, $space);
    }

    return $battleOccurs;
  }

  private function checkTakeControl($player, $space, $playerFaction)
  {
    $enemyControlled = $space->getControl() !== $playerFaction;
    $homeSpace = $space->getHomeSpace();

    $playerCanTakeControlOfOutpost = $space->getOutpost() && $enemyControlled;
    $playerCanRetakeHomeSpace = $homeSpace === $playerFaction && $enemyControlled;

    // Can this be replaced with just checking for enemy control?
    $playerCanTakeEnemyHomeSpace = $homeSpace !== null && $homeSpace !== $playerFaction && $enemyControlled;
    $playerTakesControl = $playerCanTakeControlOfOutpost || $playerCanRetakeHomeSpace || $playerCanTakeEnemyHomeSpace;

    if ($playerTakesControl) {
      GameMap::updateControl($player, $space);
    }
  }

  private function unitsCanMove($units)
  {
    $currentNumberOfMoves = count($this->ctx->getParent()->getParent()->getResolvedActions([MOVEMENT]));

    $info = $this->ctx->getInfo();
    $forcedMarchAvailable = isset($info['forcedMarchAvailable']) && $info['forcedMarchAvailable'];


    $mpMultiplier = in_array($this->ctx->getInfo()['source'], [ARMY_AP_2X, LIGHT_AP_2X, INDIAN_AP_2X, SAIL_ARMY_AP_2X]) ? 2 : 1;

    return Utils::array_some($units, function ($unit) use ($mpMultiplier, $currentNumberOfMoves, $forcedMarchAvailable) {
      $movementPoints = $unit->getMpLimit() * $mpMultiplier;
      if ($forcedMarchAvailable && !$unit->isLight()) {
        $movementPoints += 1;
      }

      return $movementPoints > $currentNumberOfMoves && !$unit->isSpent();
    });
  }

  // Movement possible if there are units that still can move
  private function checkAdditionalMovement($space, $playerFaction, $player, $info)
  {
    $units = $space->getUnits($playerFaction);
    if ($this->unitsCanMove($units)) {
      $this->ctx->getParent()->insertAsBrother(Engine::buildTree([
        'action' => MOVEMENT,
        'source' => $info['source'],
        'spaceId' => $space->getId(),
        'playerId' => $player->getId(),
        'destinationId' => $info['destinationId'],
        'requiredUnitIds' => $info['requiredUnitIds'],
        'optional' => true,
        'indianNation' => isset($info['indianNation']) ? $info['indianNation'] : null,
      ]));
    } else {
      $this->loneCommanderCheck($player, $space, $units);
    }
  }
}
