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

use function PHPSTORM_META\map;

class RaidInterception extends \BayonetsAndTomahawks\Actions\Raid
{
  public function getState()
  {
    return ST_RAID_INTERCEPTION;
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

  public function stRaidInterception()
  {
    $info = $this->ctx->getInfo();
    $unitId = $info['unitId'];
    $spaceId = $info['spaceId'];
    $startSpaceId = $info['startSpaceId'];

    $unit = Units::get($unitId);
    $space = Spaces::get($spaceId);

    $player = self::getPlayer();

    $otherPlayer = Players::getOther();
    $otherPlayerFaction = $otherPlayer->getFaction();

    // Check for interception
    $enemyUnits = $space->getUnits($otherPlayerFaction);
    $hasEnemyUnit = count($enemyUnits) > 0;

    if (!$hasEnemyUnit) {
      $this->resolveAction(['automatic' => true], true);
      return;
    }
    $hasEnemyLightUnit = Utils::array_some($enemyUnits, function ($unitOnSpace) {
      return $unitOnSpace->isLight();
    });

    // Roll for interception
    $dieResult = BTDice::roll();
    $intercepted = ($hasEnemyLightUnit && in_array($dieResult, [FLAG, HIT_TRIANGLE_CIRCLE, B_AND_T])) || $dieResult === FLAG;
    Notifications::interception($otherPlayer, $space, $dieResult, $intercepted);

    if ($intercepted && $unit->isIndian() && $player->getFaction() === FRENCH && Cards::isCardInPlay(INDIAN, PURSUIT_OF_ELEVATED_STATUS_CARD_ID) && Globals::getUsedEventCount(INDIAN) === 0) {
      // If event has not been used yet player can choose re-roll the Interception roll
      $this->ctx->insertAsBrother(Engine::buildTree([
        'action' => RAID_REROLL,
        'player' => $player->getId(),
        'spaceId' => $spaceId,
        'startSpaceId' => $startSpaceId,
        'unitId' => $unitId,
        'source' => PURSUIT_OF_ELEVATED_STATUS,
        'rollType' => RAID_INTERCEPTION,
        'optional' => true,
      ]));
    } else if ($intercepted) {
      // If intercepted move unit back to starting space
      $this->returnUnitToStartingSpace($player, $unit, $startSpaceId, $space);
      $this->ctx->updateInfo('resolveParent', true);
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

  public function stPreRaidInterceptiond()
  {
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...
}
