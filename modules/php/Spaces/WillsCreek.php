<?php
namespace BayonetsAndTomahawks\Spaces;

class WillsCreek extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WILLS_CREEK;
    $this->battlePriority = 253;
    $this->colony = VIRGINIA_AND_SOUTH;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Wills Creek');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 2061;
    $this->left = 913;
    $this->adjacentSpaces = [
      MEKEKASINK => MEKEKASINK_WILLS_CREEK,
      WINCHESTER => WILLS_CREEK_WINCHESTER,
    ];
  }
}
