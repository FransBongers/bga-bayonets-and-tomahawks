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
  const cardHtml = `<div class="bt_card" data-card-id="${card.id}"></div>`;
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

const tplTooltipWithIcon = ({
  title,
  text,
  iconHtml,
  iconWidth,
}: {
  title?: string;
  text: string;
  iconHtml: string;
  iconWidth?: number;
}): string => {
  return `<div class="icon_tooltip">
            <div class="icon_tooltip_icon"${
              iconWidth ? `style="min-width: ${iconWidth}px;"` : ''
            }>
              ${iconHtml}
            </div>
            <div class="icon_tooltip_content">
              ${title ? `<span class="tooltip_title" >${title}</span>` : ''}
              <span class="tooltip_text">${text}</span>
            </div>
          </div>`;
};

const tplTextTooltip = ({ text }: { text: string }) => {
  return `<span class="text_tooltip">${text}</span>`;
};
