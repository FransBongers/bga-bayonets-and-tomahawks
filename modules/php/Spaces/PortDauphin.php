<?php
namespace BayonetsAndTomahawks\Spaces;

class PortDauphin extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = PORT_DAUPHIN;
    $this->battlePriority = 12;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Port Dauphin');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 240;
    $this->left = 1009.5;
    $this->adjacentSpaces = [
      LOUISBOURG => LOUISBOURG_PORT_DAUPHIN,
    ];
    $this->adjacentSeaZones = [GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }
}
