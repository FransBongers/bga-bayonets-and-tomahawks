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
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundSailBoxLanding extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_SAIL_BOX_LANDING;
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

  public function stActionRoundSailBoxLanding()
  {
    $player = self::getPlayer();
    $faction = $player->getFaction();

    $units = Utils::filter(Units::getInLocation(SAIL_BOX)->toArray(), function ($unit) use ($faction) {
      return $unit->getFaction() === $faction;
    });

    if (count($units) > 0) {
      return;
    }
    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundSailBoxLanding()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundSailBoxLanding()
  {


    return [
      'spaces' => $this->getOptions(),
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

  public function actPassActionRoundSailBoxLanding()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actActionRoundSailBoxLanding($args)
  {
    self::checkAction('actActionRoundSailBoxLanding');

    $spaceId = $args['spaceId'];

    $options = $this->getOptions();

    $space = Utils::array_find($options, function ($option) use ($spaceId) {
      return $spaceId === $option->getId();
    });

    $player = self::getPlayer();
    $faction = $player->getFaction();

    $playerId = $player->getId();
    $units = Utils::filter(Units::getInLocation(SAIL_BOX)->toArray(), function ($unit) use ($faction) {
      return $unit->getFaction() === $faction;
    });
    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $units);

    $this->ctx->insertAsBrother(Engine::buildTree([
      'children' => [
        [
          'action' => MOVE_STACK,
          'playerId' => $playerId,
          'fromSpaceId' => SAIL_BOX,
          'toSpaceId' => $spaceId,
          'unitIds' => $unitIds,
        ],
        [
          'action' => MOVEMENT_OVERWHELM_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $spaceId,
        ],
        [
          'action' => MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $spaceId,
          'source' => ACTION_ROUND_SAIL_BOX_LANDING,
          'destinationId' => null,
          'requiredUnitIds' => [],
        ],
        [
          'action' => MOVEMENT_PLACE_SPENT_MARKERS,
          'playerId' => $playerId,
        ],
      ]
    ]));

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
    $player = self::getPlayer();
    $faction = $player->getFaction();


    $spaces = Spaces::getAll()->toArray();
    $friendlySeaZones = GameMap::getFriendlySeaZones($faction);

    return Utils::filter($spaces, function ($space) use ($friendlySeaZones, $faction) {
      if ($faction === FRENCH && $space->getBritishBase()) {
        return false;
      }

      return $space->isCoastal() && Utils::array_some($space->getAdjacentSeaZones(), function ($seaZone) use ($friendlySeaZones) {
        return in_array($seaZone, $friendlySeaZones);
      });
    });
  }
}
