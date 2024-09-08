<?php
namespace BayonetsAndTomahawks\Spaces;

class Toronto extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TORONTO;
    $this->battlePriority = 191;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->indianVillage = MISSISSAGUE;
    $this->name = clienttranslate('Toronto');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 1647;
    $this->left = 252;
    $this->adjacentSpaces = [
      BAYE_DE_CATARACOUY => BAYE_DE_CATARACOUY_TORONTO,
      NIAGARA => NIAGARA_TORONTO,
      OUENTIRONK => OUENTIRONK_TORONTO,
    ];
  }
}
