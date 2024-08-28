<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\Flows;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
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
    $info = $this->ctx->getInfo();

    $usedActionPoints = $this->getUsedActionPoints();

    if (isset($info['isFirstPlayer']) && $info['isFirstPlayer'] && Globals::getReactionActionPointId() !== '') {
      $usedActionPoints[] = Globals::getReactionActionPointId();
    }


    $card = null;
    $isIndianActions = isset($info['isIndianActions']) && $info['isIndianActions'];
    if ($isIndianActions) {
      $card = Cards::getTopOf(Locations::cardInPlay(INDIAN));
    } else {
      $card = Cards::getTopOf(Locations::cardInPlay($this->getPlayer()->getFaction()));
    }

    $lostAP = $this->getLostActionPoints($isIndianActions);
    $unavailableActionPoints = array_merge($usedActionPoints, $lostAP);

    $availableActionPoints = isset($info['isReaction']) && $info['isReaction'] ?
      [
        [
          'id' => $this->ctx->getInfo()['actionPointId']
        ]
      ] :
      BTHelpers::getAvailableActionPoints($unavailableActionPoints, $card, self::getPlayer()->getFaction() === FRENCH ? Globals::getAddedAPFrench() : []);

    return [
      // 'action' => $action,
      'card' => $card,
      'isIndianActions' => $isIndianActions,
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
    // Engine::resolve(PASS);
    $this->resolveAction(PASS);
  }

  public function actActionRoundActionPhase($args)
  {
    self::checkAction('actActionRoundActionPhase');
    $player = self::getPlayer();
    $actionPointId = $args['actionPointId'];

    $stateArgs = $this->argsActionRoundActionPhase();

    $actionPoint = Utils::array_find($stateArgs['availableActionPoints'], function ($availableAP) use ($actionPointId) {
      return $availableAP === $actionPointId;
    });

    // Check if AP is available.
    if ($actionPoint === null) {
      throw new \feException("ERROR 006");
    }

    Notifications::message(clienttranslate('${player_name} uses ${actionPoint}'), [
      'player' => $player,
      'actionPoint' => $actionPointId,
    ]);

    // If there are more actionPoints insert same action
    // Check for more than one as the one is resolved with this action
    if (count($stateArgs['availableActionPoints']) > 1) {
      $info = $this->ctx->getInfo();

      $node = [
        'action' => ACTION_ROUND_ACTION_PHASE,
        'playerId' => self::getPlayer()->getId(),
        'optional' => true,
        'isFirstPlayer' => isset($info['isFirstPlayer']) ? $info['isFirstPlayer'] : false,
        'isIndianActions' => isset($info['isIndianActions']) ? $info['isIndianActions'] : false,
        'isReaction' => isset($info['isReaction']) ? $info['isReaction'] : false,
      ];

      $this->ctx->insertAsBrother(Engine::buildTree($node));
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

  private function getLostActionPoints($isIndianActions)
  {
    if ($isIndianActions) {
      return Globals::getLostAPIndian();
    };
    $faction = self::getPlayer()->getFaction();
    if ($faction === BRITISH) {
      return Globals::getLostAPBritish();
    } else {
      return Globals::getLostAPFrench();
    }
  }

  private function getUsedActionPoints()
  {
    $usedActionPointIds = [];

    $siblings = $this->ctx->getParent()->getChildren();

    foreach ($siblings as $node) {
      if ($node->getAction() !== ACTION_ROUND_ACTION_PHASE || !$node->isActionResolved()) {
        continue;
      }
      $resArgs = $node->getActionResolutionArgs();
      $usedActionPointIds[] = $resArgs['actionPointId'];
    }

    return $usedActionPointIds;
  }

  // private function getAvailableActionPoints($usedActionPoints, $card)
  // {
  //   $cardActionPoints = $card->getActionPoints();

  //   $result = [];
  //   foreach ($cardActionPoints as $cIndex => $actionPoint) {
  //     $uIndex = Utils::array_find_index($usedActionPoints, function ($uActionPointId) use ($actionPoint) {
  //       return $uActionPointId === $actionPoint['id'];
  //     });
  //     if ($uIndex === null) {
  //       $result[] = $actionPoint;
  //     } else {
  //       unset($usedActionPoints[$uIndex]);
  //       $usedActionPoints = array_values($usedActionPoints);
  //     }
  //   }

  //   return $result;
  // }
}
