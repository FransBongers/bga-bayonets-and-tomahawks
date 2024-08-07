<?php
namespace BayonetsAndTomahawks\Models;

class Light extends AbstractUnit
{
  protected $staticAttributes = ['colonial', 'colony', 'counterId', 'counterText', 'faction', 'indian', 'type', 'villages'];
  protected $colonial = false;

  public function __construct($row)
  {
    $this->type = LIGHT;
    parent::__construct($row);
    $this->mpLimit = 3;
    $this->connectionTypeAllowed = [ROAD, HIGHWAY, PATH];
  }
}
