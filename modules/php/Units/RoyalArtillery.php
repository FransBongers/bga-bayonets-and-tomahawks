<?php
namespace BayonetsAndTomahawks\Units;

class RoyalArtillery extends \BayonetsAndTomahawks\Models\Artillery
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = CANONNIERS_BOMBARDIERS;
    $this->counterText = clienttranslate('Royal Artillery');
    $this->faction = BRITISH;
  }
}
