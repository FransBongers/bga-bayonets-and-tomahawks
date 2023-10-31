<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;


trait DebugTrait
{
  function debugLoadScenario($scenarioId)
  {
    Scenario::loadId($scenarioId);
    Scenario::setup();
  }

  function test()
  {
    // Spaces::setupNewGame();
    // Notifications::log('uiData', Spaces::getUiData());
    Notifications::log('all', Spaces::getAll()->toArray()[0]->getId());
    // $this->debugLoadScenario('1');
    // Notifications::log('units',Units::getAll());
  }

}
