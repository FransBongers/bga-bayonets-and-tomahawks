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
use BayonetsAndTomahawks\Managers\Tokens;
use PgSql\Lob;

class Card extends \BayonetsAndTomahawks\Helpers\DB_Model
{
  protected $id;
  protected $table = 'cards';
  protected $primary = 'card_id';
  protected $location;
  protected $state;
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
    'faction',
    'faction',
    'actionPoints',
    'buildUpDeck',
    'years'
  ];

  public function jsonSerialize()
  {
    $data = parent::jsonSerialize();

    return array_merge($data, [
      'faction' => $this->faction,
      'initiativeValue' => $this->initiativeValue,
      'years' => $this->years,
    ]);
  }

  public function getUiData()
  {
    // Notifications::log('getUiData card model', []);
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

  // public function getBuildUpDeck()
  // {
  //   return $this->buildUpDeck;
  // }
}
