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
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class EventPlaceIndianNationUnits extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_PLACE_INDIAN_NATION_UNITS;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventPlaceIndianNationUnits() {}

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

  public function stEventPlaceIndianNationUnits()
  {

    // $indianUnits = Utils::filter(Units::getInLocation(Locations::lossesBox(FRENCH))->toArray(), function ($unit) {
    //   return $unit->isIndian();
    // });

    // if (count($indianUnits) === 0) {
    //   Notifications::message(clienttranslate('There are no Indian units to take from the Losses Box'), []);
    //   $this->resolveAction(['automatic' => true]);
    //   return;
    // }

    // shuffle($indianUnits);

    // $picked = array_slice($indianUnits, 0, 2);
    // $player = Players::getPlayerForFaction(FRENCH);

    // Notifications::message(clienttranslate('${player_name} takes ${unitsLog} from the Losses Box'), [
    //   'player' => $player,
    //   'unitsLog' => Notifications::getUnitsLog($picked),
    // ]);

    // $indianNationUnits = [];

    // // TODO: Iroquois and Cherokee
    // foreach ($picked as $unit) {
    //   $villages = Utils::filter(Spaces::getMany($unit->getVillages())->toArray(), function ($space) {
    //     if (count($space->getUnits(BRITISH)) > 0) {
    //       return false;
    //     }
    //     return true;
    //   });
    //   $count = count($villages);
    //   if ($count === 0) {
    //     Notifications::message(clienttranslate('${player_name} cannot place ${tkn_unit} on its Village'), [
    //       'player' => $player,
    //       'tkn_unit' => $unit->getCounterId(),
    //     ]);
    //   } else if ($count > 1) {
    //     // TODO: insert extra state to pick village?
    //     $indianNationUnits[] = $unit;
    //   } else {
    //     $space = $villages[0];
    //     $unit->setLocation($space->getId());
    //     Notifications::placeUnits($player, [$unit], $space, FRENCH);
    //     if ($space->getControl() === BRITISH && $space->getDefaultControl() !== BRITISH) {
    //       $space->setControl($space->getDefaultControl());
    //       Notifications::loseControl(Players::getPlayerForFaction(BRITISH), $space);
    //     }
    //   }
    // }

    // if (count($indianNationUnits) > 0) {
    //   $this->ctx->insertAsBrother(Engine::buildTree([
    //     'action' => ACTION_ROUND_ACTION_PHASE,
    //     'playerId' => $player->getId(),
    //     'unitIds' => BTHelpers::returnIds($indianNationUnits),
    //   ]));
    // }

    // $this->resolveAction(['automatic' => true], true);
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventPlaceIndianNationUnits()
  {

    $info = $this->ctx->getInfo();
    $unitIds = $info['unitIds'];

    $units = Units::getMany($unitIds);

    $options = [];

    foreach ($units as $unitId => $unit) {
      $villages = Utils::filter(Spaces::getMany($unit->getVillages())->toArray(), function ($space) {
        if (count($space->getUnits(BRITISH)) > 0) {
          return false;
        }
        return true;
      });
      $options[$unitId] = [
        'unit' => $unit,
        'spaces' => $villages
      ];
    }

    return [
      'options' => $options,
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

  public function actPassEventPlaceIndianNationUnits()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actEventPlaceIndianNationUnits($args)
  {
    self::checkAction('actEventPlaceIndianNationUnits');

    $spaceId = $args['spaceId'];
    $unitId = $args['unitId'];

    $options = $this->argsEventPlaceIndianNationUnits()['options'];

    if (!isset($options[$unitId])) {
      throw new \feException("ERROR 101");
    }
    $option = $options[$unitId];

    $space = Utils::array_find($option['spaces'], function ($space) use ($spaceId) {
      return $space->getId() === $spaceId;
    });

    if ($space === null) {
      throw new \feException("ERROR 102");
    }

    $unit = $option['unit'];
    $player = self::getPlayer();

    $unit->setLocation($space->getId());
    Notifications::placeUnits($player, [$unit], $space, FRENCH);
    if ($space->getControl() === BRITISH && $space->getDefaultControl() !== BRITISH) {
      $space->setControl($space->getDefaultControl());
      Notifications::loseControl(Players::getPlayerForFaction(BRITISH), $space);
    }

    $info = $this->ctx->getInfo();
    $unitIds = $info['unitIds'];

    $remainingUnitIds = Utils::filter($unitIds, function ($remainingUnitId) use ($unitId) {
      return $unitId !== $remainingUnitId;
    });

    if (count($remainingUnitIds) > 0) {
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => EVENT_PLACE_INDIAN_NATION_UNITS,
        'playerId' => $player->getId(),
        'unitIds' => $remainingUnitIds,
      ]));
    }

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

}
