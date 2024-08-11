<?php

namespace BayonetsAndTomahawks\Connections;

class CoteDeBeaupre_Tadoussac extends \BayonetsAndTomahawks\Models\Connections\Highway
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = COTE_DE_BEAUPRE_TADOUSSAC;
    $this->coastal = true;
    $this->top = 654;
    $this->left = 306;
  }
}