<?php
namespace BayonetsAndTomahawks\Spaces;

class NewYork extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEW_YORK;
    $this->battlePriority = 999;
    $this->britishBase = true;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('New York');
    $this->victorySpace = false;
    $this->top = 1637;
    $this->left = 1052;
    $this->adjacentSpaces = [
      KINGSTON => KINGSTON_NEW_YORK,
      NEW_LONDON => NEW_LONDON_NEW_YORK,
      PHILADELPHIA => NEW_YORK_PHILADELPHIA,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
