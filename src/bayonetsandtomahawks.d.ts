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
  notificationManager: NotificationManager;
  playerManager: PlayerManager;
  tooltipManager: TooltipManager;
}

interface BayonetsAndTomahawksGamedatas extends Gamedatas {
  canceledNotifIds: string[];
  players: Record<number, BgaPlayer>;
}

interface BayonetsAndTomahawksPlayerData extends BgaPlayer {
  hexColor: string;
}