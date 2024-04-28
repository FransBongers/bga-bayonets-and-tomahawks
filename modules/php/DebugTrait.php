<?php

namespace BayonetsAndTomahawks;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Managers\ActionPoints;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\StackActions;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Managers\Tokens;
use BayonetsAndTomahawks\Models\AtomicAction;
use BayonetsAndTomahawks\Models\Space;
use BayonetsAndTomahawks\Helpers\Utils;

trait DebugTrait
{
  // function debugLoadScenario($scenarioId)
  // {
  //   Scenario::loadId($scenarioId);
  //   Scenario::setup();
  // }

  function getStacks() {
    $spaces = Spaces::getAll();
    
    $stacks = [];
    foreach($spaces as $space) {
      $units = $space->getUnits();
      // if (count($units) > 0) {
      //   Notifications::log('units '.$space->getId(),$units);
      // }
      
      $hasUnitToActivate = Utils::array_some($units, function ($unit) {
        $faction = $unit->getFaction();
        Notifications::log('faction',$faction);
        return $faction === INDIAN;
      });
      if ($hasUnitToActivate) {
        $stacks[] = $space->getId();
      }
    }
    Notifications::log('stacks',$stacks);
  }

  function test()
  {
    // Connections::setupNewGame();
    $connection = Connections::get(GRAND_SAULT_WOLASTOKUK);
    // $space = Spaces::get(CHIGNECTOU);
    // $connection->incLimitUsed(FRENCH, 2);

    Notifications::log('conneciton', $connection);

    // $lightAP = ActionPoints::get(LIGHT_AP);

    // Notifications::log('lightAP', $lightAP);

    // $result = $lightAP->canActivateStackInSpace($space, Players::get());
    // Notifications::log('canActivateStackInSpace',$result);
    // Notifications::log('canActivateStackInSpaceCount',count($result));

    // Notifications::log('action', StackActions::get(LIGHT_MOVEMENT));
    // $this->getStacks();
    
    // Scenarios::setup($options[OPTION_SCENARIO]);
    // Globals::setActionRound(ACTION_ROUND_9);
    // Tokens::move(ROUND_MARKER,ACTION_ROUND_9);
    // Notifications::log('scenario',Scenarios::get(VaudreuilsPetiteGuerre1755));
    // Globals::setScenarioId(1);
    // Scenario::loadId($scenarioId);
    // Notifications::log('scenario', Globals::getScenario());
    // Units::moveAllInLocation(HALIFAX,CHIGNECTOU);
    // $result = AtomicActions::get(ACTION_ROUND_CHOOSE_FIRST_PLAYER)->getPlayerActionsFlow(Players::get(), true);
    // Notifications::log('result',$result);
    // Notifications::log('british player', Players::getPlayerForFaction(BRITISH));
    // Cards::setupNewGame();
    // Notifications::log('test', Globals::getTest());
    // Notifications::log('static', Units::getStaticUiData());
    // Notifications::log('ui', Units::getUiData());
    // Spaces::setupNewGame();
    // Notifications::log('uiData', Spaces::getUiData());
    // Notifications::log('all', Spaces::getAll()->toArray()[0]->getId());
    // $this->debugLoadScenario('1');
    // Notifications::log('units',Units::getAll());
  }

  function ed()
  {
    $this->engineDisplay();
  }

  function engineDisplay()
  {
    Notifications::log('engine', Globals::getEngine());
  }
}
