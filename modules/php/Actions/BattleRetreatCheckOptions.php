<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class BattleRetreatCheckOptions extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_RETREAT_CHECK_OPTIONS;
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

  public function stBattleRetreatCheckOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    $isAttacker = $info['isAttacker'];
    // TODO [2024-12-16]: remove isset check. Only here to not break running games
    $isRouted =  isset($info['isRouted']) ? $info['isRouted'] : true;

    $space = Spaces::get($spaceId);
    $units = Utils::filter($space->getUnits($faction), function ($unit) {
      return !$unit->isFort();
    });
    if (count($units) === 0 || ($faction === FRENCH && $space->hasBastion())) {
      // No units to retreat
      $this->resolveAction(['automatic' => true]);
      return;
    }

    $retreatOptions = $this->getRetreatOptions($spaceId, $faction, $isAttacker, $isRouted);

    if (count($retreatOptions['spaceIds']) > 0) {
      $this->insertRetreatAction($retreatOptions);
      $this->resolveAction(['automatic' => true]);
      return;
    }

    // delete all non light units
    $player = self::getPlayer();
    $nonLightUnits = Utils::filter($units, function ($unit) {
      return !$unit->isLight();
    });

    if (count($nonLightUnits) > 0) {
      Notifications::message('${player_name} cannot comply with any Retreat Priority. All non-Light units are eliminated', ['player' => $player]);
      foreach ($nonLightUnits as $unit) {
        $unit->eliminate($player);
      }
    }

    $retreatOptions = $this->getRetreatOptions($spaceId, $faction, $isAttacker, $isRouted);

    if (count($retreatOptions['spaceIds']) > 0) {
      $this->insertRetreatAction($retreatOptions);
      $this->resolveAction(['automatic' => true]);
      return;
    }

    // delete all light units
    $lightUnits = Utils::filter($units, function ($unit) {
      return $unit->isLight();
    });

    if (count($lightUnits) > 0) {
      Notifications::message('${player_name} cannot comply with any Retreat Priority. All Light units are eliminated', ['player' => $player]);
      foreach ($lightUnits as $unit) {
        $unit->eliminate($player);
      }
    }

    $this->resolveAction(['automatic' => true]);
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRetreatCheckOptions()
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

  public function actPassBattleRetreatCheckOptions()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actBattleRetreatCheckOptions($args)
  {
    self::checkAction('actBattleRetreatCheckOptions');


    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function insertRetreatAction($retreatOptions)
  {
    $info = $this->ctx->getInfo();

    $this->ctx->insertAsBrother(new LeafNode([
      'action' => BATTLE_RETREAT,
      'playerId' => $info['playerId'],
      'faction' => $info['faction'],
      'spaceId' => $info['spaceId'],
      'isAttacker' => $info['isAttacker'],
      'retreatOptionIds' => $retreatOptions['spaceIds'],
      'overwhelmDuringRetreat' => $retreatOptions['overwhelmDuringRetreat'],
    ]));
  }



  private function filterConnectionRestrictions($possibleRetreatOptions, $units)
  {
    return Utils::filter($possibleRetreatOptions, function ($data) use ($units) {
      return $data['connection']->canBeUsedByUnits($units, true);
    });
  }

  public function getRetreatOptions($spaceId, $faction, $isAttacker, $isRouted)
  {
    $space = Spaces::get($spaceId);
    $otherFaction = BTHelpers::getOtherFaction($faction);

    $attackerUnits = $isAttacker ? $space->getUnits($faction) : $space->getUnits($otherFaction);
    $defenderUnits = !$isAttacker ? $space->getUnits($faction) : $space->getUnits($otherFaction);
    $spaceIdsAttackersEnteredFrom = array_map(function ($unit) {
      return $unit->getPreviousLocation();
    }, $attackerUnits);
    // $units = $space->getUnits($faction);
    $hasFleets = Utils::array_some($isAttacker ? $attackerUnits : $defenderUnits, function ($unit) {
      return $unit->isFleet();
    });

    $adjacentConnectionsAndSpaces = $space->getAdjacentConnectionsAndSpaces();

    if ($hasFleets) {
      // Fleet retreat priorities
      return BTHelpers::getSpacesBasedOnFleetRetreatPriorities($faction);
    }

    if ($isAttacker) {
      // Attacker retreat priorities
      $optionsFriendlyStackEnteredFrom = Utils::filter($adjacentConnectionsAndSpaces, function ($data) use ($spaceIdsAttackersEnteredFrom) {
        return in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
      });

      // Space may not have enemy units
      $optionsFriendlyStackEnteredFrom = Utils::filter($optionsFriendlyStackEnteredFrom, function ($option) use ($otherFaction) {
        $enemyUnits = $option['space']->getUnits($otherFaction);
        return count($enemyUnits) === 0;
      });

      $optionsFriendlyStackEnteredFrom = $this->filterConnectionRestrictions($optionsFriendlyStackEnteredFrom, $attackerUnits);

      if (count($optionsFriendlyStackEnteredFrom) > 0) {
        $spaceIds = array_map(function ($data) {
          return $data['space']->getId();
        }, $optionsFriendlyStackEnteredFrom);
        return [
          'spaceIds' => $spaceIds,
          'overwhelmDuringRetreat' => false,
        ];
      }
    }

    // Adjacent Space priorties
    // If defender filter spaces an enemy stack entered the battle space from this AR
    $possibleRetreatOptions = Utils::filter($adjacentConnectionsAndSpaces, function ($data) use ($spaceIdsAttackersEnteredFrom, $isAttacker) {
      return $isAttacker || !in_array($data['space']->getId(), $spaceIdsAttackersEnteredFrom);
    });

    $possibleRetreatOptions = $this->filterConnectionRestrictions($possibleRetreatOptions, $isAttacker ? $attackerUnits : $defenderUnits);

    return $this->getSpacesBasedOnAdjacentSpaceRetreatPriorities($possibleRetreatOptions, $faction,  $isRouted, $space);
  }


  private function getSpacesBasedOnAdjacentSpaceRetreatPriorities($adjacentConnectionsAndSpaces, $faction,  $isRouted, $spaceOfBattle)
  {
    $spaces = array_map(function ($adjacent) {
      return $adjacent['space'];
    }, $adjacentConnectionsAndSpaces);
    $enemyFaction = Players::otherFaction($faction);

    $spacesWithoutEnemyUnits = Utils::filter($spaces, function ($space) use ($enemyFaction) {
      return count($space->getUnits($enemyFaction)) === 0;
    });

    // 1. Friendly Home Space without enemy units
    $homeSpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
      return $space->getHomeSpace() === $faction;
    });

    if (count($homeSpaces) > 0) {
      return [
        'spaceIds' => BTHelpers::returnSpaceIds($homeSpaces),
        'overwhelmDuringRetreat' => false,
      ];
    }

    // 2. Friendly space without enemy units
    $friendlySpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
      return $space->getControl() === $faction;
    });

    if (count($friendlySpaces) > 0) {
      return [
        'spaceIds' => BTHelpers::returnSpaceIds($friendlySpaces),
        'overwhelmDuringRetreat' => false,
      ];
    }

    // 3. A Wilderness Space or friednly Indian Nation Village
    $wildernessSpaces = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($faction) {
      $isIndianNationVillage = in_array($space->getIndianVillage(), [CHEROKEE, IROQUOIS]);
      return ($space->getControl() === NEUTRAL && !$isIndianNationVillage) || ($space->getControl() === $faction && $isIndianNationVillage);
    });

    if (count($wildernessSpaces) > 0) {
      return [
        'spaceIds' => BTHelpers::returnSpaceIds($wildernessSpaces),
        'overwhelmDuringRetreat' => false,
      ];
    }

    // 4. An enemy-controlled space or enemy Indian Nation Village without enemy units or Militia
    $enemyControlledSpace = Utils::filter($spacesWithoutEnemyUnits, function ($space) use ($enemyFaction) {
      return $space->getControl() === $enemyFaction && $space->getMilitia() === 0;
    });

    if (count($enemyControlledSpace) > 0) {
      return [
        'spaceIds' => BTHelpers::returnSpaceIds($enemyControlledSpace),
        'overwhelmDuringRetreat' => false,
      ];
    }

    // 5. 
    if (!$isRouted) {
      $step5Result = $this->getStep5RetreatPriorities($spaces, $faction, $spaceOfBattle);
      if (isset($step5Result['spaceIds']) && count($step5Result['spaceIds']) > 0) {
        return $step5Result;
      }
    }

    return [
      'spaceIds' => [],
      'overwhelmDuringRetreat' => false,
    ];
  }

  private function getStep5RetreatPriorities($spaces, $faction, $spaceOfBattle)
  {
    $friendlyUnits = $spaceOfBattle->getUnits($faction);
    $friendlyUnitCount = count(Utils::filter($friendlyUnits, function ($unit) {
      return !$unit->isCommander();
    }));

    // 5A An enemy space with the fewest enemy units and Militia
    $spacesThatCanBeOverwhelmed = [];
    foreach ($spaces as $space) {
      $unitsOnSpace = $space->getUnits();
      $requiredForOverwhelm = GameMap::requiredForOverwhelm($space, $faction, $unitsOnSpace);
      if ($requiredForOverwhelm['hasEnemyUnits'] && $friendlyUnitCount >= $requiredForOverwhelm['requiredForOverwhelm']) {
        $spacesThatCanBeOverwhelmed[] = [
          'space' => $space,
          'enemyUnits' => Utils::filter($unitsOnSpace, function ($unit) use ($faction) {
            return $unit->getFaction() !== $faction;
          }),
        ];
      }
    }

    if (count($spacesThatCanBeOverwhelmed) > 0) {
      usort($spacesThatCanBeOverwhelmed, function ($a, $b) {
        return count($a['enemyUnits']) - count($b['enemyUnits']);
      });
      $fewestEnemyUnitCount = count($spacesThatCanBeOverwhelmed[0]['enemyUnits']);
      $spacesThatCanBeOverwhelmed = Utils::filter($spacesThatCanBeOverwhelmed, function ($data) use ($fewestEnemyUnitCount) {
        return count($data['enemyUnits']) === $fewestEnemyUnitCount;
      });

      return [
        'spaceIds' => array_map(function ($data) {
          return $data['space']->getId();
        }, $spacesThatCanBeOverwhelmed),
        'overwhelmDuringRetreat' => true,
      ];
    }

    // 5A.2 Neutral Indian Nation Villages
    $villagesOfNeutralIndianNation = Utils::filter($spaces, function ($space) {
      return in_array($space->getIndianVillage(), [CHEROKEE, IROQUOIS]) && $space->getDefaultControl() === INDIAN;
    });

    // Stack needs to be able to overwhelm unit of Neutral Indian Nation
    if ($friendlyUnitCount > 3 && count($villagesOfNeutralIndianNation) > 0) {
      return [
        'spaceIds' => BTHelpers::returnIds($villagesOfNeutralIndianNation),
        'overwhelmDuringRetreat' => true,
      ];
    }

    // 5B Unresolved Battle space with fewest enemy units and Militia combined
    $spacesWithUnresolvedBattle = [];
    foreach ($spaces as $space) {
      if ($space->getBattle() === 0) {
        continue;
      }
      $unitsOnSpace = $space->getUnits();
      $enemyUnits = count(Utils::filter($unitsOnSpace, function ($unit) use ($faction) {
        return !$unit->isCommander() && $unit->getFaction() !== $faction;
      })) + $space->getMilitiaForFaction(BTHelpers::getOtherFaction($faction));

      $spacesWithUnresolvedBattle[] = [
        'space' => $space,
        'enemyUnits' => $enemyUnits,
      ];
    }

    if (count($spacesWithUnresolvedBattle) > 0) {
      usort($spacesWithUnresolvedBattle, function ($a, $b) {
        return count($a['enemyUnits']) - count($b['enemyUnits']);
      });
      $fewestEnemyUnitCount = $spacesWithUnresolvedBattle[0]['enemyUnits'];
      $spacesWithUnresolvedBattle = Utils::filter($spacesWithUnresolvedBattle, function ($data) use ($fewestEnemyUnitCount) {
        return $data['enemyUnits'] === $fewestEnemyUnitCount;
      });

      // Set here because this can be triggered by overwhelm
      $unitsThatCannotFight = Globals::getUnitsThatCannotFight();
      Globals::setUnitsThatCannotFight(array_merge($unitsThatCannotFight, BTHelpers::returnIds($friendlyUnits)));

      return [
        'spaceIds' => array_map(function ($data) {
          return $data['space']->getId();
        }, $spacesWithUnresolvedBattle),
        'overwhelmDuringRetreat' => false,
      ];
    }

    return [
      'spaceIds' => [],
      'overwhelmDuringRetreat' => false,
    ];
  }
}
