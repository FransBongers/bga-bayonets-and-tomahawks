<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereOuabache extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_OUABACHE;
    $this->battlePriority = 302;
    $this->colony = PAYS_D_EN_HAUT;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Rivière Ouabache');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 2233;
    $this->left = 432.5;
    $this->adjacentSpaces = [
      FORT_OUIATENON => FORT_OUIATENON_RIVIERE_OUABACHE,
      LE_BARIL => LE_BARIL_RIVIERE_OUABACHE,
      LES_ILLINOIS => LES_ILLINOIS_RIVIERE_OUABACHE,
    ];
  }
}
