const tplCardsInPlay = () => {
  return `<div id="bt_cards_in_play">
            <div class="bt_card_in_play_container" data-faction="french">
              <div class="bt_card_in_play_title">
                <span>${_('French card')}</span>
              </div>
              <div id="french_card_in_play" class="bt_card_in_play">
                
              </div>
            </div>
            <div class="bt_card_in_play_container" data-faction="indian">
              <div class="bt_card_in_play_title">
                <span>${_('Indian card')}</span>
              </div>
              <div id="indian_card_in_play" class="bt_card_in_play">
                
              </div>
            </div>
            <div class="bt_card_in_play_container" data-faction="british">
              <div class="bt_card_in_play_title">
                <span>${_('British card')}</span>
              </div>
              <div id="british_card_in_play" class="bt_card_in_play">
                
              </div>
            </div>
          </div
  `
}
