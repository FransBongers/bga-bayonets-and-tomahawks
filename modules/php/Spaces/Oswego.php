<?php
namespace BayonetsAndTomahawks\Spaces;

class Oswego extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = OSWEGO;
    $this->battlePriority = 172;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('OSWEGO');
    $this->victorySpace = true;
  }
}
