interface AddButtonProps {
  id: string;
  text: string;
  callback: () => void;
  extraClasses?: string;
}

interface AddActionButtonProps extends AddButtonProps {
  color?: 'blue' | 'gray' | 'red' | 'none';
}

interface BayonetsAndTomahawksGame extends Game {
  addCancelButton: ({ callback }?: { callback?: Function }) => void;
  addConfirmButton: (props: { callback: Function | string }) => void;
  addLogClass: () => void;
  addPassButton: (props: { optionalAction: boolean; text?: string }) => void;
  addPlayerButton: ({
    player,
    callback,
  }: {
    player: BgaPlayer;
    callback: Function | string;
  }) => void;
  addPrimaryActionButton: (props: AddButtonProps) => void;
  addSecondaryActionButton: (props: AddButtonProps) => void;
  addDangerActionButton: (props: AddButtonProps) => void;
  addUndoButtons: (props: CommonArgs) => void;
  cancelLogs: (notifIds: string[]) => void;
  clearInterface: () => void;
  clearPossible: () => void;
  clientUpdatePageTitle: (props: {
    text: string;
    args: Record<string, unknown>;
    nonActivePlayers?: boolean;
  }) => void;
  format_string_recursive: (
    log: string,
    args: Record<string, unknown>
  ) => string;
  getPlayerId: () => number;
  getConnectionStaticData: (connection: BTConnection) => BTConnectionStaticData;
  getSpaceStaticData: (space: BTSpace | string) => BTSpaceStaticData;
  getUnitStaticData: (unit: BTUnit | string) => BTUnitStaticData;
  onCancel: () => void;
  openUnitStack: (unit: BTUnit) => void;
  setCardSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setCardSelected: (props: { id: string }) => void;
  setElementsSelected: (
    elmenents: {
      id: string;
      [key: string]: any;
    }[]
  ) => void;
  setLocationSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setLocationSelected: (props: { id: string }) => void;
  setStackSelected: (props: { spaceId: string; faction: string }) => void;
  setStackSelectable: (props: {
    spaceId: string;
    faction: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setUnitSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setUnitSelected: (props: { id: string }) => void;
  setUnitSpent: (props: { id: string }) => void;
  takeAction: (props: {
    action: string;
    atomicAction?: boolean;
    args?: Record<string, unknown>;
    checkAction?: string; // Action used in checkAction
  }) => void;
  updateLayout: () => void;
  _last_tooltip_id: number; // generic
  tooltipsToMap: [tooltipId: number, card_id: string][]; // generic
  animationManager: AnimationManager;
  battleLog: BattleLog;
  battleTab: BattleTab;
  cardsInPlay: CardsInPlay;
  hand: Hand;
  gameMap: GameMap;
  notificationManager: NotificationManager;
  playerManager: PlayerManager;
  pools: Pools;
  settings: Settings;
  stepTracker: StepTracker;
  tabbedColumn: TabbedColumn;
  tokenManager: TokenManager;
  tooltipManager: TooltipManager;
  wieChitManager: WieChitManager;
  cardManager: BTCardManager;
  discard: VoidStock<BTCard>;
  deck: LineStock<BTCard>;
}

// type BRITISH = 'british';
// type FRENCH = 'french';
// type INDIAN = 'indian';

// type Faction = BRITISH | FRENCH | INDIAN;
type BRITISH_FACTION = 'british';
type FRENCH_FACTION = 'french';
type Faction = BRITISH_FACTION | FRENCH_FACTION | 'indian';

interface BTActionPoint {
  id: string;
}

interface BTStackAction {
  id: string;
  name: string;
}

interface BTCard {
  id: string;
  actionPoints: BTActionPoint[];
  event: {
    id: string;
    title: string;
    arStart: true;
  };
  faction: string;
  initiativeValue: number;
  location: string;
  state: number;
  years: number[] | null;
}

interface BTConnection {
  id: string;
  type: string;
  britishLimit: number;
  britishRoadUsed: number;
  frenchLimit: number;
  frenchRoadUsed: number;
  road: number;
}

interface BTConnectionStaticData {
  id: string;
  left: number;
  top: number;
  coastal: boolean;
}

interface BTMarker {
  manager: 'markers';
  id: string;
  location: string;
  state: number;
  side: 'front' | 'back';
  type: string;
  stackOrder: number;
}

interface BTSpace {
  id: string;
  battle: boolean;
  colony: string | null;
  control: string;
  raided: BRITISH_FACTION | FRENCH_FACTION | null;
  fortConstruction: boolean;
  homeSpace: BRITISH_FACTION | FRENCH_FACTION | null;
  defaultControl: string;
  name: string;
  victorySpace: boolean;
  top?: number;
  left?: number;
}

interface BTSpaceStaticData {
  britishBase: boolean;
  left: number;
  name: string;
  top: number;
}

interface BTUnit {
  manager: 'units';
  id: string;
  counterId: string;
  faction: string;
  location: string;
  spent: number;
  reduced: boolean;
  stackOrder: number;
}

interface BTUnitStaticData {
  faction: 'british' | 'french';
  colony?: string | null;
  counterText: string;
  indian: boolean;
  highland: boolean;
  metropolitan: boolean;
  shape: string;
  type: string;
}

interface BTWIEChit {
  id: string;
  location: string;
  value: number;
  revealed: boolean;
}

type BTToken = BTMarker | BTUnit;

interface BTScenarioReinforcements {
  poolBritishColonialVoW: number;
  poolBritishMetropolitanVoW: number;
  poolFleets: number;
  poolFrenchMetropolitanVoW: number;
}

interface BTScenario {
  id: string;
  duration: number;
  name: string;
  reinforcements: Record<string, BTScenarioReinforcements>;
  victoryThreshold: {
    [faction: string]: {
      [year: number]: number;
    };
  };
  yearEndBonusDescriptions: {
    [faction: string]: {
      [year: number]: {
        args: {
          tkn_boldItalicText: string;
        };
        log: string;
        vpBonus: number;
      };
    };
  };
}

interface BTActiveBattleLogFactionData {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  militiaIds: string[];
  penalties: number;
  rolls: Record<string, string[]>;
  unitIds: string[];
  units: BTUnit[];
  militia: BTMarker[];
}

interface BTActiveBattleLog {
  spaceId: string;
  attacker: BRITISH_FACTION | FRENCH_FACTION;
  defender: BRITISH_FACTION | FRENCH_FACTION;
  british: BTActiveBattleLogFactionData;
  french: BTActiveBattleLogFactionData;
}

interface BTBattleLogFactionData {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  militiaIds: string[];
  penalties: number;
  rolls: Record<string, string[]>;
  unitsAfterBattle?: Array<{
    counterId: string;
    state: 'reduced' | 'destroyed' | 'full'
  }>;
  militia: BTMarker[];
  result?: number;
}

interface BTBattleLog {
  spaceId: string;
  attacker: BRITISH_FACTION | FRENCH_FACTION;
  defender: BRITISH_FACTION | FRENCH_FACTION;
  british: BTBattleLogFactionData;
  french: BTBattleLogFactionData;
}

interface BTCustomLog<T = any> {
  id: number;
  data: T;
  round: string;
  year: number;
  type: 'battleResult';
}

interface BayonetsAndTomahawksGamedatas extends Gamedatas {
  canceledNotifIds: string[];
  activeBattleLog: BTActiveBattleLog | null;
  cardsInPlay: {
    british: BTCard | null;
    french: BTCard | null;
    indian: BTCard | null;
  };
  connections: BTConnection[];
  constrolIndianNations: {
    Cherokee: BRITISH_FACTION | FRENCH_FACTION | 'neutral';
    Iroquois: BRITISH_FACTION | FRENCH_FACTION | 'neutral';
  };
  currentRound: {
    id: string;
    step: string;
    battleOrder: BattleOrderStep[];
  };
  customLogs: BTCustomLog[];
  markers: {
    year_marker: BTMarker;
    round_marker: BTMarker;
    victory_marker: BTMarker;
    open_seas_marker: BTMarker;
    french_raid_marker: BTMarker;
    british_raid_marker: BTMarker;
    british_battle_marker: BTMarker;
    french_battle_marker: BTMarker;
  } & Record<string, BTMarker>;
  playerOrder: number[];
  players: Record<number, BayonetsAndTomahawksPlayerData>;
  scenario: BTScenario;
  staticData: {
    connections: Record<string, BTConnectionStaticData>;
    units: Record<string, BTUnitStaticData>;
    spaces: Record<string, BTSpaceStaticData>;
  };
  spaces: BTSpace[];
  units: BTUnit[];
  highwayUnusableForBritish: string;
}

interface BayonetsAndTomahawksPlayerData extends BgaPlayer {
  hand: BTCard[];
  faction: BRITISH_FACTION | FRENCH_FACTION;
  actionPoints: Record<Faction, BTActionPoint[]> & {
    reactionActionPointId?: string;
  };
  wieChit: {
    chit: BTWIEChit | null;
    hasChit: boolean;
  };
}
