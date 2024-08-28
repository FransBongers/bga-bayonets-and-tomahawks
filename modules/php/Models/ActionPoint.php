<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\AtomicActions;

class ActionPoint implements \JsonSerializable
{
  protected $id;
  protected $name;
  protected $actionsAllowed = [];
  protected $movementMultiplier = 1;

  public function __construct()
  {
  }

  protected $attributes = [
    'id' => ['id', 'str'],
    'name' => ['name', 'str'],
  ];


  public function getId()
  {
    return $this->id;
  }

  public function canActivateStackInSpace($space, $player)
  {
    // Notifications::log('canActivateStackInSpace', [
    //   'space' => $space,
    //   'player' => $player,
    // ]);

    // return;
    $actions = [];

    $units = $space->getUnits();
    $playerFaction = $player->getFaction();
    // Get players units
    // To check: perhaps move to Unit model?
    $units = Utils::filter($units, function ($unit) use ($playerFaction) {
      $unitFaction = $unit->getFaction();
      if (($this->id === INDIAN_AP || $this->id === INDIAN_AP_2X) && !$unit->isIndian()) {
        return false;
      }
      if ($unit->isIndian() && Globals::getNoIndianUnitMayBeActivated()) {
        return false;
      }

      return $playerFaction === $unitFaction;
    });




    foreach ($this->actionsAllowed as $actionId) {
      // Notifications::log('actionId', $actionId);
      $action = AtomicActions::get($actionId);
      if ($action->canBePerformedBy($units,  $space, $this, $playerFaction)) {
        $actions[] = $action->getUiData();
      }
    }

    // $hasUnitToActivate = Utils::array_some($units, function ($unit) {
    //   return $unit->getFaction() === INDIAN;
    // });
    return $actions;
  }

  // public function getActionsAllowed()
  // {
  //   $actions = array_map(function ($actionId) {
  //     return AtomicActions::get($actionId);
  //   }, $this->actionsAllowed);
  //   return $actions;
  // }

  public function getName()
  {
    return $this->name;
  }

  /**
   * Return an array of attributes
   */
  public function jsonSerialize()
  {
    $data = [];
    foreach ($this->attributes as $attribute => $field) {
      $data[$attribute] = $this->$attribute;
    }

    return $data;
  }
}
