<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\AtomicActions;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class Battle extends \BayonetsAndTomahawks\Models\AtomicAction
{
  protected $factionBattleMarkerMap = [
    BRITISH => BRITISH_BATTLE_MARKER,
    FRENCH => FRENCH_BATTLE_MARKER
  ];

  protected $battleRollsSequenceStepShapeMap = [
    NON_INDIAN_LIGHT => TRIANGLE,
    INDIAN => TRIANGLE,
    HIGHLAND_BRIGADES => SQUARE,
    METROPOLITAN_BRIGADES => SQUARE,
    NON_METROPOLITAN_BRIGADES => SQUARE,
    FLEETS => CIRCLE,
    BASTIONS_OR_FORT => CIRCLE,
    ARTILLERY => CIRCLE,
  ];

  protected function getBattleSpaceId()
  {
    // TODO: check where this is used. In some places it's only parent level, not 2x parent
    $parentInfo = $this->ctx->getParent()->getParent()->getInfo();
    return $parentInfo['spaceId'];
  }

  protected function getBattleSpace()
  {
    return Spaces::get($this->getBattleSpaceId());
  }

  protected function getEnemyFaction($playerFaction)
  {
    return $playerFaction === BRITISH ? FRENCH : BRITISH;
  }

  protected function getBattleMarker($faction)
  {
    return Markers::get($this->factionBattleMarkerMap[$faction]);
  }

  protected function getBattleMarkerValue($marker)
  {
    $location = $marker->getLocation();
    $split = explode('_', $location);

    $value = intval($split[4]);
    if ($split[3] === 'minus') {
      $value = $value * -1;
    }
    if ($marker->getState() > 0) {
      $value += 10 * $marker->getState();
    }
    return $value;
  }

  protected function moveBattleVictoryMarker($player, $faction, $positions = 1)
  {
    $marker = $this->getBattleMarker($faction);
    $value = $this->getBattleMarkerValue($marker);

    $value += $positions;
    if ($value > 10) {
      $marker->setSide(floor($value / 10));
    }
    $isAttacker = explode('_', $marker->getLocation())[2] === 'attacker';

    $marker->setLocation(Locations::battleTrack($isAttacker, $value));
    Notifications::moveBattleVictoryMarker($player, $marker, $positions);
    return $value;
  }

  protected function getCommandersOnRerollsTrack()
  {
    $commanders = Units::getInLocationLike('commander_rerolls_track');
    $result = [
      BRITISH => null,
      FRENCH => null,
    ];

    foreach ($commanders as $commander) {
      $result[$commander->getFaction()] = $commander;
    }
    return $result;
  }

  /**
   * dieResults:
   * 'result' => $dieResult,
   * 'usedRerollSources' => [],
   */
  protected function getRerollOptions($diceResultsWithRerollSources, $battleRollsSequenceStep, $faction)
  {
    $commander = $this->getCommandersOnRerollsTrack()[$faction];

    $commanderRerollAvailable = $commander !== null && // There is a commander
      in_array($this->battleRollsSequenceStepShapeMap[$battleRollsSequenceStep], $commander->getRerollShapes()) && // Shape matches
      intval(explode('_', $commander->getLocation())[4]) > 0; // Rerolls are available

    $cardInPlay = Cards::getTopOf(Locations::cardInPlay($faction));
    $event = $cardInPlay->getEvent();
    $perfectVolleysAvailable = $event !== null && $event['id'] === PERFECT_VOLLEYS && Globals::getUsedEventCount($faction) < 3;
    $luckyCannonballAvailable = $event !== null && $event['id'] === LUCKY_CANNONBALL && Globals::getUsedEventCount($faction) < 3;

    $result = [];

    foreach ($diceResultsWithRerollSources as $dieResult) {
      $dieResult['availableRerollSources'] = [];

      if ($battleRollsSequenceStep === HIGHLAND_BRIGADES && !in_array(HIGHLAND_BRIGADES, $dieResult['usedRerollSources'])) {
        $dieResult['availableRerollSources'][] = HIGHLAND_BRIGADES;
      }

      if ($commanderRerollAvailable && !in_array(COMMANDER, $dieResult['usedRerollSources'])) {
        $dieResult['availableRerollSources'][] = COMMANDER;
      }

      if ($perfectVolleysAvailable && in_array($battleRollsSequenceStep,  [HIGHLAND_BRIGADES, METROPOLITAN_BRIGADES]) && !in_array(PERFECT_VOLLEYS, $dieResult['usedRerollSources'])) {
        $dieResult['availableRerollSources'][] = PERFECT_VOLLEYS;
      }

      if ($luckyCannonballAvailable && in_array($battleRollsSequenceStep,  [FLEETS, BASTIONS_OR_FORT, ARTILLERY]) && !in_array(LUCKY_CANNONBALL, $dieResult['usedRerollSources'])) {
        $dieResult['availableRerollSources'][] = LUCKY_CANNONBALL;
      }

      $result[] = $dieResult;
    }

    return $result;
    // return Utils::filter($diceResultsWithRerollSources, function ($dieResult) use ($commanderRerollAvailable) {

    //   return false;
    // });
  }

  protected function placeCommander($player, $commander, $space, $maxRating = 3)
  {

    $isDefender = $space->getDefender() === $player->getFaction();
    $commander->setLocation(Locations::commanderRerollsTrack($isDefender, min($maxRating, $commander->getRating())));

    Notifications::battleSelectCommander($player, $commander);
  }

  protected function selectCommanders($units, $players, $space, $maxRating = 3)
  {
    foreach ($players as $index => $player) {
      $commanders = Utils::filter($units, function ($unit) use ($player) {
        return $unit->getType() === COMMANDER && $unit->getFaction() === $player->getFaction();
      });
      $numberOfCommanders = count($commanders);
      if ($numberOfCommanders === 1) {
        // Place commander
        $this->placeCommander($player, $commanders[0], $space, $maxRating);
      } else if ($numberOfCommanders > 1) {
        // Insert state to select commander
        $this->ctx->insertAsBrother(
          Engine::buildTree([
            'playerId' => $player->getId(),
            'action' => BATTLE_SELECT_COMMANDER,
            'maxRating' => $maxRating,
          ])
        );
      }
    }
  }

  protected function checkIfReducedUnitsCanBeCombined($space, $faction, $player)
  {
    $action = AtomicActions::get(BATTLE_COMBINE_REDUCED_UNITS);


    $options = $action->getOptions($space, $faction);
    $canCombineReduced = Utils::array_some(array_values($options), function ($reducedUnitsForType) {
      return count($reducedUnitsForType) >= 2;
    });
    if ($canCombineReduced) {
      $this->ctx->insertAsBrother(
        Engine::buildTree([
          'playerId' => $player->getId(),
          'action' => BATTLE_COMBINE_REDUCED_UNITS,
          'spaceId' => $space->getId(),
          'faction' => $faction,
        ])
      );
    }
  }
}
