<?php
namespace BayonetsAndTomahawks\Units;

class HowardsBuffsKingsOwn extends \BayonetsAndTomahawks\Models\Brigade
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = HOWARDS_BUFFS_KINGS_OWN;
    $this->counterText = clienttranslate("Howard's Buffs & King's Own");
    $this->faction = BRITISH;
    $this->highland = true;
    $this->metropolitan = true;
  }
}
