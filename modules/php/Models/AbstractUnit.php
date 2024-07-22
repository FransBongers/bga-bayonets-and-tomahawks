<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
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
    'spent' => ['spent', 'int'],
    'extraData' => ['extra_data', 'obj'],
  ];

  protected $id = null;
  protected $counterId;
  protected $spent = 0;
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

  /*
   * STATIC DATA
   */
  protected $staticAttributes = ['counterId', 'counterText', 'faction', 'type', 'mpLimit', 'highland', 'metropolitan', 'connectionTypeAllowed'];
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
      'location' => $this->location,
      'previousLocation' => $this->previousLocation,
      'spent' => $this->spent,
      'manager' => UNITS,
      'reduced' => $this->state === 1,
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

  public function getType()
  {
    return $this->type;
  }

  public function isReduced()
  {
    return $this->state === 1;
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
    $this->setState(1);
    Notifications::reduceUnit($player, $this);
  }

  public function eliminate($player)
  {
    $this->setState(0);
    $this->setLocation(Locations::lossesBox($this->getFaction()));
    Notifications::eliminateUnit($player, $this);
  }

  public function applyHit($player = null)
  {
    $player = $player !== null ? $player : Players::getPlayerForFaction($this->getFaction());
    $eliminated = false;

    if ($this->state === 1 || $this->isIndian()) {
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

  public function placeInLosses($player)
  {
    $lossesBox =  Locations::lossesBox($player->getFaction());
    Units::move($this->getId(), $lossesBox);
    $this->location = $lossesBox;
    Notifications::placeUnitInLosses($player, $this);
  }
}
