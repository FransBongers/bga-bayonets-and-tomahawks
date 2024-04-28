<?php
namespace BayonetsAndTomahawks\Models;

class Brigade extends AbstractUnit
{
  protected $staticAttributes = ['counterId', 'counterText', 'faction', 'highland', 'metropolitan', 'type'];
  protected $highland = false;
  protected $metropolitan = false;

  public function __construct($row)
  {
    $this->type = BRIGADE;
    parent::__construct($row);
  }
}
