<?php
namespace BayonetsAndTomahawks\Spaces;

class StGeorge extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ST_GEORGE;
    $this->battlePriority = 82;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('St. George');
    $this->victorySpace = false;
  }
}
