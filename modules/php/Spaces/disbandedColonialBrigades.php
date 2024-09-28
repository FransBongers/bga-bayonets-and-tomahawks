<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Helpers\Locations;

class disbandedColonialBrigades extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = DISBANDED_COLONIAL_BRIGADES;
    $this->isSpaceOnMap = false;
    $this->name = clienttranslate('Disbanded Colonial Brigades Box');
  }
}
