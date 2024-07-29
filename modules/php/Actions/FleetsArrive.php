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
use BayonetsAndTomahawks\Models\Player;


class FleetsArrive extends \BayonetsAndTomahawks\Models\AtomicAction
{

  protected $poolReinforcementsMap = [
    POOL_FLEETS => REINFORCEMENTS_FLEETS,
    POOL_BRITISH_METROPOLITAN_VOW => REINFORCEMENTS_BRITISH,
    POOL_FRENCH_METROPOLITAN_VOW => REINFORCEMENTS_FRENCH,
  ];
}
