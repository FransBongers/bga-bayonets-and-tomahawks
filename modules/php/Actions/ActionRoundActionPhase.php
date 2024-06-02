<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\Flows;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\ActionPoints;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundActionPhase extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_ACTION_PHASE;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundActionPhase()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundActionPhase()
  {
    $action = $this->ctx->getAction();

    $siblings = $this->ctx->getParent()->getChildren();
    $usedActionPoints = [];
    foreach ($siblings as $node) {
      $siblingInfo = $node->getInfo();
      if (isset($siblingInfo['actionPointId'])) {
        $usedActionPoints[] = $siblingInfo['actionPointId'];
      }
    }

    if ($action === ACTION_ROUND_FIRST_PLAYER_ACTIONS) {
      $nodes = Engine::getUnresolvedActions([ACTION_ROUND_REACTION]);
      if (count($nodes) === 1) {
        $usedActionPoints[] = $nodes[0]->getInfo()['actionPointId'];
      }
    }

    $card = null;
    if ($action === ACTION_ROUND_INDIAN_ACTIONS) {
      $card = Cards::getTopOf(Locations::cardInPlay(INDIAN));
    } else {
      $card = Cards::getTopOf(Locations::cardInPlay($this->getPlayer()->getFaction()));
    }

    $availableActionPoints = $action === ACTION_ROUND_REACTION ?
      [
        [
          'id' => $this->ctx->getInfo()['actionPointId']
        ]
      ] :
      $this->getAvailableActionPoints($usedActionPoints, $card);

    return [
      'action' => $action,
      'card' => $card,
      'availableActionPoints' => array_map(function ($availableAP) {
        return $availableAP['id'];
      }, $availableActionPoints),
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

  public function actPassActionRoundActionPhase()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actActionRoundActionPhase($args)
  {
    self::checkAction('actActionRoundActionPhase');
    $player = self::getPlayer();
    $actionPointId = $args['actionPoint'];

    $stateArgs = $this->argsActionRoundActionPhase();

    $actionPoint = Utils::array_find($stateArgs['availableActionPoints'], function ($availableAP) use ($actionPointId) {
      return $availableAP === $actionPointId;
    });

    // Check if AP is available.
    if ($actionPoint === null) {
      throw new \feException("ERROR 006");
    }

    // If there are more actionPoints insert same action
    // Check for more than one as the one is resolved with this action
    if (count($stateArgs['availableActionPoints']) > 1) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => $this->ctx->getAction(),
        'playerId' => self::getPlayer()->getId(),
        'optional' => true,
      ]));
    }

    $this->ctx->insertAsBrother(Engine::buildTree(Flows::performAction($player, $actionPointId)));

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getAvailableActionPoints($usedActionPoints, $card)
  {
    $cardActionPoints = $card->getActionPoints();

    $result = [];
    foreach ($cardActionPoints as $cIndex => $actionPoint) {
      $uIndex = Utils::array_find_index($usedActionPoints, function ($uActionPointId) use ($actionPoint) {
        return $uActionPointId === $actionPoint['id'];
      });
      if ($uIndex === null) {
        $result[] = $actionPoint;
      } else {
        unset($usedActionPoints[$uIndex]);
        $usedActionPoints = array_values($usedActionPoints);
      }
    }

    return $result;
  }
}
