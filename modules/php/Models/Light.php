<?php
namespace BayonetsAndTomahawks\Models;

class Light extends AbstractUnit
{

  public function __construct($row)
  {
    $this->type = LIGHT;
    parent::__construct($row);
    $this->stackOrder = 8;
    $this->mpLimit = 3;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY, PATH];
  }
}
