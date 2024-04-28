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
    $this->top = 2120;
    $this->left = 571;
    $this->adjacentSpaces = [
      FORKS_OF_THE_OHIO => FORKS_OF_THE_OHIO_TU_ENDIE_WEI,
      KENINSHEKA => KENINSHEKA_TU_ENDIE_WEI,
      LE_BARIL => LE_BARIL_TU_ENDIE_WEI,
    ];
  }
}
