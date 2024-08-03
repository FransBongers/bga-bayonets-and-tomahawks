<?php
namespace BayonetsAndTomahawks\Spaces;

class PortLaJoye extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = PORT_LA_JOYE;
    $this->battlePriority = 22;
    $this->colony = ACADIE;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->militia = 1;
    $this->name = clienttranslate('Port la Joye');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 475;
    $this->left = 911.5;
    $this->adjacentSpaces = [
      CHIGNECTOU => CHIGNECTOU_PORT_LA_JOYE,
      LOUISBOURG => LOUISBOURG_PORT_LA_JOYE,
      MIRAMICHY => MIRAMICHY_PORT_LA_JOYE,
    ];
    $this->adjacentSeaZones = [GULF_OF_SAINT_LAWRENCE];
    $this->coastal = true;
  }
}
