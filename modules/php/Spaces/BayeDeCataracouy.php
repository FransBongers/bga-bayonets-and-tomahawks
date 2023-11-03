<?php
namespace BayonetsAndTomahawks\Spaces;

class BayeDeCataracouy extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BAYE_DE_CATARACOUY;
    $this->battlePriority = 162;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('BAYE DE CATARACOUY');
    $this->victorySpace = true;
    $this->top = 1482;
    $this-> left = 338.5;
  }
}
