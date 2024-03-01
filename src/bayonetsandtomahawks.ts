/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Bayonets and Tomahawks implementation : © Frans Bongers <fjmbongers@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * bayonetsandtomahawks.js
 *
 * bayonetsandtomahawks user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

declare const define; // TODO: check if we comment here or in bga-animations module?
declare const ebg;
declare const $;
declare const dojo: Dojo;
declare const _: (stringToTranslate: string) => string;
declare const g_gamethemeurl;
declare const playSound;
declare var noUiSlider;

class BayonetsAndTomahawks implements BayonetsAndTomahawksGame {
  public gamedatas: BayonetsAndTomahawksGamedatas;
  public animationManager: AnimationManager;
  public infoPanel: InfoPanel;
  public settings: Settings;
  // Boiler plate
  private alwaysFixTopActions: boolean;
  private alwaysFixTopActionsMaximum: number;
  public tooltipsToMap: [tooltipId: number, card_id: string][] = [];
  private _helpMode = false; // Use to implement help mode
  private _notif_uid_to_log_id = {};
  private _notif_uid_to_mobile_log_id = {};
  private _last_notif = null;
  public _last_tooltip_id = 0;
  private _selectableNodes = []; // TODO: use to keep track of selectable classed?
  public _connections: unknown[];

  public gameMap: GameMap;
  // public gameOptions: BayonetsAndTomahawksGamedatas['gameOptions'];
  public notificationManager: NotificationManager;
  public playerManager: PlayerManager;
  public tooltipManager: TooltipManager;
  public playAreaScale: number;
  public pools: Pools;

  public activeStates: {};

  constructor() {
    console.log("bayonetsandtomahawks constructor");
  }

  /*
    setup:
    
    This method must set up the game user interface according to current game situation specified
    in parameters.
    
    The method is called each time the game interface is displayed to a player, ie:
    _ when the game starts
    _ when a player refreshes the game page (F5)
    
    "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
  */
  public setup(gamedatas: BayonetsAndTomahawksGamedatas) {
    // Create a new div for buttons to avoid BGA auto clearing it
    dojo.place(
      "<div id='customActions' style='display:inline-block'></div>",
      $("generalactions"),
      "after"
    );
    // this.setAlwaysFixTopActions();
    // this.setupDontPreloadImages();

    this.gamedatas = gamedatas;
    // this.gameOptions = gamedatas.gameOptions;
    debug("gamedatas", gamedatas);

    this._connections = [];
    // Will store all data for active player and gets refreshed with entering player actions state
    this.activeStates = {};

    this.infoPanel = new InfoPanel(this);
    this.settings = new Settings(this);

    this.animationManager = new AnimationManager(this, {
      duration:
        this.settings.get({ id: PREF_SHOW_ANIMATIONS }) === DISABLED
          ? 0
          : 2100 - (this.settings.get({ id: PREF_ANIMATION_SPEED }) as number),
    });

    this.gameMap = new GameMap(this);
    this.pools = new Pools(this);
    // this.tooltipManager = new TooltipManager(this);
    // this.playerManager = new PlayerManager(this);

    // this.updatePlayAreaSize();
    // window.addEventListener("resize", () => {
    //   this.updatePlayAreaSize();
    //   // this.gameMap.updateGameMapSize();
    // });

    if (this.notificationManager != undefined) {
      this.notificationManager.destroy();
    }
    this.notificationManager = new NotificationManager(this);
    this.notificationManager.setupNotifications();

    // TO CHECK: add tooltips to log here?
    // dojo.connect(this.framework().notifqueue, 'addToLog', () => {
    //   this.checkLogCancel(this._last_notif == null ? null : this._last_notif.msg.uid);
    //   this.addLogClass();
    //   this.tooltipManager.checkLogTooltip(this._last_notif);
    // });
    // this.setupNotifications();
    // this.tooltipManager.setupTooltips();
    debug("Ending game setup");
  }

  // public updatePlayAreaSize() {
  //   const playAreaContainer = document.getElementById("bt_play_area_container");
  //   this.playAreaScale = Math.min(
  //     1,
  //     playAreaContainer.offsetWidth / MIN_PLAY_AREA_WIDTH
  //   );
  //   const playArea = document.getElementById("bt_play_area");
  //   playArea.style.transform = `scale(${this.playAreaScale})`;
  //   const playAreaHeight = playArea.offsetHeight;
  //   playArea.style.width =
  //     playAreaContainer.offsetWidth / this.playAreaScale + "px";
  //   console.log("playAreaHeight", playAreaHeight);
  //   playAreaContainer.style.height = playAreaHeight * this.playAreaScale + "px";
  // }

