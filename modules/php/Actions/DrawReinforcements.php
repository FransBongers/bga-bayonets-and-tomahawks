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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenarios\AmherstsJuggernaut1758_1759;

class DrawReinforcements extends \BayonetsAndTomahawks\Actions\LogisticsRounds
{

  public function getState()
  {
    return ST_DRAW_REINFORCEMENTS;
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

  public function stDrawReinforcements()
  {
    $scenario = Scenarios::get();
    $reinforcements = $scenario->getReinforcements()[Globals::getYear()];

    $info = $this->ctx->getInfo();
    $pool = $info['pool'];
    $player = self::getPlayer();

    $numberToPick = $reinforcements[$pool];

    if (
      $pool === POOL_FLEETS &&
      $scenario->getId() === AmherstsJuggernaut1758_1759 &&
      Globals::getWinteringRearAdmiralPlayed()
    ) {
      $numberToPick--;
    }

    $this->drawReinforcement($player, $pool, $numberToPick, false);

    $this->resolveAction(['automatic' => true], true);
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsDrawReinforcements()
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

  public function actPassDrawReinforcements()
  {
    Engine::resolve(PASS);
  }

  public function actDrawReinforcements($args)
  {
    self::checkAction('actDrawReinforcements');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function drawReinforcement($player, $pool, $numberToPick, $additionalDraw)
  {
    $units = Units::getInLocation($pool)->toArray();
    shuffle($units);

    $picked = array_slice($units, 0, $numberToPick);

    $location = $this->poolReinforcementsMap[$pool];
    Units::move(array_map(function ($unit) {
      return $unit->getId();
    }, $picked), $location);

    if (count($picked) > 0) {
      Notifications::drawnReinforcements($player, $picked, $location, $additionalDraw);
    } else {
      Notifications::message(clienttranslate('No tokens left in pool'));
    }
    
    $vagariesOfWarTokens = Utils::filter($picked, function ($token) {
      return $token->isVagariesOfWarToken();
    });

    // Resolve all Vagaries of War that can be auto resolved
    foreach ($vagariesOfWarTokens as $token) {
      $counterId = $token->getCounterId();
      if (in_array($counterId, [VOW_FEWER_TROOPS_BRITISH, VOW_FEWER_TROOPS_FRENCH, VOW_FEWER_TROOPS_COLONIAL])) {
        $token->removeFromPlay();
      }

      // if (in_array($counterId, [VOW_FEWER_TROOPS_PUT_BACK_BRITISH, VOW_FEWER_TROOPS_PUT_BACK_FRENCH, VOW_FEWER_TROOPS_PUT_BACK_COLONIAL])) {
      //   $token->returnToPool($pool);
      // }

      if ($counterId === VOW_FRENCH_NAVY_LOSSES_PUT_BACK) {
        $frenchFleets = Utils::filter(Units::getInLocation(POOL_FLEETS)->toArray(), function ($unit) {
          return $unit->isFleet() && $unit->getFaction() === FRENCH;
        });
        $numberOfFrenchFleets = count($frenchFleets);
        if ($numberOfFrenchFleets === 0) {
          Notifications::noFrenchFleetInPool();
        } else {
          $index = bga_rand(0, $numberOfFrenchFleets - 1);
          $frenchFleets[$index]->removeFromPool();
        }
        // $token->returnToPool($pool);
      }

      if ($counterId === VOW_PENNSYLVANIA_MUSTERS) {
        $bonusUnits = Utils::filter(Units::getInLocation(POOL_BRITISH_COLONIAL_VOW_BONUS)->toArray(), function ($unit) {
          return $unit->getCounterId() === PENN_DEL;
        });
        $pickedBonusUnits = array_slice($bonusUnits, 0, 2);
        Units::move(array_map(function ($unit) {
          return $unit->getId();
        }, $pickedBonusUnits), $location);
        Notifications::drawnBonusUnits($player, $pickedBonusUnits, $location);
        $token->removeFromPlay();
      }

      if ($counterId === VOW_PITT_SUBSIDIES) {
        $bonusUnits = Utils::filter(Units::getInLocation(POOL_BRITISH_COLONIAL_VOW_BONUS)->toArray(), function ($unit) {
          return in_array($unit->getCounterId(), [NEW_ENGLAND, NYORK_NJ, VIRGINIA_S]);
        });
        $pickedBonusUnits = [];
        $newEnglandUnits = Utils::filter($bonusUnits, function ($unit) {
          return $unit->getCounterId() === NEW_ENGLAND;
        });
        $pickedBonusUnits = array_slice($newEnglandUnits, 0, 2);
        foreach ([NYORK_NJ, VIRGINIA_S] as $counterId) {
          $bonusUnit = Utils::array_find($bonusUnits, function ($unit) use ($counterId) {
            return $unit->getCounterId() === $counterId;
          });
          if ($bonusUnit !== null) {
            $pickedBonusUnits[] = $bonusUnit;
          }
        }

        Units::move(array_map(function ($unit) {
          return $unit->getId();
        }, $pickedBonusUnits), $location);
        Notifications::drawnBonusUnits($player, $pickedBonusUnits, $location);
        $token->removeFromPlay();
      }
    }
  }
}
