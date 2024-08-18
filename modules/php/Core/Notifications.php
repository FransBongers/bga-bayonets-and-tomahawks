<?php

namespace BayonetsAndTomahawks\Core;

use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Spaces;

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

  public static function getUnitsLog($units)
  {
    $unitsLog = '';
    $unitsLogArgs = [];

    foreach ($units as $index => $unit) {
      $key = 'tkn_unit_' . $index;
      $unitsLog = $unitsLog . '${' . $key . '}';
      $unitsLogArgs[$key] = $unit->getCounterId();
    }

    return [
      'log' => $unitsLog,
      'args' => $unitsLogArgs,
    ];
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

  public static function addSpentMarkerToUnits($player, $units)
  {
    self::notifyAll('addSpentMarkerToUnits', clienttranslate('${player_name} places Spent marker on ${unitsLog} in ${tkn_boldText_space}'), [
      'player' => $player,
      'tkn_boldText_space' => Spaces::get($units[0]->getLocation())->getName(),
      'unitsLog' => self::getUnitsLog($units),
      'units' => $units,
      'i18n' => ['tkn_boldText_space']
    ]);
  }

  public static function achievedVictoryThreshold($player)
  {
    self::message(clienttranslate('${player_name} has achieved their Victory Threshold'), [
      'player' => $player,
    ]);
  }

  public static function activateStack($player, $space, $actionName)
  {
    self::message(clienttranslate('${player_name} performs ${tkn_boldText_action} with stack in ${tkn_boldText_space}'), [
      'player' => $player,
      'tkn_boldText_action' => $actionName,
      'tkn_boldText_space' => $space->getName(),
      'i18n' => ['tkn_boldText_action', 'tkn_boldText_space']
    ]);
  }

  public static function moveBattleVictoryMarker($player, $marker, $numberOfPositions)
  {
    $backward = $numberOfPositions < 0;
    $numberOfPositions = abs($numberOfPositions);

    $text = clienttranslate('${player_name} advances their Battle Victory Marker 1 position');

    if (!$backward && $numberOfPositions > 1) {
      $text = clienttranslate('${player_name} advances their Battle Victory Marker ${numberOfPositions} positions');
    } else if ($backward && $numberOfPositions === 1) {
      $text = clienttranslate('${player_name} moves their Battle Victory Marker 1 position backward');
    } else if ($backward) {
      $text = clienttranslate('${player_name} moves their Battle Victory Marker ${numberOfPositions} positions backward');
    }
      
    self::notifyAll('moveBattleVictoryMarker', $text, [
      'player' => $player,
      'marker' => $marker->jsonSerialize(),
      'numberOfPositions' => $numberOfPositions,
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

  public static function battleRemoveMarker($player, $space)
  {
    self::notifyAll('battleRemoveMarker', clienttranslate('${player_name} removes Battle marker from ${tkn_boldText_space}'), [
      'player' => $player,
      'tkn_boldText_space' => $space->getName(),
      'space' => $space,
      'i18n' => ['tkn_boldText_space']
    ]);
  }

  public static function battleCommanderCasualtyRoll($player, $commanderCasualtyRoll)
  {
    self::message(clienttranslate('${player_name} checks for Commander Casualty and rolls ${tkn_dieResult}'), [
      'player' => $player,
      'tkn_dieResult' => $commanderCasualtyRoll
    ]);
  }

  public static function battleNoUnitsLeft($player)
  {
    self::message(clienttranslate('${player_name} has no units left'), [
      'player' => $player
    ]);
  }

  public static function battleRolls($player, $battleRollsSequenceStep, $diceResults, $unitIds)
  {
    // ${tkn_dieResult}
    $diceResultsLog = [];
    $diceResultsArgs = [];
    foreach ($diceResults as $index => $dieResult) {
      $key = 'tkn_dieResult_' . $index;
      $diceResultsLog[] = '${' . $key . '}';
      $diceResultsArgs[$key] = $dieResult;
    };

    self::notifyAll('battleRolls', clienttranslate('${player_name} rolls ${diceResultsLog} with ${battleRollsSequenceStep}'), [
      'player' => $player,
      // 'tkn_boldText_space' => $space->getName(),
      // 'space' => $space,
      'diceResultsLog' => [
        'log' => implode('', $diceResultsLog),
        'args' => $diceResultsArgs,
      ],
      'battleRollsSequenceStep' => $battleRollsSequenceStep,
      // 'i18n' => ['tkn_boldText_space']
    ]);
  }

  public static function battleRollsResultAfterRerolls($diceResults)
  {
    // ${tkn_dieResult}
    $diceResultsLog = [];
    $diceResultsArgs = [];
    foreach ($diceResults as $index => $dieResult) {
      $key = 'tkn_dieResult_' . $index;
      $diceResultsLog[] = '${' . $key . '}';
      $diceResultsArgs[$key] = $dieResult;
    };

    self::message(clienttranslate('Result after rerolls: ${diceResultsLog}'), [
      'diceResultsLog' => [
        'log' => implode('', $diceResultsLog),
        'args' => $diceResultsArgs,
      ],
    ]);
  }

  public static function battleRout($faction)
  {
    self::message(clienttranslate('${faction_name} stack is routed'), [
      'faction_name' => self::getFactionName($faction),
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

  public static function battleReroll($player, $oldResult, $newResult, $rerollSource, $commander = null)
  {
    self::notifyAll('battleReroll', clienttranslate('${player_name} rerolls ${tkn_dieResult_old} to ${tkn_dieResult_new}'), [
      'player' => $player,
      'tkn_dieResult_old' => $oldResult,
      'tkn_dieResult_new' => $newResult,
      'rerollSource' => $rerollSource,
      'commander' => $commander === null ? null : $commander->jsonSerialize(),
    ]);
  }

  public static function battleReturnCommander($player, $commander, $spaceId)
  {
    self::notifyAll('battleReturnCommander', clienttranslate('${player_name} returns ${tkn_boldText_commanderName} from the Commander Rerolls track'), [
      'player' => $player,
      'tkn_boldText_commanderName' => $commander->getCounterText(),
      'commander' => $commander->jsonSerialize(),
      'spaceId' => $spaceId,
      'i18n' => ['tkn_boldText_commanderName']
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

  public static function battleWinner($player, $space)
  {
    self::message(clienttranslate('${player_name} wins the Battle in ${tkn_boldText_spaceName}'), [
      'player' => $player,
      'tkn_boldText_spaceName' => $space->getName(),
      'i18n' => ['tkn_boldText_spaceName']
    ]);
  }

  public static function commanderDraw($unit, $commander)
  {
    self::notifyAll("commanderDraw", clienttranslate('Commander draw for ${tkn_unit_brigade} is ${tkn_unit_commander}'), [
      'tkn_unit_brigade' => $unit->getCounterId(),
      'tkn_unit_commander' => $commander->getCounterId(),
      'commander' => $commander->jsonSerialize(),
    ]);
  }

  public static function construction($player, $activatedUnit, $space)
  {
    self::message(clienttranslate('${player_name} activates ${tkn_unit} to perform Construction on ${tkn_boldText_spaceName}'), [
      'player' => $player,
      'tkn_unit' => $activatedUnit->getCounterId(),
      'tkn_boldText_spaceName' => $space->getName(),
      'i18n' => ['tkn_boldText_spaceName'],
    ]);
  }


  public static function constructionFort($player, $space, $fort, $faction, $option)
  {
    switch ($option) {
      case PLACE_FORT_CONSTRUCTION_MARKER:
        $text = clienttranslate('${player_name} places ${tkn_marker} on ${tkn_boldText_spaceName}');
        break;
      case REPLACE_FORT_CONSTRUCTION_MARKER:
        $text = clienttranslate('${player_name} replaces ${tkn_marker} on ${tkn_boldText_spaceName} with ${tkn_unit}');
        break;
      case REPAIR_FORT:
        $text = clienttranslate('${player_name} repairs ${tkn_unit} on ${tkn_boldText_spaceName}');
        break;
      case REMOVE_FORT:
        $text = clienttranslate('${player_name} removes ${tkn_unit} from ${tkn_boldText_spaceName}');
        break;
      case REMOVE_FORT_CONSTRUCTION_MARKER:
        $text = clienttranslate('${player_name} removes ${tkn_marker} from ${tkn_boldText_spaceName}');
        break;
    }

    self::notifyAll("constructionFort", $text, [
      'player' => $player,
      'tkn_marker' => FORT_CONSTRUCTION_MARKER,
      'tkn_boldText_spaceName' => $space->getName(),
      'faction' => $faction,
      'fort' => $fort !== null ? $fort->jsonSerialize() : null,
      'option' => $option,
      'space' => $space->jsonSerialize(),
      'i18n' => ['tkn_boldText_spaceName']
    ]);
  }

  public static function constructionRoad($player, $connection, $sourceSpace, $destinationSpace)
  {
    $text = $connection->getRoad() === ROAD_UNDER_CONTRUCTION ?
      clienttranslate('${player_name} places ${tkn_marker_construction} on Path between ${tkn_boldText_origin} and ${tkn_boldText_destination}') :
      clienttranslate('Flip ${tkn_marker_construction} to ${tkn_marker_road} on Path between ${tkn_boldText_origin} and ${tkn_boldText_destination}?');

    self::notifyAll("constructionRoad", $text, [
      'player' => $player,
      'tkn_marker_construction' => ROAD_CONSTRUCTION_MARKER,
      'tkn_marker_road' => ROAD_MARKER,
      'tkn_boldText_origin' => $sourceSpace->getName(),
      'tkn_boldText_destination' => $destinationSpace->getName(),
      'connection' => $connection->jsonSerialize(),
      'i18n' => ['tkn_boldText_origin', 'tkn_boldText_destination']
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

  public static function eliminateUnit($player, $unit, $previousLocation)
  {
    $text = $unit->getLocation() === REMOVED_FROM_PLAY ?
      clienttranslate('${player_name} removes ${tkn_unit} on ${tkn_boldText_spaceName} from play') :
      clienttranslate('${player_name} eliminates ${tkn_unit} on ${tkn_boldText_spaceName}');

    $spaceName = '';
    if ($previousLocation === Locations::lossesBox(BRITISH)) {
      $spaceName = clienttranslate('British Losses Box');
    } else if ($previousLocation === Locations::lossesBox(FRENCH)) {
      $spaceName = clienttranslate('French Losses Box');
    } else {
      $spaceName = Spaces::get($previousLocation)->getName();
    }

    self::notifyAll("eliminateUnit", $text, [
      'player' => $player,
      'unit' => $unit->jsonSerialize(),
      'tkn_unit' => $unit->getCounterId(),
      'tkn_boldText_spaceName' => $spaceName,
      'i18n' => ['tkn_boldText_spaceName'],
    ]);
  }

  public static function drawnBonusUnits($player, $units, $location)
  {
    self::notifyAll("drawnReinforcements", clienttranslate('${player_name} takes units from the VoW Bonus pool: ${unitsLog}'), [
      'player' => $player,
      'units' => $units,
      'location' => $location,
      'unitsLog' => self::getUnitsLog($units),
    ]);
  }

  public static function drawnReinforcements($player, $units, $location)
  {
    $textMap = [
      REINFORCEMENTS_FLEETS => clienttranslate('${player_name} draws Fleets: ${unitsLog}'),
      REINFORCEMENTS_BRITISH => clienttranslate('${player_name} draws British reinforcements: ${unitsLog}'), // how to differentiate between COlonial?
      REINFORCEMENTS_FRENCH => clienttranslate('${player_name} draws French reinforcements: ${unitsLog}'),
      REINFORCEMENTS_COLONIAL => clienttranslate('${player_name} draws Colonial reinforcements: ${unitsLog}')
    ];

    self::notifyAll("drawnReinforcements", $textMap[$location], [
      'player' => $player,
      'units' => $units,
      'location' => $location,
      'unitsLog' => self::getUnitsLog($units),
    ]);
  }

  public static function gainInitiative($faction)
  {
    self::message(clienttranslate('The ${factionName} gain initiative'), [
      'factionName' => self::getFactionName($faction),
      'i18n' => ['factionName']
    ]);
  }

  public static function indianNationControl($player, $indianNation, $faction)
  {
    self::notifyAll("indianNationControl", clienttranslate('${player_name} takes control of the ${tkn_boldText_indianNation} Indian Nation'), [
      'player' => $player,
      'faction' => $faction,
      'indianNation' => $indianNation,
      'tkn_boldText_indianNation' => $indianNation === CHEROKEE ? clienttranslate('Cherokee') : clienttranslate('Iroquois Confederacy'),
      'i18n' => ['tkn_boldText_indianNation'],
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

  public static function marshalTroops($player, $activatedUnit, $targetSpace)
  {
    self::notifyAll("addSpentMarkerToUnits", clienttranslate('${player_name} activates ${tkn_unit} to Marshal Troops on ${tkn_boldText_spaceName}'), [
      'player' => $player,
      'units' => [$activatedUnit->jsonSerialize()],
      'tkn_unit' => $activatedUnit->getCounterId(),
      'tkn_boldText_spaceName' => $targetSpace->getName(),
      'i18n' => ['tkn_boldText_spaceName'],
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
      'marker' => $marker->jsonSerialize(),
    ]);
  }

  public static function moveStack($player, $units, $markers, $origin = null, $destination = null, $connection = null, $isRetreat = false, $sailMove = false)
  {
    $text = clienttranslate('${player_name} moves a stack from ${tkn_boldText_from} to ${tkn_boldText_to}');

    if ($isRetreat) {
      $text = clienttranslate('${player_name} retreats their stack from ${tkn_boldText_from} to ${tkn_boldText_to}');
    } else if ($sailMove && $destination === null) {
      $text = clienttranslate('${player_name} moves a stack from ${tkn_boldText_from} to the ${tkn_boldText_to}');
    } else if ($sailMove && $origin === null) {
      $text = clienttranslate('${player_name} Lands their units on the ${tkn_boldText_from} on ${tkn_boldText_to}');
    }

    self::notifyAll("moveStack", $text, [
      'player' => $player,
      'tkn_boldText_from' => $origin !== null ? $origin->getName() : clienttranslate('Sail Box'),
      'destinationId' => $destination !== null ? $destination->getId() : SAIL_BOX,
      'tkn_boldText_to' => $destination !== null ? $destination->getName() : clienttranslate('Sail Box'),
      'faction' => $player->getFaction(),
      'stack' => $units,
      'markers' => $markers,
      'connection' => $connection,
      'i18n' => ['tkn_boldText_from', 'tkn_boldText_to'],
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

  public static function noFrenchFleetInPool()
  {
    self::message(clienttranslate('No French Fleet in the fleet pool to remove'), []);
  }

  public static function placeStackMarker($player, $marker, $space)
  {
    self::notifyAll("placeStackMarker", clienttranslate('${player_name} places ${tkn_marker} on their stack in ${tkn_boldText_spaceName}'), [
      'player' => $player,
      'marker' => $marker->jsonSerialize(),
      'tkn_marker' => $marker->getType(),
      'tkn_boldText_spaceName' => $space->getName(),
      'i18n' => ['tkn_boldText_spaceName'],
    ]);
  }

  public static function flipUnit($player, $unit)
  {
    $text = $unit->getState() === 1 ? clienttranslate('${player_name} flips ${tkn_unit} in ${tkn_boldText_spaceName} to Reduced') : clienttranslate('${player_name} flips ${tkn_unit} in ${tkn_boldText_spaceName} to Full');

    self::notifyAll("flipUnit", $text, [
      'player' => $player,
      'unit' => $unit->jsonSerialize(),
      'tkn_boldText_spaceName' => Spaces::get($unit->getLocation())->getName(),
      'tkn_unit' => $unit->getCounterId() . ':' . ($unit->getState() === 0 ? 'reduced' : 'full'),
      'i18n' => ['tkn_boldText_spaceName']
    ]);
  }

  public static function removeFromPlay($unit, $previousLocation = null)
  {
    $text = $previousLocation === POOL_FLEETS ?
      clienttranslate('${tkn_unit} is removed from the fleet pool') :
      clienttranslate('${tkn_unit} is removed from play');

    self::notifyAll("eliminateUnit", $text, [
      'unit' => $unit->jsonSerialize(),
      'tkn_unit' => $unit->getCounterId()
    ]);
  }

  public static function removeMarkerFromStack($player, $marker, $previousLocation)
  {
    $locationId = explode('_', $previousLocation)[0];

    self::notifyAll("removeMarkerFromStack", clienttranslate('${player_name} removes ${tkn_marker} from their stack in ${tkn_boldText_spaceName}'), [
      'player' => $player,
      'marker' => $marker->jsonSerialize(),
      'from' => $previousLocation,
      'tkn_marker' => $marker->getType(),
      'tkn_boldText_spaceName' => $locationId === SAIL_BOX ? clienttranslate('Sail Box') : Spaces::get($locationId)->getName(),
      'i18n' => ['tkn_boldText_spaceName']
    ]);
  }

  public static function returnToPool($token)
  {
    $text = clienttranslate('${tkn_unit} is put back in the pool');

    self::notifyAll("returnToPool", $text, [
      'unit' => $token->jsonSerialize(),
      'tkn_unit' => $token->getCounterId()
    ]);
  }

  public static function removeMarkersEndOfActionRound($spentUnits)
  {
    self::notifyAll("removeMarkersEndOfActionRound", clienttranslate('All Spent, Landing and Marshal markers are removed'), [
      'spentUnits' => $spentUnits,
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

  public static function placeUnits($player, $units, $space, $faction)
  {
    self::notifyAll("placeUnits", clienttranslate('${player_name} places ${unitsLog} in ${tkn_boldText_spaceName}'), [
      'player' => $player,
      'unitsLog' => self::getUnitsLog($units),
      'units' => $units,
      'faction' => $faction,
      'spaceId' => $space->getId(),
      'tkn_boldText_spaceName' => $space->getName(),
      'i18n' => ['tkn_boldText_spaceName'],
    ]);
  }

  public static function placeUnitInLosses($player, $unit, $ownLossesBox)
  {
    $text = clienttranslate('${player_name} places ${tkn_unit} in their Losses Box');
    if (!$ownLossesBox) {
      $text = clienttranslate('${player_name} sends ${tkn_unit} to the Losses Box');
    }

    self::notifyAll("placeUnitInLosses", $text, [
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

  public static function yearEndBonus($player, $criteriaHaveBeenMet)
  {
    $text = $criteriaHaveBeenMet ? clienttranslate('${player_name} gets Year End Bonus') : clienttranslate('${player_name} does not get Year End Bonus');
    self::message($text, [
      'player' => $player
    ]);
  }

  public static function vagariesOfWarPickUnits($player, $counterId, $units, $location)
  {
    self::notifyAll("vagariesOfWarPickUnits", clienttranslate('${player_name} uses ${tkn_unit_vowToken} to pick ${unitsLog}'), [
      'player' => $player,
      'unitsLog' => self::getUnitsLog($units),
      'tkn_unit_vowToken' => $counterId,
      'units' => $units,
      'location' => $location,
    ]);
  }
}
