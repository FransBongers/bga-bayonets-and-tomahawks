<?php

namespace BayonetsAndTomahawks\Core;

class Notifications
{
  // .########...#######..####.##.......########.########.
  // .##.....##.##.....##..##..##.......##.......##.....##
  // .##.....##.##.....##..##..##.......##.......##.....##
  // .########..##.....##..##..##.......######...########.
  // .##.....##.##.....##..##..##.......##.......##...##..
  // .##.....##.##.....##..##..##.......##.......##....##.
  // .########...#######..####.########.########.##.....##

  // .########..##..........###....########.########
  // .##.....##.##.........##.##......##....##......
  // .##.....##.##........##...##.....##....##......
  // .########..##.......##.....##....##....######..
  // .##........##.......#########....##....##......
  // .##........##.......##.....##....##....##......
  // .##........########.##.....##....##....########
  protected static function notifyAll($name, $msg, $data)
  {
    self::updateArgs($data);
    Game::get()->notifyAllPlayers($name, $msg, $data);
  }

  protected static function notify($player, $name, $msg, $data)
  {
    $playerId = is_int($player) ? $player : $player->getId();
    self::updateArgs($data);
    Game::get()->notifyPlayer($playerId, $name, $msg, $data);
  }

  public static function message($txt, $args = [])
  {
    self::notifyAll('message', $txt, $args);
  }

  public static function messageTo($player, $txt, $args = [])
  {
    $playerId = is_int($player) ? $player : $player->getId();
    self::notify($playerId, 'message', $txt, $args);
  }

  // TODO: check how to handle this in game log
  public static function newUndoableStep($player, $stepId)
  {
    self::notify($player, 'newUndoableStep', clienttranslate('Undo to here'), [
      'stepId' => $stepId,
      'preserve' => ['stepId'],
    ]);
  }

  public static function clearTurn($player, $notifIds)
  {
    self::notifyAll('clearTurn', clienttranslate('${player_name} restarts their turn'), [
      'player' => $player,
      'notifIds' => $notifIds,
    ]);
  }

  public static function refreshHand($player, $hand)
  {
    // foreach ($hand as &$card) {
    //   $card = self::filterCardDatas($card);
    // }
    self::notify($player, 'refreshHand', '', [
      'player' => $player,
      'hand' => $hand,
    ]);
  }

  public static function refreshUI($datas)
  {
    // Keep only the thing that matters
    $fDatas = [
      // Add data here that needs to be refreshed
    ];

    unset($datas['staticData']);

    self::notifyAll('refreshUI', '', [
      // 'datas' => $fDatas,
      'datas' => $datas,
    ]);
  }

  public static function log($message, $data)
  {
    // Keep only the thing that matters
    $fDatas = [
      // Add data here that needs to be refreshed
    ];

    self::notifyAll('log', '', [
      'message' => $message,
      'data' => $data,
    ]);
  }

