<?php

namespace BayonetsAndTomahawks\Helpers;

abstract class Locations extends \APP_DbObject
{
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

  public static function discard()
  {
    return 'discard';
  }

  public static function hand($faction)
  {
    return 'hand_' . $faction;
  }

  public static function selected($faction)
  {
    return 'selected_' . $faction;
  }

  public static function cardInPlay($faction)
  {
    return 'cardInPlay_' . $faction;
  }
}