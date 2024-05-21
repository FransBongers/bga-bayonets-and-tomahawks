<?php
namespace BayonetsAndTomahawks\Spaces;

class Newfoundland extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEWFOUNDLAND;
    $this->battlePriority = 11;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('NEWFOUNDLAND');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 226;
    $this->left = 1385.5;
  }
}
