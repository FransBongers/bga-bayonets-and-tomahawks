<?php
namespace BayonetsAndTomahawks\Spaces;

class Ticonderoga extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = TICONDEROGA;
    $this->battlePriority = 131;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('TICONDEROGA');
    $this->victorySpace = true;
  }
}
