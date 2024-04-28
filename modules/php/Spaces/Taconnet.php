<?php
namespace BayonetsAndTomahawks\Spaces;

class Taconnet extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TACONNET;
    $this->battlePriority = 91;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Taconnet');
    $this->victorySpace = false;
    $this->top = 884.5;
    $this->left = 792;
    $this->adjacentSpaces = [
      KADESQUIT => KADESQUIT_TACONNET,
      MOLOJOAK => MOLOJOAK_TACONNET,
      ST_GEORGE => ST_GEORGE_TACONNET,
      YORK => TACONNET_YORK,
    ];
  }
}
