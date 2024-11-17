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
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class VagariesOfWarPickUnits extends \BayonetsAndTomahawks\Actions\LogisticsRounds
{
  protected $vowTokenNumberOfUnitsMap = [
    VOW_PICK_ONE_ARTILLERY_FRENCH => 1,
    VOW_PICK_TWO_ARTILLERY_BRITISH => 2,
    VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH => 2,
    VOW_PICK_ONE_COLONIAL_LIGHT => 1,
    VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK => 1,
  ];

  public function getState()
  {
    return ST_VAGARIES_OF_WAR_PICK_UNITS;
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

  public function stVagariesOfWarPickUnits()
  {
    $vagariesOfWarTokens = $this->getVagariesOfWarTokens();

    if (count($this->getOptions($vagariesOfWarTokens)) === 0) {
      $this->resolveAction(['automatic' => true]);
    }
  }
  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreVagariesOfWarPickUnits() {}


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsVagariesOfWarPickUnits()
  {
    $vagariesOfWarTokens = $this->getVagariesOfWarTokens();

    return [
      'options' => $this->getOptions($vagariesOfWarTokens),
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

  public function actPassVagariesOfWarPickUnits()
  {
    $player = self::getPlayer();

    Engine::resolve(PASS);
  }

  public function actVagariesOfWarPickUnits($args)
  {
    self::checkAction('actVagariesOfWarPickUnits');

    $counterId = $args['vowTokenId'];
    $selectedUnitIds = $args['selectedUnitIds'];
    $drawToken = isset($args['drawToken']) ? $args['drawToken'] : false;

    $vagariesOfWarTokens = $this->getVagariesOfWarTokens();
    $options = $this->getOptions($vagariesOfWarTokens);

    if (!isset($options[$counterId])) {
      throw new \feException("ERROR 016");
    }

    $units = $options[$counterId];

    $info = $this->ctx->getInfo();
    $pool = $info['pool'];
    $player = self::getPlayer();

    $vowToken = Utils::array_find($vagariesOfWarTokens, function ($token) use ($counterId) {
      return $token->getCounterId() === $counterId;
    });

    if ($vowToken === null) {
      throw new \feException("ERROR 018");
    }

    // Pick units
    if (count($units) > 0) {
      $selectedUnitIds = array_unique($selectedUnitIds);

      $requiredNumberToSelect = min($this->vowTokenNumberOfUnitsMap[$counterId], count($units));

      if (count($selectedUnitIds) !== $requiredNumberToSelect) {
        throw new \feException("ERROR 017");
      }
  
      $selectedUnits = Utils::filter($units, function ($unit) use ($selectedUnitIds) {
        return in_array($unit->getId(), $selectedUnitIds);
      });
  
      if (count($selectedUnits) !== count($selectedUnitIds)) {
        throw new \feException("ERROR 019");
      }
  
      $location = $this->poolReinforcementsMap[$pool];
  
      Units::move($selectedUnitIds, $location);

      if ($vowToken->getPutTokenBackInPool()) {
        // $vowToken->returnToPool($pool);
        $vowToken->setReduced(1);
      }
  
      Notifications::vagariesOfWarPickUnits($player, $vowToken, $selectedUnits, $location);
    } else if (count($units) === 0) {
      // No units to pick, draw additional token
      if(!$drawToken) {
        throw new \feException("ERROR 100");
      }
      Notifications::message(clienttranslate('${player_name} uses ${tkn_unit_vowToken} draw one additional VoW token'),[
        'player' => $player,
        'tkn_unit_vowToken' => $vowToken->getCounterId(),
      ]);
      AtomicActions::get(DRAW_REINFORCEMENTS)->drawReinforcement($player, $pool, 1, true);
    }

    if (!$vowToken->getPutTokenBackInPool()) {
      $vowToken->removeFromPlay();
    }

    $this->ctx->insertAsBrother(new LeafNode([
      'action' => VAGARIES_OF_WAR_PICK_UNITS,
      'playerId' => $player->getId(),
      'faction' => $info['faction'],
      'pool' => $pool,
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

  private function getVagariesOfWarTokens()
  {
    $info = $this->ctx->getInfo();
    $pool = $info['pool'];

    $picked = Units::getInLocation($this->poolReinforcementsMap[$pool])->toArray();

    $vagariesOfWarTokens = Utils::filter($picked, function ($unit) {
      return $unit->isVagariesOfWarToken() && !$unit->isReduced();
    });
    return $vagariesOfWarTokens;
  }

  public function getOptions($vagariesOfWarTokens)
  {

    if (count($vagariesOfWarTokens) === 0) {
      return [];
    }

    $options = [];

    foreach ($vagariesOfWarTokens as $token) {
      $counterId = $token->getCounterId();

      if (isset($options[$counterId])) {
        continue;
      }

      switch ($counterId) {
        case VOW_PICK_ONE_ARTILLERY_FRENCH;
          $options[$counterId] = Units::getInLocation(POOL_FRENCH_ARTILLERY)->toArray();
          break;
        case VOW_PICK_TWO_ARTILLERY_BRITISH;
          $options[$counterId] = Units::getInLocation(POOL_BRITISH_ARTILLERY)->toArray();
          break;
        case VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH:
          $options[$counterId] = array_merge(Units::getInLocation(POOL_BRITISH_ARTILLERY)->toArray(), Units::getInLocation(POOL_BRITISH_LIGHT)->toArray());
          break;
        case VOW_PICK_ONE_COLONIAL_LIGHT:
        case VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK:
          $options[$counterId] = Units::getInLocation(POOL_BRITISH_COLONIAL_LIGHT)->toArray();
          if (count($options[$counterId]) === 0) {
            $options[$counterId] = Utils::filter(Units::getInLocation(Locations::lossesBox(BRITISH))->toArray(), function ($unit) {
              return $unit->isColonialLight();
            });
          }
          // TODO: if not possible draw piece from bag
          break;
      }
    }

    return $options;
  }
}
