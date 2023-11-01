<?php
namespace BayonetsAndTomahawks\Units;

class NewEngland extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = NEW_ENGLAND;
    $this->counterText = clienttranslate('New England');
    $this->faction = BRITISH;
  }
}
