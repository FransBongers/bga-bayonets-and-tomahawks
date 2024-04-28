<?php
namespace BayonetsAndTomahawks\Spaces;

class OneidaLake extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONEIDA_LAKE;
    $this->battlePriority = 163;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Oneida Lake');
    $this->victorySpace = false;
    $this->top = 1520;
    $this->left = 599.5;
    $this->adjacentSpaces = [
      ALBANY => ALBANY_ONEIDA_LAKE,
      KAHUAHGO => KAHUAHGO_ONEIDA_LAKE,
      LAKE_GEORGE => LAKE_GEORGE_ONEIDA_LAKE,
      OQUAGA => ONEIDA_LAKE_OQUAGA,
      OSWEGO => ONEIDA_LAKE_OSWEGO,
    ];
  }
}
