<?php
namespace BayonetsAndTomahawks\Spaces;

class WillsCreek extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WILLS_CREEK;
    $this->battlePriority = 253;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Wills Creek');
    $this->victorySpace = false;
  }
}
