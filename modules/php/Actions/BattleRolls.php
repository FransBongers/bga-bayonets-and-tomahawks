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

class BattleRolls extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_ROLLS;
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

  public function stBattleRolls()
  {
    $parentInfo = $this->ctx->getParent()->getInfo();

    $attacker = $parentInfo['attacker'];
    $defender = $parentInfo['defender'];
    $spaceId = $parentInfo['spaceId'];

    $space = Spaces::get($spaceId);

    $units = $space->getUnits();

    // Defender / attacker in reverse order here because we insertAsBrother and want
    // defender to go last
    foreach ([$defender, $attacker] as $faction) {
      $node = [
        'children' => [],
      ];

      $player = Players::getPlayerForFaction($faction);
      $playerId = $player->getId();

      foreach (BATTLE_ROLL_SEQUENCE as $step) {
        $unitsForStep = $this->getUnitsForBattleRollSequenceStep($units, $faction, $step);
        if (count($unitsForStep) === 0) {
          continue;
        }
        $node['children'][] = [
          'action' => BATTLE_ROLLS_ROLL_DICE,
          'playerId' => $playerId,
          'faction' => $faction,
          'battleRollsSequenceStep' => $step,
          'unitIds' => array_map(function ($unit) {
            return $unit->getId();
          }, $unitsForStep),
        ];
      }

      if (count($node['children']) > 0) {
        $this->ctx->insertAsBrother(
          Engine::buildTree($node)
        );
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

  public function stPreBattleRolls()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRolls()
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

  public function actPassBattleRolls()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleRolls($args)
  {
    self::checkAction('actBattleRolls');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getUnitsForBattleRollSequenceStep($units, $faction, $step)
  {
    return Utils::filter($units, function ($unit) use ($faction, $step) {
      if ($unit->getFaction() !== $faction) {
        return false;
      }

      switch ($step) {
        case NON_INDIAN_LIGHT:
          return $unit->isNonIndianLight();
        case INDIAN:
          return $unit->isIndian();
        case HIGHLAND_BRIGADES:
          return $unit->isHighlandBrigade();
        case METROPOLITAN_BRIGADES:
          return $unit->isMetropolitanNonHighlandBrigade();
        case NON_METROPOLITAN_BRIGADES:
          return $unit->isNonMetropolitanBrigade();
        case FLEETS:
          return $unit->isFleet();
        case BASTIONS_OR_FORT:
          return $unit->isFort() || $unit->isBastion();
        case ARTILLERY:
          return $unit->isArtillery();
        default:
          return false;
      }
    });
  }
}
