<?php
namespace BayonetsAndTomahawks\Spaces;

class Montreal extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MONTREAL;
    $this->battlePriority = 122;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('MONTRÉAL');
    $this->victorySpace = true;
  }
}
