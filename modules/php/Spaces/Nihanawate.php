<?php
namespace BayonetsAndTomahawks\Spaces;

class Nihanawate extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NIHANAWATE;
    $this->battlePriority = 142;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Nihanawate');
    $this->victorySpace = false;
    $this->top = 1324.5;
    $this->left = 421;
    $this->adjacentSpaces = [
      KAHUAHGO => KAHUAHGO_NIHANAWATE,
      LA_PRESENTATION => LA_PRESENTATION_NIHANAWATE,
      SACHENDAGA => NIHANAWATE_SACHENDAGA,
      SARANAC => NIHANAWATE_SARANAC,
    ];
  }
}
