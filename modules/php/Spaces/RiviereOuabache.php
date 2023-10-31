<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereOuabache extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_OUABACHE;
    $this->battlePriority = 302;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Rivière Ouabache');
    $this->victorySpace = false;
  }
}
