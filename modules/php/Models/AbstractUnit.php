<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;

class AbstractUnit extends \BayonetsAndTomahawks\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'units';
  protected $primary = 'unit_id';
  protected $attributes = [
    'id' => ['unit_id', 'str'],
    'location' => ['unit_location', 'str'],
    'state' => ['unit_state', 'int'],
    'counterId' => ['counter_id', 'str'],
    'previousLocation' => ['previous_location', 'str'],
    'reduced' => ['reduced', 'int'],
    'spent' => ['spent', 'int'],
    'extraData' => ['extra_data', 'obj'],
  ];

  protected $id = null;
  protected $colonial = false;
  protected $colony = null;
  protected $counterId;
  protected $stackOrder = 0;
  protected $spent = 0;
  protected $reduced = 0;
  protected $faction = null;
  protected $previousLocation = null;
  protected $location = null;
  protected $state = null;
  protected $datas = null;
  protected $indian = false;
  protected $mpLimit = 0;
  protected $connectionTypeAllowed = [];
  protected $highland = false;
  protected $metropolitan = false;
  protected $villages = null;
  protected $shape = SQUARE;

  /*
   * STATIC DATA
   */
  protected $staticAttributes = [
    'colony',
    'colonial',
    'counterId',
    'counterText',
    'faction',
    'indian',
    'type',
    'mpLimit',
    'highland',
    'metropolitan',
    'connectionTypeAllowed',
    'stackOrder',
    'villages',
    'shape'
  ];
  protected $type = null;
  protected $counterText = null;

  /*
   * UNIT PROPERTIES
   * - stored in extra datas
   */
  protected $properties = [];


  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
    }
  }

  public function applyPropertiesModifiers()
  {
    $prop = $this->getExtraDatas('properties') ?? [];
    foreach ($prop as $name => $value) {
      $this->$name = $value;
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'counterId' => $this->counterId,
      'faction' => $this->getFaction(),
      'location' => $this->location,
      'previousLocation' => $this->previousLocation,
      'spent' => $this->spent,
      'manager' => UNITS,
      'reduced' => $this->reduced === 1,
      'stackOrder' => $this->stackOrder,
    ];
  }

  public function getStaticUiData()
  {
    $t = array_merge($this->staticAttributes, $this->properties);
    $data = [];
    foreach ($t as $prop) {
      if (isset($this->$prop)) {
        $data[$prop] = $this->$prop;
      }
    }

    return $data;
  }

  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.

  public function getProperty($prop)
  {
    if (!in_array($prop, $this->properties)) {
      throw new \BgaVisibleSystemException('Trying to access a non existing unit property : ' . $prop);
    }

    return $this->$prop ?? null;
  }

  public function getFaction()
  {
    return $this->faction;
  }

  public function getMpLimit()
  {
    return $this->mpLimit;
  }

  public function isIndian()
  {
    return $this->indian;
  }

  public function isNonIndianLight()
  {
    return !$this->indian && $this->type === LIGHT;
  }

  public function isBrigade()
  {
    return $this->type === BRIGADE;
  }

  public function isCommander()
  {
    return $this->type === COMMANDER;
  }

  public function isColonialBrigade()
  {
    return $this->type === BRIGADE && $this->colony !== null;
  }

  public function isColonialLight()
  {
    return $this->type === LIGHT && $this->colonial;
  }

  public function isLight()
  {
    return $this->type === LIGHT;
  }

  public function isHighlandBrigade()
  {
    return $this->type === BRIGADE && $this->highland;
  }

  public function isMetropolitanBrigade()
  {
    return $this->type === BRIGADE && $this->metropolitan;
  }

  public function isMetropolitanNonHighlandBrigade()
  {
    return $this->type === BRIGADE && $this->metropolitan && !$this->highland;
  }

  public function isNonMetropolitanBrigade()
  {
    return $this->type === BRIGADE && !$this->metropolitan && !$this->highland;
  }

  public function isFleet()
  {
    return $this->type === FLEET;
  }

  public function isBastion()
  {
    return $this->type === BASTION;
  }

  public function isFort()
  {
    return $this->type === FORT;
  }

  public function isArtillery()
  {
    return $this->type === ARTILLERY;
  }

  public function isVagariesOfWarToken()
  {
    return $this->type === VAGARIES_OF_WAR;
  }

  public function getType()
  {
    return $this->type;
  }

  public function isReduced()
  {
    return $this->reduced === 1;
  }

  // public function setReduced($value)
  // {
  //   $this->setReduced($value);
  // }

  public function isSpent()
  {
    return $this->spent === 1;
  }

  public function __call($method, $args)
  {
    if (!in_array($method, $this->properties)) {
      return parent::__call($method, $args);
    }

    return $this->getProperty($method);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  public function reduce($player)
  {
    $this->setReduced(1);
    Notifications::flipUnit($player, $this);
  }

  public function eliminate($player)
  {
    $previousLocation = $this->getLocation();
    $this->setReduced(0);
    $this->setLocation(Locations::lossesBox($this->getFaction()));
    Notifications::eliminateUnit($player, $this, $previousLocation);
    GameMap::lastEliminatedUnitCheck($player, $previousLocation, $this->getFaction());
  }

  public function removeFromPlay($player = null)
  {
    $player = $player === null ? Players::get() : $player;
    $previousLocation = $this->getLocation();
    // $this->setReduced(0);
    $this->setLocation(REMOVED_FROM_PLAY);
    // TODO: use Notifications::removeFromPlay?
    Notifications::eliminateUnit($player, $this, $previousLocation);
    GameMap::lastEliminatedUnitCheck($player, $previousLocation, $this->getFaction());
  }

  public function applyHit($player = null)
  {
    $player = $player !== null ? $player : Players::getPlayerForFaction($this->getFaction());
    $eliminated = false;

    if ($this->reduced === 1 || $this->isIndian()) {
      $this->eliminate($player);
      $eliminated = true;
    } else {
      $this->reduce($player);
    }
    return [
      'eliminated' => $eliminated,
      'unit' => $this,
    ];
  }

  public function flipToFull($player)
  {
    $this->setReduced(0);
    Notifications::flipUnit($player, $this);
  }

  public function placeInLosses($player, $faction = null)
  {
    $playerFaction = $player->getFaction();
    $boxFaction = $faction !== null ? $faction : $playerFaction;
    $lossesBox =  Locations::lossesBox($boxFaction);
    $this->setLocation($lossesBox);
    $ownLossesBox = $faction === null || $faction === $playerFaction;
    Notifications::placeUnitInLosses($player, $this, $ownLossesBox);
  }
}
