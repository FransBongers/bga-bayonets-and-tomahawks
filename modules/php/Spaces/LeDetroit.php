<?php
namespace BayonetsAndTomahawks\Spaces;

class LeDetroit extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_DETROIT;
    $this->battlePriority = 261;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('LE DÉTROIT');
    $this->victorySpace = true;
  }
}
