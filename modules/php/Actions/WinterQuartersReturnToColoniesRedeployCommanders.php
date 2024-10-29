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

class WinterQuartersReturnToColoniesRedeployCommanders extends \BayonetsAndTomahawks\Actions\WinterQuartersReturnToColonies
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_RETURN_TO_COLONIES_REDEPLOY_COMMANDERS;
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

  public function stWinterQuartersReturnToColoniesRedeployCommanders()
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

  public function stPreWinterQuartersReturnToColoniesRedeployCommanders() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersReturnToColoniesRedeployCommanders()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $data = $this->getOptions();

    return [
      'commanders' => $data['commanders'],
      'stacks' => $data['stacks'],
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

  public function actPassWinterQuartersReturnToColoniesRedeployCommanders()
  {
    $player = self::getPlayer();

    Notifications::message(clienttranslate('${player_name} does not redeploy their Commanders'), [
      'player' => $player,
    ]);

    $this->resolveAction([]);
  }

  public function actWinterQuartersReturnToColoniesRedeployCommanders($args)
  {
    self::checkAction('actWinterQuartersReturnToColoniesRedeployCommanders');

    $redeployedCommanders = $args['redeployedCommanders'];

    $stateArgs = $this->argsWinterQuartersReturnToColoniesRedeployCommanders();

    $player = self::getPlayer();

    foreach ($redeployedCommanders as $commanderId => $spaceId) {
      $commander = Utils::array_find($stateArgs['commanders'], function ($unit) use ($commanderId) {
        return $unit->getId() === $commanderId;
      });
      if ($commander === null) {
        throw new \feException("ERROR 088");
      }
      if ($commander->getLocation() === $spaceId) {
        continue;
      }

      if (!isset($stateArgs['stacks'][$spaceId])) {
        throw new \feException("ERROR 089");
      }
      $stack = $stateArgs['stacks'][$spaceId];
      Units::move($commanderId, $spaceId);

      Notifications::redeployUnit($player, $commander, Spaces::get($commander->getLocation()), $stack['space']);
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

  private function getOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    $units = Units::getAll()->toArray();
    $spaces = [];
    $spaceIds = [];
    $controlledSpaces = Spaces::getControlledBy($faction);

    foreach ($controlledSpaces as $space) {
      if ($space->getHomeSpace() === $faction && $space->getColony() !== null) {
        $spaceIds[] = $space->getId();
        $spaces[$space->getId()] = $space;
      }
    }

    $stacks = GameMap::getStacks($spaces, Utils::filter($units, function ($unit) use ($faction, $spaceIds) {
      return !$unit->isFleet() && $unit->getFaction() === $faction && in_array($unit->getLocation(), $spaceIds);
    }))[$faction];

    return [
      'commanders' => Utils::filter($units, function ($unit) use ($faction) {
        $location = $unit->getLocation();
        return $unit->getFaction() === $faction && $unit->isCommander() && !Utils::startsWith($location, 'pool') && $location !== REMOVED_FROM_PLAY;
      }),
      'stacks' => $stacks,
    ];
  }
}
