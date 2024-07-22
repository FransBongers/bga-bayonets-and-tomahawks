<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Models\Player;

class Battle extends \BayonetsAndTomahawks\Models\AtomicAction
{
  protected $factionBattleMarkerMap = [
    BRITISH => BRITISH_BATTLE_MARKER,
    FRENCH => FRENCH_BATTLE_MARKER
  ];

  protected function getBattleSpaceId()
  {
    $parentInfo = $this->ctx->getParent()->getParent()->getInfo();
    return $parentInfo['spaceId'];
  }

  protected function getBattleSpace()
  {
    return Spaces::get($this->getBattleSpaceId());
  }

  protected function getEnemyFaction($playerFaction)
  {
    return $playerFaction === BRITISH ? FRENCH : BRITISH;
  }

  protected function getBattleMarker($faction)
  {
    return Markers::get($this->factionBattleMarkerMap[$faction]);
  }

  protected function getBattleMarkerValue($marker)
  {
    $location = $marker->getLocation();
    $split = explode('_', $location);

    $value = intval($split[4]);
    if ($split[3] === 'minus') {
      $value = $value * -1;
    }
    if ($marker->getState() > 0) {
      $value += 10 * $marker->getState();
    }
    return $value;
  }

  protected function advanceBattleVictoryMarker($player, $faction, $positions = 1)
  {
    $marker = $this->getBattleMarker($faction);
    $value = $this->getBattleMarkerValue($marker);
    
    $value += $positions;
    if ($value > 10) {
      $marker->setState(floor($value / 10));
    }
    $isAttacker = explode('_',$marker->getLocation())[2] === 'attacker';

    $marker->setLocation(Locations::battleTrack($isAttacker, $value));
    Notifications::advanceBattleVictoryMarker($player, $marker, $positions);
    return $value;
  }
}
