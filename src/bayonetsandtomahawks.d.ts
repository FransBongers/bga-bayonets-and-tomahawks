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
  getUnitData: ({ counterId }: { counterId: string }) => { faction: string };
  onCancel: () => void;
  openUnitStack: (unit: BTUnit) => void;
  setCardSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setCardSelected: (props: { id: string }) => void;
  setLocationSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setLocationSelected: (props: { id: string }) => void;
  setStackSelected: (props: { spaceId: string; faction: string }) => void;
  setUnitSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setUnitSelected: (props: { id: string }) => void;
  takeAction: (props: {
    action: string;
    atomicAction?: boolean;
    args?: Record<string, unknown>;
    checkAction?: string; // Action used in checkAction
  }) => void;
  updateLayout: () => void;
  animationManager: AnimationManager;
  cardsInPlay: CardsInPlay;
  hand: Hand;
  gameMap: GameMap;
  notificationManager: NotificationManager;
  playerManager: PlayerManager;
  pools: Pools;
  settings: Settings;
  tokenManager: TokenManager;
  tooltipManager: TooltipManager;

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
  frenchLimit: number;
  road: number;
}

interface BTMarker {
  manager: 'markers';
  id: string;
  location: string;
  state: number;
  side: 'front' | 'back';
  type: string;
}

interface BTSpace {
  id: string;
  battle: boolean;
  colony: string | null;
  control: string;
  raided: BRITISH_FACTION | FRENCH_FACTION | null;
  homeSpace: BRITISH_FACTION | FRENCH_FACTION | null;
  defaultControl: string;
  name: string;
  victorySpace: boolean;
  top?: number;
  left?: number;
}

interface BTUnit {
  manager: 'units';
  id: string;
  counterId: string;
  faction: string;
  location: string;
  spent: number;
  reduced: boolean;
}

type BTToken = BTMarker | BTUnit;

interface BayonetsAndTomahawksGamedatas extends Gamedatas {
  canceledNotifIds: string[];
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
  markers: {
    year_marker: BTMarker;
    round_marker: BTMarker;
    victory_marker: BTMarker;
    open_seas_marker: BTMarker;
    french_raid_marker: BTMarker;
    british_raid_marker: BTMarker;
  } & Record<string, BTMarker>;
  playerOrder: number[];
  players: Record<number, BayonetsAndTomahawksPlayerData>;
  staticData: {
    connections: Record<string, { id: string; top: number; left: number }>;
    units: {
      [counterId: string]: {
        faction: 'british' | 'french';
        colony?: string | null;
        counterText: string;
        metropolitan: boolean;
        type: string;
      };
    };
    spaces: Record<
      string,
      {
        name: string;
      }
    >;
  };
  spaces: BTSpace[];
  units: BTUnit[];
}

interface BayonetsAndTomahawksPlayerData extends BgaPlayer {
  hexColor: string;
  hand: BTCard[];
}
