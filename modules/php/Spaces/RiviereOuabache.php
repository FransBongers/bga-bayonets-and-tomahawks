<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereOuabache extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_OUABACHE;
    $this->battlePriority = 302;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Rivière Ouabache');
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
