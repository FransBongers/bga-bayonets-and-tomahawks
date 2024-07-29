<?php
namespace BayonetsAndTomahawks\Spaces;

class Tadoussac extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TADOUSSAC;
    $this->battlePriority = 42;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Tadoussac');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 634;
    $this->left = 280.5;
    $this->adjacentSpaces = [
      COTE_DE_BEAUPRE => COTE_DE_BEAUPRE_TADOUSSAC,
    ];
    $this->adjacentSeaZones = [GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }
}
