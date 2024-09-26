<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;

class Card20 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card20';
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
      'id' => FRENCH_TRADE_GOODS_DESTROYED,
      'title' => clienttranslate('French Trade Goods Destroyed'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 5;
    $this->years = [1758, 1759];
  }


  public function resolveARStart($ctx)
  {
    $numberOfBritishControlled = count(Utils::filter(Spaces::getMany([BAYE_DE_CATARACOUY, NIAGARA])->toArray(), function ($space) {
      return $space->getControl() === BRITISH;
    }));

    if ($numberOfBritishControlled === 0) {
      Notifications::message(clienttranslate('The French do not lose any Raid points'), []);
      return;
    }

    $frenchPlayer = Players::getPlayerForFaction(FRENCH);
    $lostRaidPoints = $numberOfBritishControlled === 1 ? 3 : 5;
    Notifications::message('${player_name} loses ${number} Raid Points', [
      'player' => $frenchPlayer,
      'number' => $lostRaidPoints,
    ]);
    GameMap::awardRaidPoints($frenchPlayer, FRENCH, -$lostRaidPoints);
  }
}
