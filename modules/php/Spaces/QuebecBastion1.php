<?php
namespace BayonetsAndTomahawks\Spaces;

class QuebecBastion1 extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = QUEBEC_BASTION_1;
    $this->battlePriority = 81;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('QUÉBEC Bastion');
    $this->victorySpace = false;
    $this->top = 863;
    $this->left = 304.5;
  }
}
