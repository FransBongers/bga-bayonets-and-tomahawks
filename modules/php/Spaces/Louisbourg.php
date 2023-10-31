<?php
namespace BayonetsAndTomahawks\Spaces;

class Louisbourg extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG;
    $this->battlePriority = 13;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('LOUISBOURG');
    $this->victorySpace = true;
  }
}