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
    $this->top = 2061;
    $this->left = 913;
    $this->adjacentSpaces = [
      MEKEKASINK => MEKEKASINK_WILLS_CREEK,
      WINCHESTER => WILLS_CREEK_WINCHESTER,
    ];
  }
}
