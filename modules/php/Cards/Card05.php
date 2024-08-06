<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Managers\Players;

class Card05 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->id = 'Card05';
    $this->buildUpDeck = true;
    $this->event = [
      'id' => DISEASE_IN_FRENCH_CAMP,
      'title' => clienttranslate('Disease in French Camp'),
      AR_START => true,
    ];
    $this->faction = BRITISH;
    $this->initiativeValue = 6;
  }

  public function resolveARStart($ctx)
  {
    $ctx->insertAsBrother(new LeafNode([
      'action' => EVENT_DISEASE_IN_FRENCH_CAMP,
      'cardId' => $this->getId(),
      'playerId' => Players::getPlayerForFaction(FRENCH)->getId(),
    ]));
  }
}
