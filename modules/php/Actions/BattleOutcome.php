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
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleOutcome extends \BayonetsAndTomahawks\Actions\Battle
{

  public function getState()
  {
    return ST_BATTLE_OUTCOME;
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

  public function stBattleOutcome()
  {
    $parentInfo = $this->ctx->getParent()->getInfo();
    $space = Spaces::get($parentInfo['spaceId']);

    $outcome = $this->determineOutcome($space);

    $loser = $outcome['loser'];

    $this->ctx->insertAsBrother(new LeafNode([
      'action' => BATTLE_RETREAT_CHECK_OPTIONS,
      'playerId' => $loser['player']->getId(),
      'faction' => $loser['faction'],
      'spaceId' => $space->getId(),
      'isAttacker' => $loser['isAttacker']
    ]));

    if ($loser['isRouted']) {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_ROUT,
        'playerId' => $loser['player']->getId(),
        'faction' => $loser['faction'],
        'spaceId' => $space->getId(),
      ]));
    }

    // Determine winner
    // Apply rout penalties
    // Place commanders with their respective stack
    // retreat defeated force

    $this->resolveAction([
      'automatic' => true,
      'loser' => $loser['faction'],
      'winner' => $outcome['winner']['faction']
    ]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleOutcome()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleOutcome()
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

  public function actPassBattleOutcome()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleOutcome($args)
  {
    self::checkAction('actBattleOutcome');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function determineOutcome($space)
  {

    $defenderFaction = $space->getDefender();
    $attackerFaction = Players::otherFaction($defenderFaction);

    $attackerPlayer = Players::getPlayerForFaction($attackerFaction);
    $defenderPlayer = Players::getPlayerForFaction($defenderFaction);

    $units = $space->getUnits();

    $defendersUnits = Utils::filter($units, function ($unit) use ($defenderFaction) {
      return $unit->getFaction() === $defenderFaction;
    });

    $attackersUnits = Utils::filter($units, function ($unit) use ($attackerFaction) {
      return $unit->getFaction() === $attackerFaction;
    });

    // Check if battle is decided by one stack not having any units left
    if (count($attackersUnits) === 0) {
      // defender wins, no need to check of number of defenders units left
      Notifications::battleNoUnitsLeft($attackerPlayer);
      Notifications::battleWinner($defenderPlayer, $space);
      return [
        'winner' => [
          'player' => $defenderPlayer,
          'faction' => $defenderFaction,
          'isAttacker' => false,
        ],
        'loser' => [
          'player' => $attackerPlayer,
          'faction' => $attackerFaction,
          'isAttacker' => true,
          'isRouted' => false,
        ],
      ];
    } else if (count($defendersUnits) === 0 && count($attackersUnits) > 0) {
      // attacker wins
      Notifications::battleNoUnitsLeft($attackerPlayer);
      Notifications::battleWinner($attackerPlayer, $space);
      return [
        'loser' => [
          'player' => $defenderPlayer,
          'faction' => $defenderFaction,
          'isAttacker' => false,
          'isRouted' => false,
        ],
        'winner' => [
          'player' => $attackerPlayer,
          'faction' => $attackerFaction,
          'isAttacker' => true,
        ]
      ];
    }

    $attackerMarker = Markers::get($this->factionBattleMarkerMap[$attackerFaction]);
    $defenderMarker = Markers::get($this->factionBattleMarkerMap[$defenderFaction]);
    $attackerPosition = $this->getBattleMarkerValue($attackerMarker);
    $defenderPosition = $this->getBattleMarkerValue($defenderMarker);

    if ($attackerPosition > $defenderPosition) {
      // Attacker wins
      Notifications::battleWinner($attackerPlayer, $space);
      $routMarkerCondition = $attackerPosition - $defenderPosition >= 3;
      // TODO add data for this.
      // Store in global?
      $lastBastionOrFortressEliminated = false;

      return [
        'loser' => [
          'player' => $defenderPlayer,
          'faction' => $defenderFaction,
          'isAttacker' => false,
          'isRouted' => $routMarkerCondition || $lastBastionOrFortressEliminated,
          'eliminateNonLightUnits' => $lastBastionOrFortressEliminated,
        ],
        'winner' => [
          'player' => $attackerPlayer,
          'faction' => $attackerFaction,
          'isAttacker' => true,
        ]
      ];
    } else {
      // defender wins
      Notifications::battleWinner($defenderPlayer, $space);
      return [
        'winner' => [
          'player' => $defenderPlayer,
          'faction' => $defenderFaction,
          'isAttacker' => false,
        ],
        'loser' => [
          'player' => $attackerPlayer,
          'faction' => $attackerFaction,
          'isAttacker' => true,
          'isRouted' => $defenderPosition - $attackerPosition >= 3,
        ]
      ];
    }
  }
}
