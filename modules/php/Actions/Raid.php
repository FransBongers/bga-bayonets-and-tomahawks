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
    if ($startSpaceId !== $currentSpace->getId()) {
      Notifications::moveUnit($player, $unit, $currentSpace, Spaces::get($startSpaceId));
    }
  }

  function spaceHasEnemyUnits($space, $playerFaction)
  {
    $units = $space->getUnits();
    return Utils::array_some($units, function ($unit) use ($playerFaction) {
      return $unit->getFaction() !== $playerFaction;
    });
  }

  protected function getSpaceWeight($units, $spaceId, $playerFaction)
  {
    // Notifications::log('getSpaceWeight', $spaceId);
    $enemyUnits = Utils::filter($units, function ($unit) use ($spaceId, $playerFaction) {
      return $unit->getLocation() === $spaceId && $unit->getFaction() !== $playerFaction;
    });
    // Notifications::log('enemyUnits', $enemyUnits);
    if (count($enemyUnits) === 0) {
      return 1;
    }
    $hasEnemyLightUnit = Utils::array_some($enemyUnits, function ($unit) {
      return $unit->isLight();
    });
    if ($hasEnemyLightUnit) {
      return 0.333333;
    } else {
      return 0.666666;
    }
  }
}
