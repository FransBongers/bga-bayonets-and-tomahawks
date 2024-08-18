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

interface NotifAddSpentMarkerToUnitsArgs {
  units: BTUnit[];
}

interface NotifMoveBattleVictoryMarkerArgs {
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

interface NotifBattleRemoveMarkerArgs {
  space: BTSpace;
}

interface NotifBattleRerollArgs extends NotifWithPlayerArgs {
  commander: BTUnit | null;
}

interface NotifBattleReturnCommanderArgs extends NotifWithPlayerArgs {
  commander: BTUnit;
}

interface NotifBattleSelectCommanderArgs extends NotifWithPlayerArgs {
  commander: BTUnit;
}

interface NotifCommanderDrawArgs extends NotifWithPlayerArgs {
  commander: BTUnit;
}

interface NotifDrawCardPrivateArgs extends NotifWithPlayerArgs {
  card: BTCard;
}

interface NotifDrawnReinforcementsArgs {
  units: BTUnit[];
  location: string;
}

interface NotifConstructionFortArgs extends NotifWithPlayerArgs {
    faction: BRITISH_FACTION | FRENCH_FACTION;
  fort: BTUnit | null;
  option: string;
  space: BTSpace;
}

interface NotifConstructionRoadArgs extends NotifWithPlayerArgs {
  connection: BTConnection;
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

interface NotifIndianNationControlArgs extends NotifWithPlayerArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  indianNation: string;
}

interface NotifLoseControlArgs extends NotifWithPlayerArgs {
  faction: BRITISH_FACTION | FRENCH_FACTION;
  space: BTSpace;
}

interface NotifMarshalTroopsArgs extends NotifWithPlayerArgs {
  activatedUnit: BTUnit;
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
  connection: BTConnection | null;
  destinationId: string;
  faction: Faction;
  markers: BTMarker[];
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

interface NotifPlaceUnitsArgs extends NotifWithPlayerArgs {
  units: BTUnit[];
  faction: string;
  spaceId: string;
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

interface NotifRemoveMarkerFromStackArgs {
  marker: BTMarker;
  from: string;
}

interface NotifRemoveMarkersEndOfActionRoundArgs {
  spentUnits: BTUnit[];
}

interface NotifReturnToPoolArgs {
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

interface NotifVagariesOfWarPickUnitsArgs extends NotifWithPlayerArgs {
  units: BTUnit[];
  location: string;
}
