<?php

namespace BayonetsAndTomahawks\Scenarios;

use BayonetsAndTomahawks\Core\Notifications;
use BayonetsAndTomahawks\Helpers\Utils;
use BayonetsAndTomahawks\Managers\Spaces;
use BayonetsAndTomahawks\Units\NYorkNJ;

class AmherstsJuggernaut1758_1759 extends \BayonetsAndTomahawks\Models\Scenario
{
  public function __construct()
  {
    parent::__construct();
    $this->id = AmherstsJuggernaut1758_1759;
    $this->name = clienttranslate("Amherst's Juggernaut 1758-1759");
    $this->number = 3;
    $this->startYear = 1758;
    $this->duration = 2;
    $this->reinforcements = [
      1758 => [
        POOL_FLEETS => 9,
        POOL_BRITISH_METROPOLITAN_VOW => 5,
        POOL_BRITISH_COLONIAL_VOW => 12,
        POOL_FRENCH_METROPOLITAN_VOW => 3,
      ],
      1759 => [
        POOL_FLEETS => 9,
        POOL_BRITISH_METROPOLITAN_VOW => 2,
        POOL_BRITISH_COLONIAL_VOW => 3,
        POOL_FRENCH_METROPOLITAN_VOW => 3,
      ]
    ];
    $this->victoryMarkerLocation = VICTORY_POINTS_FRENCH_5;
    $this->victoryThreshold = [
      BRITISH => [
        1758 => 3,
        1759 => 3,
      ],
      FRENCH => [
        1758 => 5,
        1759 => -2,
      ]
    ];
    $this->yearEndBonusDescriptions = [
      BRITISH => [
        1758 => [
          'log' => clienttranslate('Control 2 or more French ${tkn_boldItalicText}'),
          'args' => [
            'tkn_boldItalicText' => clienttranslate('Settled Spaces')
          ],
          'vpBonus' => 2,
        ],
        1759 => [
          'log' => clienttranslate('For ${tkn_boldItalicText} with at least two British-controlled spaces'),
          'args' => [
            'tkn_boldItalicText' => clienttranslate('each French Colony')
          ],
          'vpBonus' => 2,
        ]
      ],
      FRENCH => [
        1758 => [
          'log' => clienttranslate('Control 3 or more British ${tkn_boldItalicText}'),
          'args' => [
            'tkn_boldItalicText' => clienttranslate('Home Spaces')
          ],
          'vpBonus' => 2,
        ],
        1759 => [
          'log' => clienttranslate('For ${tkn_boldItalicText} not controlled by the British'),
          'args' => [
            'tkn_boldItalicText' => clienttranslate('each 3-VP French space')
          ],
          'vpBonus' => 2,
        ]
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
          C_LEVIS,
          CANONNIERS_BOMBARDIERS,
          CANONNIERS_BOMBARDIERS,
          LERY,
          BERRY,
          LA_SARRE_ROYAL_ROUSSILLON,
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
          BEARN_GUYENNE,
          LANGUEDOC_LA_REINE,
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
          MASSIAC,
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
          B_43RD_46TH,
        ],
        'markers' => [
          BRITISH_CONTROL_MARKER
        ]
      ],
      HALIFAX => [
        'id' => HALIFAX,
        'units' => [
          GOREHAM,
          ROYAL_SCOTS_17TH,
          B_22ND_28TH,
          B_40TH_45TH_47TH,
          B_35TH_NEW_YORK_COMPANIES,
          B_2ND_ROYAL_AMERICAN,
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY,
          ROYAL_ARTILLERY,
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
        ]
      ],
      NEW_LONDON => [
        'id' => NEW_LONDON,
        'units' => [
          BRADSTREET,
          FRASER,
        ]
      ],
      ALBANY => [
        'id' => ALBANY,
        'units' => [
          ROGERS,
          MOHAWK,
          ROYAL_HIGHLAND,
        ]
      ],
      NEW_YORK => [
        'id' => NEW_YORK,
        'units' => [
          C_HOWE,
          B_27TH_55TH,
          ROYAL_ARTILLERY,
        ]
      ],
      PHILADELPHIA => [
        'id' => PHILADELPHIA,
        'units' => [
          FORBES,
          B_1ST_ROYAL_AMERICAN,
          ROYAL_ARTILLERY,
        ]
      ],
      CHARLES_TOWN => [
        'id' => CHARLES_TOWN,
        'units' => [
          JOHNSON,
        ]
      ],
      WINCHESTER => [
        'id' => WINCHESTER,
        'units' => [
          FREDERICK,
        ],
      ],
      SHAMOKIN => [
        'id' => SHAMOKIN,
        'units' => [
          ARMSTRONG,
          AUGUSTA
        ]
      ],
    ];
    $this->connections = [
      MEKEKASINK_WILLS_CREEK => [
        'id' => MEKEKASINK_WILLS_CREEK,
        'units' => [],
        'markers' => [
          ROAD_MARKER
        ]
      ],
    ];
    $this->pools = [
      POOL_NEUTRAL_INDIANS => [
        'units' => [
          BRITISH_IROQUOIS,
          BRITISH_IROQUOIS,
          BRITISH_IROQUOIS,
          BRITISH_CHEROKEE,
          BRITISH_CHEROKEE,
          FRENCH_IROQUOIS,
          FRENCH_IROQUOIS,
          FRENCH_IROQUOIS,
          FRENCH_CHEROKEE,
          FRENCH_CHEROKEE,
        ]
      ],
      POOL_FLEETS => [
        'units' => [
          BOSCAWEN,
          COLVILL,
          DURELL,
          HARDY,
          HOLMES,
          ROYAL_NAVY,
          SAUNDERS,
          BEAUFFREMONT,
          DE_L_ISLE,
          MARINE_ROYALE,
          VOW_FRENCH_NAVY_LOSSES_PUT_BACK,
        ],
      ],
      POOL_BRITISH_COMMANDERS => [
        'units' => [
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
        ]

      ],
      POOL_BRITISH_FORTS => [
        'units' => [
          BEDFORD,
          CROWN_POINT,
          EDWARD,
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
          B_15TH_58TH,
          B_61ST_63RD,
          B_94TH_95TH,
          HOWARDS_BUFFS_KINGS_OWN,
          CAMPBELL,
          MONTGOMERY,
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
          PENN_DEL,
          PENN_DEL,
          VIRGINIA_S,
          VIRGINIA_S,
          VOW_PICK_ONE_COLONIAL_LIGHT,
          VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK,
          VOW_FEWER_TROOPS_COLONIAL,
          VOW_FEWER_TROOPS_PUT_BACK_COLONIAL,
          VOW_PITT_SUBSIDIES,
        ],
      ],
      POOL_BRITISH_COLONIAL_VOW_BONUS => [
        'units' => [
          NEW_ENGLAND,
          NEW_ENGLAND,
          NYORK_NJ,
          VIRGINIA_S,
        ]
      ],
      POOL_FRENCH_COMMANDERS => [
        'units' => []
      ],
      POOL_FRENCH_FORTS => [
        'units' => [
          BEAUSEJOUR,
          FRONTENAC,
          JACQUES_CARTIER,
          F_LEVIS,
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
          ANGOUMOIS_BEAUVOISIS,
          BOULONNOIS_ROYAL_BARROIS,
          DE_LA_MARINE,
          FOIX_QUERCY,
          VOLONT_ETRANGERS_CAMBIS,
          VOW_PICK_ONE_ARTILLERY_FRENCH,
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
      return $this->getYearEndBonusBritish($spaces, $year);
    } else {
      return $this->getYearEndBonusFrench($spaces, $year);
    }
  }

  private function getYearEndBonusBritish($spaces, $year)
  {
    if ($year === 1758) {
      $countingSpaces = Utils::filter($spaces, function ($space) {
        return $space->isSettledSpace(FRENCH) && $space->isControlledBy(BRITISH);
      });

      if (count($countingSpaces) >= 1) {
        return 2;
      }
    } else if ($year === 1759) {
      $britishControlled = Spaces::getControlledBy(BRITISH);
      $bonus = 0;
      foreach (FRENCH_COLONIES as $colonyId) {
        $numberOfBritishControlledInColony = count(Utils::filter($britishControlled, function ($space) use ($colonyId) {
          return $space->getColony() === $colonyId;
        }));
        if ($numberOfBritishControlledInColony >= 2) {
          $bonus += 2;
        }
      }
      return $bonus;
    }


    return 0;
  }

  private function getYearEndBonusFrench($spaces, $year)
  {
    if ($year === 1758) {
      $frenchControlledSpaces = Spaces::getControlledBy(BRITISH);
      $countingSpaces = Utils::filter($frenchControlledSpaces, function ($space) {
        return $space->getHomeSpace() === BRITISH;
      });

      if (count($countingSpaces) >= 3) {
        return 2;
      }
    } else if ($year === 1759) {
      $bonus = 0;
      $spaces = Spaces::get([LOUISBOURG, MONTREAL, QUEBEC]);
      foreach ($spaces as $spaceId => $space) {
        if ($space->getControl() !== BRITISH) {
          $bonus += 2;
        }
      }
      return $bonus;
    }

    return 0;
  }
}
