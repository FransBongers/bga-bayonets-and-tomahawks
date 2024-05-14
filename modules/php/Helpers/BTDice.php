<?php

namespace BayonetsAndTomahawks\Helpers;

class BTDice extends \APP_DbObject
{
  public static function roll() {
    $result = bga_rand(0,5);
    return DIE_FACES[$result];
  }
}
