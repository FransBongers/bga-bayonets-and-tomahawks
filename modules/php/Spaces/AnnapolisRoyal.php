<?php
namespace BayonetsAndTomahawks\Spaces;

class AnnapolisRoyal extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = POINTE_SAINTE_ANNE;
    $this->battlePriority = 53;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('ANNAPOLIS ROYAL');
    $this->victorySpace = true;
  }
}
