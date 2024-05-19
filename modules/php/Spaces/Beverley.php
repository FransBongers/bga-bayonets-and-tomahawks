<?php
namespace BayonetsAndTomahawks\Spaces;

class Beverley extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BEVERLEY;
    $this->battlePriority = 271;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Beverley');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 2106;
    $this->left = 1049;
    $this->adjacentSpaces = [
      CHOTE => BEVERLEY_CHOTE,
      WINCHESTER => BEVERLEY_WINCHESTER,
    ];
  }
}
