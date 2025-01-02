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
use BayonetsAndTomahawks\Managers\ActionPoints;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Models\Player;

class ActionActivateStack extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ACTIVATE_STACK;
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionActivateStack()
  {
    $actionPointId = $this->ctx->getParent()->getInfo()['actionPointId'];
    $actionPoint = ActionPoints::get($actionPointId);
    $player = self::getPlayer();

    $spaces = Spaces::getAll();
    $stacks = [];
    foreach ($spaces as $space) {
      $actions = $actionPoint->canActivateStackInSpace($space, $player);

      if (count($actions) > 0) {
        $stacks[$space->getId()] = $actions;
      }
    }

    return [
      'stacks' => $stacks,
      'faction' => $player->getFaction(),
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

  public function actPassActionActivateStack()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actActionActivateStack($args)
  {
    self::checkAction('actActionActivateStack');

    $actionId = $args['action'];
    $stackId = $args['stack'];

    $stateArgs = $this->argsActionActivateStack();


    if (!isset($stateArgs['stacks'][$stackId])) {
      throw new \feException("Not allowed to activate selected stack");
    }
    $action = Utils::array_find($stateArgs['stacks'][$stackId], function ($action) use ($actionId) {
      return $action['id'] === $actionId;
    });
    if ($action === null) {
      throw new \feException("Not allowed to perform selected action");
    }



    $actionPointId = $this->ctx->getParent()->getInfo()['actionPointId'];
    $flow = AtomicActions::get($action['id'])->getFlow($actionPointId, self::getPlayer()->getId(), $stackId);
    $this->ctx->insertAsBrother(Engine::buildTree($flow));

    $player = self::getPlayer();
    $this->updateStats($player, $actionId);
    Notifications::activateStack($player, Spaces::get($stackId), $action['name']);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function updateStats($player, $acionId)
  {
    $playerId = $player->getId();

    switch ($acionId) {
      case MOVEMENT:
        Stats::incMovement($playerId, 1);
        return;
      case RAID_SELECT_TARGET:
        Stats::incRaid($playerId, 1);
        return;
      case MARSHAL_TROOPS:
        Stats::incMarshalTroops($playerId, 1);
        return;
      case CONSTRUCTION:
        Stats::incConstruction($playerId, 1);
        return;
      default:
        return;
    }
  }
}
