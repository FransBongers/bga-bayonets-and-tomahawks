<?php
namespace BayonetsAndTomahawks\Spaces;

class LesTroisRivieres extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_TROIS_RIVIERES;
    $this->battlePriority = 101;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Les Trois Rivières');
    $this->victorySpace = false;
    $this->top = 1009.5;
    $this-> left = 369.5;
  }
}
