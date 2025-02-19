<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\TableauOps;

class Card extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $id;
  protected $table = 'cards';
  protected $primary = 'card_id';
  protected $location;
  protected $state;
  protected $event = null;
  protected $faction;
  protected $initiativeValue = null;
  protected $actionPoints = [];
  protected $buildUpDeck = false;
  protected $years = null;

  protected $attributes = [
    'id' => ['card_id', 'str'],
    'location' => 'card_location',
    'state' => ['card_state', 'int'],
    'extraData' => ['extra_data', 'obj'],

  ];

  protected $staticAttributes = [
    'id',
    'event',
    'faction',
    'actionPoints',
    'buildUpDeck',
    'initiativeValue',
    'years'
  ];

  public function jsonSerialize()
  {
    $data = parent::jsonSerialize();

    return array_merge($data, [
      'actionPoints' => $this->actionPoints,
      'faction' => $this->faction,
      'initiativeValue' => $this->initiativeValue,
      'years' => $this->years,
      'event' => $this->event,
    ]);
  }

  public function getUiData()
  {
    return $this->jsonSerialize(); // Static datas are already in js file
  }

  public function insertAtBottom($location)
  {
    Cards::insertAtBottom($this->getId(), $location);
    $this->location = $location;
  }

  public function insertOnTop($location)
  {
    Cards::insertOnTop($this->getId(), $location);
    $this->location = $location;
  }

  public function discard()
  {
    $owner = $this->getOwner();
    Cards::insertOnTop($this->getId(), DISCARD);
    $this->location = DISCARD;
    if ($owner !== null) {
      Notifications::discardCardFromHand($owner, $this);
    } else {
      Notifications::discardCardInPlay($this);
    }
  }

  /**
   * Right now only used to remove Indian card 
   * with Smallpox Epidemic event
   */
  public function removeFromPlay()
  {
    // $owner = $this->getOwner();
    Cards::insertOnTop($this->getId(), REMOVED_FROM_PLAY);
    $this->location = REMOVED_FROM_PLAY;
    // if ($owner !== null) {
    //   Notifications::discardCardFromHand($owner, $this);
    // } else {
      Notifications::discardCardInPlay($this);
    // }
  }

  // Card is selected to be played this action round
  public function select()
  {
    Cards::move($this->getId(), Locations::selected($this->getFaction()));
  }

  public function getOwner()
  {
    if ($this->location === Locations::hand(FRENCH) || $this->location === Locations::hand(INDIAN)) {
      return Players::getPlayerForFaction(FRENCH);
    };
    if ($this->location === Locations::hand(BRITISH)) {
      return Players::getPlayerForFaction(BRITISH);
    }
    return null;
  }

  public function resolveARStart($ctx)
  {

  }

  public function getUseEventArgs()
  {
    return [];
  }

  public function useEvent($player, $spaceId = null)
  {

  }

  public function getName()
  {
    return clienttranslate('a card');
  }

  // public function getBuildUpDeck()
  // {
  //   return $this->buildUpDeck;
  // }
}
