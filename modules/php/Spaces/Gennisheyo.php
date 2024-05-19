<?php
namespace BayonetsAndTomahawks\Spaces;

class Gennisheyo extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GENNISHEYO;
    $this->battlePriority = 192;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Gennisheyo');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 1680;
    $this->left = 491;
    $this->adjacentSpaces = [
      KAHNISTIOH => GENNISHEYO_KAHNISTIOH,
      LA_PRESQU_ISLE => GENNISHEYO_LA_PRESQU_ISLE,
      NIAGARA => GENNISHEYO_NIAGARA, 
      ONONTAKE => GENNISHEYO_ONONTAKE,
      ONYIUDAONDAGWAT => GENNISHEYO_ONYIUDAONDAGWAT,
    ];
  }
}
