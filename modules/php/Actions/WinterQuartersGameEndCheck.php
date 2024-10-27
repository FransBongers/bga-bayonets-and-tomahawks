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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\WarInEuropeChits;
use BayonetsAndTomahawks\Models\Marker;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenario;

class WinterQuartersGameEndCheck extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_GAME_END_CHECK;
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

  public function stWinterQuartersGameEndCheck()
  {
    $scenario = Scenarios::get();
    $year = BTHelpers::getYear();

    $players = Players::getPlayersForFactions();

    // 1. Scenario Bonuses
    foreach ([BRITISH, FRENCH] as $faction) {
      $bonus = $scenario->getYearEndBonus($faction, $year);
      if ($bonus > 0) {
        Notifications::yearEndBonus($players[$faction], true);
        Players::scoreVictoryPoints($players[$faction], $bonus);
      } else {
        Notifications::yearEndBonus($players[$faction], false);
      }
    }

    /**
     * 2. WIE Bonus
     */
    $this->wieChitYearEndScoring($players);


    // 3. Check Victory Threshold
    foreach ([BRITISH, FRENCH] as $faction) {

      if ($scenario->hasAchievedVictoryThreshold($faction, $year)) {
        Notifications::achievedVictoryThreshold($players[$faction]);
        Players::setWinner($players[$faction]);

        // Add pre end of game state to set statistics?
        Game::get()->gamestate->jumpToState(ST_PRE_END_GAME);
        return;
      }
    }

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersGameEndCheck()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersGameEndCheck()
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

  public function actPassWinterQuartersGameEndCheck()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actWinterQuartersGameEndCheck($args)
  {
    self::checkAction('actWinterQuartersGameEndCheck');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function wieChitYearEndScoring($playersPerFaction)
  {
    $chits = [];
    $values = [];
    
    foreach([BRITISH, FRENCH] as $faction) {
      $chit = WarInEuropeChits::getTopOf(Locations::wieChitPlaceholder($faction));
      $chits[$faction] = $chit;
      $values[$faction] = $chit !== null ? $chit->getValue() : 0;
      if ($chit !== null) {
        Notifications::message(clienttranslate('${player_name} reveals their WIE chit: ${tkn_wieChit}'),[
          'player' => $playersPerFaction[$faction],
          'tkn_wieChit' => implode(':', [$faction, $chit->getValue()]),
        ]);
      }
      
    }

    if ($values[BRITISH] === $values[FRENCH]) {
      Notifications::message(clienttranslate('No VPs scored for WIE Chits'),[]);
    } else {
      $difference = abs($values[BRITISH] - $values[FRENCH]);
      $winningFaction = $values[BRITISH] > $values[FRENCH] ? BRITISH : FRENCH;
      Players::scoreVictoryPoints($playersPerFaction[$winningFaction], $difference);
    }

    // return chits
    foreach([BRITISH, FRENCH] as $faction) {
      if ($chits[$faction] === null) {
        continue;
      }
      $chits[$faction]->setLocation(Locations::wieChitPool($faction));
    }
    Notifications::returnWIEChitsToPool();
  }
}
