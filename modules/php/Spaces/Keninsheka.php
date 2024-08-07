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
    $this->top = 2161;
    $this->left = 759;
    $this->adjacentSpaces = [
      CHOTE => CHOTE_KENINSHEKA,
      TU_ENDIE_WEI => KENINSHEKA_TU_ENDIE_WEI,
    ];
  }
}
