<?php
namespace BayonetsAndTomahawks\Spaces;

class NewLondon extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEW_LONDON;
    $this->battlePriority = 999;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('New London');
    $this->victorySpace = false;
    $this->top = 1390;
    $this->left = 1100;
    $this->adjacentSpaces = [
      BOSTON => BOSTON_NEW_LONDON,
      NEW_YORK => NEW_LONDON_NEW_YORK,
      NORTHFIELD => NEW_LONDON_NORTHFIELD,
    ];
  }
}
