<?php
namespace BayonetsAndTomahawks\Spaces;

class GrandSault extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GRAND_SAULT;
    $this->battlePriority = 51;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Grand Sault');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 645;
    $this-> left = 650;
    $this->adjacentSpaces = [
      POINTE_SAINTE_ANNE => GRAND_SAULT_POINTE_SAINTE_ANNE,
      WOLASTOKUK => GRAND_SAULT_WOLASTOKUK,
      MIRAMICHY => GRAND_SAULT_MIRAMICHY,
      MATAWASKIYAK => GRAND_SAULT_MATAWASKIYAK,
    ];
  }
}
