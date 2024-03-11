<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Helpers\Utils;

use const BayonetsAndTomahawks\OPTION_STARTING_MAP_AGE_OF_REFORMATION_PROMO_VARIANT;

/**
 * Cards
 */
class Cards extends \BayonetsAndTomahawks\Helpers\Pieces
{
  protected static $table = 'cards';
  protected static $prefix = 'card_';
  protected static $customFields = ['extra_data'];
  protected static $autoremovePrefix = false;
  protected static $autoreshuffle = false;
  protected static $autoIncrement = false;

  protected static function cast($card)
  {
    return self::getCardInstance($card['card_id'], $card);
  }

  // private static function getClassPrefix($cardId)
  // {
  //   if (Utils::startsWith($cardId, 'Victory')) {
  //     return 'Victory';
  //   }
  //   if (Utils::startsWith($cardId, 'Empire')) {
  //     return 'Empire';
  //   }
  //   return 'Tableau';
  // }

  public static function getCardInstance($id, $data = null)
  {
    // $prefix = self::getClassPrefix($id);

    $className = "\BayonetsAndTomahawks\Cards\\$id";
    return new $className($data);
  }

  //////////////////////////////////
  //////////////////////////////////
  //////////// GETTERS //////////////
  //////////////////////////////////
  //////////////////////////////////

  public static function getCardsInPlay() {
    return [
      BRITISH => self::getTopOf(Locations::cardInPlay(BRITISH)),
      FRENCH => self::getTopOf(Locations::cardInPlay(FRENCH)),
      INDIAN => self::getTopOf(Locations::cardInPlay(INDIAN)),
    ];
  }

  // public static function getOfTypeInLocation($type, $location)
  // {
  //   return self::getSelectQuery()
  //     ->where(static::$prefix . 'id', 'LIKE', $type . '%')
  //     ->where(static::$prefix . 'location', 'LIKE', $location . '%')
  //     ->get()
  //     ->toArray();
  // }

  // public static function getUiData()
  // {
  //   return self::getPool()
  //     ->merge(self::getInLocationOrdered('inPlay'))
  //     ->merge(self::getInLocation('base_%'))
  //     ->merge(self::getInLocation('projects_%'))
  //     ->ui();
  // }

  // public static function getStaticData()
  // {
  //   $cards = Cards::getAll();
  //   $staticData = [];
  //   foreach($cards as $cardId => $card) {
  //     if ($card->getType() !== TABLEAU_CARD) {
  //       continue;
  //     }
  //     $staticData[explode('_',$card->getId())[0]] = $card->getStaticData();
  //   }
  //   return $staticData;
  // }

  // ..######..########.########.##.....##.########.
  // .##....##.##..........##....##.....##.##.....##
  // .##.......##..........##....##.....##.##.....##
  // ..######..######......##....##.....##.########.
  // .......##.##..........##....##.....##.##.......
  // .##....##.##..........##....##.....##.##.......
  // ..######..########....##.....#######..##.......



  private static function setupLoadCards()
  {
    // Load list of cards
    include dirname(__FILE__) . '/../Cards/list.inc.php';

    Notifications::log('cardIds', $cardIds);
    $scenario = Globals::getScenario();
    Notifications::log('scenario', $scenario);
    // return;
    foreach ($cardIds as $cId) {
      $card = self::getCardInstance($cId);

      $location = '';
      $extraData = null;
      // $location = 'deck';

      $faction = $card->getFaction();
      if ($faction === INDIAN) {
        $location = Locations::campaignDeck(INDIAN);
      } else if (!self::yearsMatchScenario($card, $scenario['cards'])) {
        $location = Locations::cardPool($faction);
      } else if ($card->getBuildUpDeck()) {
        $location = Locations::buildUpDeck($faction);
      } else {
        $location = Locations::campaignDeck($faction);
      }

      $cards[$cId] = [
        'id' => $cId,
        'location' => $location,
        'extra_data' => json_encode($extraData)
      ];
    }
    Notifications::log('cards', $cards);
    // // Create the cards
    self::create($cards, null);
    foreach([BRITISH, FRENCH] as $faction) {
      self::shuffle(Locations::buildUpDeck($faction));
      self::shuffle(Locations::campaignDeck($faction));
    }
    self::shuffle(Locations::campaignDeck(INDIAN));
  }

  /* Creation of the cards */
  public static function setupNewGame($players = null, $options = null)
  {
    self::setupLoadCards();
  }

  private static function yearsMatchScenario($card, $scenarioCards)
  {
    $years = $card->getYears();
    if ($card->getYears() === null) {
      return true;
    }
    $faction = $card->getFaction();
    // check if at least one years match
    return Utils::array_some($years, function ($year) use ($scenarioCards, $faction) {
      return in_array($year, $scenarioCards[$faction]);
    });
  }
}
