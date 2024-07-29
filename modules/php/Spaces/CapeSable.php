<?php
namespace BayonetsAndTomahawks\Spaces;

class CapeSable extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CAPE_SABLE;
    $this->battlePriority = 71;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Cape Sable');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 738;
    $this->left = 1092;
    $this->adjacentSpaces = [
      ANNAPOLIS_ROYAL => ANNAPOLIS_ROYAL_CAPE_SABLE,
      HALIFAX => CAPE_SABLE_HALIFAX,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
