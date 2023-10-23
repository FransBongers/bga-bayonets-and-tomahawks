<?php
namespace BayonetsAndTomahawks\Units;

class Langis extends Light
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->class = LANGIS;
    $this->name = clienttranslate('Langis');
    $this->faction = FRENCH;
  }
}