  //  .####.##....##.########.########.########.....###.....######..########.####..#######..##....##
  //  ..##..###...##....##....##.......##.....##...##.##...##....##....##.....##..##.....##.###...##
  //  ..##..####..##....##....##.......##.....##..##...##..##..........##.....##..##.....##.####..##
  //  ..##..##.##.##....##....######...########..##.....##.##..........##.....##..##.....##.##.##.##
  //  ..##..##..####....##....##.......##...##...#########.##..........##.....##..##.....##.##..####
  //  ..##..##...###....##....##.......##....##..##.....##.##....##....##.....##..##.....##.##...###
  //  .####.##....##....##....########.##.....##.##.....##..######.....##....####..#######..##....##

  ///////////////////////////////////////////////////
  //// Game & client states

  // onEnteringState: this method is called each time we are entering into a new game state.
  //                  You can use this method to perform some user interface changes at this moment.
  public onEnteringState(stateName: string, args: any) {
    console.log("Entering state: " + stateName, args);
    // UI changes for active player
    if (
      this.framework().isCurrentPlayerActive() &&
      this.activeStates[stateName]
    ) {
      this.activeStates[stateName].onEnteringState(args.args);
    }
    if (this.framework().isCurrentPlayerActive()) {
      this.addPrimaryActionButton({
        id: "pass_button",
        text: _("Pass"),
        callback: () => this.takeAction({ action: "passTurn" }),
      });
      this.addDangerActionButton({
        id: "end_game_button",
        text: _("End game"),
        callback: () => this.takeAction({ action: "endGame" }),
      });
    }
  }

  // onLeavingState: this method is called each time we are leaving a game state.
  //                 You can use this method to perform some user interface changes at this moment.
  //
  public onLeavingState(stateName: string) {
    console.log("Leaving state: " + stateName);
    this.clearPossible();
  }

  // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
  //                        action status bar (ie: the HTML links in the status bar).
  //
  public onUpdateActionButtons(stateName: string, args: any) {
    // console.log('onUpdateActionButtons: ' + stateName);
  }

  //  .##.....##.########.####.##.......####.########.##....##
  //  .##.....##....##.....##..##........##.....##.....##..##.
  //  .##.....##....##.....##..##........##.....##......####..
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  .##.....##....##.....##..##........##.....##.......##...
  //  ..#######.....##....####.########.####....##.......##...

  ///////////////////////////////////////////////////
  //// Utility methods - add in alphabetical order

  /*
   * Add a blue/grey button if it doesn't already exists
   */
  addActionButtonClient({
    id,
    text,
    callback,
    extraClasses,
    color = "none",
  }: {
    id: string;
    text: string;
    callback: Function | string;
    extraClasses?: string;
    color?: "blue" | "gray" | "red" | "none";
  }) {
    if ($(id)) {
      return;
    }
    this.framework().addActionButton(
      id,
      text,
      callback,
      "customActions",
      false,
      color
    );
    if (extraClasses) {
      dojo.addClass(id, extraClasses);
    }
  }

  addCancelButton() {
    this.addDangerActionButton({
      id: "cancel_btn",
      text: _("Cancel"),
      callback: () => this.onCancel(),
    });
  }

  addUndoButton() {
    this.addDangerActionButton({
      id: "undo_btn",
      text: _("Undo"),
      callback: () => this.takeAction({ action: "restart" }),
    });
  }

  addPrimaryActionButton({
    id,
    text,
    callback,
    extraClasses,
  }: {
    id: string;
    text: string;
    callback: Function | string;
    extraClasses?: string;
  }) {
    if ($(id)) {
      return;
    }
    this.framework().addActionButton(
      id,
      text,
      callback,
      "customActions",
      false,
      "blue"
    );
    if (extraClasses) {
      dojo.addClass(id, extraClasses);
    }
  }

