const tplInformationButton =
  () => `<button id="information_button" type="button" class="information_modal_button">
<div class="information_modal_icon">
  <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" /></svg>
</div>
</button>`;

const tplInfoModalTab = ({ id, text }: { id: string; text: string }) => `
  <div id="information_modal_tab_${id}" class="information_modal_tab">
    <span>${_(text)}</span>
  </div>`;

const tplInformationModalContent = ({
  tabs,
  game,
}: {
  tabs: Record<string, { text: string }>;
  game: BayonetsAndTomahawksGame;
}) => {
  

  return `
  <div id="information_modal_content">
    <div class="information_modal_tabs">
      ${Object.entries(tabs)
        .map(([id, info]) => tplInfoModalTab({ id, text: info.text }))
        .join('')}
    </div>
    <div id="bt_actions" style="display: none;">
      
    </div>
    <div id="bt_gameMap" style="display: none;">
      
    </div>
  </div>`;
};
