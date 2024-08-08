<?php
namespace BayonetsAndTomahawks\Models;

use BayonetsAndTomahawks\Core\Notifications;

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

  public function getRerollShapes()
  {
    return $this->rerollShapes;
  }

  public function eliminate($player)
  {
    $previousLocation = $this->getLocation();
    $this->setLocation(REMOVED_FROM_PLAY);
    Notifications::eliminateUnit($player, $this, $previousLocation);
  }
}
