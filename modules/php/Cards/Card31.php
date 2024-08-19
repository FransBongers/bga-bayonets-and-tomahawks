<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\AtomicActions;
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
    $action = AtomicActions::get(EVENT_ROUND_UP_MEN_AND_EQUIPMENT);
    $options = $action->getOptions(FRENCH);
    if (count($options['reduced']) + count($options['lossesBox']) === 0) {
      Notifications::message('No Reduced units to flip to Full or place from the Losses Box');
      return;
    }

    $ctx->insertAsBrother(new LeafNode([
      'action' => EVENT_ROUND_UP_MEN_AND_EQUIPMENT,
      'cardId' => $this->getId(),
      'faction' => FRENCH,
      'playerId' => Players::getPlayerForFaction(FRENCH)->getId(),
    ]));
  }
}
