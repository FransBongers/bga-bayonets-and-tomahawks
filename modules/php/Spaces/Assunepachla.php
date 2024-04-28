<?php
namespace BayonetsAndTomahawks\Spaces;

class Assunepachla extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ASSUNEPACHLA;
    $this->battlePriority = 232;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Assunepachla');
    $this->victorySpace = false;
    $this->top = 1912.5;
    $this->left = 721.5;
    $this->adjacentSpaces = [
      CAWICHNOWANE => ASSUNEPACHLA_CAWICHNOWANE,
      KITHANINK => ASSUNEPACHLA_KITHANINK,
      LOYALHANNA => ASSUNEPACHLA_LOYALHANNA,
      RAYS_TOWN => ASSUNEPACHLA_RAYS_TOWN,
    ];
  }
}