  addSecondaryActionButton({
    id,
    text,
    callback,
    extraClasses,
  }: {
    id: string;
    text: string;
    callback: Function | string;
    extraClasses?: string;
  }) {
    if ($(id)) {
      return;
    }
    this.framework().addActionButton(
      id,
      text,
      callback,
      "customActions",
      false,
      "gray"
    );
    if (extraClasses) {
      dojo.addClass(id, extraClasses);
    }
  }

  addDangerActionButton({
    id,
    text,
    callback,
    extraClasses,
  }: {
    id: string;
    text: string;
    callback: Function | string;
    extraClasses?: string;
  }) {
    if ($(id)) {
      return;
    }
    this.framework().addActionButton(
      id,
      text,
      callback,
      "customActions",
      false,
      "red"
    );
    if (extraClasses) {
      dojo.addClass(id, extraClasses);
    }
  }

  public clearInterface() {
    console.log("clear interface");
    this.playerManager.clearInterface();
  }

  clearPossible() {
    this.framework().removeActionButtons();
    dojo.empty("customActions");

    dojo.forEach(this._connections, dojo.disconnect);
    this._connections = [];

    dojo.query(`.${BT_SELECTABLE}`).removeClass(BT_SELECTABLE);
    dojo.query(`.${BT_SELECTED}`).removeClass(BT_SELECTED);
  }

  public getPlayerId(): number {
    return Number(this.framework().player_id);
  }

  public getCurrentPlayer(): BatPlayer {
    return this.playerManager.getPlayer({ playerId: this.getPlayerId() });
  }

  /**
   * Typescript wrapper for framework functions
   */
  public framework(): Framework {
    return this as unknown as Framework;
  }

  onCancel() {
    this.clearPossible();
    this.framework().restoreServerGameState();
  }

  clientUpdatePageTitle({
    text,
    args,
  }: {
    text: string;
    args: Record<string, string | number>;
  }) {
    this.gamedatas.gamestate.descriptionmyturn = this.format_string_recursive(
      _(text),
      args
    );
    this.framework().updatePageTitle();
  }
  // .########...#######..####.##.......########.########.
  // .##.....##.##.....##..##..##.......##.......##.....##
  // .##.....##.##.....##..##..##.......##.......##.....##
  // .########..##.....##..##..##.......######...########.
  // .##.....##.##.....##..##..##.......##.......##...##..
  // .##.....##.##.....##..##..##.......##.......##....##.
  // .########...#######..####.########.########.##.....##

  // .########..##..........###....########.########
  // .##.....##.##.........##.##......##....##......
  // .##.....##.##........##...##.....##....##......
  // .########..##.......##.....##....##....######..
  // .##........##.......#########....##....##......
  // .##........##.......##.....##....##....##......
  // .##........########.##.....##....##....########

  /*
   * Custom connect that keep track of all the connections
   *  and wrap clicks to make it work with help mode
   */
  connect(node: HTMLElement, action: string, callback: Function) {
    this._connections.push(dojo.connect($(node), action, callback));
  }

  onClick(node: HTMLElement, callback: Function, temporary = true) {
    let safeCallback = (evt) => {
      evt.stopPropagation();
      if (this.framework().isInterfaceLocked()) {
        return false;
      }
      if (this._helpMode) {
        return false;
      }
      callback(evt);
    };

    if (temporary) {
      this.connect($(node), "click", safeCallback);
      dojo.removeClass(node, "unselectable"); // replace with pr_selectable / pr_selected
      dojo.addClass(node, "selectable");
      this._selectableNodes.push(node);
    } else {
      dojo.connect($(node), "click", safeCallback);
    }
  }

