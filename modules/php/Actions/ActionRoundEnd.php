<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class ActionRoundEnd extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_ACTION_ROUND_END;
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

  public function stActionRoundEnd()
  {
    // Notifications::log('stActionRoundEndUpdates', []);

    // 1. Discard played cards facedown.
    $cardsInPlay = Cards::getCardsInPlay();
    Notifications::discardCardsInPlayMessage();

    foreach ($cardsInPlay as $faction => $card) {
      // TODO: remove indian card
      if ($card !== null && $card->getId() === 'Card45') {
        $card->removeFromPlay();
      } else if ($card !== null) {
        $card->discard();
      }
    }

    // 2. Remove Spent markers, as well as any remaning Landing and Marshall markers.
    $this->removeSpentLandingMarshalMarkers();

    $spaces = Spaces::getAll();
    $units = Units::getAll()->toArray();
    $stacksAndSupplySources = $this->getStacksAndSupplySources($spaces, $units);

    // 3. Supply Check (14.1)
    $this->performSupplyCheck($stacksAndSupplySources);

    // 4 Rally (14.2)
    $this->performRally($stacksAndSupplySources);

    // 5 Advance Round marker and begin next Round (7.1)
    // TODO: end of year check here?

    $currentActionRound = Globals::getActionRound();
    // Notifications::log('currentActionRound', $currentActionRound);
    $nextActionRound = null;
    switch ($currentActionRound) {
      case ACTION_ROUND_1:
        $nextActionRound = ACTION_ROUND_2;
        break;
      case ACTION_ROUND_2:
        $nextActionRound = FLEETS_ARRIVE;
        break;
      case ACTION_ROUND_3:
        $nextActionRound = COLONIALS_ENLIST;
        break;
      case ACTION_ROUND_4:
        $nextActionRound = ACTION_ROUND_5;
        break;
      case ACTION_ROUND_5:
        $nextActionRound = ACTION_ROUND_6;
        break;
      case ACTION_ROUND_6:
        $nextActionRound = ACTION_ROUND_7;
        break;
      case ACTION_ROUND_7:
        $nextActionRound = ACTION_ROUND_8;
        break;
      case ACTION_ROUND_8:
        $nextActionRound = ACTION_ROUND_9;
        break;
      case ACTION_ROUND_9:
        $nextActionRound = WINTER_QUARTERS;
        break;
      case FLEETS_ARRIVE:
        $nextActionRound = ACTION_ROUND_3;
        break;
      case COLONIALS_ENLIST:
        $nextActionRound = ACTION_ROUND_4;
        break;
      case WINTER_QUARTERS:
        // TODO: check how to handle this?
        $nextActionRound = ACTION_ROUND_1;
        break;
    }
    // Notifications::log('nextActionRound', $nextActionRound);
    Globals::setActionRound($nextActionRound);
    Globals::setFirstPlayerId(0);
    Globals::setSecondPlayerId(0);
    Globals::setReactionActionPointId('');
    Globals::setAddedAPFrench([]);
    Globals::setLostAPBritish([]);
    Globals::setLostAPFrench([]);
    Globals::setLostAPIndian([]);
    Globals::setPlacedConstructionMarkers([]);
    Spaces::setStartOfTurnControl();
    Globals::setUsedEventBritish(0);
    Globals::setUsedEventFrench(0);
    Globals::setUsedEventIndian(0);
    Globals::setNoIndianUnitMayBeActivated(false);

    Markers::move(ROUND_MARKER, $nextActionRound);
    Notifications::moveRoundMarker(Markers::get(ROUND_MARKER), $nextActionRound);

    $this->resolveAction(['automatic' => true]);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreActionRoundEnd()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsActionRoundEnd()
  {


    return [];
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

  public function actPassActionRoundEnd()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actActionRoundEnd($args)
  {
    self::checkAction('actActionRoundEnd');



    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function removeSpentLandingMarshalMarkers()
  {
    $spentUnits = Units::getSpent();
    Units::removeAllSpentMarkers();
    Connections::resetConnectionLimits();

    $landingMarkers = Utils::filter(Markers::getMarkersOfType(LANDING_MARKER), function ($marker) {
      return !Utils::startsWith($marker->getLocation(), 'supply');
    });
    $marshalTroopsMarkers = Utils::filter(Markers::getMarkersOfType(MARSHAL_TROOPS_MARKER), function ($marker) {
      return !Utils::startsWith($marker->getLocation(), 'supply');
    });

    Markers::move(array_map(function ($marker) {
      return $marker->getId();
    }, $landingMarkers), Locations::markerSupply(LANDING_MARKER));
    Markers::move(array_map(function ($marker) {
      return $marker->getId();
    }, $marshalTroopsMarkers), Locations::markerSupply(MARSHAL_TROOPS_MARKER));

    Notifications::removeMarkersEndOfActionRound($spentUnits, array_merge($landingMarkers, $marshalTroopsMarkers));
  }


  private function getStacksAndSupplySources($spaces, $units)
  {
    $stacks = GameMap::getStacks($spaces, $units);

    $supplySources = [
      BRITISH => [],
      FRENCH => [],
    ];

    // Friendly colony homespaces
    foreach ($spaces as $spaceId => $space) {
      $isColonyHomeSpace = $space->getColony() !== null && $space->getHomeSpace() !== null;
      if (!$isColonyHomeSpace) {
        continue;
      }
      foreach ([BRITISH, FRENCH] as $faction) {
        if ($space->getControl() === $faction) {
          $supplySources[$faction][] = $spaceId;
        }
      }
    }

    // Spaces with friendly fleets
    foreach ([BRITISH, FRENCH] as $faction) {
      foreach ($stacks[$faction] as $spaceId => $data) {
        if (Utils::array_some($data['units'], function ($unit) {
          return $unit->isFleet();
        }) && !in_array($spaceId, $supplySources[$faction])) {
          $supplySources[$faction][] = $spaceId;
        };
      }
    }

    return [
      'stacks' => $stacks,
      'supplySources' => $supplySources,
    ];
  }

  private function checkSupplyForFaction($faction, $stacks, $supplySources, $enemyStacks)
  {
    $spaces = Spaces::getAll();
    $connections = Connections::getAll();
    $enemyFaction = BTHelpers::getOtherFaction($faction);
    $indianNationControl = [
      CHEROKEE => Globals::getControlCherokee(),
      IROQUOIS => Globals::getControlIroquois(),
    ];
    $outOfSupplyMarkers = Markers::getMarkersOfType(OUT_OF_SUPPLY_MARKER);
    $player = Players::getPlayerForFaction($faction);

    foreach ($stacks as $spaceId => $stackInSpaceData) {
      $canUsePaths = !Utils::array_some($stackInSpaceData['units'], function ($unit) {
        return !$unit->isLight();
      });

      // Check supply
      $visited = [];
      $queue = [$spaceId];
      $inSupply = false;

      while (count($queue) > 0) {
        $currentSpaceId = array_shift($queue);
        if (isset($visited[$currentSpaceId]) && $visited[$currentSpaceId]) {
          continue;
        }
        $visited[$currentSpaceId] = true;

        if (in_array($spaceId, $supplySources)) {
          $inSupply = true;
          break;
        }

        $currentSpace = $spaces[$currentSpaceId];
        $adjacentSpaces = $currentSpace->getAdjacentSpaces();

        foreach ($adjacentSpaces as $adjacentSpaceId => $connectionId) {
          if (isset($visited[$adjacentSpaceId]) && $visited[$currentSpaceId]) {
            continue;
          }

          // Cannot use paths if stack is not entirely composed of Light Units
          $connection = $connections[$connectionId];
          if ($connection->isPath() && !$canUsePaths) {
            continue;
          }

          // Cannot use paths of Neutral Indian Nations
          $indianPath = $connection->getIndianNationPath();
          if ($canUsePaths && $indianPath !== null && $indianNationControl[$indianPath] === NEUTRAL) {
            continue;
          }

          $adjacentSpace = $spaces[$adjacentSpaceId];

          // Cannot trace through enemy controlled spaces, unless Outpost with no enemy units
          if ($adjacentSpace->getControl() === $enemyFaction && !($adjacentSpace->isOutpost() && !isset($enemyStacks[$adjacentSpaceId]))) {
            continue;
          }

          // Can use Wilderness Spaces unless they contain enemy units
          if ($adjacentSpace->getControl() === NEUTRAL && isset($enemyStacks[$adjacentSpaceId])) {
            continue;
          }

          if (in_array($adjacentSpaceId, $supplySources)) {
            $inSupply = true;
            break;
          }
          $queue[] = $adjacentSpaceId;
        }
      }

      $marker = Utils::array_find($outOfSupplyMarkers, function ($marker) use ($spaceId, $faction) {
        return $marker->getLocation() === Locations::stackMarker($spaceId, $faction);
      });
      if (!$inSupply && $marker === null) {
        GameMap::placeMarkerOnStack($player, OUT_OF_SUPPLY_MARKER, $spaces[$spaceId], $faction);
      } else if ($inSupply && $marker !== null) {
        $marker->remove($player);
      }
    }
  }

  private function performSupplyCheck($stacksAndSupplySources)
  {
    // TODO: use specific notifId for this to change styling in the logs?
    Notifications::message('${tkn_boldText}', [
      'tkn_boldText' => clienttranslate('Supply Check'),
      'i18n' => ['tkn_boldText'],
    ]);


    foreach ([BRITISH, FRENCH] as $faction) {
      $otherFaction = BTHelpers::getOtherFaction($faction);
      $this->checkSupplyForFaction(
        $faction,
        $stacksAndSupplySources['stacks'][$faction],
        $stacksAndSupplySources['supplySources'][$faction],
        $stacksAndSupplySources['stacks'][$otherFaction]
      );
    }
  }

  private function performRally($stacksAndSupplySources)
  {
    // TODO: use specific notifId for this to change styling in the logs?
    Notifications::message('${tkn_boldText}', [
      'tkn_boldText' => clienttranslate('Rally'),
      'i18n' => ['tkn_boldText'],
    ]);

    $markers = Utils::filter(Markers::getMarkersOfType(ROUT_MARKER), function ($marker) {
      return !Utils::startsWith($marker->getLocation(), 'supply');
    });

    $players = Players::getPlayersForFactions();

    foreach ([BRITISH, FRENCH] as $faction) {
      foreach ($stacksAndSupplySources['stacks'][$faction] as $spaceId => $data) {
        $marker = Utils::array_find($markers, function ($marker) use ($spaceId, $faction) {
          return $marker->getLocation() === Locations::stackMarker($spaceId, $faction);
        });
        if ($marker === null) {
          continue;
        }
        $lightUnitsOnly = !Utils::array_some($data['units'], function ($unit) {
          return !in_array($unit->getType(), [LIGHT, FORT, COMMANDER]);
        });

        $commanderRating = 0;
        foreach ($data['units'] as $unit) {
          if ($unit->isCommander() && $unit->getRating() > $commanderRating) {
            $commanderRating = $unit->getRating();
          }
        }

        $diceResults = [];
        $numberOfDice = 1 + $commanderRating;
        for ($i = 0; $i < $numberOfDice; $i++) {
          $diceResults[] = BTDice::roll();
        }


        if (in_array(FLAG, $diceResults) || ($lightUnitsOnly && in_array(B_AND_T, $diceResults))) {
          Notifications::message(clienttranslate('${player_name} rolls ${diceResults} for their stack in ${tkn_boldText_spaceName}: the stack Rallies'), [
            'player' => $players[$faction],
            'diceResults' => Notifications::diceResultsLog($diceResults),
            'tkn_boldText_spaceName' => $data['space']->getName(),
            'i18n' => ['tkn_boldText_spaceName'],
          ]);
          $marker->remove($players[$faction]);
        } else {
          Notifications::message(clienttranslate('${player_name} rolls ${diceResults} for their stack in ${tkn_boldText_spaceName}: the stack does not Rally'), [
            'player' => $players[$faction],
            'diceResults' => Notifications::diceResultsLog($diceResults),
            'tkn_boldText_spaceName' => $data['space']->getName(),
            'i18n' => ['tkn_boldText_spaceName'],
          ]);
        }
      }
    }
  }
}
