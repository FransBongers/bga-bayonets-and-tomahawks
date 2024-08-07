<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;

class AtomicActions
{
  // Mapping of actionId and corresponding class
  static $classes = [
    ACTION_ACTIVATE_STACK => 'ActionActivateStack',
    ACTION_ROUND_CHOOSE_CARD => 'ActionRoundChooseCard',
    ACTION_ROUND_CHOOSE_FIRST_PLAYER => 'ActionRoundChooseFirstPlayer',
    ACTION_ROUND_CHOOSE_REACTION => 'ActionRoundChooseReaction',
    ACTION_ROUND_END => 'ActionRoundEnd',
    ACTION_ROUND_ACTION_PHASE => 'ActionRoundActionPhase',
    ACTION_ROUND_FIRST_PLAYER_ACTIONS => 'ActionRoundActionPhase',
    ACTION_ROUND_INDIAN_ACTIONS => 'ActionRoundActionPhase',
    ACTION_ROUND_REACTION => 'ActionRoundActionPhase',
    ACTION_ROUND_RESOLVE_AR_START_EVENT => 'ActionRoundResolveARStartEvent',
    ACTION_ROUND_RESOLVE_BATTLES => 'ActionRoundResolveBattles',
    ACTION_ROUND_SECOND_PLAYER_ACTIONS => 'ActionRoundActionPhase',
    ACTION_ROUND_SAIL_BOX_LANDING => 'ActionRoundSailBoxLanding',
    ARMY_MOVEMENT => 'ArmyMovement',
    ARMY_MOVEMENT_DESTINATION => 'ArmyMovementDestination',
    BATTLE_APPLY_HITS => 'BattleApplyHits',
    BATTLE_CLEANUP => 'BattleCleanup',
    BATTLE_OUTCOME => 'BattleOutcome',
    BATTLE_PREPARATION => 'BattlePreparation',
    BATTLE_RETREAT => 'BattleRetreat',
    BATTLE_ROLLS => 'BattleRolls',
    BATTLE_ROLLS_EFFECTS => 'BattleRollsEffects',
    BATTLE_ROLLS_REROLLS => 'BattleRollsRerolls',
    BATTLE_ROLLS_ROLL_DICE => 'BattleRollsRollDice',
    BATTLE_ROUT => 'BattleRout',
    BATTLE_SELECT_COMMANDER => 'BattleSelectCommander',
    LOGISTICS_ROUND_END => 'LogisticsRoundEnd',
    COLONIALS_ENLIST_UNIT_PLACEMENT => 'ColonialsEnlistUnitPlacement',
    CONSTRUCTION => 'Construction',
    DRAW_REINFORCEMENTS => 'DrawReinforcements',
    EVENT_BRITISH_ENCROACHMENT => 'EventBritishEncroachment',
    EVENT_DELAYED_SUPPLIES_FROM_FRANCE => 'EventDelayedSuppliesFromFrance',
    EVENT_DISEASE_IN_BRITISH_CAMP => 'EventDiseaseInBritishCamp',
    EVENT_DISEASE_IN_FRENCH_CAMP => 'EventDiseaseInFrenchCamp',
    EVENT_PENNSYLVANIAS_PEACE_PROMISES => 'EventPennsylvaniasPeacePromises',
    EVENT_ROUND_UP_MEN_AND_EQUIPMENT => 'EventRoundUpMenAndEquipment',
    EVENT_SMALLPOX_INFECTED_BLANKETS => 'EventSmallpoxInfectedBlankets',
    FLEETS_ARRIVE_COMMANDER_DRAW => 'FleetsArriveCommanderDraw',
    FLEETS_ARRIVE_UNIT_PLACEMENT => 'FleetsArriveUnitPlacement',
    LIGHT_MOVEMENT => 'LightMovement',
    LIGHT_MOVEMENT_DESTINATION => 'LightMovementDestination',
    LOGISTICS_ROUND_END => 'LogisticsRoundEnd',
    MARSHAL_TROOPS => 'MarshalTroops',
    RAID => 'Raid',
    SAIL_MOVEMENT => 'SailMovement',
    SAIL_MOVEMENT_DESTINATION => 'SailMovementDestination',
    SELECT_RESERVE_CARD => 'SelectReserveCard',
    VAGARIES_OF_WAR_PICK_UNITS => 'VagariesOfWarPickUnits',
    VAGARIES_OF_WAR_PUT_BACK_IN_POOL => 'VagariesOfWarPutBackInPool',
    WINTER_QUARTERS_GAME_END_CHECK => 'WinterQuartersGameEndCheck',
    WINTER_QUARTERS_ROUND_END => 'WinterQuartersRoundEnd',
  ];

