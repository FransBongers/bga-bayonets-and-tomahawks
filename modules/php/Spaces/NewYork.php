<?php
namespace BayonetsAndTomahawks\Spaces;

class NewYork extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = NEW_YORK;
    $this->battlePriority = 999;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('New York');
    $this->victorySpace = false;
  }
}
