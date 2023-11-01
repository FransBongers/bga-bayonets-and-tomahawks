<?php
namespace BayonetsAndTomahawks\Units;

class B61st63rd extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = B_61ST_63RD;
    $this->counterText = clienttranslate('61st & 63rd');
    $this->faction = BRITISH;
    $this->metropolitan = true;
  }
}
