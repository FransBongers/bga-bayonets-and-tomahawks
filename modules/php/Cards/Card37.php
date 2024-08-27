<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;

class Card37 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COUP_DE_MAIN_CARD_ID;
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP_2X
      ],
    ];
    $this->event = [
      'id' => COUP_DE_MAIN,
      'title' => clienttranslate('Coup de Main'),
      AR_START => false,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
    $this->years = [1755, 1756];
  }

  public function getUseEventArgs()
  {
    return [
      'eventTitle' => $this->event['title'],
      'title' => clienttranslate('${you} may use Coup de Main'),
      'titleOther' => clienttranslate('${actplayer} may use Coup de Main'),
    ];
  }

  public function useEvent($player, $space = null)
  {
    Globals::setUsedEventCount(FRENCH, 1);

    Globals::setActiveBattleCoupDeMain(true);

    BTHelpers::moveBattleVictoryMarker($player, FRENCH, 1);
  }
}
