<?php

namespace BayonetsAndTomahawks\Helpers;

abstract class Locations extends \APP_DbObject
{
  public static function battleTrack($isAttacker, $value)
  {
    $sign = $value < 0 ? 'minus' : 'plus';
    $side = $isAttacker ? 'attacker' : 'defender';
    if ($value > 10) {
      $value = $value % 10;
    }
    return implode('_', ['battle', 'track', $side, $sign, abs($value)]);
  }

  public static function buildUpDeck($faction)
  {
    return 'buildUpDeck_' . $faction;
  }

  public static function campaignDeck($faction)
  {
    return 'campaignDeck_' . $faction;
  }

  public static function cardPool()
  {
    return 'cardPool';
  }

  public static function commanderRerollsTrack($isDefender, $value)
  {
    $side = !$isDefender ? 'attacker' : 'defender';
    return implode('_', ['commander', 'rerolls', 'track', $side, $value]);
  }

  public static function disbandedColonialBrigades()
  {
    return DISBANDED_COLONIAL_BRIGADES;
  }

  public static function discard()
  {
    return 'discard';
  }

  public static function hand($faction)
  {
    return 'hand_' . $faction;
  }

  public static function lossesBox($faction)
  {
    return 'lossesBox_' . $faction;
  }

  public static function markerSupply($type)
  {
    return 'supply_' . $type;
  }


  public static function raidTrack($position)
  {
    return 'raid_track_' . $position;
  }

  public static function selected($faction)
  {
    return 'selected_' . $faction;
  }

  public static function stackMarker($spaceId, $faction)
  {
    return $spaceId . '_' . $faction;
  }

  public static function yearTrack($year)
  {
    return 'year_track_' . $year;
  }

  public static function cardInPlay($faction)
  {
    return 'cardInPlay_' . $faction;
  }

  public static function victoryPointsTrack($faction, $score)
  {
    return 'victory_points_' . $faction . '_' . $score;
  }

  public static function wieChitPool($faction) {
    return 'wieChitPool_' . $faction;
  }

  public static function wieChitPlaceholder($faction) {
    return 'wieChitPlaceholder_' . $faction;
  }
}
