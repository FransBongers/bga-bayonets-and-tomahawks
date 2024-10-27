<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;

class Card54 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = 'Card54';
    $this->event = [
      'id' => LETS_SEE_HOW_THE_FRENCH_FIGHT,
      'title' => clienttranslate("Let's See How the French Fight"),
      AR_START => true,
      AR_START_SKIP_MESSAGE => true,
    ];
    $this->faction = INDIAN;
  }

  public function resolveARStart($ctx)
  {
    Globals::setNoIndianUnitMayBeActivated(true);
    Globals::setAddedAPFrench([
      [
        'id' => LIGHT_AP
      ]
    ]);
    Notifications::updateActionPoints(FRENCH, [LIGHT_AP], ADD_AP);
  }
}
