<?php
namespace BayonetsAndTomahawks\Spaces;

class FortOuiatenon extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORT_OUIATENON;
    $this->battlePriority = 282;
    $this->colony = PAYS_D_EN_HAUT;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Fort Ouiatenon');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 2174;
    $this->left = 300.5;
    $this->adjacentSpaces = [
      LE_DETROIT => FORT_OUIATENON_LE_DETROIT,
      RIVIERE_OUABACHE => FORT_OUIATENON_RIVIERE_OUABACHE,
    ];
  }
}
