<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\StackActions;

class ActionPoint implements \JsonSerializable
{
  protected $id;
  protected $name;
  protected $actionsAllowed = [];

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
      if ($this->id === INDIAN_AP && !$unit->isIndian()) {
        return false;
      }
      return $playerFaction === $unitFaction;
    });

    // Notifications::log('units', $units);
    // Notifications::log('actionsAllowed', $this->actionsAllowed);


    foreach ($this->actionsAllowed as $stackActionId) {
      $stackAction = StackActions::get($stackActionId);
      if ($stackAction->canBePerformedBy($units)) {
        $actions[] = $stackAction;
      }
    }

    // $hasUnitToActivate = Utils::array_some($units, function ($unit) {
    //   return $unit->getFaction() === INDIAN;
    // });
    return $actions;
  }

  public function getActionsAllowed()
  {
    $actions = array_map(function ($actionId) {
      return StackActions::get($actionId);
    }, $this->actionsAllowed);
    return $actions;
  }

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
