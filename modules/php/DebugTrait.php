<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Space;

trait DebugTrait
{
  function debugLoadScenario($scenarioId)
  {
    Scenario::loadId($scenarioId);
    Scenario::setup();
  }

  function test()
  {
    // Notifications::log('card10', Cards::get('Card10'));
    Cards::setupNewGame();
    // Notifications::log('test', Globals::getTest());
    // Notifications::log('static', Units::getStaticUiData());
    // Notifications::log('ui', Units::getUiData());
    // Spaces::setupNewGame();
    // Notifications::log('uiData', Spaces::getUiData());
    // Notifications::log('all', Spaces::getAll()->toArray()[0]->getId());
    // $this->debugLoadScenario('1');
    // Notifications::log('units',Units::getAll());
  }
}
