<?php
namespace BayonetsAndTomahawks\Spaces;

class LakeGeorge extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LAKE_GEORGE;
    $this->battlePriority = 152;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('LAKE GEORGE');
    $this->victorySpace = true;
    $this->top = 1375.5;
    $this-> left = 651;
  }
}
