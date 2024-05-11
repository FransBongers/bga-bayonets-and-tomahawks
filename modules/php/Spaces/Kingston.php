<?php
namespace BayonetsAndTomahawks\Spaces;

class Kingston extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KINGSTON;
    $this->battlePriority = 171;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Kingston');
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 1533;
    $this->left = 891.5;
    $this->adjacentSpaces = [
      ALBANY => ALBANY_KINGSTON,
      MINISINK => KINGSTON_MINISINK,
      NEW_YORK => KINGSTON_NEW_YORK,
      OQUAGA => KINGSTON_OQUAGA,
    ];
  }
}
