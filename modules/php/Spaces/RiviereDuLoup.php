<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereDuLoup extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_DU_LOUP;
    $this->battlePriority = 41;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Rivière du Loup');
    $this->victorySpace = false;
  }
}
