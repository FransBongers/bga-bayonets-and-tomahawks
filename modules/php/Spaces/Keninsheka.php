<?php
namespace BayonetsAndTomahawks\Spaces;

class Keninsheka extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KENINSHEKA;
    $this->battlePriority = 281;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Keninsheka');
    $this->victorySpace = false;
  }
}
