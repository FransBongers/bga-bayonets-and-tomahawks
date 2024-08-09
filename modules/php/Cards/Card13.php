<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Managers\Players;

class Card13 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card13';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP_2X
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => CHEROKEE_DIPLOMACY,
      'title' => clienttranslate('Cherokee Diplomacy'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 3;
  }

  public function resolveARStart($ctx)
  {
    $frenchPlayer = Players::getPlayerForFaction(FRENCH);
    $frenchVP = $frenchPlayer->getScore();
    if ($frenchVP >= 3) {
      Notifications::message(clienttranslate('French VPs are 3 or greater: event does not trigger.'));
      return;
    }
    if (Globals::getControlCherokee() === NEUTRAL) {
      GameMap::performIndianNationControlProcedure(CHEROKEE, BRITISH);
    } else {
      Notifications::message('${player_name} loses 2 Raid Points', [
        'player' => $frenchPlayer,
      ]);
      GameMap::awardRaidPoints($frenchPlayer, FRENCH, -2);
    }
  }
}
