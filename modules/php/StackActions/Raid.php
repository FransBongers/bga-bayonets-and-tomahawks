<?php

namespace BayonetsAndTomahawks\StackActions;

/**
 * Can just removed?
 */
class Raid extends \BayonetsAndTomahawks\Models\StackAction
{
  public function __construct()
  {
    parent::__construct();
    $this->id = RAID_SELECT_TARGET;
    $this->name = clienttranslate("Raid");
  }
}
