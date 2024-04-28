<?php
namespace BayonetsAndTomahawks\Spaces;

class Chote extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHOTE;
    $this->battlePriority = 291;
    $this->defaultControl = INDIAN;
    $this->name = clienttranslate('Chote');
    $this->victorySpace = false;
    $this->top = 2203;
    $this->left = 954;
    $this->adjacentSpaces = [
      BEVERLEY => BEVERLEY_CHOTE,
      KENINSHEKA => CHOTE_KENINSHEKA,
      KEOWEE => CHOTE_KEOWEE,
    ];
  }
}
