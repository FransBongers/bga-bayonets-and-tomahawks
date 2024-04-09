<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Preferences;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Events;
use BayonetsAndTomahawks\Managers\Tokens;
use BayonetsAndTomahawks\Managers\Players;

/*
 * Player: all utility functions concerning a player
 */

class Player extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $table = 'player';
  protected $primary = 'player_id';
  protected $attributes = [
    'id' => ['player_id', 'int'],
    'no' => ['player_no', 'int'],
    'avatar' => 'player_avatar',
    'name' => 'player_name',
    'color' => 'player_color',
    'eliminated' => 'player_eliminated',
    'score' => ['player_score', 'int'],
    'scoreAux' => ['player_score_aux', 'int'],
    'zombie' => 'player_zombie',
  ];

  /*
   * Getters
   */
  public function getPref($prefId)
  {
    return Preferences::get($this->id, $prefId);
  }

  public function jsonSerialize($currentPlayerId = null)
  {
    $data = parent::jsonSerialize();
    $isCurrentPlayer = intval($currentPlayerId) == $this->getId();

    return array_merge(
      $data,
      [
        'faction' => $this->getFaction(),
        'hand' => $isCurrentPlayer ? $this->getHand() : [],
      ],
    );
  }

  public function getId()
  {
    return (int) parent::getId();
  }

  public function getHand()
  {
    $faction = $this->getFaction();
    if ($faction === BRITISH) {
      return array_merge(Cards::getInLocationOrdered(Locations::hand(BRITISH))->toArray(),Cards::getInLocationOrdered(Locations::selected(BRITISH))->toArray());
    } else if ($faction === FRENCH) {
      return array_merge(
        Cards::getInLocationOrdered(Locations::hand(FRENCH))->toArray(),
        Cards::getInLocationOrdered(Locations::selected(FRENCH))->toArray(),
        Cards::getInLocationOrdered(Locations::selected(INDIAN))->toArray(),
      );
    }
  }

  // TODO: might set this in PlayerExtra table?
  public function getFaction()
  {
    $color = $this->getColor();
    return $color === "B73E20" ? BRITISH : FRENCH;
  }

  public function discardReserveCard()
  {
    $cards = $this->getHand();
    foreach($cards as $card) {
      $card->discard();
    }
  }
}
