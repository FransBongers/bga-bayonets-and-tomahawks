<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Helpers\Utils;

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

  public static function isCardInPlay($faction, $cardId)
  {
    $card = self::getTopOf(Locations::cardInPlay($faction));
    if ($card === null) {
      return false;
    } else {
      return $card->getId() === $cardId;
    }
  }

  public static function getCardsInPlay()
  {
    $cards = self::getSelectQuery()
      ->where(static::$prefix . 'location', 'LIKE', 'cardInPlay_' . '%')
      ->get()
      ->toArray();

    $data = [
      BRITISH => null,
      FRENCH => null,
      INDIAN => null,
    ];

    foreach ($cards as $card) {
      $data[$card->getFaction()] = $card;
    }

    return $data;
    // return [
    //   BRITISH => self::getTopOf(Locations::cardInPlay(BRITISH)),
    //   FRENCH => self::getTopOf(Locations::cardInPlay(FRENCH)),
    //   INDIAN => self::getTopOf(Locations::cardInPlay(INDIAN)),
    // ];
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

  public static function setupDecksForYear($year)
  {
    // Move all cards that are still in play to the pool
    $cards = Utils::filter(Cards::getAll()->toArray(), function ($card) {
      return $card->getLocation() !== REMOVED_FROM_PLAY;
    });
    Cards::move(BTHelpers::returnIds($cards), Locations::cardPool());


    $britishBuildupDeck = [];
    $britishCampaignDeck = [];
    $frenchBuildupDeck = [];
    $frenchCampaignDeck = [];
    $indianCampaignDeck = [];

    $cards = Cards::getInLocation(Locations::cardPool())->toArray();

    foreach ($cards as $card) {
      $cardId = $card->getId();
      $faction = $card->getFaction();
      $usedInYears = $card->getYears();

      if ($usedInYears !== null && !in_array($year, $usedInYears)) {
        continue;
      }

      if ($faction === INDIAN) {
        $indianCampaignDeck[] = $cardId;
      } else if ($faction === FRENCH && $card->getBuildUpDeck()) {
        $frenchBuildupDeck[] = $cardId;
      } else if ($faction === FRENCH) {
        $frenchCampaignDeck[] = $cardId;
      } else if ($faction === BRITISH && $card->getBuildUpDeck()) {
        $britishBuildupDeck[] = $cardId;
      } else if ($faction === BRITISH) {
        $britishCampaignDeck[] = $cardId;
      }
    }
    Cards::move($britishBuildupDeck, Locations::buildUpDeck(BRITISH));
    Cards::move($britishCampaignDeck, Locations::campaignDeck(BRITISH));
    Cards::move($frenchBuildupDeck, Locations::buildUpDeck(FRENCH));
    Cards::move($frenchCampaignDeck, Locations::campaignDeck(FRENCH));
    Cards::move($indianCampaignDeck, Locations::campaignDeck(INDIAN));

    foreach ([BRITISH, FRENCH] as $faction) {
      self::shuffle(Locations::buildUpDeck($faction));
      self::shuffle(Locations::campaignDeck($faction));
    }
    self::shuffle(Locations::campaignDeck(INDIAN));
  }

  private static function setupLoadCards()
  {
    // Load list of cards
    include dirname(__FILE__) . '/../Cards/list.inc.php';

    $scenario = Scenarios::get();

    // return;
    foreach ($cardIds as $cId) {

      $location = Locations::cardPool();
      $extraData = null;

      $cards[$cId] = [
        'id' => $cId,
        'location' => $location,
        'extra_data' => json_encode($extraData)
      ];
    }

    // Create the cards
    self::create($cards, null);
  }

  /* Creation of the cards */
  public static function setupNewGame($players = null, $options = null)
  {
    self::setupLoadCards();
  }
}
