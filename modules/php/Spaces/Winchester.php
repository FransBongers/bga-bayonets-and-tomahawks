<?php
namespace BayonetsAndTomahawks\Spaces;

class Winchester extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WINCHESTER;
    $this->battlePriority = 243;
    $this->colony = VIRGINIA_AND_SOUTH;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Winchester');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 2005;
    $this->left = 1016;
    $this->adjacentSpaces = [
      ALEXANDRIA => ALEXANDRIA_WINCHESTER,
      BEVERLEY => BEVERLEY_WINCHESTER,
      CARLISLE => CARLISLE_WINCHESTER,
      WILLS_CREEK => WILLS_CREEK_WINCHESTER,
    ];
  }
}
