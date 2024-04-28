<?php
namespace BayonetsAndTomahawks\Spaces;

class Matschedash extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MATSCHEDASH;
    $this->battlePriority = 212;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Matschedash');
    $this->victorySpace = false;
    $this->top = 1796;
    $this->left = 126;
    $this->adjacentSpaces = [
      OUENTIRONK => MATSCHEDASH_OUENTIRONK,
      SAUGINK => MATSCHEDASH_SAUGINK,
    ];
  }
}
