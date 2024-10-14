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

class ActionRoundChooseReaction extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_CHOOSE_REACTION;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundChooseReaction()
  {
    // Notifications::log('stPreActionRoundChooseReaction', $this->ctx->getInfo());

  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundChooseReaction()
  {

    $player = $this->getPlayer();
    $faction = $player->getFaction();
    $cardInPlay = Cards::getTopOf(Locations::cardInPlay($faction));

    $lostActionPoints = $faction === BRITISH ? Globals::getLostAPBritish() : Globals::getLostAPFrench();
    $addedActionPoints = $faction === FRENCH ? Globals::getAddedAPFrench() : [];
    $actionPoints = BTHelpers::getAvailableActionPoints($lostActionPoints, $cardInPlay, $addedActionPoints);

    return [
      'actionPoints' => $actionPoints,
      'faction' => $faction,
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

  public function actPassActionRoundChooseReaction()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actActionRoundChooseReaction($args)
  {

    self::checkAction('actActionRoundChooseReaction');

    $actionPointId = $args['actionPointId'];

    $stateArgs = $this->argsActionRoundChooseReaction();

    // TODO: store which AP from card
    $actionPoint = Utils::array_find($stateArgs['actionPoints'], function ($ap) use ($actionPointId) {
      return $actionPointId === $ap['id'];
    });

    if ($actionPoint === null) {
      throw new \feException("ERROR 007");
    }

    $player = self::getPlayer();
    Notifications::message(clienttranslate('${player_name} holds ${tkn_actionPoint} for Reaction'), [
      'player' => $player,
      'tkn_actionPoint' => $player->getFaction() . ':' . $actionPointId,
    ]);

    Globals::setReactionActionPointId($actionPointId);

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
