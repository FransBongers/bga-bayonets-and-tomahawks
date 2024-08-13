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
    $otherFaction = BTHelpers::getOtherFaction($playerFaction);
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];

    $space = Spaces::get($spaceId);

    $enemyUnits = $space->getUnits($otherFaction);
    $militia = $space->getHomeSpace() !== $playerFaction ? $space->getMilitia() : 0;

    $battleOccurs = count($enemyUnits) + $militia > 0;

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

    if ($battleOccurs) {
      $this->resolveAction(['automatic' => true]);
      return;
    }

    $playerTakesControl = $space->getOutpost() &&
      $space->getControl() !== $playerFaction;

    if ($playerTakesControl) {
      $space->setControl($playerFaction);
      Notifications::takeControl($player, $space);

      if ($space->getVictorySpace()) {
        Players::scoreVictoryPoints($player, $space->getValue());
      }
    }

    $source = $info['source'];

    // Movement possible if there are units that still can move
    if ($source !== CONSTRUCTION && $this->unitsCanMove($space, $playerFaction)) {
      $this->ctx->getParent()->insertAsBrother(Engine::buildTree([
        'action' => MOVEMENT,
        'source' => $source,
        'spaceId' => $spaceId,
        'playerId' => $player->getId(),
        'destinationId' => $info['destinationId'],
        'requiredUnitIds' => $info['requiredUnitIds'],
        'optional' => true,
      ]));
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

  private function unitsCanMove($space, $faction)
  {
    $currentNumberOfMoves = count($this->ctx->getParent()->getParent()->getResolvedActions([MOVEMENT]));

    $units = $space->getUnits();
    $mpMultiplier = in_array($this->ctx->getInfo()['source'], [ARMY_AP_2X, LIGHT_AP_2X, INDIAN_AP_2X, SAIL_ARMY_AP_2X]) ? 2 : 1;

    return Utils::array_some($units, function ($unit) use ($mpMultiplier, $currentNumberOfMoves) {
      return $unit->getMpLimit() * $mpMultiplier > $currentNumberOfMoves && !$unit->isSpent();
    });
  }
}
