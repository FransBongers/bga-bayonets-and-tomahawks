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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Marker;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenario;

class WinterQuartersMoveStackOnSailBox extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_MOVE_STACK_ON_SAIL_BOX;
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

  public function stWinterQuartersMoveStackOnSailBox()
  {
    $stateArgs = $this->argsWinterQuartersMoveStackOnSailBox();

    if (count($stateArgs['spaceIds']) > 1) {
      return;
    }

    if (count($stateArgs['spaceIds']) === 1 && $stateArgs['spaceIds'][0] === SAIL_BOX) {
      Notifications::message(clienttranslate('${player_name} cannot move their stack from the Sail Box'), [
        'player' => self::getPlayer(),
      ]);
      $this->resolveAction(['automatic' => true]);
    } else if (count($stateArgs['spaceIds']) === 1) {
      $this->moveStackFromSailBox($stateArgs['spaceIds'][0]);
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

  public function stPreWinterQuartersMoveStackOnSailBox() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersMoveStackOnSailBox()
  {
    $player = self::getPlayer();
    $faction = $player->getFaction();

    $spaceIds = array_map(function ($space) {
      return $space->getId();
    }, Utils::filter(Spaces::getControlledBy($faction), function ($space) use ($faction) {
      return $space->getHomeSpace() === $faction && $space->isCoastal() && $space->getControl() === $faction;
    }));

    if (count($spaceIds) === 0) {
      $spaceIds = BTHelpers::getSpacesBasedOnFleetRetreatPriorities($faction);
    }

    return [
      'spaceIds' => $spaceIds,
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

  public function actPassWinterQuartersMoveStackOnSailBox()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actWinterQuartersMoveStackOnSailBox($args)
  {
    self::checkAction('actWinterQuartersMoveStackOnSailBox');
    $spaceId = $args['spaceId'];

    $validSpace = Utils::array_some($this->argsWinterQuartersMoveStackOnSailBox()['spaceIds'], function ($possibleSpaceId) use ($spaceId) {
      return $spaceId === $possibleSpaceId;
    });

    if (!$validSpace) {
      throw new \feException("ERROR 074");
    }

    $this->moveStackFromSailBox($spaceId);


    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


  private function moveStackFromSailBox($spaceId)
  {
    $space = Spaces::get($spaceId);

    $player = self::getPlayer();
    $faction = $player->getFaction();
    $units = Utils::filter(Units::getInLocation(SAIL_BOX)->toArray(), function ($unit) use ($faction) {
      return $unit->getFaction() === $faction;
    });

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $units);

    Units::move($unitIds, $spaceId);
    $markers = Markers::getInLocation(Locations::stackMarker(SAIL_BOX, $faction))->toArray();

    Notifications::moveStackFromSailBox($player, $units, $markers, $space, $faction);
  }
}
