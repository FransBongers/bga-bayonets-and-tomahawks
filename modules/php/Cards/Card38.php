<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Managers\Players;

class Card38 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card38';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => IROQUOIS_DIPLOMACY,
      'title' => clienttranslate('Iroquois Diplomacy'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1755,1756];
  }

  public function resolveARStart($ctx)
  {
    $frenchPlayer = Players::getPlayerForFaction(FRENCH);
    $frenchVP = $frenchPlayer->getScore();
    if ($frenchVP < 4) {
      Notifications::message(clienttranslate('French VPs are not 4 or greater: event does not trigger.'));
      return;
    }
    if (Globals::getControlIroquois() === NEUTRAL) {
      GameMap::performIndianNationControlProcedure(IROQUOIS, FRENCH);
    }
  }
}
