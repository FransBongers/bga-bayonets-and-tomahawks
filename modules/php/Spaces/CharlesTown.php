<?php
namespace BayonetsAndTomahawks\Spaces;

class CharlesTown extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHARLES_TOWN;
    $this->battlePriority = 283;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('CHARLES TOWN');
    $this->victorySpace = true;
    $this->top = 2211;
    $this->left = 1410.5;
    $this->adjacentSpaces = [
      NINETY_SIX => CHARLES_TOWN_NINETY_SIX,
    ];
  }
}
