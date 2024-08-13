<?php

namespace BayonetsAndTomahawks\Scenarios;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Spaces;

class LoudounsGamble1757 extends \BayonetsAndTomahawks\Models\Scenario
{
  public function __construct()
  {
    parent::__construct();
    $this->id = LoudounsGamble1757;
    $this->name = clienttranslate("Loudoun's Gamble 1757");
    $this->number = 2;
    $this->startYear = 1757;
    $this->duration = 1;
    $this->reinforcements = [
      1757 => [
        POOL_FLEETS => 7,
        POOL_BRITISH_METROPOLITAN_VOW => 6,
        POOL_BRITISH_COLONIAL_VOW => 8,
        POOL_FRENCH_METROPOLITAN_VOW => 3,
      ]
    ];
    $this->victoryMarkerLocation = VICTORY_POINTS_FRENCH_1;
    $this->victoryThreshold = [
      BRITISH => [
        1757 => 1
      ],
      FRENCH => [
        1757 => 1
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
      MIRAMICHY => [
        'id' => MIRAMICHY,
        'units' => [
          BOISHEBERT,
        ]
      ],
      LOUISBOURG => [
        'id' => LOUISBOURG,
        'units' => [
          ARTOIS_BOURGOGNE,
          DE_LA_MARINE
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
      QUEBEC => [
        'id' => QUEBEC,
        'units' => [
          MONTCALM,
          CANONNIERS_BOMBARDIERS,
          CANONNIERS_BOMBARDIERS,
          LERY,
          LANGUEDOC_LA_REINE,
          CANADIENS,
          CANADIENS
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
          LANGLADE,
          CARILLON
        ]
      ],
      MONTREAL => [
        'id' => MONTREAL,
        'units' => [
          POUCHOT,
          RIGAUD,
          LACORNE,
          LANGIS,
          VILLIERS,
          BEARN_GUYENNE,
          LA_SARRE_ROYAL_ROUSSILLON,
        ]
      ],
      NIAGARA => [
        'id' => NIAGARA,
        'units' => [
          NIAGARA
        ]
      ],
      FORKS_OF_THE_OHIO => [
        'id' => FORKS_OF_THE_OHIO,
        'units' => [
          LIGNERY,
          DUQUESNE,
        ]
      ],
      LES_ILLINOIS => [
        'id' => LES_ILLINOIS,
        'units' => [
          AUBRY,
        ]
      ],
      LE_DETROIT => [
        'id' => LE_DETROIT,
        'units' => [
          BELESTRE,
        ]
      ],
      // British
      CHIGNECTOU => [
        'id' => CHIGNECTOU,
        'units' => [
          CUMBERLAND,
        ],
        'tokens' => [
          // TODO
          'britishControl'
        ]
      ],
      HALIFAX => [
        'id' => HALIFAX,
        'units' => [
          GOREHAM,
          B_40TH_45TH_47TH,
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY
        ]
      ],
      ANNAPOLIS_ROYAL => [
        'id' => ANNAPOLIS_ROYAL,
        'units' => [
          ANNE
        ]
      ],
      BOSTON => [
        'id' => BOSTON,
        'units' => [
          B_44TH_48TH,
          ROYAL_ARTILLERY,
        ]
      ],
      LAKE_GEORGE => [
        'id' => LAKE_GEORGE,
        'units' => [
          WILLIAM_HENRY,
          ROGERS,
        ]
      ],
      ALBANY => [
        'id' => ALBANY,
        'units' => [
          MOHAWK,
          ROYAL_HIGHLAND,
          B_35TH_NEW_YORK_COMPANIES
        ]
      ],
      NEW_YORK => [
        'id' => NEW_YORK,
        'units' => [
          ROYAL_ARTILLERY,
        ]
      ],
      CHARLES_TOWN => [
        'id' => CHARLES_TOWN,
        'units' => [
          JOHNSON,
        ]
      ],
      CARLISLE => [
        'id' => CARLISLE,
        'units' => [
          BRADSTREET,
          B_1ST_ROYAL_AMERICAN
        ]
      ],
      WINCHESTER => [
        'id' => WINCHESTER,
        'units' => [
          WASHINGTON,
        ],
        'tokens' => [
          // TODO
          'fortConstruction'
        ]
      ],
      SHAMOKIN => [
        'id' => SHAMOKIN,
        'units' => [
          ARMSTRONG,
          AUGUSTA
        ]
      ],
      // TODO
      'willsCreekPath' => [
        'id' => 'willsCreekPath',
        'units' => [],
        'tokens' => [
          'road'
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
          COLVILL,
          DURELL,
          HARDY,
          HOLBURNE,
          ROYAL_NAVY,
          BEAUFFREMONT,
          DE_LA_MOTTE,
          MARINE_ROYALE,
          VOW_FRENCH_NAVY_LOSSES_PUT_BACK,
        ],
      ],
      POOL_BRITISH_COMMANDERS => [
        'units' => [
          C_HOWE,
          WOLFE,
        ]
      ],
      POOL_BRITISH_COLONIAL_LIGHT => [
        'units' => [
          DUNN,
          PUTNAM,
        ]
      ],
      POOL_BRITISH_LIGHT => [
        'units' => [
          GAGE,
          L_HOWE,
          MORGAN,
          SCOTT,
        ]
      ],
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
          BEDFORD,
          CROWN_POINT,
          EDWARD,
          FREDERICK,
          HERKIMER,
          LIGONIER,
          ONTARIO,
          PITT,
          POWNALL,
          STANWIX,
          TICONDEROGA,
        ]
      ],
      POOL_BRITISH_METROPOLITAN_VOW => [
        'units' => [
          B_27TH_55TH,
          B_22ND_28TH,
          B_43RD_46TH,
          B_2ND_ROYAL_AMERICAN,
          CAMPBELL,
          FRASER,
          MONTGOMERY,
          VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH,
          VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH,
          VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH,
          VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH,
          VOW_FEWER_TROOPS_BRITISH,
          VOW_FEWER_TROOPS_PUT_BACK_BRITISH,
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
          VOW_PENNSYLVANIA_MUSTERS,
          VOW_PICK_ONE_COLONIAL_LIGHT,
          VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK,
          VOW_FEWER_TROOPS_COLONIAL,
          VOW_FEWER_TROOPS_PUT_BACK_COLONIAL,
        ],
      ],
      POOL_BRITISH_COLONIAL_VOW_BONUS => [
        'units' => [
          PENN_DEL, // VoW bonus
          PENN_DEL, // VoW bonus
        ]
      ],
      POOL_FRENCH_COMMANDERS => [
        'units' => [
          C_LEVIS
        ]
      ],
      POOL_FRENCH_FORTS => [
        'units' => [
          BEAUSEJOUR,
          FRONTENAC,
          JACQUES_CARTIER,
          F_LEVIS,
          MASSIAC,
          SAINT_FREDERIC,
        ]
      ],
      POOL_FRENCH_ARTILLERY => [
        'units' => [
          CANONNIERS_BOMBARDIERS,
          CANONNIERS_BOMBARDIERS,
        ]
      ],
      POOL_FRENCH_METROPOLITAN_VOW => [
        'units' => [
          BERRY,
          ANGOUMOIS_BEAUVOISIS,
          DE_LA_MARINE,
          VOLONT_ETRANGERS_CAMBIS,
          VOW_FEWER_TROOPS_FRENCH,
          VOW_FEWER_TROOPS_PUT_BACK_FRENCH,
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
      return $space->isSettledSpace(FRENCH) && $space->isControlledBy(BRITISH);
    });

    if (count($countingSpaces) >= 1) {
      return 2;
    }

    return 0;
  }

  private function getYearEndBonusFrench($spaces)
  {
    $countingSpaces = Utils::filter($spaces, function ($space) {
      return $space->isHomeSpace(BRITISH) && $space->isVictorySpace() && $space->isControlledBy(FRENCH);
    });

    if (count($countingSpaces) >= 3) {
      return 2;
    }

    return 0;
  }
}
