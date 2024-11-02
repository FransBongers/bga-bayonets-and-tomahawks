interface ActionRoundStep {
  id: string;
  stepNumber: number;
  text: string;
}

type BattleOrder = BattleOrderStep[];

interface BattleOrderStep {
  numberOfAttackers: number;
  spaceIds: string[];
}