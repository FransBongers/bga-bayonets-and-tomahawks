const tplCardTooltipContainer = ({ card, content }: { card: string; content: string }): string => {
  return `<div class="bt_card_tooltip">
  <div class="bt_card_tooltip_inner_container">
    ${content}
  </div>
  ${card}
</div>`;
};
