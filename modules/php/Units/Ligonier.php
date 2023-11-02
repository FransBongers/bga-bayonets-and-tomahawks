<?php
namespace BayonetsAndTomahawks\Units;

class Ligonier extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LIGONIER;
    $this->counterText = clienttranslate('Ligonier');
    $this->faction = BRITISH;
  }
}
