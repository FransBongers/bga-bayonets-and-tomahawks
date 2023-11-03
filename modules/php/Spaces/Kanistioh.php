<?php
namespace BayonetsAndTomahawks\Spaces;

class Kanistioh extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KANISTIOH;
    $this->battlePriority = 211;
    $this->defaultControl = INDIAN;
    $this->name = clienttranslate('Kanistioh');
    $this->victorySpace = false;
    $this->top = 1785;
    $this-> left = 530;
  }
}
