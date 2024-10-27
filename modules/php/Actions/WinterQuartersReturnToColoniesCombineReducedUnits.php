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
    $data = $this->getOptions();

    if (count($data) === 0) {
      $this->resolveAction(['automatic' => true]);
    } else if (count($data) === 1 && isset($data[DISBANDED_COLONIAL_BRIGADES])) {
      // Disbanded Colonial Brigades is the only one remaining
      $this->ctx->getParent()->pushChild(
        Engine::buildTree([
          'playerId' => self::getPlayer()->getId(),
          'action' => BATTLE_COMBINE_REDUCED_UNITS,
          'spaceId' => DISBANDED_COLONIAL_BRIGADES,
          'faction' => $this->ctx->getInfo()['faction'],
        ])
      );
      $this->resolveAction(['automatic' => true]);
    }
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
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    // $data = 

    return [
      // 'destinationIds' => $data['destinationIds'],
      'options' => $this->getOptions(),
      'faction' => $faction,
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
    Engine::resolve(PASS);
  }

  public function actWinterQuartersReturnToColoniesCombineReducedUnits($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesCombineReducedUnits');
    $spaceId = $args['spaceId'];

    $info = $this->ctx->getInfo();
    $faction = $info['faction'];
    $player = self::getPlayer();

    $this->ctx->getParent()->pushChild(
      Engine::buildTree([
        'playerId' => $player->getId(),
        'action' => BATTLE_COMBINE_REDUCED_UNITS,
        'spaceId' => $spaceId,
        'faction' => $faction,
      ])
    );

    $this->ctx->getParent()->pushChild(
      Engine::buildTree([
        'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_COMBINE_REDUCED_UNITS,
        'faction' => $faction,
        'playerId' => $player->getId(),
      ])
    );

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function getOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $units = Units::getAll()->toArray();

    $stacks = GameMap::getStacks(null, $units)[$faction];

    $options = [];
    foreach ($stacks as $spaceId => $data) {
      if ($this->canReduceUnitsInStack($data['units'])) {
        $options[$spaceId] = $data;
      }
    }

    if ($faction === FRENCH) {
      return $options;
    }

    $disbandedColonialBrigades = Utils::filter($units, function ($unit) {
      return $unit->getLocation() === DISBANDED_COLONIAL_BRIGADES;
    });
    if ($this->canReduceUnitsInStack($disbandedColonialBrigades)) {
      $options[DISBANDED_COLONIAL_BRIGADES] = [
        'units' => $disbandedColonialBrigades,
        'space' => null,
      ];
    }

    return $options;
  }

  public static function canReduceUnitsInStack($unitsInSpace)
  {
    $reducedUnits = Utils::filter($unitsInSpace, function ($unit) {
      return $unit->isReduced() && !$unit->isIndian();
    });

    $data = [
      ARTILLERY => [],
      FLEET => [],
      NON_INDIAN_LIGHT => [],
      METROPOLITAN_BRIGADES => [],
      NON_METROPOLITAN_BRIGADES => [],
    ];

    foreach ($reducedUnits as $unit) {
      if ($unit->isArtillery()) {
        $data[ARTILLERY][] = $unit;
      } else if ($unit->isFleet()) {
        $data[FLEET][] = $unit;
      } else if ($unit->isNonIndianLight()) {
        $data[NON_INDIAN_LIGHT][] = $unit;
      } else if ($unit->isMetropolitanBrigade()) {
        $data[METROPOLITAN_BRIGADES][] = $unit;
      } else if ($unit->isNonMetropolitanBrigade()) {
        $data[NON_METROPOLITAN_BRIGADES][] = $unit;
      }
    }

    return Utils::array_some(array_values($data), function ($reducedUnitsForType) {
      return count($reducedUnitsForType) >= 2;
    });
  }
}
