<?php
namespace BayonetsAndTomahawks\Spaces;

class ForksOfTheOhio extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORKS_OF_THE_OHIO;
    $this->battlePriority = 262;
    $this->defaultControl = FRENCH;
    $this->name = clienttranslate('FORKS OF THE OHIO');
    $this->victorySpace = true;
  }
}
