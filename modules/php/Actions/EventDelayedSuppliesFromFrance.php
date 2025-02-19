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
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class EventDelayedSuppliesFromFrance extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_DELAYED_SUPPLIES_FROM_FRANCE;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventDelayedSuppliesFromFrance() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventDelayedSuppliesFromFrance()
  {
    $indianCard = Cards::getInLocation(Locations::cardInPlay(INDIAN))->toArray()[0];
    $frenchCard = Cards::getInLocation(Locations::cardInPlay(FRENCH))->toArray()[0];

    $frenchAP = array_merge($frenchCard->getActionPoints(), Globals::getAddedAPFrench());

    return [
      'indianAP' => $indianCard->getActionPoints(),
      'frenchAP' => $frenchAP,
      // 'indianCard' => $indianCard,
      // 'frenchCard' => $frenchCard,
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

  public function actPassEventDelayedSuppliesFromFrance()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actEventDelayedSuppliesFromFrance($args)
  {
    self::checkAction('actEventDelayedSuppliesFromFrance');

    $frenchAP = $args['frenchAP'];
    $indianAP = $args['indianAP'];

    $stateArgs = $this->argsEventDelayedSuppliesFromFrance();

    if (count($stateArgs['indianAP']) > 0 && Utils::array_find($stateArgs['indianAP'], function ($ap) use ($indianAP) {
      return $ap['id'] === $indianAP;
    }) === null) {
      throw new \feException("ERROR 039");
    };

    if (Utils::array_find($stateArgs['frenchAP'], function ($ap) use ($frenchAP) {
      return $ap['id'] === $frenchAP;
    }) === null) {
      throw new \feException("ERROR 040");
    };

    Globals::setLostAPFrench([$frenchAP]);
    Notifications::updateActionPoints(FRENCH, [$frenchAP], REMOVE_AP);
    Globals::setLostAPIndian([$indianAP]);
    Notifications::updateActionPoints(INDIAN, [$indianAP], REMOVE_AP);

    $player = self::getPlayer();

    Notifications::message($indianAP !== null ? clienttranslate('${player_name} loses ${tkn_actionPoint_indian} and ${tkn_actionPoint_french}') : clienttranslate('${player_name} loses ${tkn_actionPoint_french}'), [
      'player' => $player,
      'tkn_actionPoint_indian' => $indianAP !== null ? implode(':', [INDIAN, $indianAP]) : '',
      'tkn_actionPoint_french' => implode(':', [FRENCH, $frenchAP]),
    ]);

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function canBeResolved()
  {
    $spaces = Spaces::getMany([DIIOHAGE, FORKS_OF_THE_OHIO, KITHANINK])->toArray();

    if (Utils::array_some($spaces, function ($space) {
      return $space->getControl() === BRITISH || $space->getRaided() === BRITISH;
    })) {
      return false;
    }
    return true;
  }

  private function getOptions()
  {
    $units = Utils::filter(Units::getAll()->toArray(), function ($unit) {
      return in_array($unit->getCounterId(), [MINGO, DELAWARE, CHAOUANON]) && $unit->getLocation() !== Locations::lossesBox(FRENCH);
    });
    return $units;
  }
}
