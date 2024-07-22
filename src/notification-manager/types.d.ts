/**
 * Note: we need to keep player_name in snake case, because the framework uses
 * it to add player colors to the log messages.
 */

interface Log {
  log: string;
  args: Record<string, unknown>;
}

interface NotifWithPlayerArgs {
  playerId: number;
  player_name: string;
}

type NotifSmallRefreshInterfaceArgs = Omit<
  BayonetsAndTomahawksGamedatas,
  'staticData'
>;

interface NotifAdvanceBattleVictoryMarkerArgs {
  marker: BTMarker;
}

interface NotifBattleArgs extends NotifWithPlayerArgs {
  space: BTSpace;
}

interface NotifBattleCleanupArgs {
  space: BTSpace;
  attackerMarker: BTMarker;
  defenderMarker: BTMarker;
}

interface NotifBattleStartArgs {
  // space: BTSpace;
  attackerMarker: BTMarker;
  defenderMarker: BTMarker;
}

interface NotifBattleSelectCommanderArgs extends NotifWithPlayerArgs {
  commander: BTUnit;
}

interface NotifDrawCardPrivateArgs extends NotifWithPlayerArgs {
  card: BTCard;
}

interface NotifDiscardCardFromHandArgs extends NotifWithPlayerArgs {
  faction: Faction;
}

interface NotifDiscardCardFromHandPrivateArgs extends NotifWithPlayerArgs {
  card: BTCard;
}

interface NotifDiscardCardsInPlayArgs {
  card: BTCard;
}

interface NotifEliminateUnitArgs extends NotifWithPlayerArgs {
  unit: BTUnit;
}

interface NotifLoseControlArgs extends NotifWithPlayerArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  space: BTSpace;
}

interface NotifMoveRaidPointsMarkerArgs {
  marker: BTMarker;
}

interface NotifMoveRoundMarkerArgs {
  nextRoundStep: string;
  marker: BTMarker;
}

interface NotifMoveStackArgs {
  stack: BTUnit[];
  destination: BTSpace;
  faction: Faction;
}

interface NotifScoreVictoryPointsArgs {
  marker: BTMarker;
  points: Record<number, number>;
}

interface NotifMoveUnitArgs {
  unit: BTUnit;
  destination: BTSpace;
  faction: Faction;
}

interface NotifMoveYearMarkerArgs {
  location: string;
  marker: BTMarker;
}

interface NotifPlaceStackMarkerArgs extends NotifWithPlayerArgs {
  marker: BTMarker;
}


interface NotifPlaceUnitInLossesArgs extends NotifWithPlayerArgs {
  unit: BTUnit;
  faction: string;
}

interface NotifPlaceRaidPointsArgs extends NotifWithPlayerArgs {
  space: BTSpace;
  faction: string;
}

interface NotifReduceUnitArgs {
  unit: BTUnit;
}

interface NotifRevealCardsInPlayArgs {
  british: BTCard;
  french: BTCard;
  indian: BTCard;
}

interface NotifSelectReserveCardArgs extends NotifWithPlayerArgs {
  faction: Faction;
}

interface NotifSelectReserveCardPrivateArgs extends NotifWithPlayerArgs {
  discardedCard: BTCard;
}

interface NotifTakeControlArgs extends NotifWithPlayerArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  space: BTSpace;
}
