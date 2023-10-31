<?php
namespace BayonetsAndTomahawks\Spaces;

class Northfield extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NORTHFIELD;
    $this->battlePriority = 141;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('NORTHFIELD');
    $this->victorySpace = true;
  }
}
