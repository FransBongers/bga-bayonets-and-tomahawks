<?php
namespace BayonetsAndTomahawks\Spaces;

class Loyalhanna extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOYALHANNA;
    $this->battlePriority = 252;
    $this->defaultControl = NEUTRAL;
    $this->name = clienttranslate('Loyalhanna');
    $this->victorySpace = false;
  }
}
