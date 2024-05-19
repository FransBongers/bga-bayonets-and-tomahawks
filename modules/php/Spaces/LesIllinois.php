<?php
namespace BayonetsAndTomahawks\Spaces;

class LesIllinois extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LES_ILLINOIS;
    $this->battlePriority = 301;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->militia = 1;
    $this->name = clienttranslate('LES ILLINOIS');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = true;
    $this->top = 2248;
    $this->left = 597;
    $this->adjacentSpaces = [
      RIVIERE_OUABACHE => LES_ILLINOIS_RIVIERE_OUABACHE,
    ];
  }
}
