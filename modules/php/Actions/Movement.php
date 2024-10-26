<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class Movement extends \BayonetsAndTomahawks\Actions\UnitMovement
{
  public function getState()
  {
    return ST_MOVEMENT;
  }

  // ..######..########....###....########.########
  // .##....##....##......##.##......##....##......
  // .##..........##.....##...##.....##....##......
  // ..######.....##....##.....##....##....######..
  // .......##....##....#########....##....##......
  // .##....##....##....##.....##....##....##......
  // ..######.....##....##.....##....##....########

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function stMovement()
  {
    $info = $this->ctx->getInfo();
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreMovement() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  /**
   * Required data
   * - source
   * - units that can move
   * - units on space that cannot move
   * - adjacent connections
   *    - for adjacent spaces, number of units for overwhelm / battle
   * - OOS and ROUT markers present
   * - units that are required to move
   * - already moved units
   * - commander options when only moving commanders with AMRY Movement
   * 
   * Stack with rout marker
   * - May not attack
   *    - may overwhelm
   * - May only move onto enemy home space if friendly controlled
   * - 
   * 
   * OOS stack
   * - No restrictions?
   */
  public function argsMovement()
  {
    $info = $this->ctx->getInfo();
    $source = $info['source'];

    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);

    $player = self::getPlayer();
    $playerFaction = $player->getFaction();


    $isArmyMovement = in_array($source, [ARMY_AP, ARMY_AP_2X, SAIL_ARMY_AP, SAIL_ARMY_AP_2X, FRENCH_LIGHT_ARMY_AP]);
    $britishForcedMarch = $playerFaction === BRITISH && $isArmyMovement && Cards::isCardInPlay(BRITISH, BRITISH_FORCED_MARCH_CARD_ID) && Globals::getUsedEventCount(BRITISH) === 0;
    $frenchForcedMarch = $playerFaction === FRENCH && $isArmyMovement && Cards::isCardInPlay(FRENCH, FRENCH_FORCED_MARCH_CARD_ID) && Globals::getUsedEventCount(FRENCH) === 0;
    $forcedMarchAvailable = $britishForcedMarch || $frenchForcedMarch;


    $unitsOnSpace = $space->getUnits($playerFaction);
    // Required move when finishing road construction
    $destination = isset($info['destinationId']) && $info['destinationId'] !== null ? Spaces::get($info['destinationId']) : null;

    $stackHasRoutMarker = $space->hasStackMarker(ROUT_MARKER, $playerFaction);;
    $adjacent = $this->getAdjacentConnectionsAndSpaces($space, $playerFaction, $destination, $stackHasRoutMarker);

    // indianNation is set if indian ap was used and this is a subsequent step
    $indianNation = isset($info['indianNation']) ? $info['indianNation'] : null;
    // Solve edge case where a unit performing double road Construction with Construction Frenzy
    // cannot move because the first construction action makes it spent.
    $requiredUnitIds = isset($info['requiredUnitIds']) ? $info['requiredUnitIds'] : [];
    $units = $this->getUnitsThatCanMove($space, $playerFaction, $unitsOnSpace, $source, $forcedMarchAvailable, $indianNation, false, $adjacent, $requiredUnitIds);
    $unitsThatCannotMove = $this->getUnitsThatCannotMove($unitsOnSpace, $units);

    return [
      'source' => $source,
      'adjacent' => $adjacent,
      'destination' => $destination,
      'fromSpace' => $space,
      'faction' => $playerFaction,
      'isArmyMovement' => $isArmyMovement,
      'units' => $units,
      'unitsThatCannotMoveCount' => count($unitsThatCannotMove),
      'previouslyMovedUnitIds' => $this->getUnitsPreviouslyMovedUnits(),
      'requiredUnitIds' => $requiredUnitIds,
      'resolvedMoves' => count($this->ctx->getParent()->getResolvedActions([MOVEMENT])),
      'forcedMarchAvailable' => $forcedMarchAvailable,
    ];
  }

  //  .########..##..........###....##....##.########.########.
  //  .##.....##.##.........##.##....##..##..##.......##.....##
  //  .##.....##.##........##...##....####...##.......##.....##
  //  .########..##.......##.....##....##....######...########.
  //  .##........##.......#########....##....##.......##...##..
  //  .##........##.......##.....##....##....##.......##....##.
  //  .##........########.##.....##....##....########.##.....##

  // ....###.....######..########.####..#######..##....##
  // ...##.##...##....##....##.....##..##.....##.###...##
  // ..##...##..##..........##.....##..##.....##.####..##
  // .##.....##.##..........##.....##..##.....##.##.##.##
  // .#########.##..........##.....##..##.....##.##..####
  // .##.....##.##....##....##.....##..##.....##.##...###
  // .##.....##..######.....##....####..#######..##....##

  public function actPassMovement()
  {
    $player = self::getPlayer();
    $info = $this->ctx->getInfo();
    $spaceId = $info['spaceId'];
    $space = Spaces::get($spaceId);
    $units = $space->getUnits($player->getFaction());

    $this->loneCommanderCheck($player, $space, $units);
    // Stats::incPassActionCount($player->getId(), 1);
    // Engine::resolve(PASS);
    $this->resolveAction(PASS);
  }

  public function actMovement($args)
  {
    self::checkAction('actMovement');

    // $unitIds = $args['unitIds'];
    $destinationId = $args['destinationId'];
    $selectedUnitIds = $args['selectedUnitIds'];

    $stateArgs = $this->argsMovement();

    $adjacent = Utils::array_find($stateArgs['adjacent'], function ($adjacentOption) use ($destinationId) {
      return $adjacentOption['space']->getId() === $destinationId;
    });

    if ($adjacent === null) {
      throw new \feException("ERROR 050");
    }

    $units = Utils::filter($stateArgs['units'], function ($unit) use ($selectedUnitIds) {
      return in_array($unit->getId(), $selectedUnitIds);
    });

    if (count($units) !== count($selectedUnitIds)) {
      throw new \feException("ERROR 051");
    }

    // $destination = $adjacent['space'];

    $player = self::getPlayer();

    $info = $this->ctx->getInfo();
    $originId = $info['spaceId'];
    // $origin = Spaces::get($originId);

    $unitIds = array_map(function ($unit) {
      return $unit->getId();
    }, $units);

    $connection = $adjacent['connection'];
    $playerId = $player->getId();

    if ($this->usesForcedMarch($stateArgs)) {
      Notifications::message(clienttranslate('${player_name} uses ${tkn_boldText_eventName}'), [
        'player' => $player,
        'tkn_boldText_eventName' => clienttranslate('Forced March'),
        'i18n' => ['tkn_boldText_eventName']
      ]);
      Globals::setUsedEventCount($stateArgs['faction'], 1);
    }

    $indianNation = isset($info['indianNation']) ? $info['indianNation'] : null;
    if (in_array($info['source'], [INDIAN_AP, INDIAN_AP_2X]) && $indianNation === null) {
      $indianNation = $units[0]->getCounterId();
    }


    $this->ctx->insertAsBrother(Engine::buildTree([
      'children' => [
        [
          'action' => MOVEMENT_LOSE_CONTROL_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $originId,
        ],
        [
          'action' => MOVEMENT_OVERWHELM_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $destinationId,
        ],
        [
          'action' => MOVEMENT_BATTLE_AND_TAKE_CONTROL_CHECK,
          'playerId' => $player->getId(),
          'spaceId' => $destinationId,
          'source' => $info['source'],
          'destinationId' => $info['destinationId'],
          'requiredUnitIds' => $info['requiredUnitIds'],
          'forcedMarchAvailable' => $stateArgs['forcedMarchAvailable'],
          'indianNation' => $indianNation,
        ]
      ]
    ]));

    $this->ctx->insertAsBrother(Engine::buildTree([
      'action' => MOVE_STACK,
      'playerId' => $playerId,
      'fromSpaceId' => $originId,
      'toSpaceId' => $destinationId,
      'unitIds' => $unitIds,
      'connectionId' => $connection->getId(),
      'forcedMarchAvailable' => $stateArgs['forcedMarchAvailable'],
    ]));

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  // Returns units already moved as part of this move action
  private function getUnitsPreviouslyMovedUnits()
  {
    $resolvedMoveActions = $this->ctx->getParent()->getResolvedActions([MOVE_STACK]);
    $movedUnitIds = [];

    foreach ($resolvedMoveActions as $node) {
      $resArgs = $node->getActionResolutionArgs();
      $unitIds = $resArgs['unitIds'];
      $movedUnitIds = array_merge($movedUnitIds, $unitIds);
    }

    return array_values(array_unique($movedUnitIds));
  }

  private function usesForcedMarch($stateArgs)
  {
    if (!$stateArgs['forcedMarchAvailable']) {
      return false;
    }

    if (!Utils::array_some($stateArgs['units'], function ($unit) {
      return !$unit->isLight();
    })) {
      return false;
    }

    $regularArmyMovementLimit = $stateArgs['resolvedMoves'] === 2 &&
      in_array($stateArgs['source'], [ARMY_AP, SAIL_ARMY_AP, FRENCH_LIGHT_ARMY_AP]);

    $doubleArmyMovementLimit = $stateArgs['resolvedMoves'] === 4 &&
      in_array($stateArgs['source'], [ARMY_AP_2X, SAIL_ARMY_AP_2X, FRENCH_LIGHT_ARMY_AP]);

    return $regularArmyMovementLimit || $doubleArmyMovementLimit;
  }

  public function getUnitsThatCannotMove($unitsOnSpace, $unitsThatCanMove)
  {
    $unitsThatCanMoveIds = BTHelpers::returnIds($unitsThatCanMove);
    return Utils::filter($unitsOnSpace, function ($unit) use ($unitsThatCanMoveIds) {
      return !in_array($unit->getId(), $unitsThatCanMoveIds);
    });
  }

  public function getAdjacentConnectionsAndSpaces($space, $faction, $destination, $stackHasRoutMarker)
  {
    $adjacent = $space->getAdjacentConnectionsAndSpaces();

    // French Lake Warships events prevents use of one specific connection
    $unusableByBritishConnectionId = Globals::getHighwayUnusableForBritish();
    if ($faction === BRITISH && $unusableByBritishConnectionId !== '') {
      $adjacent = Utils::filter($adjacent, function ($data) use ($unusableByBritishConnectionId) {
        return $data['connection']->getId() !== $unusableByBritishConnectionId;
      });
    }

    // Destination can be set if required to move due to construction
    if ($destination !== null) {
      $adjacent = Utils::filter($adjacent, function ($data) use ($destination) {
        return $data['space']->getId() === $destination->getId();
      });
    }

    $unitsOnMap = Units::getAll()->toArray();

    /**
     * For each adjacent space determine:
     * - minimum required: if stack has rout marker and space has enemy units stack must overwhelm
     * - units required for overwhelm
     */
    foreach ($adjacent as $index => $adjacentData) {
      $adjacentSpace = $adjacentData['space'];
      $adjacentSpaceId = $adjacentSpace->getId();
      $unitsOnSpace = Utils::filter($unitsOnMap, function ($unit) use ($adjacentSpaceId) {
        return $unit->getLocation() === $adjacentSpaceId;
      });
      $overwhelmData = GameMap::requiredForOverwhelm($space, $faction, $unitsOnSpace);
      $adjacent[$index]['hasEnemyUnits'] = $overwhelmData['hasEnemyUnits'];
      $adjacent[$index]['requiredForOverwhelm'] = $overwhelmData['requiredForOverwhelm'];
      $adjacent[$index]['requiredToMove'] = $stackHasRoutMarker ? $overwhelmData['requiredForOverwhelm'] : 0;
    }

    return $adjacent;
  }

  /**
   * Units can move when
   * - Allowed by AP
   * - Units has not reached MP limit
   * - No battle in the space OR battle in the space and units outnumber enemy OR battle and faction is british and enemy has bastion
   * - there is at least one adjacent location a unit can move to?
   */
  public function getUnitsThatCanMove(
    $space,
    $faction,
    $units,
    $source,
    $forcedMarchAvailable,
    $indianNation = null,
    $ignoreAlreadyMovedCheck = false,
    $adjacent = null,
    $requiredUnitIds = []
  ) {
    $currentNumberOfMoves = 0;
    $mpMultiplier = 1;
    if (!$ignoreAlreadyMovedCheck) {
      $currentNumberOfMoves = count($this->ctx->getParent()->getResolvedActions([MOVEMENT]));
      $mpMultiplier = in_array($this->ctx->getInfo()['source'], [ARMY_AP_2X, LIGHT_AP_2X, INDIAN_AP_2X, SAIL_ARMY_AP_2X]) ? 2 : 1;
    }

    $battleInSpace = $space->getBattle() === 1;

    if ($battleInSpace) {
      $data = GameMap::factionOutnumbersEnemyInSpace($space, $faction);
      if (!$data['outnumbers'] && !($faction === BRITISH && $data['enemyHasBastion'])) {
        return [];
      }
    }

    $roughSeasActive = Cards::isCardInPlay(FRENCH, ROUGH_SEAS_CARD_ID);

    // TODO: filter units that are locked in battle?
    $unitsThatCanMove = Utils::filter($units, function ($unit) use ($adjacent, $ignoreAlreadyMovedCheck, $currentNumberOfMoves, $mpMultiplier, $source, $forcedMarchAvailable, $roughSeasActive) {
      if ($roughSeasActive && $unit->isFleet()) {
        return false;
      }
      if ($unit->isFort() || $unit->isBastion()) {
        return false;
      }
      if ($unit->isIndian() && Globals::getNoIndianUnitMayBeActivated()) {
        return false;
      }
      $movementPoints = $unit->getMpLimit() * $mpMultiplier;
      if ($forcedMarchAvailable && !$unit->isLight()) {
        $movementPoints += 1;
      }
      if (!$ignoreAlreadyMovedCheck && $source !== CONSTRUCTION && $currentNumberOfMoves >= $movementPoints) {
        return false;
      }
      if ($adjacent !== null && !Utils::array_some($adjacent, function ($adjacentData) use ($unit) {
        return $adjacentData['connection']->canBeUsedByUnit($unit);
      })) {
        return false;
      }

      return !$unit->isSpent();
    });

    // Indian AP can only move units of one Indian Nation
    if (in_array($source, [INDIAN_AP, INDIAN_AP_2X])) {
      return Utils::filter($unitsThatCanMove, function ($unit) use ($indianNation) {
        if (!$unit->isIndian()) {
          return false;
        }
        return $indianNation !== null ? $unit->getCounterId() === $indianNation : true;
      });
    }
    // One commander can move with light units
    if (in_array($source, [LIGHT_AP, LIGHT_AP_2X])) {
      $lightUnitCanMove = Utils::array_some($unitsThatCanMove, function ($unit) {
        return $unit->isLight();
      });

      return Utils::filter($unitsThatCanMove, function ($unit) use ($lightUnitCanMove) {
        return $unit->isLight() || ($lightUnitCanMove && $unit->isCommander());
      });
    }

    // Solve edge case where a unit performing double road Construction with Construction Frenzy
    // cannot move because the first construction action makes it spent.
    foreach ($requiredUnitIds as $requiredUnitId) {
      $inUnits = Utils::array_some($unitsThatCanMove, function ($unit) use ($requiredUnitId) {
        return $unit->getId() === $requiredUnitId;
      });
      if (!$inUnits) {
        $unitsThatCanMove[] = Units::get($requiredUnitId);
      }
    }

    return $unitsThatCanMove;
  }

  public function getUiData()
  {
    return [
      'id' => MOVEMENT,
      'name' => clienttranslate("Movement"),
    ];
  }

  public function canBePerformedBy($units, $space, $actionPoint, $playerFaction)
  {
    return count($this->getUnitsThatCanMove($space, $playerFaction, $units, $actionPoint->getId(), false, null, true)) > 0;
  }

  public function getFlow($source, $playerId, $originId, $destinationId = null, $requiredUnitIds = [])
  {
    return [
      'originId' => $originId,
      'children' => [
        [
          'action' => MOVEMENT,
          'spaceId' => $originId,
          'source' => $source,
          'destinationId' => $destinationId,
          'requiredUnitIds' => $requiredUnitIds,
          'playerId' => $playerId,
        ],
        [
          'action' => MOVEMENT_PLACE_SPENT_MARKERS,
          'playerId' => $playerId,
        ],
      ],
    ];
  }
}
