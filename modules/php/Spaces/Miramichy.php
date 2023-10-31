<?php
namespace BayonetsAndTomahawks\Spaces;

class Miramichy extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MIRAMICHY;
    $this->battlePriority = 31;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('Miramichy');
    $this->victorySpace = true;
  }
}
