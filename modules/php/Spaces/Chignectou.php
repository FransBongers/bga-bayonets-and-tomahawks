<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Core\Notifications;

class Chignectou extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    Notifications::log('construct in class',$row);
    parent::__construct($row);
    $this->id = CHIGNECTOU;
    $this->location = CHIGNECTOU;
    $this->control = isset($row['control']) ? $row['control'] : FRENCH;
    $this->name = clienttranslate('Chignectou');
    $this->isVictorySpace = true;
  }
}
