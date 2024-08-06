<?php
namespace BayonetsAndTomahawks\Spaces;

class Oquaga extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = OQUAGA;
    $this->battlePriority = 181;
    $this->defaultControl = INDIAN;
    $this->indianVillage = IROQUOIS;
    $this->name = clienttranslate('Oquaga');
    $this->victorySpace = false;
    $this->top = 1626;
    $this->left = 700;
    $this->adjacentSpaces = [
      ALBANY => ALBANY_OQUAGA,
      CAWICHNOWANE => CAWICHNOWANE_OQUAGA,
      GNADENHUTTEN => GNADENHUTTEN_OQUAGA,
      KINGSTON => KINGSTON_OQUAGA,
      MINISINK => MINISINK_OQUAGA,
      ONEIDA_LAKE => ONEIDA_LAKE_OQUAGA,
      ONONTAKE => ONONTAKE_OQUAGA,
    ];
  }
}