  public updateLayout() {
    if (!this.settings) {
      return;
    }

    $("play_area_container").setAttribute(
      "data-two-columns",
      this.settings.get({ id: "twoColumnsLayout" })
    );

    const ROOT = document.documentElement;
    let WIDTH = $("play_area_container").getBoundingClientRect()["width"] - 8;
    const LEFT_COLUMN = 1500;
    const RIGHT_COLUMN = 1500;

    if (this.settings.get({ id: "twoColumnsLayout" }) === PREF_ENABLED) {
      WIDTH = WIDTH - 8; // grid gap
      const size = Number(this.settings.get({ id: "columnSizes" }));
      const proportions = [size, 100 - size];
      const LEFT_SIZE = (proportions[0] * WIDTH) / 100;
      const leftColumnScale = LEFT_SIZE / LEFT_COLUMN;
      ROOT.style.setProperty("--leftColumnScale", `${leftColumnScale}`);

      const RIGHT_SIZE = (proportions[1] * WIDTH) / 100;
      const rightColumnScale = RIGHT_SIZE / RIGHT_COLUMN;
      ROOT.style.setProperty("--rightColumnScale", `${rightColumnScale}`);

      $(
        "play_area_container"
      ).style.gridTemplateColumns = `${LEFT_SIZE}px ${RIGHT_SIZE}px`;
    } else {
      const LEFT_SIZE = WIDTH;
      const leftColumnScale = LEFT_SIZE / LEFT_COLUMN;
      ROOT.style.setProperty("--leftColumnScale", `${leftColumnScale}`);
      const RIGHT_SIZE = WIDTH;
      const rightColumnScale = RIGHT_SIZE / RIGHT_COLUMN;
      ROOT.style.setProperty("--leftColumnScale", `${rightColumnScale}`);
    }
  }

  // .########.########.....###....##.....##.########.##......##..#######..########..##....##
  // .##.......##.....##...##.##...###...###.##.......##..##..##.##.....##.##.....##.##...##.
  // .##.......##.....##..##...##..####.####.##.......##..##..##.##.....##.##.....##.##..##..
  // .######...########..##.....##.##.###.##.######...##..##..##.##.....##.########..#####...
  // .##.......##...##...#########.##.....##.##.......##..##..##.##.....##.##...##...##..##..
  // .##.......##....##..##.....##.##.....##.##.......##..##..##.##.....##.##....##..##...##.
  // .##.......##.....##.##.....##.##.....##.########..###..###...#######..##.....##.##....##

  // ..#######..##.....##.########.########..########..####.########..########..######.
  // .##.....##.##.....##.##.......##.....##.##.....##..##..##.....##.##.......##....##
  // .##.....##.##.....##.##.......##.....##.##.....##..##..##.....##.##.......##......
  // .##.....##.##.....##.######...########..########...##..##.....##.######....######.
  // .##.....##..##...##..##.......##...##...##...##....##..##.....##.##.............##
  // .##.....##...##.##...##.......##....##..##....##...##..##.....##.##.......##....##
  // ..#######.....###....########.##.....##.##.....##.####.########..########..######.

  /*
   * Remove non standard zoom property
   */
  onScreenWidthChange() {
    this.updateLayout();
  }

  /* @Override */
  format_string_recursive(log: string, args: Record<string, unknown>): string {
    try {
      if (log && args && !args.processed) {
        args.processed = true;

        // replace all keys that start with 'logToken'
        Object.entries(args).forEach(([key, value]) => {
          if (key.startsWith("tkn_")) {
            args[key] = getTokenDiv({
              key,
              value: value as string,
              game: this,
            });
          }
        });

        // TODO: check below code. Looks like improved way for text shadows (source ticket to ride)
        // ['you', 'actplayer', 'player_name'].forEach((field) => {
        //   if (typeof args[field] === 'string' && args[field].indexOf('#ffed00;') !== -1 && args[field].indexOf('text-shadow') === -1) {
        //     args[field] = args[field].replace('#ffed00;', '#ffed00; text-shadow: 0 0 1px black, 0 0 2px black, 0 0 3px black;');
        //   }
        // });
      }
    } catch (e) {
      console.error(log, args, "Exception thrown", e.stack);
    }
    return (this as any).inherited(arguments);
  }

  /*
   * [Undocumented] Called by BGA framework on any notification message
   * Handle cancelling log messages for restart turn
   */
  onPlaceLogOnChannel(msg: Notif<unknown>) {
    // console.log('msg', msg);
    const currentLogId = this.framework().notifqueue.next_log_id;
    const res = this.framework().inherited(arguments);
    this._notif_uid_to_log_id[msg.uid] = currentLogId;
    this._last_notif = {
      logId: currentLogId,
      msg,
    };
    // console.log('_notif_uid_to_log_id', this._notif_uid_to_log_id);
    return res;
  }

  /*
   * cancelLogs:
   *   strikes all log messages related to the given array of notif ids
   */
  checkLogCancel(notifId) {
    if (
      this.gamedatas.canceledNotifIds != null &&
      this.gamedatas.canceledNotifIds.includes(notifId)
    ) {
      this.cancelLogs([notifId]);
    }
  }

