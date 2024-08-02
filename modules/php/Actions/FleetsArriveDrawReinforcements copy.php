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
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class FleetsArriveDrawReinforcements extends \BayonetsAndTomahawks\Actions\FleetsArrive
{

  public function getState()
  {
    return ST_FLEETS_ARRIVE_DRAW_REINFORCEMENTS;
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

  public function stFleetsArriveDrawReinforcements()
  {
    // Notifications::log('stFleetsArriveDrawReinforcements', []);
    // Globals::setActionRound(ACTION_ROUND_3);
    // Markers::move(ROUND_MARKER, ACTION_ROUND_3);
    // Notifications::moveRoundMarker(Markers::get(ROUND_MARKER), ACTION_ROUND_3);

    $reinforcements = Scenarios::get()->getReinforcements()[Globals::getYear()];

    $info = $this->ctx->getInfo();

    $pool = $info['pool'];

    $units = Units::getInLocation($pool)->toArray();
    shuffle($units);

    $picked = array_slice($units, 0, $reinforcements[$pool]);

    $location = $this->poolReinforcementsMap[$pool];
    Units::move(array_map(function ($unit) {
      return $unit->getId();
    }, $picked), $location);
    Notifications::drawnReinforcements($picked, $location);

    $vagariesOfWarTokens = Utils::filter($picked, function ($token) {
      return $token->isVagariesOfWarToken();
    });

    // Resolve all Vagaries of War that can be auto resolved
    foreach ($vagariesOfWarTokens as $token) {
      $counterId = $token->getCounterId();
      if (in_array($counterId, [VOW_FEWER_TROOPS_BRITISH, VOW_FEWER_TROOPS_FRENCH])) {
        $token->removeFromPlay();
      }

      if (in_array($counterId, [VOW_FEWER_TROOPS_PUT_BACK_BRITISH, VOW_FEWER_TROOPS_PUT_BACK_FRENCH])) {
        $token->returnToPool($pool);
      }

      if ($counterId === VOW_FRENCH_NAVY_LOSSES_PUT_BACK) {
        $frenchFleets = Utils::filter(Units::getInLocation(POOL_FLEETS)->toArray(), function ($unit) {
          return $unit->isFleet() && $unit->getFaction() === FRENCH;
        });
        $numberOfFrenchFleets = count($frenchFleets);
        if ($numberOfFrenchFleets === 0) {
          Notifications::noFrenchFleetInPool();
        } else {
          $index = bga_rand(0, $numberOfFrenchFleets - 1);
          $frenchFleets[$index]->removeFromPool();
        }
        $token->returnToPool($pool);
      }
    }

    $this->resolveAction(['automatic' => true], true);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreFleetsArriveDrawReinforcements()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsFleetsArriveDrawReinforcements()
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

  public function actPassFleetsArriveDrawReinforcements()
  {
    // $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actFleetsArriveDrawReinforcements($args)
  {
    self::checkAction('actFleetsArriveDrawReinforcements');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...


}
