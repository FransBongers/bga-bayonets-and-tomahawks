<?php
namespace BayonetsAndTomahawks\Spaces;

class lossesBox_french extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = LOSSES_BOX_FRENCH;
    $this->isSpaceOnMap = false;
    $this->name = clienttranslate('Losses Box');
  }
}