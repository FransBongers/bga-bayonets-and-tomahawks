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
  }
}
