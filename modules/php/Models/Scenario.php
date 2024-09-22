<?php

namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Markers;

class Scenario implements \JsonSerializable
{
  protected $id;
  protected $name;
  protected $number;
  protected $startYear;
  protected $duration;
  protected $indianSetup;
  protected $connections;
  protected $locations;
  protected $reinforcements;
  protected $pools;
  protected $victoryMarkerLocation;
  protected $victoryThreshold = [];

  public function __construct() {}

  protected $attributes = [
    'id' => ['id', 'str'],
    'name' => ['name', 'str'],
    'number' => ['number', 'int'],
    'startYear' => ['startYear', 'int'],
    'duration' => ['duration', 'int'],
    'indianSetup' => ['indianSetup', 'obj'],
    'connections' => ['connections', 'obj'],
    'locations' => ['locations', 'obj'],
    'pools' => ['pools', 'obj'],
    'reinforcements' => ['reinforcements', 'obj'],
  ];


  public function getId()
  {
    return $this->id;
  }

  public function getConnections()
  {
    return $this->connections;
  }

  public function getDuration()
  {
    return $this->duration;
  }

  public function getIndianSetup()
  {
    return $this->indianSetup;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getLocations()
  {
    return $this->locations;
  }

  public function getPools()
  {
    return $this->pools;
  }

  public function getReinforcements()
  {
    return $this->reinforcements;
  }

  public function getStartYear()
  {
    return $this->startYear;
  }

  public function getYearEndBonus($faction, $year)
  {
    return 0;
  }

  public function getVictoryMarkerLocation()
  {
    return $this->victoryMarkerLocation;
  }

  public function hasAchievedVictoryThreshold($faction, $year)
  {
    $vpMarker = Markers::get(VICTORY_MARKER);
    // 'victory_points_' . $faction . '_' . $score;
    $splitLocation = explode('_', $vpMarker->getLocation());

    $threshold = $this->victoryThreshold[$faction][$year];
    $sideOfTrackMarkerIsOn = $splitLocation[2];
    $markerPosition = intval($splitLocation[3]) + 10 * $vpMarker->getSide();
    if ($threshold > 0 && $sideOfTrackMarkerIsOn === $faction && $markerPosition >= $threshold) {
      return true;
    } else if ($threshold < 0 && ($sideOfTrackMarkerIsOn === $faction || $markerPosition <= abs($threshold))) {
      // When threshold < 0, the faction wins if the marker is either on their side of the track or on the opponents track
      // on a position smaller than the threshold.
      return true;
    }
    return false;
  }

  /**
   * Return an array of attributes
   */
  public function jsonSerialize()
  {
    $data = [
      'id' => $this->id,
      'name' => $this->name,
      'duration' => $this->duration,
      'reinforcements' => $this->reinforcements,
    ];
    // foreach ($this->attributes as $attribute => $field) {
    //   $data[$attribute] = $this->$attribute;
    // }

    return $data;
  }
}
