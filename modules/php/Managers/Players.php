<?php

namespace BayonetsAndTomahawks\Managers;

use BayonetsAndTomahawks\Core\Game;
use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\Locations;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\PlayersExtra;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class Players extends \BayonetsAndTomahawks\Helpers\DB_Manager
{
  protected static $table = 'player';
  protected static $primary = 'player_id';
  protected static function cast($row)
  {
    return new \BayonetsAndTomahawks\Models\Player($row);
  }

  public static function setupNewGame($players, $options)
  {
    // Globals::setPlayers($players);
    // Create players
    $gameInfos = Game::get()->getGameinfos();
    $colors = $gameInfos['player_colors'];
    $query = self::DB()->multipleInsert([
      'player_id',
      'player_color',
      'player_canal',
      'player_name',
      'player_avatar',
      'player_score',
    ]);

    $values = [];
    foreach ($players as $playerId => $player) {
      $color = array_shift($colors);
      $values[] = [$playerId, $color, $player['player_canal'], $player['player_name'], $player['player_avatar'], 0];
    }

    $query->values($values);

    // Game::get()->reattributeColorsBasedOnPreferences($players, $gameInfos['player_colors']);
    Game::get()->reloadPlayersBasicInfos();
    // PlayersExtra::setupNewGame();
  }

  public static function getActiveId()
  {
    return (int) Game::get()->getActivePlayerId();
  }

  public static function getCurrentId()
  {
    return Game::get()->getCurrentPId();
  }

  public static function getAll()
  {
    $players = self::DB()->get(false);
    return $players;
  }

  /*
   * get : returns the Player object for the given player ID
   */
  public static function get($playerId = null)
  {
    $playerId = $playerId ?: self::getActiveId();
    return self::DB()
      ->where($playerId)
      ->getSingle();
  }

  public static function getOther($playerId = null)
  {
    $playerId = $playerId ?: self::getActiveId();
    return Utils::array_find(Players::getAll()->toArray(), function ($player) use ($playerId) {
      return $player->getId() !== $playerId;
    });
  }

  public static function incScore($playerId, $increment)
  {
    $value = self::get($playerId)->getScore() + $increment;
    return self::DB()->update(['player_score' => $value], $playerId);
  }

  public static function setPlayerScoreAux($playerId, $value)
  {
    return self::DB()->update(['player_score_aux' => $value], $playerId);
  }

  public static function setPlayerScore($playerId, $value)
  {
    return self::DB()->update(['player_score' => $value], $playerId);
  }


  public function getMany($playerIds)
  {
    $players = self::DB()
      ->whereIn($playerIds)
      ->get();
    return $players;
  }

  public static function getActive()
  {
    return self::get();
  }

  public static function getCurrent()
  {
    return self::get(self::getCurrentId());
  }

  public static function getPlayerForFaction($faction)
  {
    return Utils::array_find(self::getAll()->toArray(), function ($player) use ($faction) {
      return $player->getFaction() === $faction;
    });
  }

  public static function getPlayersForFactions()
  {
    $players = Players::getAll()->toArray();
    $data = [];
    foreach ([BRITISH, FRENCH] as $faction) {
      $data[$faction] = Utils::array_find($players, function ($player) use ($faction) {
        return $player->getFaction() === $faction;
      });
    }

    return $data;
  }

  public static function getPlayerIdsForFactions()
  {
    $players = Players::getAll()->toArray();
    $data = [];
    foreach ([BRITISH, FRENCH] as $faction) {
      $data[$faction] = Utils::array_find($players, function ($player) use ($faction) {
        return $player->getFaction() === $faction;
      })->getId();
    }

    return $data;
  }

  public function getNextId($player)
  {
    $playerId = is_int($player) ? $player : $player->getId();

    $table = Game::get()->getNextPlayerTable();
    return (int) $table[$playerId];
  }

  public function getPrevId($player)
  {
    $playerId = is_int($player) ? $player : $player->getId();

    $table = Game::get()->getPrevPlayerTable();
    $playerId = (int) $table[$playerId];

    return $playerId;
  }

  /*
   * Return the number of players
   */
  public function count()
  {
    return self::DB()->count();
  }

  /*
   * getUiData : get all ui data of all players
   */
  public static function getUiData($playerId)
  {
    return self::getAll()->map(function ($player) use ($playerId) {
      return $player->jsonSerialize($playerId);
    });
  }

  public static function getPlayerOrder()
  {
    $players = self::getAll()->toArray();
    usort($players, function ($a, $b) {
      return $a->getNo() - $b->getNo();
    });
    $playerOrder = array_map(function ($player) {
      return $player->getId();
    }, $players);
    return $playerOrder;
  }

  /*
   * Get current turn order according to first player variable
   */
  public function getTurnOrder($firstPlayer = null)
  {
    $players = self::getAll()->toArray();
    usort($players, function ($a, $b) {
      return $a->getNo() - $b->getNo();
    });
    $playerOrder = array_map(function ($player) {
      return $player->getId();
    }, $players);
    return $playerOrder;
  }

  /**
   * This activate next player
   */
  public function activeNext()
  {
    $playerId = self::getActiveId();
    $nextPlayer = self::getNextId((int) $playerId);

    Game::get()->gamestate->changeActivePlayer($nextPlayer);
    return $nextPlayer;
  }

  /**
   * This allow to change active player
   */
  public function changeActive($playerId)
  {
    Game::get()->gamestate->changeActivePlayer($playerId);
  }

  public static function otherFaction($playerFaction)
  {
    return $playerFaction === BRITISH ? FRENCH : BRITISH;
  }

  public static function setWinner($player)
  {
    // TODO: update scores for scenarios where players van win with 'negative points'
    // Set score relative to threshold?
  }

  public static function scoreVictoryPoints($player, $points)
  {
    $vpMarker = Markers::get(VICTORY_MARKER);

    $playersPerFaction = self::getPlayersForFactions();
    $scores = [
      BRITISH => $playersPerFaction[BRITISH]->getScore(),
      FRENCH => $playersPerFaction[FRENCH]->getScore(),
    ];
    $leadFaction = $scores[BRITISH] > $scores[FRENCH] ? BRITISH : FRENCH;
    $otherFaction = BTHelpers::getOtherFaction($leadFaction);

    $playerFaction = $player->getFaction();

    if ($playerFaction === $leadFaction) {
      $scores[$leadFaction] += $points;
    } else {
      $scores[$leadFaction] -= $points;
      if ($scores[$leadFaction] <= 0) {
        $scores[$leadFaction]--;
        $scores[$otherFaction] = $scores[$leadFaction] * -1;
        $scores[$leadFaction] = 0;
      }
    }

    $playersPerFaction[BRITISH]->setScore($scores[BRITISH]);
    $playersPerFaction[FRENCH]->setScore($scores[FRENCH]);

    $newLeadFaction = $scores[BRITISH] > $scores[FRENCH] ? BRITISH : FRENCH;

    $vpMarkerPoints = $scores[$newLeadFaction] % 10;
    $vpMarkerPoints = $vpMarkerPoints === 0 ? 10 : $vpMarkerPoints;

    $vpMarkerLocation = Locations::victoryPointsTrack($newLeadFaction, $vpMarkerPoints);

    $vpMarker->setLocation($vpMarkerLocation);
    if ($scores[$newLeadFaction] > 10) {
      $vpMarker->setState(1);
    } else if ($scores[$newLeadFaction] <= 10) {
      $vpMarker->setState(0);
    }

    $player = $playersPerFaction[$playerFaction];
    $otherPlayer = $playersPerFaction[BTHelpers::getOtherFaction($playerFaction)];

    Notifications::scoreVictoryPoints($player, $otherPlayer, $vpMarker, $points);
  }
}
