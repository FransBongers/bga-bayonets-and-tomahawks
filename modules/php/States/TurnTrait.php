<?php

namespace BayonetsAndTomahawks\States;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;
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

    Cards::setupDecksForYear(BTHelpers::getYear());

    // $britishReserveCards = Cards::pickForLocation(2, Locations::buildUpDeck(BRITISH), Locations::hand(BRITISH))->toArray();
    // $frenchReserveCards = Cards::pickForLocation(2, Locations::buildUpDeck(FRENCH), Locations::hand(FRENCH))->toArray();

    // foreach (array_merge($britishReserveCards, $frenchReserveCards) as $card) {
    //   Notifications::drawCard($card->getOwner(), $card);
    // }

    $node = [
      'children' => [
        [
          'action' => DRAW_RESERVE_CARDS,
        ],
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
            'action' => ACTION_ROUND_DRAW_CARDS,
          ],
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
      $node = $this->getColonialsEnlistFlow($britishPlayerId);
      $engineCallback = ['method' => 'stSetupActionRound'];
    } else if ($currentRoundStep === WINTER_QUARTERS) {
      $node = $this->getWinterQuartersFlow($britishPlayerId, $frenchPlayerId);
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

  function stPreEndOfGame() {}


  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getCurrentYear() {}

  public function getWinterQuartersFlow($britishPlayerId, $frenchPlayerId)
  {
    $playerIds = [
      BRITISH => $britishPlayerId,
      FRENCH => $frenchPlayerId,
    ];

    $node = [
      'children' => [
        [
          'action' => WINTER_QUARTERS_GAME_END_CHECK,
        ],
        // 17.2.1 and 17.2.2
        [
          'action' => WINTER_QUARTERS_REMOVE_MARKERS,
        ],
      ],
    ];

    // Move stacks on the sail box
    $unitsOnSailBox = Units::getInLocation(SAIL_BOX)->toArray();
    foreach ([BRITISH, FRENCH] as $faction) {
      if (Utils::array_some($unitsOnSailBox, function ($unit) use ($faction) {
        return $unit->getFaction() === $faction;
      })) {
        $node['children'][] =         [
          'action' => WINTER_QUARTERS_MOVE_STACK_ON_SAIL_BOX,
          'playerId' => $playerIds[$faction],
          'faction' => $faction,
        ];
      }
    }

    $node['children'] = array_merge(
      $node['children'],
      [
        // Place Indian Units on their villages
        [
          'action' => WINTER_QUARTERS_PLACE_INDIAN_UNITS,
        ],
        // Move Colonal Brigades to Disbanded Colonial Brigades
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_DISBAND_COLONIAL_BRIGADES,
              'playerId' => $playerIds[BRITISH],
            ]
          ]
        ],
        // Return to Colonies
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK,
              'faction' => BRITISH,
              'playerId' => $playerIds[BRITISH],
            ]
          ]
        ],
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK,
              'faction' => BRITISH,
              'playerId' => $playerIds[BRITISH],
            ]
          ]
        ],
        [
          'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS,
          'faction' => BRITISH,
          'playerId' => $playerIds[BRITISH],
          'optional' => true,
        ],
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS,
              'faction' => BRITISH,
              'playerId' => $playerIds[BRITISH],
            ]
          ]
        ],
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK,
              'faction' => FRENCH,
              'playerId' => $playerIds[FRENCH],
            ]
          ]
        ],
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK,
              'faction' => FRENCH,
              'playerId' => $playerIds[FRENCH],
            ]
          ]
        ],
        [
          'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS,
          'faction' => FRENCH,
          'playerId' => $playerIds[FRENCH],
          'optional' => true,
        ],
        [
          'children' => [
            [
              'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS,
              'faction' => FRENCH,
              'playerId' => $playerIds[FRENCH],
            ]
          ]
        ],
        // Return all Fleets on the map to the fleets pool
        [
          'action' => WINTER_QUARTERS_RETURN_FLEETS,
        ],
        // Place units from Losses Box
        [
          'action' => WINTER_QUARTERS_PLACE_UNITS_FROM_LOSSES_BOX,
          'faction' => BRITISH,
          'playerId' => $playerIds[BRITISH],
        ],
        [
          'action' => WINTER_QUARTERS_PLACE_UNITS_FROM_LOSSES_BOX,
          'faction' => FRENCH,
          'playerId' => $playerIds[FRENCH],
        ],
        // Reset cards
        // Advance Year Marker
        [
          'action' => WINTER_QUARTERS_ROUND_END,
        ]
      ]
    );

    // Place Indian Units on their villages
    // $node['children'][] = [
    //   'action' => WINTER_QUARTERS_PLACE_INDIAN_UNITS,
    // ];
    // Move Colonal Brigades to Disbanded Colonial Brigades
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_DISBAND_COLONIAL_BRIGADES,
    //       'playerId' => $playerIds[BRITISH],
    //     ]
    //   ]
    // ];
    // Return to Colonies
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK,
    //       'faction' => BRITISH,
    //       'playerId' => $playerIds[BRITISH],
    //     ]
    //   ]
    // ];
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK,
    //       'faction' => BRITISH,
    //       'playerId' => $playerIds[BRITISH],
    //     ]
    //   ]
    // ];
    // $node['children'][] = [
    //   'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS,
    //   'faction' => BRITISH,
    //   'playerId' => $playerIds[BRITISH],
    //   'optional' => true,
    // ];
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS,
    //       'faction' => BRITISH,
    //       'playerId' => $playerIds[BRITISH],
    //     ]
    //   ]
    // ];
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_SELECT_STACK,
    //       'faction' => FRENCH,
    //       'playerId' => $playerIds[FRENCH],
    //     ]
    //   ]
    // ];
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_STEP2_SELECT_STACK,
    //       'faction' => FRENCH,
    //       'playerId' => $playerIds[FRENCH],
    //     ]
    //   ]
    // ];
    // $node['children'][] = [
    //   'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS,
    //   'faction' => FRENCH,
    //   'playerId' => $playerIds[FRENCH],
    //   'optional' => true,
    // ];
    // $node['children'][] = [
    //   'children' => [
    //     [
    //       'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS,
    //       'faction' => FRENCH,
    //       'playerId' => $playerIds[FRENCH],
    //     ]
    //   ]
    // ];

    // $node['children'][] = [
    //   'action' => WINTER_QUARTERS_ROUND_END,
    // ];

    return $node;
  }

  public function getColonialsEnlistFlow($britishPlayerId)
  {
    $node = [
      'children' => [
        [
          'action' => DRAW_REINFORCEMENTS,
          'playerId' => $britishPlayerId,
          'pool' => POOL_BRITISH_COLONIAL_VOW,
        ],
        [
          'action' => VAGARIES_OF_WAR_PICK_UNITS,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
          'pool' => POOL_BRITISH_COLONIAL_VOW,
        ],
        [
          'action' => VAGARIES_OF_WAR_PUT_BACK_IN_POOL,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
          'pool' => POOL_BRITISH_COLONIAL_VOW,
        ],
        [
          'action' => COLONIALS_ENLIST_UNIT_PLACEMENT,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
        ],
        [
          'action' => LOGISTICS_ROUND_END,
          'logisticsRound' => COLONIALS_ENLIST
        ],
      ],
    ];
    return $node;
  }

  public function getFleetsArriveFlow($britishPlayerId, $frenchPlayerId)
  {
    $node = [
      'children' => [
        [
          'action' => DRAW_REINFORCEMENTS,
          'playerId' => $britishPlayerId,
          'pool' => POOL_FLEETS,
        ],
        [
          'action' => VAGARIES_OF_WAR_PUT_BACK_IN_POOL,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
          'pool' => POOL_FLEETS,
        ],
        [
          'action' => DRAW_REINFORCEMENTS,
          'playerId' => $britishPlayerId,
          'pool' => POOL_BRITISH_METROPOLITAN_VOW,
        ],
        [
          'action' => VAGARIES_OF_WAR_PICK_UNITS,
          'playerId' => $britishPlayerId,
          'faction' => BRITISH,
          'pool' => POOL_BRITISH_METROPOLITAN_VOW,
        ],
        [
          'action' => VAGARIES_OF_WAR_PUT_BACK_IN_POOL,
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
          'action' => DRAW_REINFORCEMENTS,
          'playerId' => $frenchPlayerId,
          'pool' => POOL_FRENCH_METROPOLITAN_VOW,
        ],
        [
          'action' => VAGARIES_OF_WAR_PICK_UNITS,
          'playerId' => $frenchPlayerId,
          'faction' => FRENCH,
          'pool' => POOL_FRENCH_METROPOLITAN_VOW,
        ],
        [
          'action' => VAGARIES_OF_WAR_PUT_BACK_IN_POOL,
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
          'action' => LOGISTICS_ROUND_END,
          'logisticsRound' => FLEETS_ARRIVE
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
