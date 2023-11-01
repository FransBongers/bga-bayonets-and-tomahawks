<?php
namespace BayonetsAndTomahawks\Models;

class Fleet extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FLEET;
    parent::__construct($row);
  }

}
