<?php
namespace BayonetsAndTomahawks\Models;

class BastionModel extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = BASTION_UNIT_TYPE;
    parent::__construct($row);
  }

}
