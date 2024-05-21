<?php
namespace BayonetsAndTomahawks\Spaces;

class LouisbourgBastion2 extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG_BASTION_2;
    $this->battlePriority = 13;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('LOUISBOURG Bastion');
    $this->victorySpace = false;
    $this->top = 316;
    $this->left = 1075.5;
  }
}
