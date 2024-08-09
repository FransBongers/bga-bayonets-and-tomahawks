<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Managers\Players;

class Card19 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card19';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => ARMY_AP
      ],
      [
        'id' => ARMY_AP
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
    $this->faction = BRITISH;
    $this->initiativeValue = 1;
    $this->years = [1758, 1759];
  }

  public function resolveARStart($ctx)
  {
    $frenchPlayer = Players::getPlayerForFaction(FRENCH);
    $frenchVP = $frenchPlayer->getScore();
    if ($frenchVP >= 3) {
      Notifications::message(clienttranslate('French VPs are 3 or greater: event does not trigger.'));
      return;
    }
    if (Globals::getControlIroquois() === NEUTRAL) {
      GameMap::performIndianNationControlProcedure(IROQUOIS, BRITISH);
    } else {
      Notifications::message('${player_name} loses 2 Raid Points', [
        'player' => $frenchPlayer,
      ]);
      GameMap::awardRaidPoints($frenchPlayer, FRENCH, -2);
    }
  }
}
