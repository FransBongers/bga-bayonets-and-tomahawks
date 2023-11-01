<?php
namespace BayonetsAndTomahawks\Spaces;

class Boston extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = BOSTON;
    $this->battlePriority = 999;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('Boston');
    $this->victorySpace = false;
  }
}
