<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Managers\Players;

class Card18 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card18';
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
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->buildUpDeck = true;
    $this->event = [
      'id' => WINTERING_REAR_ADMIRAL,
      'title' => clienttranslate('Wintering Rear Admiral'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 4;
    $this->years = [1758, 1759];
  }

  public function resolveARStart($ctx)
  {
    $ctx->insertAsBrother(new LeafNode([
      'action' => EVENT_WINTERING_REAR_ADMIRAL,
      'cardId' => $this->getId(),
      'playerId' => Players::getPlayerForFaction(BRITISH)->getId(),
    ]));
  }
}
