const tplTabbedColumnTab = (id: string, info: { text: string }) => `
<div id="bt_tabbed_column_tab_${id}" class="bt_tabbed_column_tab" data-state="inactive">
  <span>${_(info.text)}</span>
</div>
`;

const tplTabbedColumn = (tabs: TabbedColumnTabInfo) => {
  return `
  <div id="bt_tabbed_column">
    <div id="bt_tabbed_column_tabs">
      ${Object.entries(tabs)
        .map(([id, info]) => tplTabbedColumnTab(id, info))
        .join('')}
    </div>
    <div id="bt_tabbed_column_content_cards" class="bt_tabbed_column_content" data-visible="false">
    </div>
    <div id="bt_tabbed_column_content_battle" class="bt_tabbed_column_content" data-visible="false">
    </div>
    <div id="bt_tabbed_column_content_pools" class="bt_tabbed_column_content" data-visible="false">
    </div>
    <div id="bt_tabbed_column_content_playerAid" class="bt_tabbed_column_content" data-visible="false">
     TODO
    </div>
  </div>`;
};
