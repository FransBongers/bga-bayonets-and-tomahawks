<?php
namespace BayonetsAndTomahawks\Spaces;

class CharlesTown extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHARLES_TOWN;
    $this->battlePriority = 283;
    $this->colony = VIRGINIA_AND_SOUTH;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 3;
    $this->name = clienttranslate('CHARLES TOWN');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = true;
    $this->top = 2211;
    $this->left = 1410.5;
    $this->adjacentSpaces = [
      NINETY_SIX => CHARLES_TOWN_NINETY_SIX,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN];
    $this->coastal = true;
  }
}
