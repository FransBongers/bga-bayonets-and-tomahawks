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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundChooseFirstPlayer extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_CHOOSE_FIRST_PLAYER;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundChooseFirstPlayer()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundChooseFirstPlayer()
  {

    // Notifications::log('argsActionRoundChooseFirstPlayer',[]);
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

  public function actPassActionRoundChooseFirstPlayer()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actActionRoundChooseFirstPlayer($args)
  {
    self::checkAction('actActionRoundChooseFirstPlayer');

    $firstPlayerId = $args['playerId'];

    $players = Players::getAll()->toArray();

    // $firstPlayer = Players::get($firstPlayerId);
    $firstPlayer = Utils::array_find($players, function ($player) use ($firstPlayerId) {
      return $player->getId() === $firstPlayerId;
    });
    $secondPlayer = Utils::array_find($players, function ($player) use ($firstPlayerId) {
      return $player->getId() !== $firstPlayerId;
    });

    Globals::setFirstPlayerId($firstPlayer->getId());
    Globals::setSecondPlayerId($secondPlayer->getId());
    // TODO: AR Start events?

    //
    // Notifications::log('firstPlayer', $firstPlayer);
    // Notifications::log('secondPlayer', $secondPlayer);
    // TODO: AR start events
    // $actionRoundFlow = [
    //   'children' => [
    //     $this->getPlayerActionsFlow($firstPlayer, true),
    //     $this->getPlayerActionsFlow($secondPlayer, false),
    //     // [
    //     //   'action' => ACTION_ROUND_REACTION,
    //     //   'playerId' => $firstPlayerId,
    //     // ],
    //     [
    //       'action' => ACTION_ROUND_RESOLVE_BATTLES,
    //       'playerId' => $firstPlayerId,
    //     ],
    //     [
    //       'action' => ACTION_ROUND_END,
    //       'playerId' => $firstPlayerId,
    //     ]
    //   ]
    // ];

    // $this->ctx->insertAsBrother(Engine::buildTree($actionRoundFlow));

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getPlayerActionsFlow($player, $isFirstplayer)
  {
    $playerId = $player->getId();
    $flow = [
      'children' => [
        [
          'action' => ACTION_ROUND_SAIL_BOX_LANDING,
          'playerId' => $playerId,
        ]
      ]
    ];

    if ($player->getFaction() === FRENCH) {
      $flow['children'][] = [
        'children' => [
          [
            'action' => ACTION_ROUND_INDIAN_ACTIONS,
            'playerId' => $playerId,
            'optional' => true,
          ]
        ]
      ];
    }
    if ($isFirstplayer) {
      $flow['children'][] = [
        'action' => ACTION_ROUND_CHOOSE_REACTION,
        'playerId' => $playerId,
        'optional' => true,
      ];
    }
    $flow['children'][] = [
      'children' => [
        [
          'action' => $isFirstplayer ? ACTION_ROUND_FIRST_PLAYER_ACTIONS : ACTION_ROUND_SECOND_PLAYER_ACTIONS,
          'playerId' => $playerId,
          'optional' => true,
        ]
      ]
    ];
    return $flow;
  }
}
