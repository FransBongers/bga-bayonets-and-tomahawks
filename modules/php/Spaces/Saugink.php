<?php
namespace BayonetsAndTomahawks\Spaces;

class Saugink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SAUGINK;
    $this->battlePriority = 231;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Saugink');
    $this->victorySpace = false;
    $this->top = 1894;
    $this->left = 78.5;
    $this->adjacentSpaces = [
      LE_DETROIT => LE_DETROIT_SAUGINK,
      MATSCHEDASH => MATSCHEDASH_SAUGINK,
    ];
  }
}
