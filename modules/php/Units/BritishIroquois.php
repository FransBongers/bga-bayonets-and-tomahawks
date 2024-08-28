<?php

namespace BayonetsAndTomahawks\Units;

use BayonetsAndTomahawks\Core\Globals;

class BritishIroquois extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BRITISH_IROQUOIS;
    $this->counterText = clienttranslate('IROQUOIS');
    $this->faction = BRITISH;
    $this->indian = true;
    $this->villages = [ONONTAKE, OQUAGA, KAHNISTIOH];
  }
}
