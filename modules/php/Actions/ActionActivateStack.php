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
    Notifications::log('argsActionActivateStack',[]);
    $actionPointId = $this->ctx->getParent()->getInfo()['actionPointId'];
    $actionPoint = ActionPoints::get($actionPointId);
    $player = self::getPlayer();

    $spaces = Spaces::getAll();
    $stacks = [];
    foreach ($spaces as $space) {
      $actions = $actionPoint->canActivateStackInSpace($space, $player);

      // $units = $space->getUnits();

      // $hasUnitToActivate = Utils::array_some($units, function ($unit) {
      //   return $unit->getFaction() === INDIAN;
      // });
      // if ($hasUnitToActivate) {
      //   $stacks[] = $space->getId();
      // }
      if (count($actions) > 0) {
        $stacks[$space->getId()] = $actions;
      }
    }

    return [
      'stacks' => $stacks,
      // 'actionsAllowed' => $actionPoint->getActionsAllowed(),
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
    Notifications::log('action', $actionId);
    Notifications::log('stack', $stackId);

    $args = $this->argsActionActivateStack();

    if (!isset($args['stacks'][$stackId])) {
      throw new \feException("Not allowed to activate selected stack");
    }
    $action = Utils::array_find($args['stacks'][$stackId], function ($action) use ($actionId) {
      return $action->getId() === $actionId;
    });
    if ($action === null) {
      throw new \feException("Not allowed to perform selected action");
    }

    $flow = $action->getFlow(self::getPlayer()->getId(), $stackId);
    $this->ctx->insertAsBrother(Engine::buildTree($flow));

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
