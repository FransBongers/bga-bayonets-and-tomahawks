const tplStepTrackerContent = (currentRound: string) =>
  getStepConfig(currentRound)
    .map(
      (roundConfig) => `
  <li id="${roundConfig.id}" data-active="false">${roundConfig.stepNumber}. ${_(
        roundConfig.text
      )}</li>
`
    )
    .join('');

const tplBattleOrderStep = (
  game: BayonetsAndTomahawksGame,
  { numberOfAttackers, spaceIds }: BattleOrderStep
) =>
  `<li id="battleOrderStep${numberOfAttackers}" data-active="false" class="step_tracker_battle_step">${spaceIds
    .map((spaceId) => _(game.getSpaceStaticData(spaceId).name))
    .sort((a, b) => (a > b ? 1 : -1))
    .join(', ')}</li>`;

const tplStepTracker = (currentRound: string) => `
<div id="step_tracker" class='player-board'>
  <div id="step_stracker_title_container">
    <span id="step_stracker_title" class="bt_title">${_(
      getCurrentRoundName(currentRound)
    )}</span>
  </div>
  <ul id="step_tracker_content">
  ${tplStepTrackerContent(currentRound)}
  </ul>
</div>`;
