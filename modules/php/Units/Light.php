<?php
namespace BayonetsAndTomahawks\Units;
// use M44\Board;

class Light extends AbstractUnit
{
  public function __construct($row)
  {
    $this->type = LIGHT;
    parent::__construct($row);
  }

}
