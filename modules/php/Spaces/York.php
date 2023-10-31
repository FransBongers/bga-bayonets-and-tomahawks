<?php
namespace BayonetsAndTomahawks\Spaces;

class York extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = YORK;
    $this->battlePriority = 111;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('YORK');
    $this->victorySpace = true;
  }
}