  public static function get($actionId, $ctx = null)
  {
    if (!\array_key_exists($actionId, self::$classes)) {
      // throw new \feException(print_r(debug_print_backtrace()));
      // throw new \feException(print_r(Globals::getEngine()));
      throw new \BgaVisibleSystemException('Trying to get an atomic action not defined in Actions.php : ' . $actionId);
    }
    $name = '\BayonetsAndTomahawks\Actions\\' . self::$classes[$actionId];
    return new $name($ctx);
  }

  public static function getActionOfState($stateId, $throwErrorIfNone = true)
  {
    foreach (array_keys(self::$classes) as $actionId) {
      if (self::getState($actionId, null) == $stateId) {
        return $actionId;
      }
    }

    if ($throwErrorIfNone) {
      throw new \BgaVisibleSystemException('Trying to fetch args of a non-declared atomic action in state ' . $stateId);
    } else {
      return null;
    }
  }

  public static function isDoable($actionId, $ctx, $player)
  {
    $res = self::get($actionId, $ctx)->isDoable($player);
    return $res;
  }

  public static function getErrorMessage($actionId)
  {
    $actionId = ucfirst(mb_strtolower($actionId));
    $msg = sprintf(
      Game::get()::translate(
        'Attempting to take an action (%s) that is not possible. Either another card erroneously flagged this action as possible, or this action was possible until another card interfered.'
      ),
      $actionId
    );
    return $msg;
  }

  public static function getState($actionId, $ctx)
  {
    return self::get($actionId, $ctx)->getState();
  }

  public static function getArgs($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'args' . $action->getClassName();
    $args = \method_exists($action, $methodName) ? $action->$methodName() : [];
    return array_merge($args, ['optionalAction' => $ctx->isOptional()]);
  }

  public static function takeAction($actionId, $actionName, $args, $ctx)
  {
    $player = Players::getActive();
    if (!self::isDoable($actionId, $ctx, $player)) {
      throw new \BgaUserException(self::getErrorMessage($actionId));
    }

    $action = self::get($actionId, $ctx);
    $methodName = $actionName; //'act' . self::$classes[$actionId];
    $action->$methodName($args);
  }

  /**
   * Execute state action
   */
  public static function stAction($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'st' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
    }
  }

  /**
   * Action executed before activating the state
   */
  public static function stPreAction($actionId, $ctx)
  {
    $action = self::get($actionId, $ctx);
    $methodName = 'stPre' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
      // TODO: check if we need irreversible check at some points
      // if ($ctx->isIrreversible(Players::get($ctx->getPId()))) {
      //   Engine::checkpoint();
      // }
    }
  }

  /**
   * Executes pass action as defined in atomic action
   */
  public static function pass($actionId, $ctx)
  {
    if (!$ctx->isOptional()) {
      self::error($ctx->toArray());
      throw new \BgaVisibleSystemException('This action is not optional');
    }

    $action = self::get($actionId, $ctx);
    $methodName = 'actPass' . $action->getClassName();
    if (\method_exists($action, $methodName)) {
      $action->$methodName();
    } else {
      Engine::resolve(PASS);
    }

    Engine::proceed();
  }
}
