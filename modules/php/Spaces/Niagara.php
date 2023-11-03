<?php
namespace BayonetsAndTomahawks\Spaces;

class Niagara extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIAGARA;
    $this->battlePriority = 201;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('NIAGARA');
    $this->victorySpace = true;
    $this->top = 1728.5;
    $this-> left = 384.5;
  }
}
