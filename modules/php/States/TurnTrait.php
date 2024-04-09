<?php

namespace BayonetsAndTomahawks\States;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Log;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\ActionCards;
use BayonetsAndTomahawks\Managers\Market;
use BayonetsAndTomahawks\Managers\Meeples;
use BayonetsAndTomahawks\Managers\Scores;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\ZooCards;

trait TurnTrait
{
  /**
   * State function when starting a turn useful to intercept
   * for some cards that happens at that moment
   */
  function stBeforeStartOfTurn()
  {
    // TODO: check end callback
    $this->initCustomDefaultTurnOrder('default', \ST_TURNACTION, ST_BEFORE_START_OF_TURN, true);
  }

  function stSetupYear()
  {
    // $year = Globals::getYear();
    // $year += 1;
    // Globals::setYear($year);
    // Globals::setActionRound(ACTION_ROUND_1);
    // TODO: move tokens?

    // TODO: check how we should handle giving extra time
    $players = Players::getAll();
    foreach($players as $player) {
      self::giveExtraTime($player->getId());
    }

    $node = [
      'children' => [
        [
          'action' => SELECT_RESERVE_CARD,
          'playerId' => 'all',
        ],
      ],
    ];

    Engine::setup($node, ['method' => 'stSetupActionRound']);
    Engine::proceed();
  }

  function stSetupActionRound() {
    $player = Players::getActive();
    self::giveExtraTime($player->getId());

    // Stats::incPlayerTurnCount($player);
    // Stats::incTurnCount(1);
    $node = [];
    $currentRoundStep = Globals::getActionRound();
    Notifications::log('currentRoundStep',$currentRoundStep);
    if (in_array($currentRoundStep,[
      ACTION_ROUND_1,
      ACTION_ROUND_2,
      ACTION_ROUND_3,
      ACTION_ROUND_4,
      ACTION_ROUND_5,
      ACTION_ROUND_6,
      ACTION_ROUND_7,
      ACTION_ROUND_8,
      ACTION_ROUND_9,
    ])) {
      $node = [
        'children' => [
          [
            'action' => ACTION_ROUND_CHOOSE_CARD,
            'playerId' => 'all',
          ],
        ],
      ];
    } else if ($currentRoundStep === FLEETS_ARRIVE) {
      $node = [
        'children' => [
          [
            'action' => FLEETS_ARRIVE_DRAW_REINFORCEMENTS,
            'playerId' => 'all',
          ],
        ],
      ];
    } else if ($currentRoundStep === COLONIALS_ENLIST) {
      $node = [
        'children' => [
          [
            'action' => COLONIALS_ENLIST_DRAW_REINFORCEMENTS,
            'playerId' => 'all',
          ],
        ],
      ];
    } else if ($currentRoundStep === WINTER_QUARTERS) {
      $node = [
        'children' => [
          [
            'action' => WINTER_QUARTERS_GAME_END_CHECK,
            'playerId' => 'all',
          ],
        ],
      ];
    }

    // Inserting leaf Action card
    Engine::setup($node, ['method' => $currentRoundStep === WINTER_QUARTERS ? 'stSetupYear' : 'stSetupActionRound']); // End of action round
    Engine::proceed();
  }

  /**
   * Activate next player
   */
  function stTurnAction()
  {
    $player = Players::getActive();
    self::giveExtraTime($player->getId());

    // Stats::incPlayerTurnCount($player);
    // Stats::incTurnCount(1);
    $node = [
      'children' => [
        [
          'action' => SELECT_RESERVE_CARD,
          'playerId' => 'all',
        ],
        [
          'action' => ACTION_ROUND_CHOOSE_CARD,
          'playerId' => 'all',
        ],
        // [
        //   'children' => [
        //     [
        //       'action' => FREE_ACTION,
        //       'optional' => true,
        //       'playerId' => $player->getId(),
        //     ],
        //   ]
        // ]
      ],
    ];
    // Notifications::startTurn($player);

    // Inserting leaf Action card
    Engine::setup($node, ['method' => 'stTurnAction']);
    Engine::proceed();
  }

  /*******************************
   ********************************
   ********** END OF TURN *********
   ********************************
   *******************************/

  /**
   * End of turn : replenish and check break
   */
  function stEndOfTurn()
  {

    $player = Players::getActive();

    // $unableToRefresh = Market::refresh($player);

    // if ($unableToRefresh) {
    //   Game::get()->gamestate->jumpToState(ST_PATRON_VICTORY);
    // } else {
      $this->nextPlayerCustomOrder('default');
    // }
  }

  function endOfGameInit()
  {
    // if (Globals::getEndFinalScoringDone() !== true) {
    //   // Trigger discard state
    //   Engine::setup(
    //     [
    //       'action' => DISCARD_SCORING,
    //       'playerId' => 'all',
    //       'args' => ['current' => Players::getActive()->getId()],
    //     ],
    //     ''
    //   );
    //   Engine::proceed();
    // } else {
    //   // Goto scoring state
    //   $this->gamestate->jumpToState(\ST_PRE_END_OF_GAME);
    // }
    // return;
  }

  function stPreEndOfGame()
  {
    // Arcade first
    // $card = ZooCards::getSingle('S281_Arcade', false);
    // if (!is_null($card)) {
    //   $card->preScore();
    // }

    // foreach (Players::getAll() as $playerId => $player) {
    //   foreach ($player->getPlayedCards(CARD_SPONSOR) as $cId => $card) {
    //     $card->score();
    //   }
    //   foreach ($player->getScoringHand() as $cId => $card) {
    //     $card->score();
    //   }
    // }

    // // Victory column last
    // $card = ZooCards::getSingle('S274_VictoryColumn', false);
    // if (!is_null($card)) {
    //   $card->postScore();
    // }

    // // Send final notif
    // foreach (Players::getAll() as $playerId => $player) {
    //   // Make sure to call Players::get() because score was modified but it's cached in $player
    //   $score = $player->updateScore(true);
    // }

    // Log::clearUndoableStepNotifications(true);
    // if (Globals::isSolo() && Globals::getSoloChallenge() > 0) {
    //   // new setup for solo challenge
    //   $this->setupNextGame();
    // } else {
    //   Globals::setEnd(true);
    //   $this->gamestate->nextState('');
    // }
  }

  /*
  function stLaunchEndOfGame()
  {
    foreach (ZooCards::getAllCardsWithMethod('EndOfGame') as $card) {
      $card->onEndOfGame();
    }
    Globals::setTurn(15);
    Globals::setLiveScoring(true);
    Scores::update(true);
    Notifications::seed(Globals::getGameSeed());
    $this->gamestate->jumpToState(\ST_END_GAME);
  }
  */
}
