<?php
namespace BayonetsAndTomahawks\Models;
// use M44\Board;

class Light extends AbstractUnit
{
  protected $staticAttributes = ['counterId', 'counterText', 'faction', 'indian', 'type'];
  protected $indian = false;

  public function __construct($row)
  {
    $this->type = LIGHT;
    parent::__construct($row);
  }
}
