<?php

namespace BayonetsAndTomahawks\Core\Engine;

use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;

/**
 * Contains function to get complex flows for Engine
 * TODO: is there a better place for this?
 */
abstract class Flows
{
  public static function performAction($player, $actionPointId)
  {
    $playerId = $player->getId();
    return [
      'actionPointId' => $actionPointId,
      'children' => [
        [
          'action' => ACTION_ACTIVATE_STACK,
          'playerId' => $playerId,
        ],
      ]
    ];
  }
}
