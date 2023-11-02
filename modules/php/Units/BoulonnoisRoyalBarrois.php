<?php
namespace BayonetsAndTomahawks\Units;

class BoulonnoisRoyalBarrois extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = BOULONNOIS_ROYAL_BARROIS;
    $this->counterText = clienttranslate('Boulonnois & Royal-Barrois');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
