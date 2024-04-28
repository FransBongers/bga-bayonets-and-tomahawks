<?php
namespace BayonetsAndTomahawks\Spaces;

class Quebec extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = QUEBEC;
    $this->battlePriority = 81;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('QUÉBEC');
    $this->victorySpace = true;
    $this->top = 863.5;
    $this->left = 428.5;
    $this->adjacentSpaces = [
      COTE_DE_BEAUPRE => COTE_DE_BEAUPRE_QUEBEC,
      COTE_DU_SUD => COTE_DU_SUD_QUEBEC,
      JACQUES_CARTIER => JACQUES_CARTIER_QUEBEC,
      LES_TROIS_RIVIERES => LES_TROIS_RIVIERES_QUEBEC,
    ];
  }
}
