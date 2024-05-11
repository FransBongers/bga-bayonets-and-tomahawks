<?php
namespace BayonetsAndTomahawks\Spaces;

class Diiohage extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DIIOHAGE;
    $this->battlePriority = 251;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Diiohage');
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 2003;
    $this->left = 494;
    $this->adjacentSpaces = [
      FORKS_OF_THE_OHIO => DIIOHAGE_FORKS_OF_THE_OHIO,
      LA_PRESQU_ISLE => DIIOHAGE_LA_PRESQU_ISLE,
      LE_BARIL => DIIOHAGE_LE_BARIL,
      LE_DETROIT => DIIOHAGE_LE_DETROIT,
    ];
  }
}
