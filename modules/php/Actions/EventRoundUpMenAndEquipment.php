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
use BayonetsAndTomahawks\Managers\Cards;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Managers\Units;
use BayonetsAndTomahawks\Models\Player;

class EventRoundUpMenAndEquipment extends \BayonetsAndTomahawks\Models\AtomicAction
{
  public function getState()
  {
    return ST_EVENT_ROUND_UP_MEN_AND_EQUIPMENT;
  }

  // .########..########..########.......###.....######..########.####..#######..##....##
  // .##.....##.##.....##.##............##.##...##....##....##.....##..##.....##.###...##
  // .##.....##.##.....##.##...........##...##..##..........##.....##..##.....##.####..##
  // .########..########..######......##.....##.##..........##.....##..##.....##.##.##.##
  // .##........##...##...##..........#########.##..........##.....##..##.....##.##..####
  // .##........##....##..##..........##.....##.##....##....##.....##..##.....##.##...###
  // .##........##.....##.########....##.....##..######.....##....####..#######..##....##

  public function stPreEventRoundUpMenAndEquipment()
  {
  }


  // ....###....########...######....######.
  // ...##.##...##.....##.##....##..##....##
  // ..##...##..##.....##.##........##......
  // .##.....##.########..##...####..######.
  // .#########.##...##...##....##........##
  // .##.....##.##....##..##....##..##....##
  // .##.....##.##.....##..######....######.

  public function argsEventRoundUpMenAndEquipment()
  {

    // Notifications::log('argsEventRoundUpMenAndEquipment',[]);
    return [
      'options' => $this->getOptions(),
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

  public function actPassEventRoundUpMenAndEquipment()
  {
    $player = self::getPlayer();
    // Stats::incPassActionCount($player->getId(), 1);
    Engine::resolve(PASS);
  }

  public function actEventRoundUpMenAndEquipment($args)
  {
    self::checkAction('actEventRoundUpMenAndEquipment');
    $selectedReducedUnitIds = $args['selectedReducedUnitIds'];
    $placedUnit = $args['placedUnit'];

    $options = $this->getOptions();

    if (count($selectedReducedUnitIds) > 0) {
      $this->flipReducedUnitsToFull($selectedReducedUnitIds, $options['reduced']);
    } else {
      $this->placeUnitFromLossesBox($placedUnit, $options['lossesBox']);
    }

    $this->resolveAction($args);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  private function flipReducedUnitsToFull($selectedReducedUnitIds, $possibleUnits)
  {
    if (count($selectedReducedUnitIds) > 2) {
      throw new \feException("ERROR 034");
    }
    $player = self::getPlayer();
    foreach ($selectedReducedUnitIds as $unitId) {
      $unit = Utils::array_find($possibleUnits, function ($possibleUnit) use ($unitId) {
        return $unitId === $possibleUnit->getId();
      });
      if ($unit === null) {
        throw new \feException("ERROR 035");
      }
      $unit->flipToFull($player);
    }
  }

  private function placeUnitFromLossesBox($placedUnit, $lossesBoxOptions)
  {
    if ($placedUnit === null) {
      throw new \feException("ERROR 036");
    };
    $unitId = $placedUnit['unitId'];
    $spaceId = $placedUnit['spaceId'];
    if (!isset($lossesBoxOptions[$unitId])) {
      throw new \feException("ERROR 037");
    }
    $option = $lossesBoxOptions[$unitId];
    if (!in_array($spaceId, $option['spaceIds'])) {
      throw new \feException("ERROR 038");
    }
    $unit = $option['unit'];
    $space = Spaces::get($spaceId);
    $unit->setLocation($spaceId);
    $player = self::getPlayer();
    Notifications::placeUnits($player, [$unit], $space, $player->getFaction());
  }

  private function mapSpacesToSpaceIds($spaces)
  {
    return array_map(function ($space) {
      return $space->getId();
    }, $spaces);
  }

  private function getBritishOptions()
  {
    $units = Units::getAll()->toArray();
    $reducedBritishUnits = Utils::filter($units, function ($unit) {
      return $unit->getFaction() === BRITISH && $unit->isReduced() && !$unit->isFort();
    });

    $unitsInLossesBox = Utils::filter($units, function ($unit) {
      return $unit->getLocation() === Locations::lossesBox(BRITISH);
    });
    $lossesBox = [];

    if (count($unitsInLossesBox) > 0) {
      $possibleSpaces = Utils::filter(Spaces::getControlledBy(BRITISH), function ($space) {
        return $space->getHomeSpace() !== null;
      });
      foreach ($unitsInLossesBox as $unit) {
        $unitColony = $unit->getColony();
        if ($unitColony !== null) {
          $lossesBox[$unit->getId()] = [
            'unit' => $unit,
            'spaceIds' => $this->mapSpacesToSpaceIds(Utils::filter($possibleSpaces, function ($space) use ($unitColony) {
              return $space->getColony() === $unitColony;
            }))
          ];
        } else if ($unit->isIndian()) {
          $unitIndianVillage = $unit->getCounterId();
          $indianVillage = Utils::array_find($possibleSpaces, function ($space) use ($unitIndianVillage) {
            return $unitIndianVillage === $space->getIndianVillage();
          });

          $lossesBox[$unit->getId()] = [
            'unit' => $unit,
            'spaceIds' => $indianVillage !== null ? [$indianVillage->getId()] : $this->mapSpacesToSpaceIds($possibleSpaces),
          ];
        } else {
          $lossesBox[$unit->getId()] = [
            'unit' => $unit,
            'spaceIds' => $this->mapSpacesToSpaceIds($possibleSpaces),
          ];
        }
      }
    }

    return [
      'reduced' => $reducedBritishUnits,
      'lossesBox' => $lossesBox,
    ];
  }

  private function getFrenchOptions()
  {
    $units = Units::getAll()->toArray();
    // $frenchBrigades = Utils::filter($units, function ($unit) {
    //   return $unit->getFaction() === FRENCH &&
    //     $unit->isBrigade() &&
    //     in_array($unit->getLocation(), SPACES);
    // });
    // $metropolitan = Utils::filter($frenchBrigades, function ($unit) {
    //   return $unit->isMetropolitanBrigade();
    // });
    // if (count($metropolitan) > 0) {
    //   return $metropolitan;
    // } else {
    //   return $frenchBrigades;
    // }
  }

  private function getOptions()
  {
    $info = $this->ctx->getInfo();
    $faction = $info['faction'];

    if ($faction === BRITISH) {
      return $this->getBritishOptions();
    } else {
      return $this->getFrenchOptions();
    }
  }
}
