<?php
namespace BayonetsAndTomahawks\Models;
// use M44\Board;

class BastionModel extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = BASTION_UNIT_TYPE;
    parent::__construct($row);
  }

}
