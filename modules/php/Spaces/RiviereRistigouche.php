<?php
namespace BayonetsAndTomahawks\Spaces;

class RiviereRistigouche extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = RIVIERE_RISTIGOUCHE;
    $this->battlePriority = 23;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Rivière Ristigouche');
    $this->victorySpace = false;
  }
}
