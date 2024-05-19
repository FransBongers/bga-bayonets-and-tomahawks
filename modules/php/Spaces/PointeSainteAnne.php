<?php
namespace BayonetsAndTomahawks\Spaces;

class PointeSainteAnne extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = POINTE_SAINTE_ANNE;
    $this->battlePriority = 52;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Pointe Sainte Anne');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 651;
    $this->left = 770.5;
    $this->adjacentSpaces = [
      CHIGNECTOU => CHIGNECTOU_POINTE_SAINTE_ANNE,
      GRAND_SAULT => GRAND_SAULT_POINTE_SAINTE_ANNE,
      KADESQUIT => KADESQUIT_POINTE_SAINTE_ANNE,
      MIRAMICHY => MIRAMICHY_POINTE_SAINTE_ANNE,
    ];
  }
}
