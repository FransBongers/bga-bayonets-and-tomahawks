<?php
namespace BayonetsAndTomahawks\Spaces;

class Wolastokuk extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WOLASTOKUK;
    $this->battlePriority = 63;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Wolastokuk');
    $this->victorySpace = false;
    $this->top = 732;
    $this->left = 579;
    $this->adjacentSpaces = [
      COTE_DU_SUD => COTE_DU_SUD_WOLASTOKUK,
      GRAND_SAULT => GRAND_SAULT_WOLASTOKUK,
      MOZODEBINEBESEK => MOZODEBINEBESEK_WOLASTOKUK,
    ];
  }
}
