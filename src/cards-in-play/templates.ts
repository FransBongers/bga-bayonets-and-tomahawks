const tplCardsInPlay = () => {
  return `<div id="bt_cards_in_play">
            <div class="bt_card_in_play_container">
              <span>${_('French card')}</span>
              <div id="french_card_in_play" class="bt_card_in_play">
                <div class="bt_card_in_play_border"></div>
              </div>
            </div>
            <div class="bt_card_in_play_container">
              <span>${_('Indian card')}</span>
              <div id="indian_card_in_play" class="bt_card_in_play">
                <div class="bt_card_in_play_border"></div>
              </div>
            </div>
            <div class="bt_card_in_play_container">
              <span>${_('British card')}</span>
              <div id="british_card_in_play" class="bt_card_in_play">
                <div class="bt_card_in_play_border"></div>
              </div>
            </div>
          </div
  `
}
