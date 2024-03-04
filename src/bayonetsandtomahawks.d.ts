interface AddButtonProps {
  id: string;
  text: string;
  callback: () => void;
  extraClasses?: string;
}

interface AddActionButtonProps extends AddButtonProps {
  color?: "blue" | "gray" | "red" | "none";
}

interface BayonetsAndTomahawksGame extends Game {
  addCancelButton: () => void;
  addConfirmButton: (props: { callback: Function | string }) => void;
  addPrimaryActionButton: (props: AddButtonProps) => void;
  addSecondaryActionButton: (props: AddButtonProps) => void;
  addDangerActionButton: (props: AddButtonProps) => void;
  clearInterface: () => void;
  clearPossible: () => void;
  clientUpdatePageTitle: (props: {
    text: string;
    args: Record<string, unknown>;
    nonActivePlayers?: boolean;
  }) => void;
  getPlayerId: () => number;
  setCardSelectable: (props: {
    id: string;
    callback: (event: PointerEvent) => void;
  }) => void;
  setCardSelected: (props: { id: string }) => void;
  takeAction: (props: {
    action: string;
    args?: Record<string, unknown>;
  }) => void;
  updateLayout: () => void;
  animationManager: AnimationManager;
  hand: Hand;
  // updatePlayAreaSize: () => void;
  notificationManager: NotificationManager;
  // playAreaScale: number;
  playerManager: PlayerManager;
  settings: Settings;
  tooltipManager: TooltipManager;

  cardManager: BTCardManager;
}

interface BTCard {
  id: string;
  faction: string;
  initiativeValue: number;
  location: string;
  state: number;
  years: number[] | null;
}

interface BTMarker {
  id: string;
  location: string;
  state: number;
}

interface BTSpace {
  id: string;
  control: string;
  defaultControl: string;
  name: string;
  victorySpace: boolean;
  top?: number;
  left?: number;
}

interface BTUnit {
  id: number;
  counterId: string;
  location: string;
}

interface BayonetsAndTomahawksGamedatas extends Gamedatas {
  canceledNotifIds: string[];
  markers: {
    year_marker: BTMarker;
    round_marker: BTMarker;
    victory_marker: BTMarker;
    open_seas_marker: BTMarker;
    french_raid_marker: BTMarker;
    british_raid_marker: BTMarker;
  };
  playerOrder: number[];
  players: Record<number, BayonetsAndTomahawksPlayerData>;
  staticData: {
    units: {
      [counterId: string]: {
        faction: "british" | "french" | "indian";
      };
    };
  };
  spaces: BTSpace[];
  units: BTUnit[];
}

interface BayonetsAndTomahawksPlayerData extends BgaPlayer {
  hexColor: string;
  hand: BTCard[];
}
