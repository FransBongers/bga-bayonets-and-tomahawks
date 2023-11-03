<?php
namespace BayonetsAndTomahawks\Spaces;

class Carlisle extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE;
    $this->battlePriority = 223;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('CARLISLE');
    $this->victorySpace = true;
    $this->top = 1903;
    $this-> left = 953.5;
  }
}
