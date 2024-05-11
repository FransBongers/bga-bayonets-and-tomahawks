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
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\StackActions;
use BayonetsAndTomahawks\Managers\Tokens;
use BayonetsAndTomahawks\Models\Player;

class UnitMovement extends \BayonetsAndTomahawks\Actions\StackAction
{
  // public function getState()
  // {
  //   return ST_MOVEMENT_SELECT_DESTINATION_AND_UNITS;
  // }

  // // ..######..########....###....########.########
  // // .##....##....##......##.##......##....##......
  // // .##..........##.....##...##.....##....##......
  // // ..######.....##....##.....##....##....######..
  // // .......##....##....#########....##....##......
  // // .##....##....##....##.....##....##....##......
  // // ..######.....##....##.....##....##....########

  // // ....###.....######..########.####..#######..##....##
  // // ...##.##...##....##....##.....##..##.....##.###...##
  // // ..##...##..##..........##.....##..##.....##.####..##
  // // .##.....##.##..........##.....##..##.....##.##.##.##
  // // .#########.##..........##.....##..##.....##.##..####
  // // .##.....##.##....##....##.....##..##.....##.##...###
  // // .##.....##..######.....##....####..#######..##....##

  // public function stMovementSelectDestinationAndUnits()
  // {

  // }

  // // .########..########..########.......###.....######..########.####..#######..##....##
  // // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  // public function stPreMovementSelectDestinationAndUnits()
  // {
  // }


  // // ....###....########...######....######.
  // // ...##.##...##.....##.##....##..##....##
  // // ..##...##..##.....##.##........##......
  // // .##.....##.########..##...####..######.
  // // .#########.##...##...##....##........##
  // // .##.....##.##....##..##....##..##....##
  // // .##.....##.##.....##..######....######.

  // public function argsMovementSelectDestinationAndUnits()
  // {
  //   $info = $this->ctx->getInfo();
  //   $parentInfo = $this->ctx->getParent()->getInfo();
  //   $player = self::getPlayer();
  //   $stackActionId = $parentInfo['stackAction'];
  //   $stackAction = StackActions::get($stackActionId);
  //   $indianActionPoint = $parentInfo['indianActionPoint'];

  //   $spaceId = $info['space'];
  //   $space = Spaces::get($spaceId);

  //   $units = $stackAction->getUnitThatCanPerformAction($space->getUnits($player->getFaction()),$indianActionPoint);

  //   $adjacentSpaces = $space->getAdjacentSpaces();

  //   foreach ($adjacentSpaces as $targetSpaceId => $connection) {

  //   }

  //   return [
  //     'info' => $info,
  //     'parentInfo' => $parentInfo,
  //     'units' => $units,
  //   ];
  // }

  // //  .########..##..........###....##....##.########.########.
  // //  .##.....##.##.........##.##....##..##..##.......##.....##
  // //  .##.....##.##........##...##....####...##.......##.....##
  // //  .########..##.......##.....##....##....######...########.
  // //  .##........##.......#########....##....##.......##...##..
  // //  .##........##.......##.....##....##....##.......##....##.
  // //  .##........########.##.....##....##....########.##.....##

  // // ....###.....######..########.####..#######..##....##
  // // ...##.##...##....##....##.....##..##.....##.###...##
  // // ..##...##..##..........##.....##..##.....##.####..##
  // // .##.....##.##..........##.....##..##.....##.##.##.##
  // // .#########.##..........##.....##..##.....##.##..####
  // // .##.....##.##....##....##.....##..##.....##.##...###
  // // .##.....##..######.....##....####..#######..##....##

  // public function actPassMovementSelectDestinationAndUnits()
  // {
  //   $player = self::getPlayer();
  //   // Stats::incPassActionCount($player->getId(), 1);
  //   Engine::resolve(PASS);
  // }

  // public function actMovementSelectDestinationAndUnits($args)
  // {
  //   self::checkAction('actMovementSelectDestinationAndUnits');



  //   $this->resolveAction($args, true);
  // }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
