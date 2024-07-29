<?php

namespace BayonetsAndTomahawks\States;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Log;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\AtomicActions;


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
    foreach ($players as $player) {
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

  function stSetupActionRound()
  {
    $playerIds = Players::getPlayerIdsForFactions();
    foreach ($playerIds as $faction => $playerId) {
      self::giveExtraTime($playerId);
    }

    $britishPlayerId = $playerIds[BRITISH];
    $frenchPlayerId = $playerIds[FRENCH];

    // Stats::incPlayerTurnCount($player);
    // Stats::incTurnCount(1);
    $node = [];
    $currentRoundStep = Globals::getActionRound();

    $isActionRound = in_array($currentRoundStep, [
      ACTION_ROUND_1,
      ACTION_ROUND_2,
      ACTION_ROUND_3,
      ACTION_ROUND_4,
      ACTION_ROUND_5,
      ACTION_ROUND_6,
      ACTION_ROUND_7,
      ACTION_ROUND_8,
      ACTION_ROUND_9,
    ]);

    $engineCallback = null;
    if ($isActionRound) {
      $node = [
        'children' => [
          [
            'action' => ACTION_ROUND_CHOOSE_CARD,
            'playerId' => 'all',
          ],
        ],
      ];
      $engineCallback = ['method' => 'stFirstPlayerActionPhase'];
    } else if ($currentRoundStep === FLEETS_ARRIVE) {
      $node = $this->getFleetsArriveFlow($britishPlayerId, $frenchPlayerId);
      $engineCallback = ['method' => 'stSetupActionRound'];
    } else if ($currentRoundStep === COLONIALS_ENLIST) {
      $node = [
        'children' => [
          [
            'action' => COLONIALS_ENLIST_DRAW_REINFORCEMENTS,
            'playerId' => 'all',
          ],
        ],
      ];
      $engineCallback = ['method' => 'stSetupActionRound'];
    } else if ($currentRoundStep === WINTER_QUARTERS) {
      $node = [
        'children' => [
          [
            'action' => WINTER_QUARTERS_GAME_END_CHECK,
            'playerId' => 'all',
          ],
        ],
      ];
      $engineCallback = ['method' => 'stSetupYear'];
    }

    // Inserting leaf Action card
    Engine::setup($node, $engineCallback); // End of action round
    Engine::proceed();
  }

  function stFirstPlayerActionPhase()
  {
    $playerId = Globals::getFirstPlayerId();
    self::giveExtraTime($playerId);

    $node = $this->getPlayerActionsPhaseFlow($playerId, true);

    Engine::setup($node, ['method' => 'stSecondPlayerActionPhase']); // End of action round
    Engine::proceed();
  }

  function stSecondPlayerActionPhase()
  {
    $playerId = Globals::getSecondPlayerId();
    self::giveExtraTime($playerId);

    $node = $this->getPlayerActionsPhaseFlow($playerId, false);

    Engine::setup($node, ['method' => 'stReaction']); // End of action round
    Engine::proceed();
  }

  function stReaction()
  {
    $playerId = Globals::getFirstPlayerId();
    self::giveExtraTime($playerId);

    $reactionActionPointId = Globals::getReactionActionPointId();

    if ($reactionActionPointId === '') {
      $this->stBattlesAndEndOfActionRound();
      return;
    }

    $node = [
      'children' => [
        [
          'action' => ACTION_ROUND_ACTION_PHASE,
          'playerId' => $playerId,
          'optional' => true,
          'isReaction' => true,
          'actionPointId' => $reactionActionPointId,
        ]
      ]
    ];

    Engine::setup($node, ['method' => 'stBattlesAndEndOfActionRound']);
    Engine::proceed();
  }

  function stBattlesAndEndOfActionRound()
  {
    $playerId = Globals::getFirstPlayerId();
    $node = [
      'children' => [
        [
          'action' => ACTION_ROUND_RESOLVE_BATTLES,
          'playerId' => $playerId,
        ],
        [
          'action' => ACTION_ROUND_END,
          'playerId' => $playerId,
        ]
      ]
    ];

    Engine::setup($node, ['method' => 'stSetupActionRound']); // End of action round
    Engine::proceed();
  }

  /**
   * Activate next player
   * TODO: is this even used?
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

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getFleetsArriveFlow($britishPlayerId, $frenchPlayerId)
  {
    $node = [
      'children' => [
        [
          'action' => FLEETS_ARRIVE_DRAW_REINFORCEMENTS,
          'pool' => POOL_FLEETS,
        ],
        [
          'action' => FLEETS_ARRIVE_DRAW_REINFORCEMENTS,
          'pool' => POOL_BRITISH_METROPOLITAN_VOW,
        ],
        [
          'action' => FLEETS_ARRIVE_VAGARIES_OF_WAR,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
          'pool' => POOL_BRITISH_METROPOLITAN_VOW,
        ],
        [
          'action' => FLEETS_ARRIVE_COMMANDER_DRAW,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
          'pool' => POOL_BRITISH_METROPOLITAN_VOW,
        ],
        [
          'action' => FLEETS_ARRIVE_DRAW_REINFORCEMENTS,
          'pool' => POOL_FRENCH_METROPOLITAN_VOW,
        ],
        [
          'action' => FLEETS_ARRIVE_VAGARIES_OF_WAR,
          'playerId' => $frenchPlayerId,
          'faction' => FRENCH,
          'pool' => POOL_FRENCH_METROPOLITAN_VOW,
        ],
        [
          'action' => FLEETS_ARRIVE_COMMANDER_DRAW,
          'playerId' => $frenchPlayerId,
          'faction' => FRENCH,
          'pool' => POOL_FRENCH_METROPOLITAN_VOW,
        ],
        [
          'action' => FLEETS_ARRIVE_UNIT_PLACEMENT,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
        ],
        [
          'action' => FLEETS_ARRIVE_UNIT_PLACEMENT,
          'playerId' => $frenchPlayerId,
          'faction' => FRENCH,
        ],
        [
          'action' => FLEETS_ARRIVE_END_OF_ROUND,
        ],
      ],
    ];
    return $node;
  }

  public function getPlayerActionsPhaseFlow($playerId, $isFirstplayer)
  {
    $player = Players::get($playerId);

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
            'action' => ACTION_ROUND_ACTION_PHASE,
            'playerId' => $playerId,
            'optional' => true,
            'isIndianActions' => true,
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
          'action' => ACTION_ROUND_ACTION_PHASE,
          'playerId' => $playerId,
          'optional' => true,
          'isFirstPlayer' => $isFirstplayer,
        ]
      ]
    ];
    return $flow;
  }
}
