<?php
namespace BayonetsAndTomahawks\Spaces;

class Boston extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BOSTON;
    $this->battlePriority = 999;
    $this->colony = NEW_ENGLAND;
    $this->britishBase = true;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Boston');
    $this->victorySpace = false;
    $this->top = 1174;
    $this->left = 1096;
    $this->adjacentSpaces = [
      NEW_LONDON => BOSTON_NEW_LONDON,
      NORTHFIELD => BOSTON_NORTHFIELD,
      YORK => BOSTON_YORK,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