  // .##.....##.########..########.....###....########.########
  // .##.....##.##.....##.##.....##...##.##......##....##......
  // .##.....##.##.....##.##.....##..##...##.....##....##......
  // .##.....##.########..##.....##.##.....##....##....######..
  // .##.....##.##........##.....##.#########....##....##......
  // .##.....##.##........##.....##.##.....##....##....##......
  // ..#######..##........########..##.....##....##....########

  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  /*
   * Automatically adds some standard field about player and/or card
   */
  protected static function updateArgs(&$args)
  {
    if (isset($args['player'])) {
      $args['player_name'] = $args['player']->getName();
      $args['playerId'] = $args['player']->getId();
      unset($args['player']);
    }
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private static function getFactionName($faction)
  {
    $map = [
      BRITISH => clienttranslate('British'),
      FRENCH => clienttranslate('French'),
      INDIAN => clienttranslate("Indian"),
    ];
    return $map[$faction];
  }

  // ..######......###....##.....##.########
  // .##....##....##.##...###...###.##......
  // .##.........##...##..####.####.##......
  // .##...####.##.....##.##.###.##.######..
  // .##....##..#########.##.....##.##......
  // .##....##..##.....##.##.....##.##......
  // ..######...##.....##.##.....##.########

  // .##.....##.########.########.##.....##..#######..########...######.
  // .###...###.##..........##....##.....##.##.....##.##.....##.##....##
  // .####.####.##..........##....##.....##.##.....##.##.....##.##......
  // .##.###.##.######......##....#########.##.....##.##.....##..######.
  // .##.....##.##..........##....##.....##.##.....##.##.....##.......##
  // .##.....##.##..........##....##.....##.##.....##.##.....##.##....##
  // .##.....##.########....##....##.....##..#######..########...######.

  public static function activateStack($player, $space, $actionName)
  {
    self::message(clienttranslate('${player_name} performs ${tkn_boldText_action} with stack in ${tkn_boldText_space}'), [
      'player' => $player,
      'tkn_boldText_action' => $actionName,
      'tkn_boldText_space' => $space->getName(),
      'i18n' => ['tkn_boldText_action', 'tkn_boldText_space']
    ]);
  }

  public static function battle($player, $space)
  {
    self::notifyAll('battle', clienttranslate('${player_name} attacks ${tkn_boldText_space}'), [
      'player' => $player,
      'tkn_boldText_space' => $space->getName(),
      'space' => $space,
      'i18n' => ['tkn_boldText_space']
    ]);
  }

  public static function battleStart($space, $attackerMarker, $defenderMarker)
  {
    self::notifyAll('battleStart', clienttranslate('Battle in ${tkn_boldText_space}'), [
      'tkn_boldText_space' => $space->getName(),
      // 'space' => $space,
      'attackerMarker' => $attackerMarker->jsonSerialize(),
      'defenderMarker' => $defenderMarker->jsonSerialize(),
      'i18n' => ['tkn_boldText_space']
    ]);
  }

  public static function battleCleanup($space, $attackerMarker, $defenderMarker)
  {
    self::notifyAll('battleCleanup', '', [
      'space' => $space,
      'attackerMarker' => $attackerMarker->jsonSerialize(),
      'defenderMarker' => $defenderMarker->jsonSerialize(),
    ]);
  }

  public static function battleSelectCommander($player, $commander)
  {
    self::notifyAll('battleSelectCommander', clienttranslate('${player_name} selects ${tkn_boldText_commanderName} to use in the Battle'), [
      'player' => $player,
      'tkn_boldText_commanderName' => $commander->getCounterText(),
      'commander' => $commander->jsonSerialize(),
      'i18n' => ['tkn_boldText_commanderName']
    ]);
  }
  

  public static function drawCard($player, $card)
  {
    self::notify($player, 'drawCardPrivate', clienttranslate('Private: ${player_name} draws  ${cardId}'), [
      'player' => $player,
      'card' => $card,
      'cardId' => $card->getId(),
    ]);
  }

  public static function discardCardFromHand($player, $card)
  {
    $factionName = self::getFactionName($card->getFaction());

    self::notify($player, 'discardCardFromHandPrivate', clienttranslate('Private: ${player_name} discards card ${tkn_card}'), [
      'player' => $player,
      'card' => $card,
      'tkn_card' => $card->getId(),
    ]);

    self::notifyAll("discardCardFromHand", clienttranslate('${player_name} discards a ${factionName} card'), [
      'player' => $player,
      'faction' => $card->getFaction(),
      'factionName' => $factionName,
      'preserve' => ['playerId'],
      'i18n' => ['factionName']
    ]);
  }

  public static function discardCardInPlay($card)
  {
    self::notifyAll("discardCardInPlay", '', [
      'card' => $card,
    ]);
  }

  public static function discardCardsInPlayMessage()
  {
    self::message(clienttranslate('All played cards are discarded'), []);
  }

  public static function discardReserveCards()
  {
    self::message(clienttranslate('Both players discard their Reserve card'), []);
  }

  public static function gainInitiative($faction)
  {
    self::message(clienttranslate('The ${factionName} gain initiative'), [
      'factionName' => self::getFactionName($faction),
      'i18n' => ['factionName']
    ]);
  }

  public static function interception($otherPlayer, $space, $dieResult, $intercepted)
  {
    $message = $intercepted ?
      clienttranslate('${player_name} rolls ${tkn_dieResult} for Interception in ${tkn_boldText}. The Raid is intercepted') :
      clienttranslate('${player_name} rolls ${tkn_dieResult} for Interception in ${tkn_boldText}. The Raid is not intercepted');

    self::message($message, [
      'player' => $otherPlayer,
      'tkn_boldText' => $space->getName(),
      'tkn_dieResult' => $dieResult,
      'i18n' => ['tkn_boldText']
    ]);
  }

  public static function loseControl($player, $space)
  {
    self::notifyAll("loseControl", clienttranslate('${player_name} loses control of ${tkn_boldText_space}'), [
      'player' => $player,
      'faction' => $player->getFaction(),
      'tkn_boldText_space' => $space->getName(),
      'space' => $space,
      'i18n' => ['tkn_boldText_space'],
    ]);
  }

  public static function moveRaidPointsMarker($raidMarker)
  {
    self::notifyAll("moveRaidPointsMarker", '', [
      'marker' => $raidMarker->jsonSerialize(),
    ]);
  }

  public static function moveRoundMarker($marker, $nextRoundStep)
  {
    self::notifyAll("moveRoundMarker", '', [
      'nextRoundStep' => $nextRoundStep,
      'marker' => $marker,
    ]);
  }

  public static function moveStack($player, $units, $origin, $destination)
  {
    self::notifyAll("moveStack", clienttranslate('${player_name} moves a stack from ${originName} to ${destinationName}'), [
      'player' => $player,
      'originName' => $origin->getName(),
      'destination' => $destination,
      'destinationName' => $destination->getName(),
      'faction' => $player->getFaction(),
      'stack' => $units,
      'i18n' => ['originName', 'destinationName'],
    ]);
  }

  public static function moveUnit($player, $unit, $origin, $destination)
  {
    self::notifyAll("moveUnit", clienttranslate('${player_name} moves ${tkn_unit} from ${tkn_boldText_1} to ${tkn_boldText_2}'), [
      'player' => $player,
      'tkn_boldText_1' => $origin->getName(),
      'destination' => $destination,
      'tkn_boldText_2' => $destination->getName(),
      'faction' => $player->getFaction(),
      'unit' => $unit->jsonSerialize(),
      'tkn_unit' => $unit->getCounterId(),
      'i18n' => ['tkn_boldText_1', 'tkn_boldText_2'],
    ]);
  }

  public static function scoreVictoryPoints($player, $otherPlayer, $marker, $points)
  {
    $message = '';
    if ($points === 1) {
      $message = clienttranslate('${player_name} scores ${tkn_boldText_points} Victory Point');
    } else if ($points > 0) {
      $message = clienttranslate('${player_name} scores ${tkn_boldText_points} Victory Points');
    } else if ($points === -1) {
      $message = clienttranslate('${player_name} loses ${tkn_boldText_points} Victory Point');
    } else if ($points < 0) {
      $message = clienttranslate('${player_name} loses ${tkn_boldText_points} Victory Points');
    }


    self::notifyAll("scoreVictoryPoints", $message, [
      'player' => $player,
      'marker' => $marker->jsonSerialize(),
      'tkn_boldText_points' => abs($points),
      'points' => [
        $player->getId() => $player->getScore(),
        $otherPlayer->getId() => $otherPlayer->getScore(),
      ]
    ]);
  }

  public static function moveYearMarker($marker, $location)
  {
    self::notifyAll("moveYearMarker", '', [
      'location' => $location,
      'marker' => $marker,
    ]);
  }

  public static function placeUnitInLosses($player, $unit)
  {
    self::notifyAll("placeUnitInLosses", clienttranslate('${player_name} places ${tkn_unit} in their Losses Box'), [
      'player' => $player,
      'unit' => $unit,
      'tkn_unit' => $unit->getCounterId()
      // 'preserve' => ['playerId'],
    ]);
  }

  public static function raidPoints($player, $space, $raidPoints)
  {
    $message = $raidPoints === 1 ?
      clienttranslate('${player_name} gains ${tkn_boldText} Raid Point') :
      clienttranslate('${player_name} gains ${tkn_boldText} Raid Points');

    self::notifyAll("raidPoints", $message, [
      'player' => $player,
      'faction' => $player->getFaction(),
      'space' => $space,
      'tkn_boldText' => $raidPoints,
    ]);
  }

  public static function raidResolution($player, $raidResolution, $raidIsSuccessful)
  {
    $message = $raidIsSuccessful ?
      clienttranslate('${player_name} rolls ${tkn_dieResult} for Raid Resolution: the Raid succeeds') :
      clienttranslate('${player_name} rolls ${tkn_dieResult} for Raid Resolution: the Raid fails');

    self::message($message, [
      'player' => $player,
      'tkn_dieResult' => $raidResolution,
    ]);
  }

  public static function revealCardsInPlay($britishCard, $frenchCard, $indianCard)
  {
    self::notifyAll("revealCardsInPlay", clienttranslate('Both players have selected a card. Cards are revealed'), [
      'british' => $britishCard,
      'french' => $frenchCard,
      'indian' => $indianCard,
    ]);
  }

  public static function selectReserveCard($player)
  {
    // self::notify($player,'selectReserveCardPrivate', clienttranslate('Private: ${player_name} discard ${cardId}'),[
    //   'player' => $player,
    //   'discardedCard' => $discardedCard,
    //   'cardId' => $discardedCard->getId(),
    // ]);

    self::notifyAll("selectReserveCard", clienttranslate('${player_name} selects their reserve card'), [
      'player' => $player,
      'faction' => $player->getFaction(),
      // 'preserve' => ['playerId'],
    ]);
  }

  public static function takeControl($player, $space)
  {
    self::notifyAll("takeControl", clienttranslate('${player_name} takes control of ${tkn_boldText_space}'), [
      'player' => $player,
      'faction' => $player->getFaction(),
      'tkn_boldText_space' => $space->getName(),
      'space' => $space,
      'i18n' => ['tkn_boldText_space'],
    ]);
  }
}
