<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundChooseFirstPlayer extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_CHOOSE_FIRST_PLAYER;
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

  public function stActionRoundChooseFirstPlayer()
  {
    BTHelpers::updateStepTracker(SELECT_FIRST_PLAYER_STEP);
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundChooseFirstPlayer()
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

  public function actPassActionRoundChooseFirstPlayer()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actActionRoundChooseFirstPlayer($args)
  {
    self::checkAction('actActionRoundChooseFirstPlayer');

    $firstPlayerId = $args['playerId'];

    $players = Players::getAll()->toArray();

    // $firstPlayer = Players::get($firstPlayerId);
    $firstPlayer = Utils::array_find($players, function ($player) use ($firstPlayerId) {
      return $player->getId() === $firstPlayerId;
    });
    $secondPlayer = Utils::array_find($players, function ($player) use ($firstPlayerId) {
      return $player->getId() !== $firstPlayerId;
    });

    Notifications::message(clienttranslate('${player_name} chooses the ${faction} to be first'), [
      'player' => self::getPlayer(),
      'faction' => Notifications::getFactionName($firstPlayer->getFaction()),
    ]);

    Globals::setFirstPlayerId($firstPlayer->getId());
    Globals::setSecondPlayerId($secondPlayer->getId());

    $cardsInPlay = Cards::getCardsInPlay();

    foreach([$firstPlayer, $secondPlayer] as $player) {
      $faction = $player->getFaction();
      if ($faction === FRENCH && $cardsInPlay[INDIAN]->getEvent() !== null && $cardsInPlay[INDIAN]->getEvent()[AR_START]) {
        $this->ctx->getParent()->pushChild(new LeafNode([
          'action' => ACTION_ROUND_RESOLVE_AR_START_EVENT,
          'playerId' => $player->getId(),
          'faction' => INDIAN,
          'cardId' => $cardsInPlay[INDIAN]->getId(),
        ]));
      }
      if ($cardsInPlay[$faction]->getEvent() !== null && $cardsInPlay[$faction]->getEvent()[AR_START]) {
        $this->ctx->getParent()->pushChild(new LeafNode([
          'action' => ACTION_ROUND_RESOLVE_AR_START_EVENT,
          'playerId' => $player->getId(),
          'faction' => $faction,
          'cardId' => $cardsInPlay[$faction]->getId(),
        ]));
      }
    }

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
