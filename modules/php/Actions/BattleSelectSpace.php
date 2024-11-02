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
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleSelectSpace extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_SELECT_SPACE;
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

  public function stBattleSelectSpace() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleSelectSpace()
  {
    $info = $this->ctx->getInfo();
    $spaceIds = $info['spaceIds'];

    return [
      'spaces' => Spaces::get($spaceIds)->toArray(),
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

  public function actPassBattleSelectSpace()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleSelectSpace($args)
  {
    self::checkAction('actBattleSelectSpace');
    $spaceId = $args['spaceId'];

    $info = $this->ctx->getInfo();
    $spaceIds = $info['spaceIds'];
    $numberOfAttackers = $info['numberOfAttackers'];

    if (!in_array($spaceId, $spaceIds)) {
      throw new \feException("ERROR 098");
    }
    $player = self::getPlayer();
    $playerId = $player->getId();

    Notifications::message(clienttranslate('${player_name} selects ${tkn_boldText_spaceName} as Space for the next Battle'), [
      'player' => $player,
      'tkn_boldText_spaceName' => Spaces::get($spaceId)->getName(),
      'i18n' => ['tkn_boldText_spaceName'],
    ]);

    $remainingSpaceIds = Utils::filter($spaceIds, function ($id) use ($spaceId) {
      return $id !== $spaceId;
    });

    $resolveBattlesAction = AtomicActions::get(ACTION_ROUND_RESOLVE_BATTLES);

    if (count($remainingSpaceIds) > 1) {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_SELECT_SPACE,
        'playerId' => Globals::getFirstPlayerId(),
        'spaceIds' => $remainingSpaceIds,
        'numberOfAttackers' => $numberOfAttackers,
      ]));
    } else {
      $this->ctx->insertAsBrother(Engine::buildTree(
        $resolveBattlesAction->getBattleFlow($playerId, $remainingSpaceIds[0], $numberOfAttackers)
      ));
    }

    $this->ctx->insertAsBrother(Engine::buildTree(
      $resolveBattlesAction->getBattleFlow($playerId, $spaceId, $numberOfAttackers)
    ));

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
