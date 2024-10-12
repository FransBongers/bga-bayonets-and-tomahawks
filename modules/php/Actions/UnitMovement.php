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

  public function loneCommanderCheck($player, $space, $units)
  {
    if (    count($units) === count(Utils::filter($units, function ($unit) {
      return $unit->isCommander();
    }))) {
      $this->ctx->insertAsBrother(
        Engine::buildTree([
          'action' => MOVEMENT_LONE_COMMANDER,
          'spaceId' => $space->getId(),
          'playerId' => $player->getId(),
        ])
      );
    }

  }
}