  public cancelLogs(notifIds: string[]) {
    notifIds.forEach((uid) => {
      if (this._notif_uid_to_log_id.hasOwnProperty(uid)) {
        let logId = this._notif_uid_to_log_id[uid];
        if ($("log_" + logId)) dojo.addClass("log_" + logId, "cancel");
      }
    });
  }

  addLogClass() {
    if (this._last_notif == null) return;

    let notif = this._last_notif;
    if ($("log_" + notif.logId)) {
      let type = notif.msg.type;
      if (type == "history_history") type = notif.msg.args.originalType;

      dojo.addClass("log_" + notif.logId, "notif_" + type);
    }
  }

  /*
   * [Undocumented] Override BGA framework functions to call onLoadingComplete when loading is done
   */
  setLoader(value, max) {
    this.framework().inherited(arguments);
    if (!this.framework().isLoadingComplete && value >= 100) {
      this.framework().isLoadingComplete = true;
      this.onLoadingComplete();
    }
  }

  onLoadingComplete() {
    // debug('Loading complete');
    this.cancelLogs(this.gamedatas.canceledNotifIds);
  }

  /* @Override */
  updatePlayerOrdering() {
    this.framework().inherited(arguments);

    const container = document.getElementById("player_boards");
    const infoPanel = document.getElementById("info_panel");
    if (!container) {
      return;
    }
    container.insertAdjacentElement("afterbegin", infoPanel);
  }

  setAlwaysFixTopActions(alwaysFixed = true, maximum = 30) {
    this.alwaysFixTopActions = alwaysFixed;
    this.alwaysFixTopActionsMaximum = maximum;
    this.adaptStatusBar();
  }

  adaptStatusBar() {
    (this as any).inherited(arguments);

    if (this.alwaysFixTopActions) {
      const afterTitleElem = document.getElementById("after-page-title");
      const titleElem = document.getElementById("page-title");
      let zoom = (getComputedStyle(titleElem) as any).zoom;
      if (!zoom) {
        zoom = 1;
      }

      const titleRect = afterTitleElem.getBoundingClientRect();
      if (
        titleRect.top < 0 &&
        titleElem.offsetHeight <
          (window.innerHeight * this.alwaysFixTopActionsMaximum) / 100
      ) {
        const afterTitleRect = afterTitleElem.getBoundingClientRect();
        titleElem.classList.add("fixed-page-title");
        titleElem.style.width = (afterTitleRect.width - 10) / zoom + "px";
        afterTitleElem.style.height = titleRect.height + "px";
      } else {
        titleElem.classList.remove("fixed-page-title");
        titleElem.style.width = "auto";
        afterTitleElem.style.height = "0px";
      }
    }
  }

  // .########..#######......######..##.....##.########..######..##....##
  // ....##....##.....##....##....##.##.....##.##.......##....##.##...##.
  // ....##....##.....##....##.......##.....##.##.......##.......##..##..
  // ....##....##.....##....##.......#########.######...##.......#####...
  // ....##....##.....##....##.......##.....##.##.......##.......##..##..
  // ....##....##.....##....##....##.##.....##.##.......##....##.##...##.
  // ....##.....#######......######..##.....##.########..######..##....##

  //....###..........##....###....##.....##
  //...##.##.........##...##.##....##...##.
  //..##...##........##..##...##....##.##..
  //.##.....##.......##.##.....##....###...
  //.#########.##....##.#########...##.##..
  //.##.....##.##....##.##.....##..##...##.
  //.##.....##..######..##.....##.##.....##

  actionError(actionName: string) {
    this.framework().showMessage(`cannot take ${actionName} action`, "error");
  }

  /*
   * Make an AJAX call with automatic lock
   */
  takeAction({
    action,
    data = {},
  }: {
    action: string;
    data?: Record<string, unknown>;
  }) {
    console.log(`takeAction ${action}`, data);
    if (!this.framework().checkAction(action)) {
      this.actionError(action);
      return;
    }
    data.lock = true;
    const gameName = this.framework().game_name;
    this.framework().ajaxcall(
      `/${gameName}/${gameName}/${action}.html`,
      data,
      this,
      () => {}
    );
  }
}
