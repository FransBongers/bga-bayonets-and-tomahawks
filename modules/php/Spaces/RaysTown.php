<?php
namespace BayonetsAndTomahawks\Spaces;

class RaysTown extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RAYS_TOWN;
    $this->battlePriority = 241;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('RaysTown');
    $this->victorySpace = false;
    $this->top = 1970.5;
    $this->left = 852;
    $this->adjacentSpaces = [
      ASSUNEPACHLA => ASSUNEPACHLA_RAYS_TOWN,
      CARLISLE => CARLISLE_RAYS_TOWN,
      LOYALHANNA => LOYALHANNA_RAYS_TOWN,
      MEKEKASINK => MEKEKASINK_RAYS_TOWN,
      SHAMOKIN => RAYS_TOWN_SHAMOKIN,
    ];
  }
}
