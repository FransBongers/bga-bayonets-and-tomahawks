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
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundResolveBattles extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_RESOLVE_BATTLES;
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

  public function stActionRoundResolveBattles()
  {
    $battleOrder = $this->getBattleLocations();
    $playerId = self::getPlayer()->getId();
    $firstPlayerId = Globals::getFirstPlayerId();

    Notifications::battleOrder($battleOrder);
    Globals::setBattleOrder($battleOrder);

    $battleNodes = [
      'children' => []
    ];

    foreach ($battleOrder as $data) {
      $spaceIds = $data['spaceIds'];
      $numberOfAttackers = $data['numberOfAttackers'];

      $battleNodes['children'][] = [
        'action' => UPDATE_STEP_TRACKER,
        'step' => 'battleOrderStep' . $numberOfAttackers,
      ];

      if (count($spaceIds) === 1) {
        $spaceId = $spaceIds[0];
        $battleNodes['children'][] = $this->getBattleFlow($playerId, $spaceId, $numberOfAttackers);
      } else {
        $battleNodes['children'][] = [
          'action' => BATTLE_SELECT_SPACE,
          'playerId' => $firstPlayerId,
          'spaceIds' => $spaceIds,
          'numberOfAttackers' => $numberOfAttackers,
        ];
      }
    }

    if (count($battleNodes['children']) > 0) {
      $this->ctx->insertAsBrother(Engine::buildTree($battleNodes));
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

  public function stPreActionRoundResolveBattles() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundResolveBattles()
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

  public function actPassActionRoundResolveBattles()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actActionRoundResolveBattles($args)
  {
    self::checkAction('actActionRoundResolveBattles');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getNumberOfAttackingUnits($space, $units)
  {
    $attacker = BTHelpers::getOtherFaction($space->getDefender());
    $location = $space->getId();
    $attackingUnits = Utils::filter($units, function ($unit) use ($location, $attacker) {
      return $unit->getLocation() === $location && $unit->getFaction() === $attacker;
    });
    return count($attackingUnits);
  }

  public function getBattleLocations()
  {
    $battleLocations = Spaces::getBattleLocations();
    $units = Units::getAll()->toArray();

    // First group locations by number of attacking units
    $groupedLocations = [];
    foreach ($battleLocations as $space) {
      $numberOfAttackingUnits = $this->getNumberOfAttackingUnits($space, $units);
      if (isset($groupedLocations[$numberOfAttackingUnits])) {
        $groupedLocations[$numberOfAttackingUnits][] = $space;
      } else {
        $groupedLocations[$numberOfAttackingUnits] = [$space];
      }
    }

    // Map to array so it can be sorted
    $mappedLocations = [];
    foreach ($groupedLocations as $unitCount => $spaces) {
      $mappedLocations[] = [
        'numberOfAttackers' => $unitCount,
        'spaceIds' => BTHelpers::returnIds($spaces)
      ];
    }

    // Sort by attacking units
    usort($mappedLocations, function ($a, $b) {
      return $a['numberOfAttackers'] - $b['numberOfAttackers'];
    });

    return $mappedLocations;
  }

  public function getBattleFlow($playerId, $spaceId, $numberOfAttackers)
  {
    return [
      'spaceId' => $spaceId,
      'numberOfAttackers' => $numberOfAttackers,
      'children' => [
        [
          'action' => BATTLE_PREPARATION,
          'playerId' => $playerId,
        ],
        [
          'action' => BATTLE_PRE_SELECT_COMMANDER,
        ],
        [
          'action' => BATTLE_PENALTIES,
        ],
        [
          'action' => BATTLE_ROLLS,
          'playerId' => $playerId,
        ],
        [
          'action' => BATTLE_MILITIA_ROLLS,
        ],
        [
          'action' => BATTLE_OUTCOME,
          'playerId' => $playerId,
        ],
        [
          'action' => BATTLE_CLEANUP,
          'playerId' => $playerId,
        ]
      ]
    ];
  }
}
