<?php
namespace BayonetsAndTomahawks\Spaces;

class LaPresquIsle extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LA_PRESQU_ISLE;
    $this->battlePriority = 222;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate("La Presqu'Isle");
    $this->victorySpace = false;
    $this->top = 1874;
    $this->left = 468;
    $this->adjacentSpaces = [
      DIIOHAGE => DIIOHAGE_LA_PRESQU_ISLE,
      GENNISHEYO => GENNISHEYO_LA_PRESQU_ISLE,
      KITHANINK => KITHANINK_LA_PRESQU_ISLE,
      NIAGARA => LA_PRESQU_ISLE_NIAGARA,
    ];
  }
}
