<?php
namespace BayonetsAndTomahawks\Spaces;

class Winchester extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WINCHESTER;
    $this->battlePriority = 243;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Winchester');
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
