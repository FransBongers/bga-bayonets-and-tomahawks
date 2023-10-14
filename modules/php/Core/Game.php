<?php
namespace BayonetsAndTomahawks\Core;
use bayonetsandtomahawks;

/*
 * Game: a wrapper over table object to allow more generic modules
 */
class Game
{
  public static function get()
  {
    return bayonetsandtomahawks::get();
  }
}
