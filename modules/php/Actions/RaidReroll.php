<?php

namespace BayonetsAndTomahawks\Actions;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Core\Engine;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Engine\LeafNode;
use BayonetsAndTomahawks\Core\Stats;
use BayonetsAndTomahawks\Helpers\BTDice;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\PathCalculator;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Connections;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Markers;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class RaidReroll extends \BayonetsAndTomahawks\Actions\Raid
{
  public function getState()
  {
    return ST_RAID_REROLL;
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

  public function stRaidReroll()
  {

  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreRaidRerolld()
  {



  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsRaidReroll()
  {
    $info = $this->ctx->getInfo();

    $source = $info['source'];
    $rollType = $info['rollType'];

    return [
      'rollType' => $rollType,
      'source' => $source,
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

  public function actPassRaidReroll()
  {
    $player = self::getPlayer();

    $info = $this->ctx->getInfo();

    $spaceId = $info['spaceId'];
    $startSpaceId = $info['startSpaceId'];
    $unitId = $info['unitId'];
    $source = $info['source'];
    $rollType = $info['rollType'];

    $text = '';
    $args = [
      'player' => self::getPlayer(),
      'tkn_boldText_eventName' => clienttranslate('Pursuit of Elevated Status'),
      'i18n' => ['tkn_boldText_eventName'],
    ];
    if ($source === PURSUIT_OF_ELEVATED_STATUS && $rollType === RAID_INTERCEPTION) {
      $text = clienttranslate('${player_name} does not use ${tkn_boldText_eventName} to reroll the Interception roll');
    } else if ($source === PURSUIT_OF_ELEVATED_STATUS && $rollType === RAID_RESOLUTION) {
      $text = clienttranslate('${player_name} does not use ${tkn_boldText_eventName} to reroll the Raid roll');
      if (Globals::getUsedEventCount(INDIAN) === 1) {
        Globals::setUsedEventCount(INDIAN, 2);
      }
    }

    Notifications::message($text, $args);

    $this->returnUnitToStartingSpace($player, Units::get($unitId), $startSpaceId, Spaces::get($spaceId));
    $this->ctx->updateInfo('resolveParent', true);

    $this->resolveAction(PASS, true);
  }

  public function actRaidReroll($args)
  {
    self::checkAction('actRaidReroll');

    $reroll = $args['reroll'];

    // Only path to there should confirm using it
    if (!$reroll) {
      throw new \feException("ERROR 069");
    }

    $info = $this->ctx->getInfo();

    $spaceId = $info['spaceId'];
    $startSpaceId = $info['startSpaceId'];
    $unitId = $info['unitId'];
    $source = $info['source'];
    $rollType = $info['rollType'];
    $player = self::getPlayer();

    $text = '';
    $args = [
      'player' => self::getPlayer(),
      'tkn_boldText_eventName' => clienttranslate('Pursuit of Elevated Status'),
      'i18n' => ['tkn_boldText_eventName'],
    ];
    if ($source === PURSUIT_OF_ELEVATED_STATUS && $rollType === RAID_INTERCEPTION) {
      $text = clienttranslate('${player_name} uses ${tkn_boldText_eventName} to reroll the Interception roll');
      Globals::setUsedEventCount(INDIAN, 1);
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => RAID_INTERCEPTION,
        'playerId' => $player->getId(),
        'unitId' => $unitId,
        'spaceId' => $spaceId,
        'startSpaceId' => $startSpaceId,
      ]));
    } else if ($source === PURSUIT_OF_ELEVATED_STATUS && $rollType === RAID_RESOLUTION) {
      $text = clienttranslate('${player_name} uses ${tkn_boldText_eventName} to reroll the Raid roll');
      Globals::setUsedEventCount(INDIAN, 2);
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => RAID_RESOLUTION,
        'playerId' => $player->getId(),
        'unitId' => $unitId,
        'spaceId' => $spaceId,
        'startSpaceId' => $startSpaceId,
      ]));
    }


    Notifications::message($text, $args);

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
