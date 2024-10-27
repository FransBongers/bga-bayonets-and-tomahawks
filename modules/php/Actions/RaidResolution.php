<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class RaidResolution extends \BayonetsAndTomahawks\Actions\Raid
{
  public function getState()
  {
    return ST_RAID_RESOLUTION;
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

  public function stRaidResolution()
  {
    $info = $this->ctx->getInfo();
    $unitId = $info['unitId'];
    $spaceId = $info['spaceId'];
    $startSpaceId = $info['startSpaceId'];

    $unit = Units::get($unitId);
    $space = Spaces::get($spaceId);
    $player = self::getPlayer();
    $playerFaction = $player->getFaction();

    $raidResolution = BTDice::roll();
    $raidIsSuccessful = in_array($raidResolution, [FLAG, HIT_TRIANGLE_CIRCLE, B_AND_T]);

    Notifications::raidResolution($player, $raidResolution, $raidIsSuccessful);

    if (!$raidIsSuccessful && $unit->isIndian() && $player->getFaction() === FRENCH && Cards::isCardInPlay(INDIAN, PURSUIT_OF_ELEVATED_STATUS_CARD_ID) && Globals::getUsedEventCount(INDIAN) < 2) {
      // If event has not been used yet player can choose re-roll the Raid roll
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => RAID_REROLL,
        'player' => $player->getId(),
        'spaceId' => $spaceId,
        'startSpaceId' => $startSpaceId,
        'unitId' => $unitId,
        'source' => PURSUIT_OF_ELEVATED_STATUS,
        'rollType' => RAID_RESOLUTION,
        'optional' => true,
      ]));
      $this->resolveAction(['automatic' => true], true);
      return;
    }

    $placeInLosses = $raidIsSuccessful && $unit->isIndian();

    if ($placeInLosses && $player->getFaction() === FRENCH && Cards::isCardInPlay(INDIAN, A_RIGHT_TO_PLUNDER_AND_CAPTIVES_CARD_ID) && Globals::getUsedEventCount(INDIAN) === 0) {
      $placeInLosses = false;
      Globals::setUsedEventCount(INDIAN, 1);
    }

    if ($placeInLosses) {
      // Place in friendly losses box
      $unit->placeInLosses($player);
    } else {
      // Move unit back to starting space
      $this->returnUnitToStartingSpace($player, $unit, $startSpaceId, $space);
    }

    if ($raidIsSuccessful) {
      $raidPoints = $space->getHomeSpace() !== null ? $space->getValue() : 1;

      // Place raided marker
      $space->setRaided($playerFaction);
      Notifications::raidPoints($player, $space, $raidPoints);

      if ($playerFaction === FRENCH && Cards::getTopOf(Locations::cardInPlay(FRENCH))->getId() === 'Card36' && Globals::getUsedEventCount(FRENCH) === 0) {
        Notifications::message(
          clienttranslate('${player_name} gains ${tkn_boldText_raidPoints} bonus Raid Points with ${tkn_boldText_eventName}'),
          [
            'player' => $player,
            'tkn_boldText_raidPoints' => '2',
            'tkn_boldText_eventName' => clienttranslate('Frontiers Ablaze'),
            'i18n' => ['tkn_boldText_eventName']
          ]
        );
        $raidPoints += 2;
        Globals::setUsedEventCount(FRENCH, 1);
      }

      GameMap::awardRaidPoints($player, $playerFaction, $raidPoints);

      // Use Staged Lacrosse game
      if (isset($info['useStagedLacrossGame']) && $info['useStagedLacrossGame']) {
        $britishFort = $space->getUnits(BRITISH)[0];
        $britishFort->eliminate($player);
      }
    }

    $this->resolveAction(['automatic' => true], true);
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreRaidResolutiond()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsRaidResolution()
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

  public function actPassRaidResolution()
  {
    $player = self::getPlayer();
    Engine::resolve(PASS);
  }

  public function actRaidResolution($args)
  {
    self::checkAction('actRaid');

    $this->resolveAction($args, true);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

}
