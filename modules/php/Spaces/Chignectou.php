<?php
namespace BayonetsAndTomahawks\Spaces;

use BayonetsAndTomahawks\Core\Notifications;

class Chignectou extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CHIGNECTOU;
    $this->battlePriority = 33;
    $this->defaultControl = FRENCH;
    $this->homeSpace = FRENCH;
    $this->name = clienttranslate('CHIGNECTOU');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = true;
    $this->top = 570;
    $this->left = 891;
    $this->adjacentSpaces = [
      ANNAPOLIS_ROYAL => ANNAPOLIS_ROYAL_CHIGNECTOU,
      HALIFAX => CHIGNECTOU_HALIFAX,
      KWANOSKWAMCOK => CHIGNECTOU_KWANOSKWAMCOK,
      MIRAMICHY => CHIGNECTOU_MIRAMICHY,
      POINTE_SAINTE_ANNE => CHIGNECTOU_POINTE_SAINTE_ANNE,
      PORT_LA_JOYE => CHIGNECTOU_PORT_LA_JOYE,
    ];
  }
}
