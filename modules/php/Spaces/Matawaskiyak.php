<?php
namespace BayonetsAndTomahawks\Spaces;

class Matawaskiyak extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MATAWASKIYAK;
    $this->battlePriority = 43;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Matawaskiyak');
    $this->victorySpace = false;
  }
}
