<?php
namespace BayonetsAndTomahawks\Models;

class Commander extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = COMMANDER;
    parent::__construct($row);
  }

}
