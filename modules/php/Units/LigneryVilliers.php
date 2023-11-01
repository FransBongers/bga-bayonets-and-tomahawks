<?php
namespace BayonetsAndTomahawks\Units;

class LigneryVilliers extends \BayonetsAndTomahawks\Models\Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = LIGNERY_VILIIERS;
    $this->counterText = clienttranslate('Lignery Villiers');
    $this->faction = FRENCH;
  }
}
