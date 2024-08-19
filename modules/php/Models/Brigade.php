<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Managers\WarInEuropeChits;

class Brigade extends AbstractUnit
{
  protected $staticAttributes = ['counterId', 'colony', 'counterText', 'faction', 'highland', 'metropolitan', 'type'];
  protected $highland = false;
  protected $metropolitan = false;
  protected $officerGorget = false;

  public function __construct($row)
  {
    $this->type = BRIGADE;
    parent::__construct($row);
    $this->mpLimit = 2;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY];
  }

  public function applyHit($player = null)
  {
    if ($this->highland) {
      Globals::setActiveBattleHighlandBrigadeHit(true);
    }
    return parent::applyHit($player);
  }

  public function hasOfficerGorget()
  {
    return $this->officerGorget;
  }

  public function eliminate($player)
  {
    parent::eliminate($player);
    if ($this->metropolitan) {
      WarInEuropeChits::drawChit(BTHelpers::getOtherFaction($this->getFaction()));
    }
  }
}
