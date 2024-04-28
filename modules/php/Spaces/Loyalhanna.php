<?php
namespace BayonetsAndTomahawks\Spaces;

class Loyalhanna extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOYALHANNA;
    $this->battlePriority = 252;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Loyalhanna');
    $this->victorySpace = false;
    $this->top = 1997.5;
    $this->left = 740.5;
    $this->adjacentSpaces = [
      ASSUNEPACHLA => ASSUNEPACHLA_LOYALHANNA,
      FORKS_OF_THE_OHIO => FORKS_OF_THE_OHIO_LOYALHANNA,
      RAYS_TOWN => LOYALHANNA_RAYS_TOWN,
    ];
  }
}
