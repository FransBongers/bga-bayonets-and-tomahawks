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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Marker;
use BayonetsAndTomahawks\Models\Player;
use BayonetsAndTomahawks\Scenario;

class WinterQuartersReturnToColonies extends \BayonetsAndTomahawks\Models\AtomicAction
{

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  protected function canUnitsRemainOnSpace($space, $units, $stackUnits, $stackUnitIds, $faction)
  {
    $data = $this->getUnitsThatCanRemainOnSpace($space, $units, $stackUnits, $faction);

    return $data['maxTotal'] === null || $data['maxTotal'] > 0;
  }

  protected function getUnitsThatCanRemainOnSpace($space, $units, $stackUnits, $faction)
  {
    $stackUnitIds = BTHelpers::returnIds($stackUnits);

    $cannotRemain = [
      'maxBrigades' => 0,
      'maxTotal' => 0,
    ];

    $spaceId = $space->getId();
    $friendlyUnitsOnSpace = Utils::filter($units, function ($unit) use ($faction, $spaceId) {
      return $unit->getLocation() === $spaceId && $unit->getFaction() === $faction;
    });
    $isNonSettledSpaceWithFriendlyFort = !$space->isSettledSpace() && Utils::array_some($friendlyUnitsOnSpace, function ($unit) {
      return $unit->isFort();
    });

    // Can leave 1 unit of there is not already a unit there not belonging to the stack
    if ($isNonSettledSpaceWithFriendlyFort) {

      $numberOfNonStackFriendlyUnitsOnSpace = count(Utils::filter($friendlyUnitsOnSpace, function ($unit) use ($stackUnitIds) {
        return !$unit->isFort() && !in_array($unit->getId(), $stackUnitIds);
      }));

      if ($numberOfNonStackFriendlyUnitsOnSpace === 0) {
        return [
          'maxBrigades' => 1,
          'maxTotal' => 1,
        ];
      } else {
        return $cannotRemain;
      }
    }

    $isFrienlyControlledEnemySettledSpace = $space->isSettledSpace(BTHelpers::getOtherFaction($faction)) && $space->getControl() === $faction;

    if ($isFrienlyControlledEnemySettledSpace) {
      // TODO: check logic for commanders
      $stackHasNonBrigadeUnit = Utils::array_some($stackUnits, function ($unit) {
        return !$unit->isBrigade();
      });
      $otherFriendlyBrigadesOnSpace = Utils::filter($friendlyUnitsOnSpace, function ($unit) use ($stackUnitIds) {
        return !in_array($unit->getId(), $stackUnitIds) && $unit->isBrigade();
      });
      if ($stackHasNonBrigadeUnit || count($otherFriendlyBrigadesOnSpace) < $space->getValue()) {
        $maxBrigades = $space->getValue() - count($otherFriendlyBrigadesOnSpace);
        return [
          'maxBrigades' => max($maxBrigades, 0),
          'maxTotal' => null,
        ];
      } else {
        return $cannotRemain;
      }
    }

    return $cannotRemain;
  }
}
