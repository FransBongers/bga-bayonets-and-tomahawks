<?php
namespace BayonetsAndTomahawks\Spaces;

class Oswego extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = OSWEGO;
    $this->battlePriority = 172;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('OSWEGO');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 1541;
    $this->left = 473.5;
    $this->adjacentSpaces = [
      BAYE_DE_CATARACOUY => BAYE_DE_CATARACOUY_OSWEGO,
      KAHUAHGO => KAHUAHGO_OSWEGO,
      ONEIDA_LAKE => ONEIDA_LAKE_OSWEGO,
      ONONTAKE => ONONTAKE_OSWEGO,
      ONYIUDAONDAGWAT => ONYIUDAONDAGWAT_OSWEGO,
    ];
  }
}
