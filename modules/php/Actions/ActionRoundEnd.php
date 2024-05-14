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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundEnd extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_END;
  }

  // ..######..########....###....########.########
  // .##....##....##......##.##......##....##......
  // .##..........##.....##...##.....##....##......
  // ..######.....##....##.....##....##....######..
  // .......##....##....#########....##....##......
  // .##....##....##....##.....##....##....##......
  // ..######.....##....##.....##....##....########

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function stActionRoundEnd()
  {
    // Notifications::log('stActionRoundEndUpdates', []);

    // 1. Discard played cards facedown.
    $cardsInPlay = Cards::getCardsInPlay();
    Notifications::discardCardsInPlayMessage();
    // Notifications::log('cardsInPlay',$cardsInPlay);
    foreach($cardsInPlay as $faction => $card) {
      if ($card !== null) {
        $card->discard();
      }
    }
    // Notifications::discardCardInPlay($cardsInPlay);

    // 2. Remove Spent markers, as well as any remaning Landing and Marshall markers.

    // 3. Supply Check (14.1)

    // 4 Rally (14.2)

    // 5 Advance Round marker and begin next Round (7.1)
    // TODO: end of year check here?

    $currentActionRound = Globals::getActionRound();
    // Notifications::log('currentActionRound', $currentActionRound);
    $nextActionRound = null;
    switch ($currentActionRound) {
      case ACTION_ROUND_1:
        $nextActionRound = ACTION_ROUND_2;
        break;
      case ACTION_ROUND_2:
        $nextActionRound = FLEETS_ARRIVE;
        break;
      case ACTION_ROUND_3:
        $nextActionRound = COLONIALS_ENLIST;
        break;
      case ACTION_ROUND_4:
        $nextActionRound = ACTION_ROUND_5;
        break;
      case ACTION_ROUND_5:
        $nextActionRound = ACTION_ROUND_6;
        break;
      case ACTION_ROUND_6:
        $nextActionRound = ACTION_ROUND_7;
        break;
      case ACTION_ROUND_7:
        $nextActionRound = ACTION_ROUND_8;
        break;
      case ACTION_ROUND_8:
        $nextActionRound = ACTION_ROUND_9;
        break;
      case ACTION_ROUND_9:
        $nextActionRound = WINTER_QUARTERS;
        break;
      case FLEETS_ARRIVE:
        $nextActionRound = ACTION_ROUND_3;
        break;
      case COLONIALS_ENLIST:
        $nextActionRound = ACTION_ROUND_4;
        break;
      case WINTER_QUARTERS:
        // TODO: check how to handle this?
        $nextActionRound = ACTION_ROUND_1;
        break;
    }
    // Notifications::log('nextActionRound', $nextActionRound);
    Globals::setActionRound($nextActionRound);
    Markers::move(ROUND_MARKER,$nextActionRound);
    Notifications::moveRoundMarker(Markers::get(ROUND_MARKER), $nextActionRound);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundEnd()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundEnd()
  {


    return [];
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

  public function actPassActionRoundEnd()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actActionRoundEnd($args)
  {
    self::checkAction('actActionRoundEnd');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
