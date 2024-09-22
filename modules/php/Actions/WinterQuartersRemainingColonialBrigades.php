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

class WinterQuartersRemainingColonialBrigades extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES;
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

  public function stWinterQuartersRemainingColonialBrigades()
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

  public function stPreWinterQuartersRemainingColonialBrigades() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsWinterQuartersRemainingColonialBrigades()
  {

    return [
      'options' => $this->getOptions(),
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

  public function actPassWinterQuartersRemainingColonialBrigades()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actWinterQuartersRemainingColonialBrigades($args)
  {
    self::checkAction('actWinterQuartersRemainingColonialBrigades');

    $spaceId = $args['spaceId'];
    $selectedUnitIds = $args['selectedUnitIds'];

    $options = $this->getOptions();

    if (!isset($options[$spaceId])) {
      throw new \feException("ERROR 075");
    }

    $option = $options[$spaceId];

    $unitsThatRemain = Utils::filter($option['units'], function ($unit) use ($selectedUnitIds) {
      return in_array($unit->getId(), $selectedUnitIds);
    });

    if (count($unitsThatRemain) !== count($selectedUnitIds)) {
      throw new \feException("ERROR 076");
    }

    if (count($unitsThatRemain) > $option['maxRemain']) {
      throw new \feException("ERROR 077");
    }

    $unitsToDisband = Utils::filter($option['units'], function ($unit) use ($selectedUnitIds) {
      return !in_array($unit->getId(), $selectedUnitIds);
    });

    Units::move(array_map(function ($unit) {
      return $unit->getId();
    }, $unitsToDisband), DISBANDED_COLONIAL_BRIGADES);

    $player = self::getPlayer();

    Notifications::winterQuartersRemainingColonialBrigades($player, $unitsToDisband, $unitsThatRemain, $option['space']);

    if (count($this->getOptions($spaceId)) > 0) {
      $node = [
        'action' => WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES,
        'playerId' => $player->getId(),
      ];

      $this->ctx->insertAsBrother(Engine::buildTree($node));
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

  private function getOptions($resolvedSpaceId = null)
  {
    $alreadyResolved = $this->ctx->getParent()->getResolvedActions([WINTER_QUARTERS_REMAINING_COLONIAL_BRIGADES]);

    $resolvedSpaceIds = $resolvedSpaceId !== null ? [$resolvedSpaceId] : [];

    foreach ($alreadyResolved as $node) {
      $resArgs = $node->getActionResolutionArgs();
      $spaceId = $resArgs['spaceId'];
      $resolvedSpaceIds[] = $spaceId;
    }

    $units = Units::getAll()->toArray();
    $spaces = Spaces::getAll();

    $options = [];

    foreach ($units as $unit) {
      if (!$unit->isColonialBrigade()) {
        continue;
      };
      $location = $unit->getLocation();
      if (in_array($location, $resolvedSpaceIds) || !in_array($location, SPACES) || in_array($location, BASTIONS)) {
        continue;
      }

      if (isset($options[$location])) {
        $options[$location]['units'][] = $unit;
      } else {
        $options[$location] = [
          'space' => $spaces[$location],
          'maxRemain' => $spaces[$location]->getValue(),
          'units' => [$unit]
        ];
      }
    }

    return $options;
  }
}
