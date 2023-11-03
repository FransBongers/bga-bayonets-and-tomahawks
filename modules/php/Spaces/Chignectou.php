<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Core\Notifications;

class Chignectou extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU;
    $this->battlePriority = 33;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('CHIGNECTOU');
    $this->victorySpace = true;
    $this->top = 570;
    $this-> left = 891;
  }
}
