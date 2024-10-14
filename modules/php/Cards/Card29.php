<?php

namespace BayonetsAndTomahawks\Cards;

use BayonetsAndTomahawks\Core\Globals;
use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\BTHelpers;
use BayonetsAndTomahawks\Helpers\GameMap;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Players;
use BayonetsAndTomahawks\Managers\Spaces;

class Card29 extends \BayonetsAndTomahawks\Models\Card
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->actionPoints = [
      [
        'id' => LIGHT_AP
      ],
      [
        'id' => SAIL_ARMY_AP
      ]
    ];
    $this->id = 'Card29';
    $this->event = [
      'id' => CHEROKEE_DIPLOMACY,
      'title' => clienttranslate('Cherokee Diplomacy'),
      AR_START => true,
    ];
    $this->faction = FRENCH;
    $this->initiativeValue = 2;
  }

  public function resolveARStart($ctx)
  {
    $frenchPlayer = Players::getPlayerForFaction(FRENCH);
    $year = BTHelpers::getYear();
    $currentControl = Globals::getControlCherokee();
    if (in_array($year, [1755, 1756]) || $currentControl === BRITISH) {
      GameMap::awardRaidPoints($frenchPlayer, FRENCH, 2);
      Notifications::message(clienttranslate('${player_name} gains ${tkn_boldText} Raid Points'),[
        'player' => $frenchPlayer,
        'tkn_boldText' => 2,
      ]);
      
      return;
    } else if ($currentControl === FRENCH) {
      Notifications::message(clienttranslate('The French already control the Cherokee Indian Nation'),[]);
      return;
    }

    $spaces = [CHARLES_TOWN, NINETY_SIX, BEVERLEY, WILLS_CREEK, WINCHESTER, ALEXANDRIA, PHILADELPHIA, CARLISLE, EASTON, KEOWEE, CHOTE, MEKEKASINK, RAYS_TOWN, SHAMOKIN, GNADENHUTTEN, MINISINK, NEW_YORK];

    $frenchControlledBritishHomeSpaces = Utils::filter(Spaces::get($spaces)->toArray(), function ($space) {
      return $space->getHomeSpace() === BRITISH && $space->getControl() === FRENCH;
    });

    if (count($frenchControlledBritishHomeSpaces) >= 2) {
      GameMap::performIndianNationControlProcedure(CHEROKEE, FRENCH);
    } else {
      Notifications::message(clienttranslate('The French do not control the required British Home Spaces: event does not trigger'));
    }
  }
}
