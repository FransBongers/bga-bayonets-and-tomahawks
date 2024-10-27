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

class WinterQuartersReturnToColoniesMoveStack extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK;
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

  public function stWinterQuartersReturnToColoniesMoveStack()
  {
    $info = $this->ctx->getInfo();

    $faction = $info['faction'];
    $originId = $info['originId'];
    $destinationId = $info['destinationId'];
    $path = $info['path'];
    $unitIds = $info['unitIds'];
    $player = self::getPlayer();

    $units = Units::getAll()->toArray();
    $spaces = Spaces::getAll();

    $stackUnits = Utils::filter($units, function ($unit) use ($unitIds) {
      return in_array($unit->getId(), $unitIds);
    });

    $movedSpaces = [];

    while (count($path) > 0) {
      $nextSpaceId = array_shift($path);
      $space = $spaces[$nextSpaceId];

      $movedSpaces[] = $space;
      if ($nextSpaceId === $destinationId) {
        continue;
      }

      if ($this->canUnitsRemainOnSpace($space, $units, $stackUnits, $unitIds, $faction)) {
        $this->ctx->getParent()->pushChild(new LeafNode([
          'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_LEAVE_UNITS,
          'spaceId' => $nextSpaceId,
          'faction' => $faction,
          'playerId' => $player->getId(),
          'originId' => $originId,
          'destinationId' => $destinationId,
          'path' => $path,
          'unitIds' => $unitIds,
          'optional' => true,
        ]));
        break;
      }
    }

    Units::move($unitIds, $movedSpaces[count($movedSpaces) -1]->getId());

    Notifications::winterQuartersReturnToColoniesMove($player,$stackUnits, $movedSpaces, $faction);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersReturnToColoniesMoveStack() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersReturnToColoniesMoveStack()
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

  public function actPassWinterQuartersReturnToColoniesMoveStack()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actWinterQuartersReturnToColoniesMoveStack($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesMoveStack');


    // $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
