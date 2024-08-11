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

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionActivateStack()
  {
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
    // Stats::incPassActionCount($player->getId(), 1);
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
    $flow = AtomicActions::get($action['id'])->getFlow($actionPointId, self::getPlayer()->getId(), $stackId, in_array($actionPointId, [INDIAN_AP, INDIAN_AP_2X]));
    $this->ctx->insertAsBrother(Engine::buildTree($flow));

    Notifications::activateStack(self::getPlayer(), Spaces::get($stackId), $action['name']);

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
