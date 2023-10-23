<?php
namespace BayonetsAndTomahawks\Spaces;

class Louisbourg extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOUISBOURG;
    $this->location = LOUISBOURG;
    $this->control = isset($row['control']) ? $row['control'] : FRENCH;
    $this->name = clienttranslate('Louisbourg');
    $this->isVictorySpace = true;
  }
}
