const tplCardTooltipContainer = ({
  card,
  content,
}: {
  card: string;
  content: string;
}): string => {
  return `<div class="bt_card_tooltip">
  <div class="bt_card_tooltip_inner_container">
    ${content}
  </div>
  ${card}
</div>`;
};

const tplCardTooltip = ({
  card,
  game,
  imageOnly = false,
}: {
  card: BTCard;
  game: BayonetsAndTomahawksGame;
  imageOnly?: boolean;
}) => {
  const cardHtml = `<div class="bt_card" data-card-id="${
    card.id
  }"></div>`;
  if (imageOnly) {
    return `<div class="bt_card_only_tooltip">${cardHtml}</div>`;
  }

  return tplCardTooltipContainer({
    card: cardHtml,
    content: `
      TODO
    `,
  });
};