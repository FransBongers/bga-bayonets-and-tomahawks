<?php

namespace BayonetsAndTomahawks\StackActions;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;

class LightMovement extends \BayonetsAndTomahawks\Models\StackAction
{
  public function __construct()
  {
    parent::__construct();
    $this->id = LIGHT_MOVEMENT;
    $this->name = clienttranslate("Light Movement");
  }

  public function canBePerformedBy($units)
  {
    $hasLightUnit = Utils::array_some($units, function ($unit) {
      // Notifications::log('unit', $unit);
      $unitType = $unit->getType();
      // Notifications::log('unitType', $unitType);
      return $unitType === LIGHT;
      // TODO: unit may not have moved already?
      // Battle?
    });
    // Notifications::log('LightMovement canBePerformedBy', $hasLightUnit);
    return $hasLightUnit;
  }

  public function getFlow($playerId, $originId)
  {
    return [
      'stackAction' => LIGHT_MOVEMENT,
      'originId' => $originId,
      'children' => [
        [
          'action' => MOVEMENT_SELECT_DESTINATION_AND_UNITS,
          'space' => $originId,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
