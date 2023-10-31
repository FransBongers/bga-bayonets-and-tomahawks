<?php
namespace BayonetsAndTomahawks\Spaces;

class CharlesTown extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHARLES_TOWN;
    $this->battlePriority = 283;
    $this->defaultControl = BRITISH;
    $this->name = clienttranslate('CHARLES TOWN');
    $this->victorySpace = true;
  }
}
