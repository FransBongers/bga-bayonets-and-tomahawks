<?php
namespace BayonetsAndTomahawks\Spaces;

class PortLaJoye extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = PORT_LA_JOYE;
    $this->battlePriority = 22;
    $this->defaultControl = NEUTRAL;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('Port la Joye');
    $this->value = 2;
    $this->victorySpace = false;
    $this->top = 475;
    $this->left = 911.5;
    $this->adjacentSpaces = [
      CHIGNECTOU => CHIGNECTOU_PORT_LA_JOYE,
      LOUISBOURG => LOUISBOURG_PORT_LA_JOYE,
      MIRAMICHY => MIRAMICHY_PORT_LA_JOYE,
    ];
  }
}
