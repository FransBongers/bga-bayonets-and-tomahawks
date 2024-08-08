<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Managers\Players;

class Card34 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card34';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => ARMY_AP_2X
      ]
    ];
    $this->event = [
      'id' => DISEASE_IN_BRITISH_CAMP,
      'title' => clienttranslate('Disease in British Camp'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 3;
  }

  public function resolveARStart($ctx)
  {
    $ctx->insertAsBrother(new LeafNode([
      'action' => EVENT_DISEASE_IN_BRITISH_CAMP,
      'cardId' => $this->getId(),
      'playerId' => Players::getPlayerForFaction(BRITISH)->getId(),
    ]));
  }
}
