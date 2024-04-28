<?php
namespace BayonetsAndTomahawks\Spaces;

class Molojoak extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MOLOJOAK;
    $this->battlePriority = 92;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Molôjoak');
    $this->victorySpace = false;
    $this->top = 890;
    $this->left = 682;
    $this->adjacentSpaces = [
      NAMASKONKIK => MOLOJOAK_NAMASKONKIK,
      MOZODEBINEBESEK => MOLOJOAK_MOZODEBINEBESEK,
      TACONNET => MOLOJOAK_TACONNET,
      ZAWAKWTEGOK => MOLOJOAK_ZAWAKWTEGOK,
    ];
  }
}
