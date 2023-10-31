<?php
namespace BayonetsAndTomahawks\Spaces;

class CoteDuSud extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DU_SUD;
    $this->battlePriority = 62;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Côte du Sud');
    $this->victorySpace = false;
  }
}
