<?php
namespace BayonetsAndTomahawks\Spaces;

class Goasek extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = GOASEK;
    $this->battlePriority = 112;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Goasek');
    $this->victorySpace = false;
    $this->top = 1058;
    $this->left = 666;
    $this->adjacentSpaces = [
      MAMHLAWBAGOK => GOASEK_MAMHLAWBAGOK,
      NUMBER_FOUR => GOASEK_NUMBER_FOUR,
      TICONDEROGA => GOASEK_TICONDEROGA,
      ZAWAKWTEGOK => GOASEK_ZAWAKWTEGOK,
    ];
  }
}
