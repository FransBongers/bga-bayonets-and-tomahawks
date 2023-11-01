<?php
namespace BayonetsAndTomahawks\Models;
// use M44\Board;

class Brigade extends AbstractUnit
{
  protected $staticAttributes = ['counterId', 'counterText', 'faction', 'highland', 'metropolitan', 'type'];
  protected $highland = false;
  protected $metropolitan = false;

  public function __construct($row)
  {
    $this->type = LIGHT;
    parent::__construct($row);
  }
}
