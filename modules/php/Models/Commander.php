<?php
namespace BayonetsAndTomahawks\Models;
// use M44\Board;

class Commander extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = COMMANDER;
    parent::__construct($row);
  }

}
