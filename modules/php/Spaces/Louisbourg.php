<?php
namespace BayonetsAndTomahawks\Spaces;

class Louisbourg extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG;
    $this->battlePriority = 13;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('LOUISBOURG');
    $this->value = 3;
    $this->victorySpace = true;
    $this->top = 317;
    $this->left = 1141.5;
    $this->adjacentSpaces = [
      PORT_DAUPHIN => LOUISBOURG_PORT_DAUPHIN,
      PORT_LA_JOYE => LOUISBOURG_PORT_LA_JOYE,
    ];
  }
}
