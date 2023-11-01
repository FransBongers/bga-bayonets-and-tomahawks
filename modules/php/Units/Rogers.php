<?php
namespace BayonetsAndTomahawks\Units;

class Rogers extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ROGERS;
    $this->counterText = clienttranslate('Rogers');
    $this->faction = BRITISH;
    $this->colonial = true;
  }
}
