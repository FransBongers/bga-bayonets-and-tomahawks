<?php
namespace BayonetsAndTomahawks\Models;
// use M44\Board;

class Fort extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = FORT;
    parent::__construct($row);
  }

}
