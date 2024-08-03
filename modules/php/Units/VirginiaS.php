<?php
namespace BayonetsAndTomahawks\Units;

class VirginiaS extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->colony = VIRGINIA_AND_SOUTH;
    $this->counterId = VIRGINIA_S;
    $this->counterText = clienttranslate('Virginia & S.');
    $this->faction = BRITISH;
  }
}
