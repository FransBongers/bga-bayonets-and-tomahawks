<?php
namespace BayonetsAndTomahawks\Spaces;

class WaabishkiigooGichigami extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = WAABISHKIIGOO_GICHIGAMI;
    $this->battlePriority = 242;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Waabishkiigoo Gichigami');
    $this->victorySpace = false;
  }
}
