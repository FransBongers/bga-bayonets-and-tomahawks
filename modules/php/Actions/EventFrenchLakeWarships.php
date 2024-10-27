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
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class EventFrenchLakeWarships extends \BayonetsAndTomahawks\Models\AtomicAction
{
  private $relevantConnections = [
    BAYE_DE_CATARACOUY_OSWEGO,
    BAYE_DE_CATARACOUY_TORONTO,
    NIAGARA_TORONTO,
    NIAGARA_ONYIUDAONDAGWAT,
    ONYIUDAONDAGWAT_OSWEGO,
    ISLE_AUX_NOIX_TICONDEROGA,
    // LAKE_GEORGE_TICONDEROGA,
  ];

  public function getState()
  {
    return ST_EVENT_FRENCH_LAKE_WARSHIPS;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventFrenchLakeWarships() {}

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

  public function stEventDiseaseInFrenchCamp()
  {
    $result = $this->getOptions();

    if (count($result) === 0) {
      Notifications::message(_('No Highway can be chosen'), []);
      $this->resolveAction(['automatic' => true]);
    }
  }

  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventFrenchLakeWarships()
  {

    return [
      'options' => $this->getOptions()
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

  public function actPassEventFrenchLakeWarships()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actEventFrenchLakeWarships($args)
  {
    self::checkAction('actEventFrenchLakeWarships');
    $connectionId = $args['connectionId'];

    $options = $this->getOptions();

    $connection = Utils::array_find($options, function ($option) use ($connectionId) {
      return $option->getId() === $connectionId;
    });

    if ($connection === null) {
      throw new \feException("ERROR 090");
    }

    Globals::setHighwayUnusableForBritish($connectionId);

    Notifications::frenchLakeWarships(self::getPlayer(), $connection);

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
    $connections = Connections::get($this->relevantConnections)->toArray();
    $spaces = Spaces::getAll();

    $options = [];

    foreach ($connections as $connection) {
      $spaceIds = explode('_', $connection->getId());
      if ($spaces[$spaceIds[0]]->getControl() === BRITISH && $spaces[$spaceIds[1]]->getControl() === BRITISH) {
        continue;
      }
      $options[] = $connection;
    }

    return $options;
  }
}
