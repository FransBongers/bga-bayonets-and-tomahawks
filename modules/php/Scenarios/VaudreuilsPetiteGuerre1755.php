<?php

namespace BayonetsAndTomahawks\Scenarios;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Spaces;

class VaudreuilsPetiteGuerre1755 extends \BayonetsAndTomahawks\Models\Scenario
{
  public function __construct()
  {
    parent::__construct();
    $this->id = VaudreuilsPetiteGuerre1755;
    $this->name = clienttranslate("Vaudreil's Petite Guerre 1755");
    $this->number = 1;
    $this->startYear = 1755;
    $this->duration = 1;
    $this->reinforcements = [
      1755 => [
        POOL_FLEETS => 5,
        POOL_BRITISH_METROPOLITAN_VOW => 3,
        POOL_BRITISH_COLONIAL_VOW => 8,
        POOL_FRENCH_METROPOLITAN_VOW => 3,
      ]
    ];
    $this->victoryMarkerLocation = VICTORY_POINTS_FRENCH_1;
    $this->victoryThreshold = [
      BRITISH => [
        1755 => 1
      ],
      FRENCH => [
        1755 => 1
      ]
    ];
    $this->indianSetup = [
      // Indian Setup
      MIRAMICHY => [
        'id' => MIRAMICHY,
        'units' => [
          MICMAC,
        ]
      ],
      GRAND_SAULT => [
        'id' => GRAND_SAULT,
        'units' => [
          MALECITE,
        ]
      ],
      LES_TROIS_RIVIERES => [
        'id' => LES_TROIS_RIVIERES,
        'units' => [
          ABENAKI,
        ]
      ],
      MONTREAL => [
        'id' => MONTREAL,
        'units' => [
          MISSISSAGUE,
        ]
      ],
      TORONTO => [
        'id' => TORONTO,
        'units' => [
          KAHNAWAKE,
        ]
      ],
      GENNISHEYO => [
        'id' => GENNISHEYO,
        'units' => [
          SENECA,
        ]
      ],
      KITHANINK => [
        'id' => KITHANINK,
        'units' => [
          DELAWARE,
        ]
      ],
      DIIOHAGE => [
        'id' => DIIOHAGE,
        'units' => [
          MINGO,
        ]
      ],
      FORKS_OF_THE_OHIO => [
        'id' => FORKS_OF_THE_OHIO,
        'units' => [
          CHAOUANON,
        ]
      ],
      LE_DETROIT => [
        'id' => LE_DETROIT,
        'units' => [
          OUTAOUAIS,
        ]
      ],
    ];
    $this->locations = [
      // French Setup
      CHIGNECTOU => [
        'id' => CHIGNECTOU,
        'units' => [
          BEAUSEJOUR,
          LANGIS,
        ]
      ],
      LOUISBOURG_BASTION_1 => [
        'id' => LOUISBOURG_BASTION_1,
        'units' => [
          BASTION,
        ]
      ],
      LOUISBOURG_BASTION_2 => [
        'id' => LOUISBOURG_BASTION_2,
        'units' => [
          BASTION,
        ]
      ],
      POINTE_SAINTE_ANNE => [
        'id' => POINTE_SAINTE_ANNE,
        'units' => [
          BOISHEBERT,
        ]
      ],
      QUEBEC => [
        'id' => QUEBEC,
        'units' => [
          RIGAUD,
          CANADIENS,
          CANADIENS,
          CANONNIERS_BOMBARDIERS,
          CANONNIERS_BOMBARDIERS,
        ]
      ],
      QUEBEC_BASTION_1 => [
        'id' => QUEBEC_BASTION_1,
        'units' => [
          BASTION,
        ]
      ],
      QUEBEC_BASTION_2 => [
        'id' => QUEBEC_BASTION_2,
        'units' => [
          BASTION,
        ]
      ],
      TICONDEROGA => [
        'id' => TICONDEROGA,
        'units' => [
          SAINT_FREDERIC,
        ]
      ],
      MONTREAL => [
        'id' => MONTREAL,
        'units' => [
          BEAUJEU,
          LACORNE,
        ]
      ],
      NIAGARA => [
        'id' => NIAGARA,
        'units' => [
          LERY,
        ]
      ],
      FORKS_OF_THE_OHIO => [
        'id' => FORKS_OF_THE_OHIO,
        'units' => [
          LIGNERY,
          VILLIERS,
          DUQUESNE,
        ]
      ],
      LES_ILLINOIS => [
        'id' => LES_ILLINOIS,
        'units' => [
          AUBRY,
          BELESTRE,
        ]
      ],
      LE_DETROIT => [
        'id' => LE_DETROIT,
        'units' => [
          LANGLADE,
        ]
      ],
      // British
      HALIFAX => [
        'id' => HALIFAX,
        'units' => [
          GOREHAM,
          B_40TH_45TH_47TH,
          ROYAL_ARTILLERY
        ]
      ],
      ANNAPOLIS_ROYAL => [
        'id' => ANNAPOLIS_ROYAL,
        'units' => [
          ANNE,
        ]
      ],
      RUMFORD => [
        'id' => RUMFORD,
        'units' => [
          ROGERS,
        ]
      ],
      BOSTON => [
        'id' => BOSTON,
        'units' => [
          B_50TH_51ST,
          ROYAL_ARTILLERY,
        ]
      ],
      ALBANY => [
        'id' => ALBANY,
        'units' => [
          MOHAWK,
        ]
      ],
      NEW_YORK => [
        'id' => NEW_YORK,
        'units' => [
          B_35TH_NEW_YORK_COMPANIES
        ]
      ],
      CHARLES_TOWN => [
        'id' => CHARLES_TOWN,
        'units' => [
          JOHNSON
        ]
      ],
      OSWEGO => [
        'id' => OSWEGO,
        'units' => [
          ONTARIO
        ]
      ],
      WILLS_CREEK => [
        'id' => WILLS_CREEK,
        'units' => [
          WASHINGTON
        ]
      ],
    ];
    $this->pools = [
      POOL_NEUTRAL_INDIANS => [
        'units' => [
          IROQUOIS,
          IROQUOIS,
          IROQUOIS,
          CHEROKEE,
          CHEROKEE,
        ]
      ],
      POOL_FLEETS => [
        'units' => [
          BOSCAWEN,
          DURELL,
          HOLBURNE,
          ROYAL_NAVY,
          DE_LA_MOTTE,
          MARINE_ROYALE,
        ]
      ],
      POOL_BRITISH_COMMANDERS => [
        'units' => [
          BRADSTREET,
        ]
      ],
      POOL_BRITISH_COLONIAL_LIGHT => [
        'units' => [
          ARMSTRONG,
          PUTNAM,
        ]
      ],
      POOL_BRITISH_LIGHT => [],
      POOL_BRITISH_ARTILLERY => [
        'units' => [
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY,
        ]
      ],
      POOL_BRITISH_FORTS => [
        'units' => [
          AUGUSTA,
          BEDFORD,
          CROWN_POINT,
          CUMBERLAND,
          EDWARD,
          FREDERICK,
          HERKIMER,
          LIGONIER,
          PITT,
          POWNALL,
          STANWIX,
          TICONDEROGA,
          WILLIAM_HENRY,
        ]
      ],
      POOL_BRITISH_METROPOLITAN_VOW => [
        'units' => [
          B_1ST_ROYAL_AMERICAN,
          B_44TH_48TH,
          ROYAL_HIGHLAND,
          VOW_PICK_TWO_ARTILLERY_BRITISH,
          VOW_FEWER_TROOPS_BRITISH
        ],
      ],
      POOL_BRITISH_COLONIAL_VOW => [
        'units' => [
          NEW_ENGLAND,
          NEW_ENGLAND,
          NEW_ENGLAND,
          NEW_ENGLAND,
          NEW_ENGLAND,
          NYORK_NJ,
          NYORK_NJ,
          VIRGINIA_S,
          VIRGINIA_S,
          VOW_PICK_ONE_COLONIAL_LIGHT,
          VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK,
          VOW_FEWER_TROOPS_COLONIAL,
          VOW_FEWER_TROOPS_PUT_BACK_COLONIAL
        ],
      ],
      POOL_BRITISH_COLONIAL_VOW_BONUS => [],
      POOL_FRENCH_COMMANDERS => [
        'units' => [
          POUCHOT,
        ]
      ],
      POOL_FRENCH_FORTS => [
        'units' => [
          CARILLON,
          FRONTENAC,
          JACQUES_CARTIER,
          F_LEVIS,
          MASSIAC,
          NIAGARA,
        ]
      ],
      POOL_FRENCH_ARTILLERY => [
        'units' => [
          CANONNIERS_BOMBARDIERS,
        ]
      ],
      POOL_FRENCH_METROPOLITAN_VOW => [
        'units' => [
          BEARN_GUYENNE,
          ARTOIS_BOURGOGNE,
          DE_LA_MARINE,
          LANGUEDOC_LA_REINE,
          VOW_FEWER_TROOPS_FRENCH,
        ],
      ]
    ];
  }

  public function getYearEndBonus($faction, $year)
  {
    $spaces = Spaces::getAll()->toArray();
    if ($faction === BRITISH) {
      return $this->getYearEndBonusBritish($spaces);
    } else {
      return $this->getYearEndBonusFrench($spaces);
    }
  }

  private function getYearEndBonusBritish($spaces)
  {
    $countingSpaces = Utils::filter($spaces, function ($space) {
      return $space->isHomeSpace(FRENCH) && $space->isVictorySpace() && $space->isControlledBy(BRITISH);
    });

    if (count($countingSpaces) >= 2) {
      return 2;
    }

    return 0;
  }

  private function getYearEndBonusFrench($spaces)
  {
    $countingSpaces = Utils::filter($spaces, function ($space) {
      return $space->isSettledSpace(BRITISH) && $space->isControlledBy(FRENCH);
    });

    if (count($countingSpaces) >= 1) {
      return 2;
    }

    return 0;
  }
}
