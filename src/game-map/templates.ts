const tplGameMap = () => `
<div id="bt_game_map_containter">
  <div class="bt_game_map_zoom_buttons">
    <button id="bt_game_map_zoom_out_button" type="button" class="bga-zoom-button bga-zoom-out-icon" style="margin-bottom: -5px;"></button>
    <button id="bt_game_map_zoom_in_button" type="button" class="bga-zoom-button bga-zoom-in-icon" style="margin-bottom: -5px;"></button>
  </div>
  <div id="bt_game_map">
    <div class="bt_marker" data-marker-type="victory_point"></div>
    <div class="bt_token" data-faction="french" data-unit-type="bastion"></div>
    <div class="bt_token" data-faction="indian" data-unit-type="micmac"></div>
  </div>
</div>`

// <i class="fa-regular fa-magnifying-glass-plus"></i>