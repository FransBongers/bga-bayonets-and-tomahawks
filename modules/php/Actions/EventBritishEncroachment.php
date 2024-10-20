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
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class EventBritishEncroachment extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_BRITISH_ENCROACHMENT;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventBritishEncroachment()
  {
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

  public function stEventBritishEncroachment()
  {

    $indianUnits = Utils::filter(Units::getInLocation(Locations::lossesBox(FRENCH))->toArray(), function ($unit) {
      return $unit->isIndian();
    });

    if (count($indianUnits) === 0) {
      Notifications::message(clienttranslate('There are no Indian units to take from the Losses Box'), []);
      $this->resolveAction(['automatic' => true]);
      return;
    }

    shuffle($indianUnits);

    $picked = array_slice($indianUnits, 0, 2);
    $player = Players::getPlayerForFaction(FRENCH);

    Notifications::message(clienttranslate('${player_name} takes ${unitsLog} from the Losses Box'), [
      'player' => $player,
      'unitsLog' => Notifications::getUnitsLog($picked),
    ]);

    // TODO: Iroquois and Cherokee
    foreach ($picked as $unit) {
      $villages = Utils::filter(Spaces::getMany($unit->getVillages())->toArray(), function ($space) {
        if (count($space->getUnits(BRITISH)) > 0) {
          return false;
        }
        return true;
      });
      $count = count($villages);
      if ($count === 0) {
        Notifications::message(clienttranslate('${player_name} cannot place ${tkn_unit} on its Village'), [
          'player' => $player,
          'tkn_unit' => $unit->getCounterId(),
        ]);
      } else if ($count > 1) {
        // TODO: insert extra state to pick village?
      } else {
        $space = $villages[0];
        $unit->setLocation($space->getId());
        Notifications::placeUnits($player, [$unit], $space, FRENCH);
        if ($space->getControl() === BRITISH && $space->getDefaultControl() !== BRITISH) {
          $space->setControl($space->getDefaultControl());
          Notifications::loseControl(Players::getPlayerForFaction(BRITISH), $space);
        }
      }
    }

    $this->resolveAction(['automatic' => true], true);
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventBritishEncroachment()
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

  public function actPassEventBritishEncroachment()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actEventBritishEncroachment($args)
  {
    self::checkAction('actEventBritishEncroachment');


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
