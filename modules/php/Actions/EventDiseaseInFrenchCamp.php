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
use BayonetsAndTomahawks\Models\Player;

class EventDiseaseInFrenchCamp extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_DISEASE_IN_FRENCH_CAMP;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventDiseaseInFrenchCamp()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventDiseaseInFrenchCamp()
  {

    // Notifications::log('argsEventDiseaseInFrenchCamp',[]);
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

  public function actPassEventDiseaseInFrenchCamp()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actEventDiseaseInFrenchCamp($args)
  {
    self::checkAction('actEventDiseaseInFrenchCamp');
    $unitId = $args['unitId'];

    $options = $this->getOptions();

    $unit = Utils::array_find($options, function ($possibleunit) use ($unitId) {
      return $unitId === $possibleunit->getId();
    });

    if ($unit === null) {
      throw new \feException("ERROR 031");
    }

    $unit->eliminate(self::getPlayer());

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function getDiseaseInBritishCampOptions()
  {
    return [];
  }

  private function getDiseaseInFrenchCampOptions()
  {
    $units = Units::getAll()->toArray();
    $frenchBrigades = Utils::filter($units, function ($unit) {
      return $unit->getFaction() === FRENCH &&
        $unit->isBrigade() &&
        in_array($unit->getLocation(), SPACES);
    });
    $metropolitan = Utils::filter($frenchBrigades, function ($unit) {
      return $unit->isMetropolitanBrigade();
    });
    if (count($metropolitan) > 0) {
      return $metropolitan;
    } else {
      return $frenchBrigades;
    }
  }

  private function getOptions()
  {
    $info = $this->ctx->getInfo();

    $cardId = $info['cardId'];
    $card = Cards::get($cardId);

    $eventId = $card->getEvent()['id'];

    if ($eventId === DISEASE_IN_BRITISH_CAMP) {
      return $this->getDiseaseInBritishCampOptions();
    } else if ($eventId === DISEASE_IN_FRENCH_CAMP) {
      return $this->getDiseaseInFrenchCampOptions();
    }
  }
}
