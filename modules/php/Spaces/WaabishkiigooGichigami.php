<?php
namespace BayonetsAndTomahawks\Spaces;

class WaabishkiigooGichigami extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WAABISHKIIGOO_GICHIGAMI;
    $this->battlePriority = 242;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Waabishkiigoo Gichigami');
    $this->victorySpace = false;
    $this->top = 1966;
    $this->left = 311;
    $this->adjacentSpaces = [
      LE_DETROIT => LE_DETROIT_WAABISHKIIGOO_GICHIGAMI,
      NIAGARA => NIAGARA_WAABISHKIIGOO_GICHIGAMI,
    ];
  }
}
