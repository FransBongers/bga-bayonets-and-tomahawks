<?php
namespace BayonetsAndTomahawks\Spaces;

class TuEndieWei extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TU_ENDIE_WEI;
    $this->battlePriority = 272;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Tu-Endie-Wei');
    $this->victorySpace = false;
  }
}
