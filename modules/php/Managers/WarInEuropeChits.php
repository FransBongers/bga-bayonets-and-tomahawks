<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Scenarios;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Models\WarInEuropeChit;

/**
 * extends
 */
class WarInEuropeChits extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'wie_chits';
  protected static $prefix = 'wie_chit_';
  protected static $customFields = [
    'value'
  ];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;

  protected static function cast($chit)
  {
    // return [
    //   'id' => $token['marker_id'],
    //   'location' => $token['marker_location'],
    //   'state' => intval($token['marker_state']),
    // ];
    return new WarInEuropeChit($chit);
  }


  // ..######...########.########.########.########.########...######.
  // .##....##..##..........##.......##....##.......##.....##.##....##
  // .##........##..........##.......##....##.......##.....##.##......
  // .##...####.######......##.......##....######...########...######.
  // .##....##..##..........##.......##....##.......##...##.........##
  // .##....##..##..........##.......##....##.......##....##..##....##
  // ..######...########....##.......##....########.##.....##..######.


  public static function drawChit($faction)
  {
    $player = Players::getPlayerForFaction($faction);

    $placeholder = Locations::wieChitPlaceholder($faction);
    $pool = Locations::wieChitPool($faction);

    $currentChit = self::getTopOf($placeholder);
    $draw = self::getTopOf($pool);

    $placeChit = false;

    if ($currentChit === null) {
      $draw->setLocation($placeholder);
      $draw->setRevealed(0);
      $placeChit = true;
    } else if ($draw->getValue() > $currentChit->getValue()) {
      $draw->setLocation($placeholder);
      $draw->setRevealed(0);
      $currentChit->setLocation($pool);
      $placeChit = true; 
    }
    Notifications::drawWieChit($player, $currentChit, $draw, $placeChit);

    self::shuffle($pool);
  }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......

  /* Creation of the tokens */
  public static function setupNewGame($players = null, $options = null)
  {
    $chits = [];

    $chitValues = [0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2];

    foreach ([BRITISH, FRENCH] as $faction) {
      shuffle($chitValues);
      foreach ($chitValues as $index => $value) {
        $id = implode('_', ['wieChit', $faction, $index]);
        $chits[$id] = [
          'id' => $id,
          'location' => Locations::wieChitPool($faction),
          'state' => 0,
          'value' => $value,
        ];
      }
    }

    self::create($chits, null);

    self::shuffle(Locations::wieChitPool(BRITISH));
    self::shuffle(Locations::wieChitPool(FRENCH));
  }
}
