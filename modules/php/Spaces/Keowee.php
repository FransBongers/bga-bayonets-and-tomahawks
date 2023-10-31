<?php
namespace BayonetsAndTomahawks\Spaces;

class Keowee extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = KEOWEE;
    $this->battlePriority = 293;
    $this->defaultControl = INDIAN;
    $this->name = clienttranslate('Keowee');
    $this->victorySpace = false;
  }
}
