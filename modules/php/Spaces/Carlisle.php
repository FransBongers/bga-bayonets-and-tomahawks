<?php
namespace BayonetsAndTomahawks\Spaces;

class Carlisle extends \BayonetsAndTomahawks\Models\Space
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->id = CARLISLE;
    $this->battlePriority = 223;
    $this->defaultControl = BRITISH;
    $this->homeSpace = BRITISH;
    $this->militia = 2;
    $this->name = clienttranslate('CARLISLE');
    $this->settledSpace = true;
    $this->value = 2;
    $this->victorySpace = true;
    $this->top = 1904.5;
    $this->left = 954.5;
    $this->adjacentSpaces = [
      EASTON => CARLISLE_EASTON,
      PHILADELPHIA => CARLISLE_PHILADELPHIA,
      RAYS_TOWN => CARLISLE_RAYS_TOWN,
      SHAMOKIN => CARLISLE_SHAMOKIN,
      WINCHESTER => CARLISLE_WINCHESTER,
    ];
  }
}
