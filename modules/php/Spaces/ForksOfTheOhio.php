<?php
namespace BayonetsAndTomahawks\Spaces;

class ForksOfTheOhio extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = FORKS_OF_THE_OHIO;
    $this->battlePriority = 262;
    $this->colony = PAYS_D_EN_HAUT;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->indianVillage = CHAOUANON;
    $this->name = clienttranslate('FORKS OF THE OHIO');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 2084;
    $this->left = 677;
    $this->adjacentSpaces = [
      DIIOHAGE => DIIOHAGE_FORKS_OF_THE_OHIO,
      KITHANINK => FORKS_OF_THE_OHIO_KITHANINK,
      LOYALHANNA => FORKS_OF_THE_OHIO_LOYALHANNA,
      MEKEKASINK => FORKS_OF_THE_OHIO_MEKEKASINK,
      TU_ENDIE_WEI => FORKS_OF_THE_OHIO_TU_ENDIE_WEI,
    ];
  }
}
