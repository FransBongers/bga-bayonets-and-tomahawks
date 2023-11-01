<?php
namespace BayonetsAndTomahawks\Units;

class Wolfe extends \BayonetsAndTomahawks\Models\Commander
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->counterId = WOLFE;
    $this->counterText = clienttranslate('Wolfe');
    $this->faction = BRITISH;
  }
}
