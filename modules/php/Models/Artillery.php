<?php
namespace BayonetsAndTomahawks\Models;
// use M44\Board;

class Artillery extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = ARTILLERY;
    parent::__construct($row);
  }

}
