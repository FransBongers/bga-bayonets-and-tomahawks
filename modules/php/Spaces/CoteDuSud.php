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
    $this->top = 740;
    $this->left = 459;
    $this->adjacentSpaces = [
      COTE_DE_BEAUPRE => COTE_DE_BEAUPRE_COTE_DU_SUD,
      QUEBEC => COTE_DU_SUD_QUEBEC,
      RIVIERE_DU_LOUP => COTE_DU_SUD_RIVIERE_DU_LOUP,
      WOLASTOKUK => COTE_DU_SUD_WOLASTOKUK,
    ];
  }
}
