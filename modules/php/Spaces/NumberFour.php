<?php
namespace BayonetsAndTomahawks\Spaces;

class NumberFour extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NUMBER_FOUR;
    $this->battlePriority = 123;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Number Four');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 1168;
    $this->left = 794;
    $this->adjacentSpaces = [
      GOASEK => GOASEK_NUMBER_FOUR,
      NORTHFIELD => NORTHFIELD_NUMBER_FOUR,
      MIKAZAWITEGOK => MIKAZAWITEGOK_NUMBER_FOUR,
      TICONDEROGA => NUMBER_FOUR_TICONDEROGA,
      ZAWAKWTEGOK => NUMBER_FOUR_ZAWAKWTEGOK,
    ];
  }
}
