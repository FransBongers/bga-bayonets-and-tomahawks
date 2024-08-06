<?php
namespace BayonetsAndTomahawks\Spaces;

class LeDetroit extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LE_DETROIT;
    $this->battlePriority = 261;
    $this->colony = PAYS_D_EN_HAUT;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->indianVillage = OUTAOUAIS;
    $this->militia = 1;
    $this->name = clienttranslate('LE DÉTROIT');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = true;
    $this->top = 2087;
    $this->left = 192;
    $this->adjacentSpaces = [
      DIIOHAGE => DIIOHAGE_LE_DETROIT,
      FORT_OUIATENON => FORT_OUIATENON_LE_DETROIT,
      SAUGINK => LE_DETROIT_SAUGINK,
      WAABISHKIIGOO_GICHIGAMI => LE_DETROIT_WAABISHKIIGOO_GICHIGAMI,
    ];
  }
}
