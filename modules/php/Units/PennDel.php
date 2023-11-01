<?php
namespace BayonetsAndTomahawks\Units;

class PennDel extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = PENN_DEL;
    $this->counterText = clienttranslate('Penn. & Del.');
    $this->faction = BRITISH;
  }
}
