<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Players;

class Card53 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card53';
    $this->actionPoints = [
      [
        'id' => INDIAN_AP
      ],
    ];
    $this->event = [
      'id' => PENNSYLVANIAS_PEACE_PROMISES,
      'title' => clienttranslate("Pennsylvania's Peace Promises"),
      AR_START => true,
    ];
    $this->faction = INDIAN;
  }

  public function resolveARStart($ctx)
  {
    $action = AtomicActions::get(EVENT_PENNSYLVANIAS_PEACE_PROMISES);

    if (!$action->canBeResolved()) {
      // TODO: message?
      return;
    }

    // TODO: check auto resolve
    $ctx->insertAsBrother(new LeafNode([
      'action' => EVENT_PENNSYLVANIAS_PEACE_PROMISES,
      'cardId' => $this->getId(),
      'playerId' => Players::getPlayerForFaction(BRITISH)->getId(),
    ]));
  }
}
