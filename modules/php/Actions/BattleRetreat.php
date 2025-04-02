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
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleRetreat extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_RETREAT;
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

  public function stBattleRetreat()
  {
    $info = $this->ctx->getInfo();
    $retreatOptionIds = $info['retreatOptionIds'];

    if (count($retreatOptionIds) > 1) {
      return;
    }

    // Retreat to single option
    $this->retreat($retreatOptionIds[0]);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleRetreat()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRetreat()
  {
    $info = $this->ctx->getInfo();
    $retreatOptionIds = $info['retreatOptionIds'];

    $retreatOptions = [];

    // Add extra check here since args can be executed before state function
    // that should handle the SAIL_BOX case
    if ($retreatOptionIds[0] !== SAIL_BOX) {
      $retreatOptions = Spaces::getMany($retreatOptionIds)->toArray();
    }

    return [
      'retreatOptions' => $retreatOptions,
      'retreatOptionIds' => $retreatOptionIds
    ];
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

  public function actPassBattleRetreat()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleRetreat($args)
  {
    self::checkAction('actBattleRetreat');

    $spaceId = $args['spaceId'];

    $options = $this->argsBattleRetreat()['retreatOptions'];

    $space = Utils::array_find($options, function ($space) use ($spaceId) {
      return $space->getId() === $spaceId;
    });

    if ($space === null) {
      throw new \feException("ERROR 013");
    }

    $this->retreat($spaceId);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function retreat($toSpaceId)
  {
    $info = $this->ctx->getInfo();
    $playerId = $this->ctx->getPlayerId();
    $faction = $info['faction'];

    $fromSpace = Spaces::get($info['spaceId']);

    $units = Utils::filter($fromSpace->getUnits($faction), function ($unit) {
      return !$unit->isFort();
    });

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $units);

    $overwhelmDuringRetreat = isset($info['overwhelmDuringRetreat']) ? $info['overwhelmDuringRetreat'] : false;

    if ($overwhelmDuringRetreat) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => BATTLE_OVERWHELM_DURING_RETREAT,
        'playerId' => Players::getOther($playerId)->getId(),
        'spaceId' => $toSpaceId,
      ]));
    }

    // Insert in reverse order to we check control after movement
    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK,
      'playerId' => $playerId,
      'spaceId' => $toSpaceId,
      'source' => BATTLE_RETREAT,
    ]));
    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => MOVE_STACK,
      'playerId' => $playerId,
      'fromSpaceId' => $fromSpace->getId(),
      'toSpaceId' => $toSpaceId,
      'unitIds' => $unitIds,
    ]));
  }
}
