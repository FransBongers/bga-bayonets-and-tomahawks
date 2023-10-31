<?php
namespace BayonetsAndTomahawks\Spaces;

class Matschedash extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = MATSCHEDASH;
    $this->battlePriority = 212;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Matschedash');
    $this->victorySpace = false;
  }
}
