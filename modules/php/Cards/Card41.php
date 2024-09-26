<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Managers\Players;

class Card41 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = INDOMITABLE_ABBATIS_CARD_ID;
    $this->actionPoints = [
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => FRENCH_LIGHT_ARMY_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => INDOMITABLE_ABBATIS,
      'title' => clienttranslate('Indomitable Abbatis'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 1;
    $this->years = [1758, 1759];
  }

  public function getUseEventArgs()
  {
    return [
      'eventTitle' => $this->event['title'],
      'title' => clienttranslate('${you} may use Indomitable Abbatis'),
      'titleOther' => clienttranslate('${actplayer} may use Indomitable Abbatis'),
    ];
  }

  public function useEvent($player, $space = null)
  {
    Globals::setUsedEventCount(FRENCH, 1);

    BTHelpers::moveBattleVictoryMarker(Players::getPlayerForFaction(BRITISH), BRITISH, -2);
  }
}
