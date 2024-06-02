<?php
namespace BayonetsAndTomahawks\Models;

class Commander extends AbstractUnit
{
  protected $rating = 0;
  protected $rerollShapes = [];

  public function __construct($row)
  {
    $this->type = COMMANDER;
    parent::__construct($row);
    $this->mpLimit = 2;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY];
  }

  public function getRating()
  {
    return $this->rating;
  }
}
