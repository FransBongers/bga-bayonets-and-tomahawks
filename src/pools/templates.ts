const tplPoolsContainer = () => {
  return `
  <div id="bt_pools_container">
    ${tplPool({type: 'french'})}
    ${tplPool({type: 'indian'})}
    ${tplPool({type: 'british'})}
  </div>`
}

const tplPool = ({ type }: { type: string }): string => {
  return `<div id="bt_pool_${type}" class="bt_unit_pool">
  <div class="bt_token" data-faction="french" data-unit-type="pouchot"></div>
  </div>`;
};

