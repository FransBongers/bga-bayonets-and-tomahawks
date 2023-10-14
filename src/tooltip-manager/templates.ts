const tplCardTooltipContainer = ({ card, content }: { card: string; content: string }): string => {
  return `<div class="bat_card_tooltip">
  <div class="bat_card_tooltip_inner_container">
    ${content}
  </div>
  ${card}
</div>`;
};
