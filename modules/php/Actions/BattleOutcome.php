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
    $loserPlayerId = $loser['player']->getId();
    $loserFaction = $loser['faction'];

    if (Utils::array_find($space->getUnits($loserFaction), function ($unit) {return $unit->isFort();}) !== null) {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_FORT_ELIMINATION,
        'playerId' => $loserPlayerId,
        'faction' => $loserFaction,
        'spaceId' => $space->getId(),
        'isRouted' => $loser['isRouted'],
      ]));
    }

    $this->ctx->insertAsBrother(new LeafNode([
      'action' => BATTLE_RETREAT_CHECK_OPTIONS,
      'playerId' => $loserPlayerId,
      'faction' => $loserFaction,
      'spaceId' => $space->getId(),
      'isAttacker' => $loser['isAttacker']
    ]));

    if ($loser['isRouted']) {
      $this->ctx->insertAsBrother(new LeafNode([
        'action' => BATTLE_ROUT,
        'playerId' => $loserPlayerId,
        'faction' => $loserFaction,
        'spaceId' => $space->getId(),
      ]));
    }

    // Determine winner
    // Apply rout penalties
    // Place commanders with their respective stack
    // retreat defeated force

    $this->resolveAction([
      'automatic' => true,
      'loser' => $loserFaction,
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

    $militiaCounterMap = [
      BRITISH => BRITISH_MILITIA_MARKER,
      FRENCH => FRENCH_MILITIA_MARKER
    ];

    $spaceId = $this->ctx->getParent()->getInfo()['spaceId'];
    // To check: can attacking side have militia in the battle?
    $attackerMilitia = count(Markers::getOfTypeInLocation($militiaCounterMap[$attackerFaction], Locations::stackMarker($spaceId, $attackerFaction)));
    $defenderMilitia = count(Markers::getOfTypeInLocation($militiaCounterMap[$defenderFaction], Locations::stackMarker($spaceId, $defenderFaction)));
    
    Notifications::log('attackerMilitia', $attackerMilitia);
    Notifications::log('defenderMilitia', $defenderMilitia);

    $attackerUnitCount = count($attackersUnits) + $attackerMilitia;
    $defenderUnitCount = count($defendersUnits) + $defenderMilitia;

    // Check if battle is decided by one stack not having any units left
    if ($attackerUnitCount === 0) {
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
    } else if ($defenderUnitCount === 0 && $attackerUnitCount > 0) {
      // attacker wins
      Notifications::battleNoUnitsLeft($defenderPlayer);
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
