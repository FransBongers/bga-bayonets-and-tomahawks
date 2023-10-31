<?php
namespace BayonetsAndTomahawks\Spaces;

class Mozodebinebesek extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOZODEBINEBESEK;
    $this->battlePriority = 74;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Môzodebinebesek');
    $this->victorySpace = false;
  }
}
