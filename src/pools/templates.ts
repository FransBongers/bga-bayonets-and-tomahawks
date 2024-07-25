const tplPoolsContainer = () => {
  return `
  <div id="bt_right_column">
    ${tplPoolFleets()}
    ${tplPoolNeutralIndians()}
    ${tplPoolBritish()}
    ${tplPoolFrench()}
  </div>`
}

// ${tplPool({type: 'french'})}
// ${tplPool({type: 'indian'})}
// ${tplPool({type: 'british'})}

const tplPoolFleets = () => `
<div id="bt_pool_fleets" class="bt_unit_pool_container">
  <div><span>${_('Fleets')}</span></div>
  <div id="poolFleets" class="bt_unit_pool"></div>
</div>`;

const tplPoolNeutralIndians = () => `
<div id="bt_pool_neutralIndians" class="bt_unit_pool_container">
  <div><span>${_('Neutral Indians')}</span></div>
  <div id="poolNeutralIndians" class="bt_unit_pool"></div>
</div>`;

const tplPoolBritish = () => `
<div id="bt_pool_british" class="bt_unit_pool_container">
  <div><span>${_('British')}</span></div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Commanders')}</span></div>
    <div id="poolBritishCommanders" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Light')}</span></div>
    <div id="poolBritishLight" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Artillery')}</span></div>
    <div id="poolBritishArtillery" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Forts')}</span></div>
    <div id="poolBritishForts" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Metropolitan Brigades & VoW')}</span></div>
    <div id="poolBritishMetropolitanVoW" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Colonial Brigades & VoW')}</span></div>
    <div id="poolBritishColonialVoW" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Colonial VoW Bonus')}</span></div>
    <div id="poolBritishColonialVoWBonus" class="bt_unit_pool"></div>
  </div>
</div>
`

const tplPoolFrench = () => `
<div id="bt_pool_french" class="bt_unit_pool_container">
  <div><span>${_('French')}</span></div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Commanders')}</span></div>
    <div id="poolFrenchCommanders" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Light')}</span></div>
    <div id="poolFrenchLight" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Artillery')}</span></div>
    <div id="poolFrenchArtillery" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Forts')}</span></div>
    <div id="poolFrenchForts" class="bt_unit_pool"></div>
  </div>
  <div>
    <div class="bt_unit_pool_section_title"><span>${_('Metropolitan Brigades & VoW')}</span></div>
    <div id="poolFrenchMetropolitanVoW" class="bt_unit_pool"></div>
  </div>
</div>
`

const tplPool = ({ type }: { type: string }): string => {
  return `<div id="bt_pool_${type}" class="bt_unit_pool">
  </div>`;
};

