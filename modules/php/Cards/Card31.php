<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Managers\Players;

class Card31 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card31';
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->event = [
      'id' => ROUND_UP_MEN_AND_EQUIPMENT,
      'title' => clienttranslate('Round up Men & Equipment'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 4;
  }

  public function resolveARStart($ctx)
  {
    $ctx->insertAsBrother(new LeafNode([
      'action' => EVENT_ROUND_UP_MEN_AND_EQUIPMENT,
      'cardId' => $this->getId(),
      'faction' => FRENCH,
      'playerId' => Players::getPlayerForFaction(FRENCH)->getId(),
    ]));
  }
}
