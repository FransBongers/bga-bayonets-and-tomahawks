<?php
namespace BayonetsAndTomahawks\Units;

class CanonniersBombardiers extends \BayonetsAndTomahawks\Models\Artillery
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CANONNIERS_BOMBARDIERS;
    $this->counterText = clienttranslate('Canonniers Bombardiers');
    $this->faction = FRENCH;
  }
}
