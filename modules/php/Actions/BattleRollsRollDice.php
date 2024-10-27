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

class BattleRollsRollDice extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_ROLLS_ROLL_DICE;
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

  public function stBattleRollsRollDice()
  {
    $info = $this->ctx->getInfo();

    $faction = $info['faction'];
    $unitIds = $info['unitIds'];
    $battleRollsSequenceStep = $info['battleRollsSequenceStep'];
    $player = self::getPlayer();

    $diceResults = [];

    for ($i = 0; $i < count($unitIds); $i++) {
      $diceResults[] = BTDice::roll();
    }

    Notifications::battleRolls($player, $battleRollsSequenceStep, $diceResults, $unitIds);

    $diceResultsWithRerollSources = array_map(function ($dieResult) {
      return [
        'result' => $dieResult,
        'usedRerollSources' => [],
      ];
    }, $diceResults);

    // Check if possible to reroll
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
      // Else, apply results
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_ROLLS_EFFECTS,
        'playerId' => $player->getId(),
        'battleRollsSequenceStep' => $battleRollsSequenceStep,
        'diceResults' => $diceResults,
        'faction' => $faction,
      ]));
    }

    $this->resolveAction(['automatic' => true], true);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleRollsRollDice()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRollsRollDice()
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

  public function actPassBattleRollsRollDice()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleRollsRollDice($args)
  {
    self::checkAction('actBattleRollsRollDice');



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
