<?php

namespace BayonetsAndTomahawks\Scenarios;

$scenarios[2] = [
  'meta_data' => [
    'scenario_id' => '2',
    'name' => clienttranslate("Loudoun's Gamble 1757"),
  ],
  'locations' => [
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
        BASTION,
        BASTION,
        ARTOIS_BOURGOGNE,
        DE_LA_MARINE
      ]
    ],
    QUEBEC => [
      'id' => QUEBEC,
      'units' => [
        MONTCALM,
        BASTION,
        BASTION,
        CANONNIERS_BOMBARDIERS,
        CANONNIERS_BOMBARDIERS,
        LERY,
        LANGUEDOC_LA_REINE,
        CANADIENS,
        CANADIENS
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
  ],
  'pools' => [
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
      ],
      'vow' => []
    ],
    POOL_BRITISH_COMMANDERS => [
      'units' => [
        C_HOWE,
        WOLFE,
      ]
    ],
    POOL_BRITISH_LIGHT => [
      'units' => [
        // Light colonial
        DUNN,
        PUTNAM,
        // Light
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
      ],
      'vow' => []
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
        PENN_DEL, // VoW bonus
        PENN_DEL, // VoW bonus
      ],
      'vow' => []
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
      ],
      'vow' => []
    ]
  ]
];
