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

class MovementOverwhelmCheck extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_MOVEMENT_OVERWHELM_CHECK;
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

  public function stMovementOverwhelmCheck()
  {
    $player = self::getPlayer();
    $playerFaction = $player->getFaction();
    $spaceId = $this->ctx->getInfo()['spaceId'];
    $space = Spaces::get($spaceId);

    $data = GameMap::factionOutnumbersEnemyInSpace($space, $playerFaction);

    $overwhelm = $data['overwhelm'];

    if ($overwhelm && $data['hasEnemyUnitsExcludingMilitia']) {
      Notifications::message(clienttranslate('${player_name} Overwhelms enemy stack in ${tkn_boldText_spaceName}'), [
        'player' => $player,
        'tkn_boldText_spaceName' => $space->getName(),
        'i18n' => ['tkn_boldText_spaceName']
      ]);
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => BATTLE_RETREAT_CHECK_OPTIONS,
        'playerId' => Players::getOther($player->getId())->getId(),
        'faction' => BTHelpers::getOtherFaction($player->getFaction()),
        'spaceId' => $spaceId,
        'isAttacker' => false,
        'isRouted' => false,
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
}
