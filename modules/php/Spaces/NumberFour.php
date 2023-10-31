<?php
namespace BayonetsAndTomahawks\Spaces;

class NumberFour extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NUMBER_FOUR;
    $this->battlePriority = 123;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Number Four');
    $this->victorySpace = false;
  }
}
