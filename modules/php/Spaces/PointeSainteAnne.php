<?php
namespace BayonetsAndTomahawks\Spaces;

class PointeSainteAnne extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = POINTE_SAINTE_ANNE;
    $this->battlePriority = 52;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Pointe Sainte Anne');
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
