<?php
namespace BayonetsAndTomahawks\Models;

class Fort extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FORT;
    parent::__construct($row);
  }

}
