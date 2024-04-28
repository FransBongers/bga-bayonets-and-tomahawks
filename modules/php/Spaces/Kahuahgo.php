<?php
namespace BayonetsAndTomahawks\Spaces;

class Kahuahgo extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KAHUAHGO;
    $this->battlePriority = 153;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Kahuahgo');
    $this->victorySpace = false;
    $this->top = 1420;
    $this->left = 494;
    $this->adjacentSpaces = [
      LA_PRESENTATION => KAHUAHGO_LA_PRESENTATION,
      NIHANAWATE => KAHUAHGO_NIHANAWATE,
      ONEIDA_LAKE => KAHUAHGO_ONEIDA_LAKE,
      OSWEGO => KAHUAHGO_OSWEGO,
    ];
  }
}
