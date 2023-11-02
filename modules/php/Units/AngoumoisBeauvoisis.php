<?php
namespace BayonetsAndTomahawks\Units;

class AngoumoisBeauvoisis extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = ANGOUMOIS_BEAUVOISIS;
    $this->counterText = clienttranslate('Angoumois & Beauvoisis');
    $this->faction = FRENCH;
    $this->metropolitan = true;
  }
}
