<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class Raid extends \BayonetsAndTomahawks\Actions\StackAction
{
  protected function returnUnitToStartingSpace($player, $unit, $startSpaceId, $currentSpace)
  {
    $unitId = $unit->getId();
    $unit->setSpent(1);
    Units::move($unitId, $startSpaceId);
    Notifications::moveUnit($player, $unit, $currentSpace, Spaces::get($startSpaceId));
  }

  function spaceHasEnemyUnits($space, $playerFaction)
  {
    $units = $space->getUnits();
    return Utils::array_some($units, function ($unit) use ($playerFaction) {
      return $unit->getFaction() !== $playerFaction;
    });
  }
}
