<?php
namespace BayonetsAndTomahawks\Spaces;

class Matawaskiyak extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MATAWASKIYAK;
    $this->battlePriority = 43;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Matawaskiyak');
    $this->victorySpace = false;
    $this->top = 638.5;
    $this->left = 493;
    $this->adjacentSpaces = [
      GRAND_SAULT => GRAND_SAULT_MATAWASKIYAK,
      RIVIERE_DU_LOUP => MATAWASKIYAK_RIVIERE_DU_LOUP,
    ];
  }
}
