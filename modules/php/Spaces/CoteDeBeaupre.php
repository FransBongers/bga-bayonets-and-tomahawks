<?php
namespace BayonetsAndTomahawks\Spaces;

class CoteDeBeaupre extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DE_BEAUPRE;
    $this->battlePriority = 61;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Côte de Beaupré');
    $this->victorySpace = false;
    $this->top = 734;
    $this-> left = 360.5;
  }
}
