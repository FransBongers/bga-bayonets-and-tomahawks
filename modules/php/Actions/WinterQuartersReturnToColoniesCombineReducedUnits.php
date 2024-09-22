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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Marker;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenario;

class WinterQuartersReturnToColoniesCombineReducedUnits extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS;
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

  public function stWinterQuartersReturnToColoniesCombineReducedUnits()
  {
    // $data = $this->getOptions();


    // if (count($data['options']) === 0) {
    //   $this->resolveAction(['automatic' => true, 'unitIds' => []]);
    // }
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersReturnToColoniesCombineReducedUnits() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersReturnToColoniesCombineReducedUnits()
  {
    // $info = $this->ctx->getInfo();
    // $faction = $info['faction'];

    // $data = $this->getOptions();

    return [
      // 'destinationIds' => $data['destinationIds'],
      // 'options' => $data['options'],
      // 'faction' => $faction,
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

  public function actPassWinterQuartersReturnToColoniesCombineReducedUnits()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actWinterQuartersReturnToColoniesCombineReducedUnits($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesCombineReducedUnits');
    

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...
  
  // public static function checkIfReducedUnitsCanBeCombined($unitsInSpace, $faction, $player)
  // {
  //   $reducedUnits = Utils::filter($units, function ($unit) {
  //     return $unit->isReduced() && !$unit->isIndian();
  //   });

  //   $data = [
  //     ARTILLERY => [],
  //     FLEET => [],
  //     NON_INDIAN_LIGHT => [],
  //     METROPOLITAN_BRIGADES => [],
  //     NON_METROPOLITAN_BRIGADES => [],
  //   ];

  //   foreach ($reducedUnits as $unit) {
  //     if ($unit->isArtillery()) {
  //       $data[ARTILLERY][] = $unit;
  //     } else if ($unit->isFleet()) {
  //       $data[FLEET][] = $unit;
  //     } else if ($unit->isNonIndianLight()) {
  //       $data[NON_INDIAN_LIGHT][] = $unit;
  //     } else if ($unit->isMetropolitanBrigade()) {
  //       $data[METROPOLITAN_BRIGADES][] = $unit;
  //     } else if ($unit->isNonMetropolitanBrigade()) {
  //       $data[NON_METROPOLITAN_BRIGADES][] = $unit;
  //     }
  //   }


  //   $options = $action->getOptions($space, $faction);
  //   $canCombineReduced = Utils::array_some(array_values($options), function ($reducedUnitsForType) {
  //     return count($reducedUnitsForType) >= 2;
  //   });
  //   if ($canCombineReduced) {
  //     $this->ctx->insertAsBrother(
  //       Engine::buildTree([
  //         'playerId' => $player->getId(),
  //         'action' => BATTLE_COMBINE_REDUCED_UNITS,
  //         'spaceId' => $space->getId(),
  //         'faction' => $faction,
  //       ])
  //     );
  //   }
  // }

}
