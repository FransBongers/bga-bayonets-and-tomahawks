<?php
namespace BayonetsAndTomahawks\Spaces;

class Shamokin extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = SHAMOKIN;
    $this->battlePriority = 221;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->name = clienttranslate('Shamokin');
    $this->outpost = true;
    $this->value = 1;
    $this->victorySpace = false;
    $this->top = 1828;
    $this->left = 813.5;
    $this->adjacentSpaces = [
      CARLISLE => CARLISLE_SHAMOKIN,
      CAWICHNOWANE => CAWICHNOWANE_SHAMOKIN,
      GNADENHUTTEN => GNADENHUTTEN_SHAMOKIN,
      RAYS_TOWN => RAYS_TOWN_SHAMOKIN,
    ];
  }
}
