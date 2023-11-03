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
  addPrimaryActionButton: (props: AddButtonProps) => void;
  addSecondaryActionButton: (props: AddButtonProps) => void;
  addDangerActionButton: (props: AddButtonProps) => void;
  clearInterface: () => void;
  clearPossible: () => void;
  updatePlayAreaSize: () => void;
  notificationManager: NotificationManager;
  playAreaScale: number;
  playerManager: PlayerManager;
  tooltipManager: TooltipManager;
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

interface BayonetsAndTomahawksGamedatas extends Gamedatas {
  canceledNotifIds: string[];
  players: Record<number, BgaPlayer>;
  spaces: BTSpace[]
}

interface BayonetsAndTomahawksPlayerData extends BgaPlayer {
  hexColor: string;
}