<?php
namespace BayonetsAndTomahawks\Spaces;

class LeBaril extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_BARIL;
    $this->battlePriority = 273;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Le Baril');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 2134;
    $this->left = 458;
    $this->adjacentSpaces = [
      DIIOHAGE => DIIOHAGE_LE_BARIL,
      RIVIERE_OUABACHE => LE_BARIL_RIVIERE_OUABACHE,
      TU_ENDIE_WEI => LE_BARIL_TU_ENDIE_WEI,
    ];
  }
}
