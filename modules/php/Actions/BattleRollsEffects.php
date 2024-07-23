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

class BattleRollsEffects extends \BayonetsAndTomahawks\Actions\Battle
{
  public function getState()
  {
    return ST_BATTLE_ROLLS_EFFECTS;
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

  public function stBattleRollsEffects()
  {
    $info = $this->ctx->getInfo();

    $faction = $info['faction'];
    // $unitIds = $info['unitIds'];
    $battleRollsSequenceStep = $info['battleRollsSequenceStep'];
    $player = self::getPlayer();

    $diceResults = $info['diceResults'];

    Notifications::log('stBattleRollsEffects', []);

    $playerId = $player->getId();
    $otherPlayer = Players::getOther($playerId);

    $space = $this->getBattleSpace();

    // Hit results
    $diceWithHits = $this->getDiceWithHits($diceResults, $battleRollsSequenceStep);
    if (count($diceWithHits) > 0) {
      $results = $this->applyHitResults($space, $diceWithHits, $battleRollsSequenceStep, $player, $faction);
      if (count($results['possibleUnitsToApplyHitTo']) > 0) {
        // Insert this first so it is resolved after applying hit
        $remainingDiceResults = $this->getRemainingDiceResults($diceResults, $diceWithHits, $results['unprocessedDice']);
        if (count($remainingDiceResults) > 0) {
          $this->ctx->insertAsBrother(new LeafNode([
            'action' => BATTLE_ROLLS_EFFECTS,
            'playerId' => $playerId,
            'battleRollsSequenceStep' => $battleRollsSequenceStep,
            // 'unitIds' => $unitIds,
            'diceResults' => $remainingDiceResults,
            'faction' => $faction,
          ]));
        }

        $this->ctx->insertAsBrother(new LeafNode([
          'action' => BATTLE_APPLY_HITS,
          'playerId' => $otherPlayer->getId(),
          'unitIds' => $results['possibleUnitsToApplyHitTo'],
          'spaceId' => $this->getBattleSpaceId(),
          'faction' => $otherPlayer->getFaction(),
        ]));
        $this->resolveAction(['automatic' => true]);
        return;
      }
    }

    // B&T
    $diceWithBT = $this->getDiceWithFace($diceResults, B_AND_T);
    if (count($diceWithBT) > 0) {
      $btresults = $this->applyBAndTResults($space, $diceWithBT, $battleRollsSequenceStep, $player, $faction);

      if (count($btresults['possibleUnitsToApplyHitTo']) > 0) {
        // Insert this first so it is resolved after applying hit
        $remainingDiceResults = $this->getRemainingDiceResults($diceResults, $diceWithBT, $btresults['unprocessedDice']);
        if (count($remainingDiceResults) > 0) {
          $this->ctx->insertAsBrother(new LeafNode([
            'action' => BATTLE_ROLLS_EFFECTS,
            'playerId' => $playerId,
            'battleRollsSequenceStep' => $battleRollsSequenceStep,
            'diceResults' => $remainingDiceResults,
            'faction' => $faction,
          ]));
        }

        $this->ctx->insertAsBrother(new LeafNode([
          'action' => BATTLE_APPLY_HITS,
          'playerId' => $otherPlayer->getId(),
          'unitIds' => $btresults['possibleUnitsToApplyHitTo'],
          'spaceId' => $this->getBattleSpaceId(),
          'faction' => $otherPlayer->getFaction(),
        ]));
        $this->resolveAction(['automatic' => true]);
        return;
      }
    }
    // Flag
    $diceWithFlagCount = count($this->getDiceWithFace($diceResults, FLAG));
    if ($diceWithFlagCount > 0) {
      $this->advanceBattleVictoryMarker($player, $faction, $diceWithFlagCount);
    }
    // Miss


    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreBattleRollsEffects()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsBattleRollsEffects()
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

  public function actPassBattleRollsEffects()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actBattleRollsEffects($args)
  {
    self::checkAction('actBattleRollsEffects');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getDiceWithFace($diceResults, $dieFace)
  {
    return Utils::filter($diceResults, function ($dieResult) use ($dieFace) {
      return $dieResult === $dieFace;
    });
  }

  // .########........###....##....##.########.....########
  // .##.....##......##.##...###...##.##.....##.......##...
  // .##.....##.....##...##..####..##.##.....##.......##...
  // .########.....##.....##.##.##.##.##.....##.......##...
  // .##.....##....#########.##..####.##.....##.......##...
  // .##.....##....##.....##.##...###.##.....##.......##...
  // .########.....##.....##.##....##.########........##...

  private function applyBAndTResults($space, $diceResults, $battleRollsSequenceStep, $player, $faction)
  {

    $unprocessedDice = $diceResults;
    $processedDice = [];
    $possibleUnitsToApplyHitTo = [];

    if (in_array($battleRollsSequenceStep, [NON_INDIAN_LIGHT, INDIAN, MILITIA])) {
      return [
        'unprocessedDice' => [],
        'possibleUnitsToApplyHitTo' => $possibleUnitsToApplyHitTo,
      ];
    }

    $enemyBrigades = Utils::filter($space->getUnits(), function ($unit) use ($faction) {
      return $unit->getFaction() !== $faction && $unit->isBrigade();
    });
    Notifications::log('enemyBrigades', $enemyBrigades);


    while (count($unprocessedDice) > 0) {
      $dieResult = array_pop($unprocessedDice);
      $processedDice[] = $dieResult;

      if ($battleRollsSequenceStep === FLEETS) {
        // Insert action to move a non-eliminated fleet
        break;
      }

      if (in_array($battleRollsSequenceStep, [HIGHLAND_BRIGADES, METROPOLITAN_BRIGADES, NON_METROPOLITAN_BRIGADES])) {
        // Remove 1 enemy Militia
      }

      if (!in_array($battleRollsSequenceStep, [HIGHLAND_BRIGADES, METROPOLITAN_BRIGADES, ARTILLERY, FORT, BASTION])) {
        continue;
      }
      if (count($enemyBrigades) === 0) {
        // No enemy brigades to take a hit
        continue;
      }
      $newPosition = $this->advanceBattleVictoryMarker($player, $faction);
      if ($newPosition <= 0) {
        continue;
      }
      $unitsToTakeHit = Utils::filter($enemyBrigades, function ($unit) {
        return $unit->isMetropolitanBrigade();
      });
      if (count($unitsToTakeHit) === 0) {
        // All remaining brigades are non-metropolitan
        $unitsToTakeHit = $enemyBrigades;
      }
      if (count($unitsToTakeHit) === 1) {
        $hitResult = $unitsToTakeHit[0]->applyHit();
        $enemyBrigades = $this->updateEnemyUnits($enemyBrigades, $hitResult);
      } else {
        // More than one unit, enemy must make choice
        $possibleUnitsToApplyHitTo = array_map(function ($unit) {
          return $unit->getId();
        }, $unitsToTakeHit);
      }
    }

    return [
      'unprocessedDice' => $unprocessedDice,
      'possibleUnitsToApplyHitTo' => $possibleUnitsToApplyHitTo,
    ];
  }


  // .##.....##.####.########..######.
  // .##.....##..##.....##....##....##
  // .##.....##..##.....##....##......
  // .#########..##.....##.....######.
  // .##.....##..##.....##..........##
  // .##.....##..##.....##....##....##
  // .##.....##.####....##.....######.

  /**
   * allDiceResults for this stap as in info
   * processedDice, dice that were being resolved (ie all hit dice, all B&Ts)
   * unprocessedDice, all dice that of processedDice, that still need to be processed
   */
  private function getRemainingDiceResults($allDiceResults, $processedDice, $unprocessedDice)
  {
    $remainingOtherDice = Utils::filter($allDiceResults, function ($die) use ($processedDice) {
      return !in_array($die, $processedDice);
    });
    return array_merge($unprocessedDice, $remainingOtherDice);
  }

  private function getEnemyUnitsThatCanTakeHit($enemyUnits, $battleRollsSequenceStep)
  {
    switch ($battleRollsSequenceStep) {
      case NON_INDIAN_LIGHT:
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isNonIndianLight();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isIndian();
          });
        }
        break;
      case INDIAN:
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isIndian();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isNonIndianLight();
          });
        }
        break;
      case HIGHLAND_BRIGADES:
      case METROPOLITAN_BRIGADES:
        if (!Globals::getActiveBattleHighlandBrigadeHit()) {
          $units = Utils::filter($enemyUnits, function ($unit) {
            return $unit->isHighlandBrigade();
          });
          if (count($units) > 0) {
            return $units;
          }
        }
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isMetropolitanBrigade();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isNonMetropolitanBrigade();
          });
        }
        break;
      case NON_METROPOLITAN_BRIGADES:
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isNonMetropolitanBrigade();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          if (!Globals::getActiveBattleHighlandBrigadeHit()) {
            $units = Utils::filter($enemyUnits, function ($unit) {
              return $unit->isHighlandBrigade();
            });
            if (count($units) > 0) {
              return $units;
            }
          }
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isMetropolitanBrigade();
          });
        }
        break;
      case FLEETS:
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isFleet();
        });
        if (count($units) > 0) {
          return $units;
        }
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isArtillery();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isFort();
          });
        }
        break;
      case BASTIONS_OR_FORT:
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isArtillery();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isFleet();
          });
        }
        break;
      case ARTILLERY:
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isArtillery();
        });
        if (count($units) > 0) {
          return $units;
        }
        $units = Utils::filter($enemyUnits, function ($unit) {
          return $unit->isFort() || $unit->isBastion();
        });
        if (count($units) > 0) {
          return $units;
        } else {
          return Utils::filter($enemyUnits, function ($unit) {
            return $unit->isFleet();
          });
        }
        break;
      default:
        return [];
    }
  }

  private function updateEnemyUnits($enemyUnits, $hitResult)
  {
    $unit = $hitResult['unit'];
    $eliminated = $hitResult['eliminated'];
    $enemyUnits = Utils::filter($enemyUnits, function ($enemyUnit) use ($unit) {
      return $enemyUnit->getId() !== $unit->getId();
    });
    if (!$eliminated) {
      $enemyUnits[] = $unit;
    }
    return $enemyUnits;
  }

  private function applyHitResults($space, $diceResults, $battleRollsSequenceStep, $player, $faction)
  {
    $enemyUnits = $this->getEnemeyUnitsOfSameShape($space, $battleRollsSequenceStep, $this->getEnemyFaction($faction));

    $unprocessedDice = $diceResults;
    $processedDice = [];
    $possibleUnitsToApplyHitTo = [];

    while (count($unprocessedDice) > 0) {
      $dieResult = array_pop($unprocessedDice);
      $processedDice[] = $dieResult;
      $unitsThatCanTakeHit = $this->getEnemyUnitsThatCanTakeHit($enemyUnits, $battleRollsSequenceStep);
      if (count($unitsThatCanTakeHit) === 0) {
        // No more units that can take a hit
        $unprocessedDice = [];
        break;
      }
      // Hit is scored, advance battle victory marker
      $newPosition = $this->advanceBattleVictoryMarker($player, $faction);
      if ($newPosition <= 0) {
        continue;
      }
      if (count($unitsThatCanTakeHit) === 1) {
        // Apply hit and continue
        $hitResult = $unitsThatCanTakeHit[0]->applyHit();
        $enemyUnits = $this->updateEnemyUnits($enemyUnits, $hitResult);
        continue;
      }
      // Multiple units that can take hit
      $reducedEnemyUnits = Utils::filter($unitsThatCanTakeHit, function ($unit) {
        return $unit->isReduced();
      });
      if (count($reducedEnemyUnits) === 1) {
        $hitResult = $reducedEnemyUnits[0]->applyHit();
        $enemyUnits = $this->updateEnemyUnits($enemyUnits, $hitResult);
      } else {
        // Count > 2, opponent must make choice
        $possibleUnitsToApplyHitTo = array_map(function ($unit) {
          return $unit->getId();
        }, $unitsThatCanTakeHit);
        break;
      }
    }

    return [
      'unprocessedDice' => $unprocessedDice,
      'possibleUnitsToApplyHitTo' => $possibleUnitsToApplyHitTo,
    ];
  }


  private function getDiceWithHits($diceResults, $battleRollsSequenceStep)
  {
    return Utils::filter($diceResults, function ($dieResult) use ($battleRollsSequenceStep) {
      switch ($battleRollsSequenceStep) {
        case NON_INDIAN_LIGHT:
        case INDIAN:
          return $dieResult === HIT_TRIANGLE_CIRCLE;
        case HIGHLAND_BRIGADES:
        case METROPOLITAN_BRIGADES:
        case NON_METROPOLITAN_BRIGADES:
          return $dieResult === HIT_SQUARE_CIRCLE;
        case FLEETS:
        case BASTIONS_OR_FORT:
        case ARTILLERY:
          return $dieResult === HIT_SQUARE_CIRCLE || $dieResult === HIT_TRIANGLE_CIRCLE;
        default:
          return false;
      }
    });
  }

  private function getEnemeyUnitsOfSameShape($space, $battleRollsSequenceStep, $enemyFaction)
  {
    $units = $space->getUnits();

    return Utils::filter($units, function ($unit) use ($battleRollsSequenceStep, $enemyFaction) {
      if ($unit->getFaction() !== $enemyFaction) {
        return false;
      }

      switch ($battleRollsSequenceStep) {
        case NON_INDIAN_LIGHT:
        case INDIAN:
          return $unit->isNonIndianLight() || $unit->isIndian();
        case HIGHLAND_BRIGADES:
        case METROPOLITAN_BRIGADES:
        case NON_METROPOLITAN_BRIGADES:
          return $unit->isMetropolitanBrigade() || $unit->isNonMetropolitanBrigade();
        case FLEETS:
          return $unit->isFleet() || $unit->isArtillery() || $unit->isFort();
        case BASTIONS_OR_FORT:
        case ARTILLERY:
          return $unit->isFleet() || $unit->isArtillery() || $unit->isFort() || $unit->isBastion();
        default:
          return false;
      }
    });
  }
}
