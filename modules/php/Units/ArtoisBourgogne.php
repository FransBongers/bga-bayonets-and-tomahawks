<?php
namespace BayonetsAndTomahawks\Units;

class ArtoisBourgogne extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ANGOUMOIS_BEAUVOISIS;
    $this->counterText = clienttranslate('Artois & Bourgogne');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
