<?php
namespace BayonetsAndTomahawks\Spaces;

class QuebecBastion2 extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = QUEBEC_BASTION_2;
    $this->battlePriority = 81;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('QUÉBEC Bastion');
    $this->victorySpace = false;
    $this->top = 863;
    $this->left = 362.5;
  }
}
