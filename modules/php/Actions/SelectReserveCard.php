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
use BayonetsAndTomahawks\Managers\Cards;

class SelectReserveCard extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_SELECT_RESERVE_CARD;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreSelectReserveCard()
  {
    Notifications::log('stPreSelectReserveCard', $this->ctx->getInfo());
    Cards::pickForLocation(2, Locations::buildUpDeck(BRITISH), Locations::hand(BRITISH));
    Cards::pickForLocation(2, Locations::buildUpDeck(FRENCH), Locations::hand(FRENCH));
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsSelectReserveCard()
  {

    // $data = [
    //   BRITISH => Cards::getInLocation(Locations::hand(BRITISH))->toArray(),
    //   FRENCH => Cards::getInLocation(Locations::hand(FRENCH))->toArray(),
    // ];

    // args['_private'][specificPid]=
    $data['_private'] = [];
    $players = Players::getAll();
    foreach ($players as $player) {
      $data['_private'][$player->getId()] = $player->getHand();
    }

    return $data;
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

  public function actPassSelectReserveCard()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  // public function actSelectReserveCard($cardId, $strength)
  public function actSelectReserveCard($args)
  {
    $cardId = $args['cardId'];

    self::checkAction('actSelectReserveCard');
    Notifications::log('actSelectReserveCard', $args);
    $player = Players::getCurrent();
    $stateArgs = $this->argsSelectReserveCard();

    $selectedCard = Utils::array_find($stateArgs['_private'][$player->getId()], function ($card) use ($cardId) {
      return $cardId === $card->getId();
    });
    Notifications::log('inArray', $selectedCard);
    if ($selectedCard === null) {
      throw new \BgaVisibleSystemException('This card cannot be selected');
    }

    // Make the player inactive
    $game = Game::get();
    $game->gamestate->setPlayerNonMultiactive($player->getId(), 'next');
    if (count($game->gamestate->getActivePlayerList()) > 0) {
      return;
    }

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
