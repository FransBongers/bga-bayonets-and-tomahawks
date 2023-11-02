<?php
namespace BayonetsAndTomahawks\Units;

class Bedford extends \BayonetsAndTomahawks\Models\Fort
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BEDFORD;
    $this->counterText = clienttranslate('Bedford');
    $this->faction = BRITISH;
  }
}
