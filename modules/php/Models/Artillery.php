<?php
namespace BayonetsAndTomahawks\Models;

class Artillery extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = ARTILLERY;
    parent::__construct($row);
    $this->stackOrder = 3;
    $this->mpLimit = 2;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY];
  }

}
