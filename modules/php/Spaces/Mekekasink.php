<?php
namespace BayonetsAndTomahawks\Spaces;

class Mekekasink extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MEKEKASINK;
    $this->battlePriority = 263;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Mekekasink');
    $this->victorySpace = false;
    $this->top = 2077;
    $this->left = 806;
    $this->adjacentSpaces = [
      FORKS_OF_THE_OHIO => FORKS_OF_THE_OHIO_MEKEKASINK,
      RAYS_TOWN => MEKEKASINK_RAYS_TOWN,
      WILLS_CREEK => MEKEKASINK_WILLS_CREEK,
    ];
  }
}
