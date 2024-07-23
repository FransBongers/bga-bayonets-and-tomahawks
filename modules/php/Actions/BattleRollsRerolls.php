<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleRollsRerolls extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_ROLLS_REROLLS;
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

  public function stBattleRollsRerolls()
  {


    // $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleRollsRerolls()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRollsRerolls()
  {
    $info = $this->ctx->getInfo();

    $battleRollsSequenceStep = $info['battleRollsSequenceStep'];
    $diceResultsWithRerollSources = $info['diceResultsWithRerollSources'];
    $faction = $info['faction'];

    $diceResults = [];

    $rerollOptions = $this->getRerollOptions($diceResultsWithRerollSources, $battleRollsSequenceStep, $faction);

    foreach ($rerollOptions as $index => $rerollOption) {
      if (count($rerollOption['availableRerollSources']) > 0) {
        $rerollOption['index'] = $index;
        $diceResults[] = $rerollOption;
      }
    }

    return [
      'diceResults' => $diceResults,
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

  public function actPassBattleRollsRerolls()
  {
    $info = $this->ctx->getInfo();

    $playerId = $info['playerId'];
    $battleRollsSequenceStep = $info['battleRollsSequenceStep'];
    $diceResultsWithRerollSources = $info['diceResultsWithRerollSources'];
    $faction = $info['faction'];

    $diceResults = array_map(function ($dieResult) {
      return $dieResult['result'];
    }, $diceResultsWithRerollSources);

    $diceWereRerolled = Utils::array_some($diceResultsWithRerollSources, function ($dieResult) {
      return count($dieResult['usedRerollSources']) > 0;
    });

    $this->ctx->insertAsBrother(new LeafNode([
      'action' => BATTLE_ROLLS_EFFECTS,
      'playerId' => $playerId,
      'battleRollsSequenceStep' => $battleRollsSequenceStep,
      'diceResults' => $diceResults,
      'faction' => $faction,
    ]));

    $this->resolveAction(PASS);
  }

  public function actBattleRollsRerolls($args)
  {
    self::checkAction('actBattleRollsRerolls');

    $dieResult = $args['dieResult'];
    $index = $dieResult['index'];
    $rerollSource = $args['rerollSource'];

    $info = $this->ctx->getInfo();

    $battleRollsSequenceStep = $info['battleRollsSequenceStep'];
    $diceResultsWithRerollSources = $info['diceResultsWithRerollSources'];
    $faction = $info['faction'];

    $rerollOptions = $this->getRerollOptions($diceResultsWithRerollSources, $battleRollsSequenceStep, $faction);

    if (!isset($rerollOptions[$index])) {
      throw new \feException("ERROR 014");
    }

    $rerollOption = $rerollOptions[$index];

    if (!in_array($rerollSource, $rerollOption['availableRerollSources'])) {
      throw new \feException("ERROR 015");
    }

    $oldResult = $diceResultsWithRerollSources[$index]['result'];
    $newResult = BTDice::roll();
    // Check commander death
    $player = self::getPlayer();

    $commander = null;
    if ($rerollSource === COMMANDER) {
      $commander = $this->moveCommanderLeft($faction);
    }

    Notifications::battleReroll($player, $oldResult, $newResult, $rerollSource, $commander);

    $placeNewCommander = false;
    $currentCommanderValue = 3;
    if ($rerollSource === COMMANDER && $newResult === MISS) {
      $commanderCasualtyRoll = BTDice::roll();
      Notifications::battleCommanderCasualtyRoll($player, $commanderCasualtyRoll);
      if ($commanderCasualtyRoll === MISS && $commander !== null) {
        $currentCommanderValue = intval(explode('_', $commander->getLocation())[4]);
        $commander->eliminate($player);
        $placeNewCommander = true;
      }
    }

    $diceResultsWithRerollSources[$index]['result'] = $newResult;
    $diceResultsWithRerollSources[$index]['usedRerollSources'][] = $rerollSource;

    $currentDiceResults = array_map(function ($dieResult) {
      return $dieResult['result'];
    }, $diceResultsWithRerollSources);
    Notifications::battleRollsResultAfterRerolls($currentDiceResults);
    // Else, apply results

    $rerollOptions = $this->getRerollOptions($diceResultsWithRerollSources, $battleRollsSequenceStep, $faction);
    if (Utils::array_some($rerollOptions, function ($rerollOption) {
      return count($rerollOption['availableRerollSources']) > 0;
    })) {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_ROLLS_REROLLS,
        'playerId' => $player->getId(),
        'battleRollsSequenceStep' => $battleRollsSequenceStep,
        'diceResultsWithRerollSources' => $diceResultsWithRerollSources,
        'faction' => $faction,
        'optional' => true,
      ]));
    } else {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_ROLLS_EFFECTS,
        'playerId' => $player->getId(),
        'battleRollsSequenceStep' => $battleRollsSequenceStep,
        'diceResults' => $currentDiceResults,
        'faction' => $faction,
      ]));
    }

    if ($placeNewCommander) {
      $space = Spaces::get(Globals::getActiveBattleSpaceId());
      $units = $space->getUnits();
      $this->selectCommanders($units, [$player], $space, $currentCommanderValue);
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

  private function moveCommanderLeft($faction)
  {
    $commander = $this->getCommandersOnRerollsTrack()[$faction];
    $splitLocation = explode('_', $commander->getLocation());
    $newValue = intval($splitLocation[4]) - 1;
    $newLocation = Locations::commanderRerollsTrack($splitLocation[3] === 'defender', $newValue);
    $commander->setLocation($newLocation);
    return $commander;
  }
}
