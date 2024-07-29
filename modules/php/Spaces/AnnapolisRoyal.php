<?php
namespace BayonetsAndTomahawks\Spaces;

class AnnapolisRoyal extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ANNAPOLIS_ROYAL;
    $this->battlePriority = 53;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('ANNAPOLIS ROYAL');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 664;
    $this->left = 972.5;
    $this->adjacentSpaces = [
      CAPE_SABLE => ANNAPOLIS_ROYAL_CAPE_SABLE,
      CHIGNECTOU => ANNAPOLIS_ROYAL_CHIGNECTOU,
      HALIFAX => ANNAPOLIS_ROYAL_HALIFAX,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
