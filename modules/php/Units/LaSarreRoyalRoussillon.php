<?php
namespace BayonetsAndTomahawks\Units;

class LaSarreRoyalRoussillon extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LA_SARRE_ROYAL_ROUSSILLON;
    $this->counterText = clienttranslate('La Sarre & Royal-Roussillon');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
