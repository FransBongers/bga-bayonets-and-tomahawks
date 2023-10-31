<?php
namespace BayonetsAndTomahawks\Spaces;

class LesIllinois extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_ILLINOIS;
    $this->battlePriority = 301;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('LES ILLINOIS');
    $this->victorySpace = true;
  }
}
