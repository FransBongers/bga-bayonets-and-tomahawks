<?php
namespace BayonetsAndTomahawks\Spaces;

class LakeGeorge extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LAKE_GEORGE;
    $this->battlePriority = 152;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('LAKE GEORGE');
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 1375.5;
    $this-> left = 651;
    $this->adjacentSpaces = [
      ALBANY => ALBANY_LAKE_GEORGE,
      MIKAZAWITEGOK => LAKE_GEORGE_MIKAZAWITEGOK,
      ONEIDA_LAKE => LAKE_GEORGE_ONEIDA_LAKE,
      SACHENDAGA => LAKE_GEORGE_SACHENDAGA,
      TICONDEROGA => LAKE_GEORGE_TICONDEROGA,
    ];
  }
}
