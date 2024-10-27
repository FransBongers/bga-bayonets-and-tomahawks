<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Preferences;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Events;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\WarInEuropeChits;

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

    $wieChit = WarInEuropeChits::getTopOf(Locations::wieChitPlaceholder($this->getFaction()));



    return array_merge(
      $data,
      [
        'faction' => $this->getFaction(),
        'hand' => $isCurrentPlayer ? $this->getHand() : [],
        'actionPoints' => $this->getActionPointsInPlay(),
        'wieChit' => [
          'hasChit' => $wieChit !== null,
          'chit' => $isCurrentPlayer || ($wieChit !== null && $wieChit->isRevealed()) ? $wieChit : null,
        ],
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
      return array_merge(Cards::getInLocationOrdered(Locations::hand(BRITISH))->toArray(), Cards::getInLocationOrdered(Locations::selected(BRITISH))->toArray());
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
    // TODO: remove old color
    // Old and updated color. 
    return in_array($color, ["B73E1F", "B73E20"]) ? BRITISH : FRENCH;
  }

  public function discardReserveCard()
  {
    $cards = $this->getHand();
    foreach ($cards as $card) {
      $card->discard();
    }
  }

  public function getActionPointsInPlay()
  {
    $faction = $this->getFaction();
    $actionPoints = [
      $faction => [],
    ];
    
    $cardsInPlay = Cards::getCardsInPlay();
    if ($cardsInPlay[$faction] !== null) {
      $lostAP = BTHelpers::getLostActionPoints($faction);
      $actionPoints[$faction] = BTHelpers::getAvailableActionPoints($lostAP, $cardsInPlay[$faction], $faction === FRENCH ? Globals::getAddedAPFrench() : []);
    }
    if ($faction === FRENCH && $cardsInPlay[INDIAN] !== null) {
      $lostAP = BTHelpers::getLostActionPoints(INDIAN);
      $actionPoints[INDIAN] = BTHelpers::getAvailableActionPoints($lostAP, $cardsInPlay[INDIAN], []);
    } else if ($faction === FRENCH) {
      $actionPoints[INDIAN] = [];
    }

    if (Globals::getFirstPlayerId() === $this->getId()) {
      $actionPoints['reactionActionPointId'] = Globals::getReactionActionPointId();
    }

    return $actionPoints;
  }
}
