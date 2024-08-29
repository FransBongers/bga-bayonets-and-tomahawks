<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Managers\Units;

class Louisbourg extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG;
    $this->battlePriority = 13;
    $this->colony = ACADIE;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->militia = 2;
    $this->name = clienttranslate('LOUISBOURG');
    $this->settledSpace = true;
    $this->value = 3;
    $this->victorySpace = true;
    $this->top = 317;
    $this->left = 1141.5;
    $this->adjacentSpaces = [
      PORT_DAUPHIN => LOUISBOURG_PORT_DAUPHIN,
      PORT_LA_JOYE => LOUISBOURG_PORT_LA_JOYE,
    ];
    $this->adjacentSeaZones = [ATLANTIC_OCEAN, GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }

  public function hasBastion() 
  {
    return count(Units::getInLocation(LOUISBOURG_BASTION_1)) + count(Units::getInLocation(LOUISBOURG_BASTION_2)) > 0;
  }
}
