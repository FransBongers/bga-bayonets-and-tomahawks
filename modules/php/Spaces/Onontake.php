<?php
namespace BayonetsAndTomahawks\Spaces;

class Onontake extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = ONONTAKE;
    $this->battlePriority = 183;
    $this->defaultControl = INDIAN;
    $this->name = clienttranslate('Onontake');
    $this->victorySpace = false;
  }
}
