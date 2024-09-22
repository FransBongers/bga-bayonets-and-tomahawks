<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
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

class WinterQuartersReturnToColoniesLeaveUnits extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_RETURN_TO_COLONIES_LEAVE_UNITS;
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

  public function stWinterQuartersReturnToColoniesLeaveUnits()
  {


    // $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreWinterQuartersReturnToColoniesLeaveUnits() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersReturnToColoniesLeaveUnits()
  {
    $info = $this->ctx->getInfo();

    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    // 'originId' => $originId,
    // 'destinationId' => $destinationId,
    // 'path' => $path,
    $stackUnitIds = $info['unitIds'];

    $space = Spaces::get($spaceId);
    $units = Units::getAll()->toArray();
    $stackUnits = Utils::filter($units, function ($unit) use ($stackUnitIds) {
      return in_array($unit->getId(), $stackUnitIds);
    });


    $data = $this->getUnitsThatCanRemainOnSpace($space, $units, $stackUnits, $stackUnitIds, $faction);

    return [
      'faction' => $faction,
      'units' => $stackUnits,
      'space' => $space,
      'maxBrigades' => $data['maxBrigades'],
      'maxTotal' => $data['maxTotal'],
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

  public function actPassWinterQuartersReturnToColoniesLeaveUnits()
  {
    $player = self::getPlayer();
    $info = $this->ctx->getInfo();

    $faction = $info['faction'];
    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    Notifications::message(clienttranslate('${player_name} does not leave units on ${tkn_boldText_spaceName}') . [
      'player' => $player,
      'tkn_boldText_spaceName' => $space->getName(),
      'i18n' => ['tkn_boldText_spaceName'],
    ]);

    $originId = $info['originId'];
    $destinationId = $info['destinationId'];
    $path = $info['path'];
    $unitIds = $info['unitIds'];

    $this->ctx->getParent()->pushChild(new LeafNode([
      'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK,
      'faction' => $faction,
      'playerId' => $player->getId(),
      'originId' => $originId,
      'destinationId' => $destinationId,
      'path' => $path,
      'unitIds' => $unitIds,
    ]));

    $this->resolveAction(PASS);
  }

  public function actWinterQuartersReturnToColoniesLeaveUnits($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesLeaveUnits');

    $selectedUnitIds = $args['selectedUnitIds'];

    $stateArgs = $this->argsWinterQuartersReturnToColoniesLeaveUnits();

    $unitsThatRemain = Utils::filter($stateArgs['units'], function ($unit) use ($selectedUnitIds) {
      return in_array($unit->getId(), $selectedUnitIds);
    });

    if ($stateArgs['maxTotal'] !== null && count($unitsThatRemain) > $stateArgs['maxTotal']) {
      throw new \feException("ERROR 082");
    }

    $remainingBrigades = Utils::filter($unitsThatRemain, function ($unit) {
      return $unit->isBrigade();
    });

    if (count($remainingBrigades) > $stateArgs['maxBrigades']) {
      throw new \feException("ERROR 083");
    }

    $unitsThatDoNotRemain = Utils::filter($stateArgs['units'], function ($unit) use ($selectedUnitIds) {
      return !in_array($unit->getId(), $selectedUnitIds);
    });

    $player = self::getPlayer();

    Notifications::winterQuartersReturnToColoniesLeaveUnits($player, $unitsThatRemain, $stateArgs['space']);

    if (count($unitsThatDoNotRemain) > 0) {
      $info = $this->ctx->getInfo();
      $faction = $info['faction'];
      $originId = $info['originId'];
      $destinationId = $info['destinationId'];
      $path = $info['path'];

      $this->ctx->getParent()->pushChild(new LeafNode([
        'action' => WINTER_QUARTERS_RETURN_TO_COLONIES_MOVE_STACK,
        'faction' => $faction,
        'playerId' => $player->getId(),
        'originId' => $originId,
        'destinationId' => $destinationId,
        'path' => $path,
        'unitIds' => array_map(function ($unit) {
          return $unit->getId();
        }, $unitsThatDoNotRemain),
      ]));
    }

    // if (count($this->getOptions($spaceId)) > 0) {
    //   $node = [
    //     'action' => WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES,
    //     'playerId' => $player->getId(),
    //   ];

    //   $this->ctx->insertAsBrother(Engine::buildTree($node));
    // }

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
