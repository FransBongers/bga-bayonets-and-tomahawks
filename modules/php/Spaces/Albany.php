<?php
namespace BayonetsAndTomahawks\Spaces;

class Albany extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ALBANY;
    $this->battlePriority = 161;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('ALBANY');
    $this->value = 3;
    $this->victorySpace = true;
    $this->top = 1440;
    $this->left = 787;
    $this->adjacentSpaces = [
      KINGSTON => ALBANY_KINGSTON,
      LAKE_GEORGE => ALBANY_LAKE_GEORGE,
      NORTHFIELD => ALBANY_NORTHFIELD,
      ONEIDA_LAKE => ALBANY_ONEIDA_LAKE,
      OQUAGA => ALBANY_OQUAGA
    ];
  }
}
