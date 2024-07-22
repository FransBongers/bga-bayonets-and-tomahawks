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
use BayonetsAndTomahawks\Models\Player;

class ActionRoundChooseCard extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_CHOOSE_CARD;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundChooseCard()
  {
    // Notifications::log('stPreActionRoundChooseCard', $this->ctx->getInfo());
    // TODO: locationd depends on AR
    $buildupDeck = in_array(Globals::getActionRound(), [ACTION_ROUND_1, ACTION_ROUND_2, ACTION_ROUND_3]);
    $britishCard = Cards::pickForLocation(1, $buildupDeck ? Locations::buildUpDeck(BRITISH) : Locations::campaignDeck(BRITISH), Locations::hand(BRITISH))->toArray()[0];
    $frenchCard = Cards::pickForLocation(1, $buildupDeck ? Locations::buildUpDeck(FRENCH) : Locations::campaignDeck(FRENCH), Locations::hand(FRENCH))->toArray()[0];
    $indianCard = Cards::pickForLocation(1, Locations::campaignDeck(INDIAN), Locations::selected(INDIAN))->toArray()[0];
    // Notifications::log('cards', [
    //   BRITISH => $britishCard[0],
    //   FRENCH => $frenchCard[0],
    //   INDIAN => $indianCard[0]
    // ]);
    foreach (Players::getAll() as $player) {
      $faction = $player->getFaction();
      if ($faction === BRITISH) {
        Notifications::drawCard($player, $britishCard);
      } else {
        Notifications::drawCard($player, $frenchCard);
        Notifications::drawCard($player, $indianCard);
      }
    }
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundChooseCard()
  {
    $privateData = [];

    foreach (Players::getAll() as $player) {
      $faction = $player->getFaction();
      $hand = $player->getHand();
      if ($faction === BRITISH) {
        $privateData[$player->getId()]['cards'] = $hand;
      } else {
        $privateData[$player->getId()]['cards'] = Utils::filter($hand, function ($card) {
          return $card->getFaction() === FRENCH;
        });
        $privateData[$player->getId()]['indianCard'] = Cards::getTopOf(Locations::selected(INDIAN));
      }
      $privateData[$player->getId()]['selectedCard'] = Cards::getTopOf(Locations::selected($faction));
    }

    return [
      '_private' => $privateData,
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

  public function actPassActionRoundChooseCard()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  // public function actActionRoundChooseCard($cardId, $strength)
  public function actActionRoundChooseCard($args)
  {
    $cardId = $args['cardId'];

    self::checkAction('actActionRoundChooseCard');
    Notifications::log('actActionRoundChooseCard', $args);

    $player = Players::getCurrent();

    $stateArgs = $this->argsActionRoundChooseCard();
    $availableCardsForPlayer = $stateArgs['_private'][$player->getId()]['cards'];

    $selectedCard = Utils::array_find($availableCardsForPlayer, function ($card) use ($cardId) {
      return $cardId === $card->getId();
    });

    if ($selectedCard === null) {
      throw new \feException("ERROR 011");
    }

    $selectedCard->select();
    // Cards::move($cardId, Locations::cardInPlay($player->getFaction()));

    // Make the player inactive
    $game = Game::get();
    $game->gamestate->setPlayerNonMultiactive($player->getId(), 'next');
    if (count($game->gamestate->getActivePlayerList()) > 0) {
      return;
    }

    foreach ([BRITISH, FRENCH, INDIAN] as $faction) {
      Cards::moveAllInLocation(Locations::selected($faction), Locations::cardInPlay($faction));
    }
    // Cards::moveAllInLocation(Locations::hand(INDIAN), Locations::cardInPlay(INDIAN));

    $britishCard = Cards::getTopOf(Locations::cardInPlay(BRITISH));
    $frenchCard = Cards::getTopOf(Locations::cardInPlay(FRENCH));
    $indianCard = Cards::getTopOf(Locations::cardInPlay(INDIAN));
    Notifications::revealCardsInPlay($britishCard, $frenchCard, $indianCard);

    // Determine initiative
    $factionWithInitiative = $frenchCard->getInitiativeValue() >= $britishCard->getInitiativeValue() ? FRENCH : BRITISH;
    Notifications::gainInitiative($factionWithInitiative);
    $factionPlayer = Players::getPlayerForFaction($factionWithInitiative);

    // Add choose first player step to engine
    $this->ctx->insertAsBrother(new LeafNode([
      'action' => ACTION_ROUND_CHOOSE_FIRST_PLAYER,
      'playerId' => $factionPlayer->getId(),
    ]));

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
