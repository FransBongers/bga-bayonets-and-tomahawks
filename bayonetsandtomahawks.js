var Modal = (function () {
    function Modal(id, config) {
        var _this = this;
        this.open = false;
        this.container = 'ebd-body';
        this.class = 'custom_popin';
        this.autoShow = false;
        this.modalTpl = "\n    <div id='popin_${id}_container' class=\"${class}_container\">\n      <div id='popin_${id}_underlay' class=\"${class}_underlay\"></div>\n      <div id='popin_${id}_wrapper' class=\"${class}_wrapper\">\n        <div id=\"popin_${id}\" class=\"${class}\">\n          ${titleTpl}\n          ${closeIconTpl}\n          ${helpIconTpl}\n          ${contentsTpl}\n        </div>\n      </div>\n    </div>\n  ";
        this.closeIcon = 'fa-times-circle';
        this.closeIconTpl = '<a id="popin_${id}_close" class="${class}_closeicon"><i class="fa ${closeIcon} fa-2x" aria-hidden="true"></i></a>';
        this.closeAction = 'destroy';
        this.closeWhenClickOnUnderlay = true;
        this.helpIcon = null;
        this.helpLink = '#';
        this.helpIconTpl = '<a href="${helpLink}" target="_blank" id="popin_${id}_help" class="${class}_helpicon"><i class="fa ${helpIcon} fa-2x" aria-hidden="true"></i></a>';
        this.title = null;
        this.titleTpl = '<h2 id="popin_${id}_title" class="${class}_title">${title}</h2>';
        this.contentsTpl = "\n      <div id=\"popin_${id}_contents\" class=\"${class}_contents\">\n        ${contents}\n      </div>";
        this.contents = '';
        this.verticalAlign = 'center';
        this.animationDuration = 500;
        this.fadeIn = true;
        this.fadeOut = true;
        this.openAnimation = false;
        this.openAnimationTarget = null;
        this.openAnimationDelta = 200;
        this.onShow = null;
        this.onHide = null;
        this.statusElt = null;
        this.scale = 1;
        this.breakpoint = null;
        if (id === undefined) {
            console.error('You need an ID to create a modal');
            throw 'You need an ID to create a modal';
        }
        this.id = id;
        Object.entries(config).forEach(function (_a) {
            var key = _a[0], value = _a[1];
            if (value !== undefined) {
                _this[key] = value;
            }
        });
        this.create();
        if (this.autoShow)
            this.show();
    }
    Modal.prototype.isDisplayed = function () {
        return this.open;
    };
    Modal.prototype.isCreated = function () {
        return this.id != null;
    };
    Modal.prototype.create = function () {
        var _this = this;
        dojo.destroy('popin_' + this.id + '_container');
        var titleTpl = this.title == null ? '' : dojo.string.substitute(this.titleTpl, this);
        var closeIconTpl = this.closeIcon == null ? '' : dojo.string.substitute(this.closeIconTpl, this);
        var helpIconTpl = this.helpIcon == null ? '' : dojo.string.substitute(this.helpIconTpl, this);
        var contentsTpl = dojo.string.substitute(this.contentsTpl, this);
        var modalTpl = dojo.string.substitute(this.modalTpl, {
            id: this.id,
            class: this.class,
            titleTpl: titleTpl,
            closeIconTpl: closeIconTpl,
            helpIconTpl: helpIconTpl,
            contentsTpl: contentsTpl,
        });
        dojo.place(modalTpl, this.container);
        dojo.style('popin_' + this.id + '_container', {
            display: 'none',
            position: 'absolute',
            left: '0px',
            top: '0px',
            width: '100%',
            height: '100%',
        });
        dojo.style('popin_' + this.id + '_underlay', {
            position: 'absolute',
            left: '0px',
            top: '0px',
            width: '100%',
            height: '100%',
            zIndex: 949,
            opacity: 0,
            backgroundColor: 'white',
        });
        dojo.style('popin_' + this.id + '_wrapper', {
            position: 'fixed',
            left: '0px',
            top: '0px',
            width: 'min(100%,100vw)',
            height: '100vh',
            zIndex: 950,
            opacity: 0,
            display: 'flex',
            justifyContent: 'center',
            alignItems: this.verticalAlign,
            paddingTop: this.verticalAlign == 'center' ? 0 : '125px',
            transformOrigin: 'top left',
        });
        this.adjustSize();
        this.resizeListener = dojo.connect(window, 'resize', function () { return _this.adjustSize(); });
        if (this.closeIcon != null && $('popin_' + this.id + '_close')) {
            dojo.connect($('popin_' + this.id + '_close'), 'click', function () { return _this[_this.closeAction](); });
        }
        if (this.closeWhenClickOnUnderlay) {
            dojo.connect($('popin_' + this.id + '_underlay'), 'click', function () { return _this[_this.closeAction](); });
            dojo.connect($('popin_' + this.id + '_wrapper'), 'click', function () { return _this[_this.closeAction](); });
            dojo.connect($('popin_' + this.id), 'click', function (evt) { return evt.stopPropagation(); });
        }
    };
    Modal.prototype.updateContent = function (newContent) {
        var contentContainerId = "popin_".concat(this.id, "_contents");
        dojo.empty(contentContainerId);
        dojo.place(newContent, contentContainerId);
    };
    Modal.prototype.adjustSize = function () {
        var bdy = dojo.position(this.container);
        dojo.style('popin_' + this.id + '_container', {
            width: bdy.w + 'px',
            height: bdy.h + 'px',
        });
        if (this.breakpoint != null) {
            var newModalWidth = bdy.w * this.scale;
            var modalScale = newModalWidth / this.breakpoint;
            if (modalScale > 1)
                modalScale = 1;
            dojo.style('popin_' + this.id, {
                transform: "scale(".concat(modalScale, ")"),
                transformOrigin: this.verticalAlign == 'center' ? 'center center' : 'top center',
            });
        }
    };
    Modal.prototype.getOpeningTargetCenter = function () {
        var startTop, startLeft;
        if (this.openAnimationTarget == null) {
            startLeft = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0) / 2;
            startTop = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0) / 2;
        }
        else {
            var target = dojo.position(this.openAnimationTarget);
            startLeft = target.x + target.w / 2;
            startTop = target.y + target.h / 2;
        }
        return {
            x: startLeft,
            y: startTop,
        };
    };
    Modal.prototype.fadeInAnimation = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var containerId = 'popin_' + _this.id + '_container';
            if (!$(containerId))
                reject();
            if (_this.runningAnimation)
                _this.runningAnimation.stop();
            var duration = _this.fadeIn ? _this.animationDuration : 0;
            var animations = [];
            animations.push(dojo.fadeIn({
                node: 'popin_' + _this.id + '_wrapper',
                duration: duration,
            }));
            animations.push(dojo.animateProperty({
                node: 'popin_' + _this.id + '_underlay',
                duration: duration,
                properties: { opacity: { start: 0, end: 0.7 } },
            }));
            if (_this.openAnimation) {
                var pos = _this.getOpeningTargetCenter();
                animations.push(dojo.animateProperty({
                    node: 'popin_' + _this.id + '_wrapper',
                    properties: {
                        transform: { start: 'scale(0)', end: 'scale(1)' },
                        top: { start: pos.y, end: 0 },
                        left: { start: pos.x, end: 0 },
                    },
                    duration: _this.animationDuration + _this.openAnimationDelta,
                }));
            }
            _this.runningAnimation = dojo.fx.combine(animations);
            dojo.connect(_this.runningAnimation, 'onEnd', function () { return resolve(); });
            _this.runningAnimation.play();
            setTimeout(function () {
                if ($('popin_' + _this.id + '_container'))
                    dojo.style('popin_' + _this.id + '_container', 'display', 'block');
            }, 10);
        });
    };
    Modal.prototype.show = function () {
        var _this = this;
        if (this.isOpening || this.open)
            return;
        if (this.statusElt !== null) {
            dojo.addClass(this.statusElt, 'opened');
        }
        this.adjustSize();
        this.isOpening = true;
        this.isClosing = false;
        this.fadeInAnimation().then(function () {
            if (!_this.isOpening)
                return;
            _this.isOpening = false;
            _this.open = true;
            if (_this.onShow !== null) {
                _this.onShow();
            }
        });
    };
    Modal.prototype.fadeOutAnimation = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            var containerId = 'popin_' + _this.id + '_container';
            if (!$(containerId))
                reject();
            if (_this.runningAnimation)
                _this.runningAnimation.stop();
            var duration = _this.fadeOut ? _this.animationDuration + (_this.openAnimation ? _this.openAnimationDelta : 0) : 0;
            var animations = [];
            animations.push(dojo.fadeOut({
                node: 'popin_' + _this.id + '_wrapper',
                duration: duration,
            }));
            animations.push(dojo.animateProperty({
                node: 'popin_' + _this.id + '_underlay',
                duration: duration,
                properties: { opacity: { start: 0.7, end: 0 } },
            }));
            if (_this.openAnimation) {
                var pos = _this.getOpeningTargetCenter();
                animations.push(dojo.animateProperty({
                    node: 'popin_' + _this.id + '_wrapper',
                    properties: {
                        transform: { start: 'scale(1)', end: 'scale(0)' },
                        top: { start: 0, end: pos.y },
                        left: { start: 0, end: pos.x },
                    },
                    duration: _this.animationDuration,
                }));
            }
            _this.runningAnimation = dojo.fx.combine(animations);
            dojo.connect(_this.runningAnimation, 'onEnd', function () { return resolve(); });
            _this.runningAnimation.play();
        });
    };
    Modal.prototype.hide = function () {
        var _this = this;
        if (this.isClosing)
            return;
        this.isClosing = true;
        this.isOpening = false;
        this.fadeOutAnimation().then(function () {
            if (!_this.isClosing || _this.isOpening)
                return;
            _this.isClosing = false;
            _this.open = false;
            dojo.style('popin_' + _this.id + '_container', 'display', 'none');
            if (_this.onHide !== null) {
                _this.onHide();
            }
            if (_this.statusElt !== null) {
                dojo.removeClass(_this.statusElt, 'opened');
            }
        });
    };
    Modal.prototype.destroy = function () {
        var _this = this;
        if (this.isClosing)
            return;
        this.isOpening = false;
        this.isClosing = true;
        this.fadeOutAnimation().then(function () {
            if (!_this.isClosing || _this.isOpening)
                return;
            _this.isClosing = false;
            _this.open = false;
            _this.kill();
        });
    };
    Modal.prototype.kill = function () {
        if (this.runningAnimation)
            this.runningAnimation.stop();
        var underlayId = 'popin_' + this.id + '_container';
        dojo.destroy(underlayId);
        dojo.disconnect(this.resizeListener);
        this.id = null;
        if (this.statusElt !== null) {
            dojo.removeClass(this.statusElt, 'opened');
        }
    };
    return Modal;
}());
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (g && (g = 0, op[0] && (_ = 0)), _) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var __spreadArray = (this && this.__spreadArray) || function (to, from, pack) {
    if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
        if (ar || !(i in from)) {
            if (!ar) ar = Array.prototype.slice.call(from, 0, i);
            ar[i] = from[i];
        }
    }
    return to.concat(ar || Array.prototype.slice.call(from));
};
var BgaZone = (function () {
    function BgaZone(config) {
        var _this = this;
        this.animateMoveToZone = function (_a) {
            var fromRect = _a.fromRect, element = _a.element, classesToAdd = _a.classesToAdd, classesToRemove = _a.classesToRemove, zIndex = _a.zIndex, duration = _a.duration;
            return __awaiter(_this, void 0, void 0, function () {
                var _b, _c;
                return __generator(this, function (_d) {
                    switch (_d.label) {
                        case 0:
                            (_b = element.classList).remove.apply(_b, (classesToRemove || []));
                            (_c = element.classList).add.apply(_c, (classesToAdd || []));
                            this.setItemCoords({ node: element });
                            return [4, this.animationManager.play(new BgaSlideAnimation({
                                    element: element,
                                    transitionTimingFunction: "linear",
                                    fromRect: fromRect,
                                    zIndex: zIndex,
                                    duration: duration,
                                }))];
                        case 1:
                            _d.sent();
                            return [2];
                    }
                });
            });
        };
        var animationManager = config.animationManager, itemGap = config.itemGap, itemHeight = config.itemHeight, itemWidth = config.itemWidth, containerId = config.containerId;
        this.animationManager = animationManager;
        this.itemGap = itemGap || 0;
        this.itemHeight = itemHeight;
        this.itemWidth = itemWidth;
        this.containerId = containerId;
        this.containerElement = document.getElementById(containerId);
        this.items = [];
        this.setPattern(config.pattern || "grid");
        this.autoWidth = false;
        this.autoHeight = true;
        this.customPattern = config.customPattern;
        if (!this.containerElement) {
            console.error("containerElement null");
            return;
        }
        if (getComputedStyle(this.containerElement).position !== "absolute") {
            this.containerElement.style.position = "relative";
        }
    }
    BgaZone.prototype.getContainerId = function () {
        return this.containerId;
    };
    BgaZone.prototype.remove = function (_a) {
        var input = _a.input, _b = _a.destroy, destroy = _b === void 0 ? false : _b;
        return __awaiter(this, void 0, void 0, function () {
            var itemsToRemove;
            var _this = this;
            return __generator(this, function (_c) {
                itemsToRemove = Array.isArray(input) ? input : [input];
                itemsToRemove.forEach(function (id) {
                    var index = _this.items.findIndex(function (item) { return item.id === id; });
                    if (index < 0) {
                        return;
                    }
                    _this.items.splice(index, 1);
                    if (destroy) {
                        var element = $(id);
                        element && element.remove();
                    }
                });
                return [2, this.updateDisplay()];
            });
        });
    };
    BgaZone.prototype.removeAll = function (_a) {
        var _b = _a === void 0 ? { destroy: false } : _a, destroy = _b.destroy;
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_c) {
                if (destroy) {
                    this.items.forEach(function (item) {
                        var id = item.id;
                        var node = $(id);
                        node.remove();
                    });
                }
                this.items = [];
                return [2, this.updateDisplay()];
            });
        });
    };
    BgaZone.prototype.moveToZone = function (_a) {
        var input = _a.items, classesToAdd = _a.classesToAdd, classesToRemove = _a.classesToRemove, _b = _a.animationSettings, animationSettings = _b === void 0 ? {} : _b, inputItemsToRemove = _a.itemsToRemove;
        return __awaiter(this, void 0, void 0, function () {
            var items, itemsToRemove, animations, duration, zIndex;
            var _this = this;
            return __generator(this, function (_c) {
                switch (_c.label) {
                    case 0:
                        items = Array.isArray(input) ? input : [input];
                        if (inputItemsToRemove) {
                            itemsToRemove = Array.isArray(inputItemsToRemove.elements)
                                ? inputItemsToRemove.elements
                                : [inputItemsToRemove.elements];
                            itemsToRemove.forEach(function (id) {
                                var index = _this.items.findIndex(function (item) { return item.id === id; });
                                if (index < 0) {
                                    return;
                                }
                                _this.items.splice(index, 1);
                                if (inputItemsToRemove.destroy) {
                                    var element = $(id);
                                    element && element.remove();
                                }
                            });
                        }
                        items.forEach(function (_a) {
                            var id = _a.id, weight = _a.weight;
                            _this.items.push({
                                id: id,
                                weight: weight,
                            });
                        });
                        this.sortItems();
                        animations = [];
                        duration = animationSettings.duration, zIndex = animationSettings.zIndex;
                        items.forEach(function (item) {
                            var element = document.getElementById(item.id);
                            if (!element) {
                                console.error("newElement null");
                                return;
                            }
                            var fromRect = element.getBoundingClientRect();
                            var attachTo = document.getElementById(_this.containerId);
                            attachTo.appendChild(element);
                            animations.push(_this.animateMoveToZone({
                                element: element,
                                classesToAdd: classesToAdd,
                                classesToRemove: classesToRemove,
                                zIndex: zIndex,
                                duration: duration,
                                fromRect: fromRect,
                            }));
                        });
                        return [4, Promise.all(__spreadArray(__spreadArray([], this.getUpdateAnimations(items.map(function (_a) {
                                var id = _a.id;
                                return id;
                            })).map(function (anim) {
                                return _this.animationManager.play(anim);
                            }), true), animations, true))];
                    case 1:
                        _c.sent();
                        return [2];
                }
            });
        });
    };
    BgaZone.prototype.setItemCoords = function (_a) {
        var node = _a.node;
        var index = this.items.findIndex(function (item) { return item.id === node.id; });
        var coords = this.itemToCoords({ index: index });
        var top = coords.y, left = coords.x;
        node.style.position = "absolute";
        node.style.top = "".concat(top, "px");
        node.style.left = "".concat(left, "px");
    };
    BgaZone.prototype.placeInZone = function (_a) {
        var input = _a.items, _b = _a.animationSettings, animationSettings = _b === void 0 ? {} : _b;
        return __awaiter(this, void 0, void 0, function () {
            var inputItems, duration, animations;
            var _this = this;
            return __generator(this, function (_c) {
                switch (_c.label) {
                    case 0:
                        inputItems = Array.isArray(input) ? input : [input];
                        inputItems.forEach(function (_a) {
                            var id = _a.id, weight = _a.weight;
                            _this.items.push({ id: id, weight: weight });
                        });
                        this.sortItems();
                        duration = animationSettings.duration;
                        animations = [];
                        inputItems.forEach(function (_a) {
                            var _b;
                            var element = _a.element, id = _a.id, from = _a.from, zIndex = _a.zIndex;
                            var node = dojo.place(element, _this.containerId);
                            node.style.position = "absolute";
                            node.style.zIndex = "".concat(zIndex || 0);
                            _this.setItemCoords({ node: node });
                            if (from) {
                                var fromRect = (_b = $(from)) === null || _b === void 0 ? void 0 : _b.getBoundingClientRect();
                                animations.push(new BgaSlideAnimation({
                                    element: node,
                                    transitionTimingFunction: "linear",
                                    fromRect: fromRect,
                                    duration: duration,
                                }));
                            }
                        });
                        return [4, this.animationManager.playParallel(__spreadArray(__spreadArray([], this.getUpdateAnimations(inputItems.map(function (_a) {
                                var id = _a.id;
                                return id;
                            })), true), animations, true))];
                    case 1:
                        _c.sent();
                        return [2];
                }
            });
        });
    };
    BgaZone.prototype.setupItems = function (_a) {
        var _this = this;
        var input = _a.items;
        var inputItems = Array.isArray(input) ? input : [input];
        inputItems.forEach(function (_a) {
            var id = _a.id, weight = _a.weight;
            _this.items.push({ id: id, weight: weight });
        });
        this.sortItems();
        inputItems.forEach(function (_a) {
            var element = _a.element, zIndex = _a.zIndex;
            var node = dojo.place(element, _this.containerId);
            node.style.position = "absolute";
        });
        this.getUpdateAnimations();
    };
    BgaZone.prototype.updateDisplay = function () {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0: return [4, this.animationManager.playParallel(this.getUpdateAnimations())];
                    case 1: return [2, _a.sent()];
                }
            });
        });
    };
    BgaZone.prototype.getUpdateAnimations = function (skip) {
        var _this = this;
        var animations = [];
        var containerHeight = 0;
        var containerWidth = 0;
        this.items.forEach(function (item, index) {
            var element = $(item.id);
            var fromRect = element.getBoundingClientRect();
            if (element) {
                var _a = _this.itemToCoords({ index: index }), left = _a.x, top_1 = _a.y, width = _a.w, height = _a.h;
                if (!(skip || []).includes(item.id)) {
                    element.style.top = "".concat(top_1, "px");
                    element.style.left = "".concat(left, "px");
                    animations.push(new BgaSlideAnimation({ element: element, fromRect: fromRect }));
                }
                if (_this.containerId === "pp_kabul_transcaspia_border") {
                    console.log(item.id, index, left, top_1, width, height);
                }
                containerWidth = Math.max(containerWidth, left + width);
                containerHeight = Math.max(containerHeight, top_1 + height);
            }
        });
        if (this.autoHeight) {
            this.containerElement.style.height = "".concat(containerHeight, "px");
        }
        if (this.autoWidth) {
            this.containerElement.style.width = "".concat(containerWidth, "px");
        }
        return animations;
    };
    BgaZone.prototype.itemToCoords = function (_a) {
        var index = _a.index;
        var boundingClientRect = this.containerElement.getBoundingClientRect();
        var containerWidth = boundingClientRect.width;
        var containerHeight = boundingClientRect.height;
        var itemCount = this.getItemCount();
        var props = {
            index: index,
            containerHeight: containerHeight,
            containerWidth: containerWidth,
            itemCount: itemCount,
        };
        switch (this.pattern) {
            case "grid":
                return this.itemToCoordsGrid(props);
            case "ellipticalFit":
                return this.itemToCoordsEllipticalFit(props);
            case "verticalFit":
                return this.itemToCoordsVerticalFit(props);
            case "horizontalFit":
                return this.itemToCoordsHorizontalFit(props);
            case "custom":
                var custom = this.customPattern
                    ? this.customPattern(props)
                    : { x: 0, y: 0, w: 0, h: 0 };
                return custom;
        }
    };
    BgaZone.prototype.itemToCoordsGrid = function (_a) {
        var e = _a.index, t = _a.containerWidth;
        var i = Math.max(1, Math.floor(t / (this.itemWidth + this.itemGap))), n = Math.floor(e / i), o = {};
        o["y"] = n * (this.itemHeight + this.itemGap);
        o["x"] = (e - n * i) * (this.itemWidth + this.itemGap);
        o["w"] = this.itemWidth;
        o["h"] = this.itemHeight;
        return o;
    };
    BgaZone.prototype.itemToCoordsEllipticalFit = function (_a) {
        var e = _a.index, t = _a.containerWidth, i = _a.containerHeight, n = _a.itemCount;
        var o = t / 2, a = i / 2, s = 3.1415927, r = {
            w: this.itemWidth,
            h: this.itemHeight,
        };
        r["w"] = this.itemWidth;
        r["h"] = this.itemHeight;
        var l = n - (e + 1);
        if (l <= 4) {
            var c = r.w, d = (r.h * a) / o, h = s + l * ((2 * s) / 5);
            r["x"] = o + c * Math.cos(h) - r.w / 2;
            r["y"] = a + d * Math.sin(h) - r.h / 2;
        }
        else if (l > 4) {
            (c = 2 * r.w),
                (d = (2 * r.h * a) / o),
                (h = s - s / 2 + (l - 4) * ((2 * s) / Math.max(10, n - 5)));
            r["x"] = o + c * Math.cos(h) - r.w / 2;
            r["y"] = a + d * Math.sin(h) - r.h / 2;
        }
        return r;
    };
    BgaZone.prototype.itemToCoordsHorizontalFit = function (_a) {
        var e = _a.index, t = _a.containerWidth, i = _a.containerHeight, n = _a.itemCount;
        var o = {};
        o["w"] = this.itemWidth;
        o["h"] = this.itemHeight;
        var a = n * this.itemWidth;
        if (a <= t)
            var s = this.itemWidth, r = (t - a) / 2;
        else
            (s = (t - this.itemWidth) / (n - 1)), (r = 0);
        o["x"] = Math.round(e * s + r);
        o["y"] = 0;
        return o;
    };
    BgaZone.prototype.itemToCoordsVerticalFit = function (_a) {
        var e = _a.index, i = _a.containerHeight, n = _a.itemCount;
        var o = {};
        o["w"] = this.itemWidth;
        o["h"] = this.itemHeight;
        var a = n * this.itemHeight;
        if (a <= i)
            var s = this.itemHeight, r = (i - a) / 2;
        else
            (s = (i - this.itemHeight) / (n - 1)), (r = 0);
        o["y"] = Math.round(e * s + r);
        o["x"] = 0;
        return o;
    };
    BgaZone.prototype.setPattern = function (pattern) {
        switch (pattern) {
            case "grid":
                this.autoHeight = true;
                this.pattern = pattern;
                break;
            case "verticalFit":
            case "horizontalFit":
            case "ellipticalFit":
                this.autoHeight = false;
                this.pattern = pattern;
                break;
            case "custom":
                this.pattern = pattern;
                break;
            default:
                console.error("zone::setPattern: unknow pattern: " + pattern);
        }
    };
    BgaZone.prototype.sortItems = function () {
        return this.items.sort(function (a, b) {
            var aWeight = a.weight || 0;
            var bWeight = b.weight || 0;
            return aWeight > bWeight ? 1 : aWeight < bWeight ? -1 : 0;
        });
    };
    BgaZone.prototype.removeTo = function (input) {
        return __awaiter(this, void 0, void 0, function () {
            var inputItems, animations;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        inputItems = Array.isArray(input) ? input : [input];
                        animations = [];
                        inputItems.forEach(function (_a) {
                            var id = _a.id, _b = _a.destroy, destroy = _b === void 0 ? true : _b, to = _a.to;
                            var index = _this.items.findIndex(function (item) { return item.id === id; });
                            if (index < 0) {
                                return;
                            }
                            _this.items.splice(index, 1);
                            var element = $(id);
                            var toElement = $(to);
                            var fromRect = element.getBoundingClientRect();
                            var toRect = toElement.getBoundingClientRect();
                            var top = toRect.top - fromRect.top;
                            var left = toRect.left - fromRect.left;
                            element.style.top = "".concat(_this.pxNumber(element.style.top) + top, "px");
                            element.style.left = "".concat(_this.pxNumber(element.style.left) + left, "px");
                            animations.push(_this.animateRemoveTo({ element: element, fromRect: fromRect, destroy: destroy }));
                        });
                        this.sortItems();
                        return [4, Promise.all(__spreadArray(__spreadArray([], this.getUpdateAnimations().map(function (anim) {
                                return _this.animationManager.play(anim);
                            }), true), animations, true))];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    BgaZone.prototype.animateRemoveTo = function (_a) {
        var element = _a.element, fromRect = _a.fromRect, destroy = _a.destroy;
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0: return [4, this.animationManager.play(new BgaSlideAnimation({
                            element: element,
                            fromRect: fromRect,
                        }))];
                    case 1:
                        _b.sent();
                        if (destroy) {
                            element.remove();
                        }
                        return [2];
                }
            });
        });
    };
    BgaZone.prototype.getItems = function () {
        return this.items.map(function (item) { return item.id; });
    };
    BgaZone.prototype.getItemCount = function () {
        return this.items.length;
    };
    BgaZone.prototype.pxNumber = function (px) {
        if ((px || "").endsWith("px")) {
            return Number(px.slice(0, -2));
        }
        else {
            return 0;
        }
    };
    return BgaZone;
}());
var BgaAnimation = (function () {
    function BgaAnimation(animationFunction, settings) {
        this.animationFunction = animationFunction;
        this.settings = settings;
        this.played = null;
        this.result = null;
        this.playWhenNoAnimation = false;
    }
    return BgaAnimation;
}());
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
function attachWithAnimation(animationManager, animation) {
    var _a;
    var settings = animation.settings;
    var element = settings.animation.settings.element;
    var fromRect = element.getBoundingClientRect();
    settings.animation.settings.fromRect = fromRect;
    settings.attachElement.appendChild(element);
    (_a = settings.afterAttach) === null || _a === void 0 ? void 0 : _a.call(settings, element, settings.attachElement);
    return animationManager.play(settings.animation);
}
var BgaAttachWithAnimation = (function (_super) {
    __extends(BgaAttachWithAnimation, _super);
    function BgaAttachWithAnimation(settings) {
        var _this = _super.call(this, attachWithAnimation, settings) || this;
        _this.playWhenNoAnimation = true;
        return _this;
    }
    return BgaAttachWithAnimation;
}(BgaAnimation));
function cumulatedAnimations(animationManager, animation) {
    return animationManager.playSequence(animation.settings.animations);
}
var BgaCumulatedAnimation = (function (_super) {
    __extends(BgaCumulatedAnimation, _super);
    function BgaCumulatedAnimation(settings) {
        var _this = _super.call(this, cumulatedAnimations, settings) || this;
        _this.playWhenNoAnimation = true;
        return _this;
    }
    return BgaCumulatedAnimation;
}(BgaAnimation));
function showScreenCenterAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d;
        var settings = animation.settings;
        var element = settings.element;
        var elementBR = element.getBoundingClientRect();
        var xCenter = (elementBR.left + elementBR.right) / 2;
        var yCenter = (elementBR.top + elementBR.bottom) / 2;
        var x = xCenter - (window.innerWidth / 2);
        var y = yCenter - (window.innerHeight / 2);
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        var transitionTimingFunction = (_b = settings.transitionTimingFunction) !== null && _b !== void 0 ? _b : 'linear';
        element.style.zIndex = "".concat((_c = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _c !== void 0 ? _c : 10);
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionEnd);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms ").concat(transitionTimingFunction);
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_d = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _d !== void 0 ? _d : 0, "deg)");
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaShowScreenCenterAnimation = (function (_super) {
    __extends(BgaShowScreenCenterAnimation, _super);
    function BgaShowScreenCenterAnimation(settings) {
        return _super.call(this, showScreenCenterAnimation, settings) || this;
    }
    return BgaShowScreenCenterAnimation;
}(BgaAnimation));
function slideToAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d, _e;
        var settings = animation.settings;
        var element = settings.element;
        var _f = getDeltaCoordinates(element, settings), x = _f.x, y = _f.y;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        var transitionTimingFunction = (_b = settings.transitionTimingFunction) !== null && _b !== void 0 ? _b : 'linear';
        element.style.zIndex = "".concat((_c = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _c !== void 0 ? _c : 10);
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionEnd);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms ").concat(transitionTimingFunction);
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_d = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _d !== void 0 ? _d : 0, "deg) scale(").concat((_e = settings.scale) !== null && _e !== void 0 ? _e : 1, ")");
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaSlideToAnimation = (function (_super) {
    __extends(BgaSlideToAnimation, _super);
    function BgaSlideToAnimation(settings) {
        return _super.call(this, slideToAnimation, settings) || this;
    }
    return BgaSlideToAnimation;
}(BgaAnimation));
function slideAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a, _b, _c, _d, _e;
        var settings = animation.settings;
        var element = settings.element;
        var _f = getDeltaCoordinates(element, settings), x = _f.x, y = _f.y;
        var duration = (_a = settings.duration) !== null && _a !== void 0 ? _a : 500;
        var originalZIndex = element.style.zIndex;
        var originalTransition = element.style.transition;
        var transitionTimingFunction = (_b = settings.transitionTimingFunction) !== null && _b !== void 0 ? _b : 'linear';
        element.style.zIndex = "".concat((_c = settings === null || settings === void 0 ? void 0 : settings.zIndex) !== null && _c !== void 0 ? _c : 10);
        element.style.transition = null;
        element.offsetHeight;
        element.style.transform = "translate(".concat(-x, "px, ").concat(-y, "px) rotate(").concat((_d = settings === null || settings === void 0 ? void 0 : settings.rotationDelta) !== null && _d !== void 0 ? _d : 0, "deg)");
        var timeoutId = null;
        var cleanOnTransitionEnd = function () {
            element.style.zIndex = originalZIndex;
            element.style.transition = originalTransition;
            success();
            element.removeEventListener('transitioncancel', cleanOnTransitionEnd);
            element.removeEventListener('transitionend', cleanOnTransitionEnd);
            document.removeEventListener('visibilitychange', cleanOnTransitionEnd);
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        };
        var cleanOnTransitionCancel = function () {
            var _a;
            element.style.transition = "";
            element.offsetHeight;
            element.style.transform = (_a = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _a !== void 0 ? _a : null;
            element.offsetHeight;
            cleanOnTransitionEnd();
        };
        element.addEventListener('transitioncancel', cleanOnTransitionCancel);
        element.addEventListener('transitionend', cleanOnTransitionEnd);
        document.addEventListener('visibilitychange', cleanOnTransitionCancel);
        element.offsetHeight;
        element.style.transition = "transform ".concat(duration, "ms ").concat(transitionTimingFunction);
        element.offsetHeight;
        element.style.transform = (_e = settings === null || settings === void 0 ? void 0 : settings.finalTransform) !== null && _e !== void 0 ? _e : null;
        timeoutId = setTimeout(cleanOnTransitionEnd, duration + 100);
    });
    return promise;
}
var BgaSlideAnimation = (function (_super) {
    __extends(BgaSlideAnimation, _super);
    function BgaSlideAnimation(settings) {
        return _super.call(this, slideAnimation, settings) || this;
    }
    return BgaSlideAnimation;
}(BgaAnimation));
function pauseAnimation(animationManager, animation) {
    var promise = new Promise(function (success) {
        var _a;
        var settings = animation.settings;
        var duration = (_a = settings === null || settings === void 0 ? void 0 : settings.duration) !== null && _a !== void 0 ? _a : 500;
        setTimeout(function () { return success(); }, duration);
    });
    return promise;
}
var BgaPauseAnimation = (function (_super) {
    __extends(BgaPauseAnimation, _super);
    function BgaPauseAnimation(settings) {
        return _super.call(this, pauseAnimation, settings) || this;
    }
    return BgaPauseAnimation;
}(BgaAnimation));
function shouldAnimate(settings) {
    var _a;
    return document.visibilityState !== 'hidden' && !((_a = settings === null || settings === void 0 ? void 0 : settings.game) === null || _a === void 0 ? void 0 : _a.instantaneousMode);
}
function getDeltaCoordinates(element, settings) {
    var _a;
    if (!settings.fromDelta && !settings.fromRect && !settings.fromElement) {
        throw new Error("[bga-animation] fromDelta, fromRect or fromElement need to be set");
    }
    var x = 0;
    var y = 0;
    if (settings.fromDelta) {
        x = settings.fromDelta.x;
        y = settings.fromDelta.y;
    }
    else {
        var originBR = (_a = settings.fromRect) !== null && _a !== void 0 ? _a : settings.fromElement.getBoundingClientRect();
        var originalTransform = element.style.transform;
        element.style.transform = '';
        var destinationBR = element.getBoundingClientRect();
        element.style.transform = originalTransform;
        x = (destinationBR.left + destinationBR.right) / 2 - (originBR.left + originBR.right) / 2;
        y = (destinationBR.top + destinationBR.bottom) / 2 - (originBR.top + originBR.bottom) / 2;
    }
    if (settings.scale) {
        x /= settings.scale;
        y /= settings.scale;
    }
    return { x: x, y: y };
}
function logAnimation(animationManager, animation) {
    var settings = animation.settings;
    var element = settings.element;
    if (element) {
        console.log(animation, settings, element, element.getBoundingClientRect(), element.style.transform);
    }
    else {
        console.log(animation, settings);
    }
    return Promise.resolve(false);
}
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var AnimationManager = (function () {
    function AnimationManager(game, settings) {
        this.game = game;
        this.settings = settings;
        this.zoomManager = settings === null || settings === void 0 ? void 0 : settings.zoomManager;
        if (!game) {
            throw new Error('You must set your game as the first parameter of AnimationManager');
        }
    }
    AnimationManager.prototype.getZoomManager = function () {
        return this.zoomManager;
    };
    AnimationManager.prototype.setZoomManager = function (zoomManager) {
        this.zoomManager = zoomManager;
    };
    AnimationManager.prototype.getSettings = function () {
        return this.settings;
    };
    AnimationManager.prototype.animationsActive = function () {
        return document.visibilityState !== 'hidden' && !this.game.instantaneousMode;
    };
    AnimationManager.prototype.play = function (animation) {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p, _q;
        return __awaiter(this, void 0, void 0, function () {
            var settings, _r;
            return __generator(this, function (_s) {
                switch (_s.label) {
                    case 0:
                        animation.played = animation.playWhenNoAnimation || this.animationsActive();
                        if (!animation.played) return [3, 2];
                        settings = animation.settings;
                        (_a = settings.animationStart) === null || _a === void 0 ? void 0 : _a.call(settings, animation);
                        (_b = settings.element) === null || _b === void 0 ? void 0 : _b.classList.add((_c = settings.animationClass) !== null && _c !== void 0 ? _c : 'bga-animations_animated');
                        animation.settings = __assign(__assign({}, animation.settings), { duration: (_g = (_e = (_d = animation.settings) === null || _d === void 0 ? void 0 : _d.duration) !== null && _e !== void 0 ? _e : (_f = this.settings) === null || _f === void 0 ? void 0 : _f.duration) !== null && _g !== void 0 ? _g : 500, scale: (_l = (_j = (_h = animation.settings) === null || _h === void 0 ? void 0 : _h.scale) !== null && _j !== void 0 ? _j : (_k = this.zoomManager) === null || _k === void 0 ? void 0 : _k.zoom) !== null && _l !== void 0 ? _l : undefined });
                        _r = animation;
                        return [4, animation.animationFunction(this, animation)];
                    case 1:
                        _r.result = _s.sent();
                        (_o = (_m = animation.settings).animationEnd) === null || _o === void 0 ? void 0 : _o.call(_m, animation);
                        (_p = settings.element) === null || _p === void 0 ? void 0 : _p.classList.remove((_q = settings.animationClass) !== null && _q !== void 0 ? _q : 'bga-animations_animated');
                        return [3, 3];
                    case 2: return [2, Promise.resolve(animation)];
                    case 3: return [2];
                }
            });
        });
    };
    AnimationManager.prototype.playParallel = function (animations) {
        return __awaiter(this, void 0, void 0, function () {
            var _this = this;
            return __generator(this, function (_a) {
                return [2, Promise.all(animations.map(function (animation) { return _this.play(animation); }))];
            });
        });
    };
    AnimationManager.prototype.playSequence = function (animations) {
        return __awaiter(this, void 0, void 0, function () {
            var result, others;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!animations.length) return [3, 3];
                        return [4, this.play(animations[0])];
                    case 1:
                        result = _a.sent();
                        return [4, this.playSequence(animations.slice(1))];
                    case 2:
                        others = _a.sent();
                        return [2, __spreadArray([result], others, true)];
                    case 3: return [2, Promise.resolve([])];
                }
            });
        });
    };
    AnimationManager.prototype.playWithDelay = function (animations, delay) {
        return __awaiter(this, void 0, void 0, function () {
            var promise;
            var _this = this;
            return __generator(this, function (_a) {
                promise = new Promise(function (success) {
                    var promises = [];
                    var _loop_1 = function (i) {
                        setTimeout(function () {
                            promises.push(_this.play(animations[i]));
                            if (i == animations.length - 1) {
                                Promise.all(promises).then(function (result) {
                                    success(result);
                                });
                            }
                        }, i * delay);
                    };
                    for (var i = 0; i < animations.length; i++) {
                        _loop_1(i);
                    }
                });
                return [2, promise];
            });
        });
    };
    AnimationManager.prototype.attachWithAnimation = function (animation, attachElement) {
        var attachWithAnimation = new BgaAttachWithAnimation({
            animation: animation,
            attachElement: attachElement
        });
        return this.play(attachWithAnimation);
    };
    return AnimationManager;
}());
var CardStock = (function () {
    function CardStock(manager, element, settings) {
        this.manager = manager;
        this.element = element;
        this.settings = settings;
        this.cards = [];
        this.selectedCards = [];
        this.selectionMode = 'none';
        manager.addStock(this);
        element === null || element === void 0 ? void 0 : element.classList.add('card-stock');
        this.bindClick();
        this.sort = settings === null || settings === void 0 ? void 0 : settings.sort;
    }
    CardStock.prototype.remove = function () {
        var _a;
        this.manager.removeStock(this);
        (_a = this.element) === null || _a === void 0 ? void 0 : _a.remove();
    };
    CardStock.prototype.getCards = function () {
        return this.cards.slice();
    };
    CardStock.prototype.isEmpty = function () {
        return !this.cards.length;
    };
    CardStock.prototype.getSelection = function () {
        return this.selectedCards.slice();
    };
    CardStock.prototype.isSelected = function (card) {
        var _this = this;
        return this.selectedCards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
    };
    CardStock.prototype.contains = function (card) {
        var _this = this;
        return this.cards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
    };
    CardStock.prototype.getCardElement = function (card) {
        return this.manager.getCardElement(card);
    };
    CardStock.prototype.canAddCard = function (card, settings) {
        return !this.contains(card);
    };
    CardStock.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a, _b, _c, _d;
        if (!this.canAddCard(card, settings)) {
            return Promise.resolve(false);
        }
        var promise;
        var originStock = this.manager.getCardStock(card);
        var index = this.getNewCardIndex(card);
        var settingsWithIndex = __assign({ index: index }, (settings !== null && settings !== void 0 ? settings : {}));
        var updateInformations = (_a = settingsWithIndex.updateInformations) !== null && _a !== void 0 ? _a : true;
        var needsCreation = true;
        if (originStock === null || originStock === void 0 ? void 0 : originStock.contains(card)) {
            var element = this.getCardElement(card);
            if (element) {
                promise = this.moveFromOtherStock(card, element, __assign(__assign({}, animation), { fromStock: originStock }), settingsWithIndex);
                needsCreation = false;
                if (!updateInformations) {
                    element.dataset.side = ((_b = settingsWithIndex === null || settingsWithIndex === void 0 ? void 0 : settingsWithIndex.visible) !== null && _b !== void 0 ? _b : this.manager.isCardVisible(card)) ? 'front' : 'back';
                }
            }
        }
        else if ((_c = animation === null || animation === void 0 ? void 0 : animation.fromStock) === null || _c === void 0 ? void 0 : _c.contains(card)) {
            var element = this.getCardElement(card);
            if (element) {
                promise = this.moveFromOtherStock(card, element, animation, settingsWithIndex);
                needsCreation = false;
            }
        }
        if (needsCreation) {
            var element = this.manager.createCardElement(card, ((_d = settingsWithIndex === null || settingsWithIndex === void 0 ? void 0 : settingsWithIndex.visible) !== null && _d !== void 0 ? _d : this.manager.isCardVisible(card)));
            promise = this.moveFromElement(card, element, animation, settingsWithIndex);
        }
        if (settingsWithIndex.index !== null && settingsWithIndex.index !== undefined) {
            this.cards.splice(index, 0, card);
        }
        else {
            this.cards.push(card);
        }
        if (updateInformations) {
            this.manager.updateCardInformations(card);
        }
        if (!promise) {
            console.warn("CardStock.addCard didn't return a Promise");
            promise = Promise.resolve(false);
        }
        if (this.selectionMode !== 'none') {
            promise.then(function () { var _a; return _this.setSelectableCard(card, (_a = settingsWithIndex.selectable) !== null && _a !== void 0 ? _a : true); });
        }
        return promise;
    };
    CardStock.prototype.getNewCardIndex = function (card) {
        if (this.sort) {
            var otherCards = this.getCards();
            for (var i = 0; i < otherCards.length; i++) {
                var otherCard = otherCards[i];
                if (this.sort(card, otherCard) < 0) {
                    return i;
                }
            }
            return otherCards.length;
        }
        else {
            return undefined;
        }
    };
    CardStock.prototype.addCardElementToParent = function (cardElement, settings) {
        var _a;
        var parent = (_a = settings === null || settings === void 0 ? void 0 : settings.forceToElement) !== null && _a !== void 0 ? _a : this.element;
        if ((settings === null || settings === void 0 ? void 0 : settings.index) === null || (settings === null || settings === void 0 ? void 0 : settings.index) === undefined || !parent.children.length || (settings === null || settings === void 0 ? void 0 : settings.index) >= parent.children.length) {
            parent.appendChild(cardElement);
        }
        else {
            parent.insertBefore(cardElement, parent.children[settings.index]);
        }
    };
    CardStock.prototype.moveFromOtherStock = function (card, cardElement, animation, settings) {
        var promise;
        var element = animation.fromStock.contains(card) ? this.manager.getCardElement(card) : animation.fromStock.element;
        var fromRect = element === null || element === void 0 ? void 0 : element.getBoundingClientRect();
        this.addCardElementToParent(cardElement, settings);
        this.removeSelectionClassesFromElement(cardElement);
        promise = fromRect ? this.animationFromElement(cardElement, fromRect, {
            originalSide: animation.originalSide,
            rotationDelta: animation.rotationDelta,
            animation: animation.animation,
        }) : Promise.resolve(false);
        if (animation.fromStock && animation.fromStock != this) {
            animation.fromStock.removeCard(card);
        }
        if (!promise) {
            console.warn("CardStock.moveFromOtherStock didn't return a Promise");
            promise = Promise.resolve(false);
        }
        return promise;
    };
    CardStock.prototype.moveFromElement = function (card, cardElement, animation, settings) {
        var promise;
        this.addCardElementToParent(cardElement, settings);
        if (animation) {
            if (animation.fromStock) {
                promise = this.animationFromElement(cardElement, animation.fromStock.element.getBoundingClientRect(), {
                    originalSide: animation.originalSide,
                    rotationDelta: animation.rotationDelta,
                    animation: animation.animation,
                });
                animation.fromStock.removeCard(card);
            }
            else if (animation.fromElement) {
                promise = this.animationFromElement(cardElement, animation.fromElement.getBoundingClientRect(), {
                    originalSide: animation.originalSide,
                    rotationDelta: animation.rotationDelta,
                    animation: animation.animation,
                });
            }
        }
        else {
            promise = Promise.resolve(false);
        }
        if (!promise) {
            console.warn("CardStock.moveFromElement didn't return a Promise");
            promise = Promise.resolve(false);
        }
        return promise;
    };
    CardStock.prototype.addCards = function (cards, animation, settings, shift) {
        if (shift === void 0) { shift = false; }
        return __awaiter(this, void 0, void 0, function () {
            var promises, result, others, _loop_2, i, results;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (!this.manager.animationsActive()) {
                            shift = false;
                        }
                        promises = [];
                        if (!(shift === true)) return [3, 4];
                        if (!cards.length) return [3, 3];
                        return [4, this.addCard(cards[0], animation, settings)];
                    case 1:
                        result = _a.sent();
                        return [4, this.addCards(cards.slice(1), animation, settings, shift)];
                    case 2:
                        others = _a.sent();
                        return [2, result || others];
                    case 3: return [3, 5];
                    case 4:
                        if (typeof shift === 'number') {
                            _loop_2 = function (i) {
                                setTimeout(function () { return promises.push(_this.addCard(cards[i], animation, settings)); }, i * shift);
                            };
                            for (i = 0; i < cards.length; i++) {
                                _loop_2(i);
                            }
                        }
                        else {
                            promises = cards.map(function (card) { return _this.addCard(card, animation, settings); });
                        }
                        _a.label = 5;
                    case 5: return [4, Promise.all(promises)];
                    case 6:
                        results = _a.sent();
                        return [2, results.some(function (result) { return result; })];
                }
            });
        });
    };
    CardStock.prototype.removeCard = function (card, settings) {
        var promise;
        if (this.contains(card) && this.element.contains(this.getCardElement(card))) {
            promise = this.manager.removeCard(card, settings);
        }
        else {
            promise = Promise.resolve(false);
        }
        this.cardRemoved(card, settings);
        return promise;
    };
    CardStock.prototype.cardRemoved = function (card, settings) {
        var _this = this;
        var index = this.cards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
        if (index !== -1) {
            this.cards.splice(index, 1);
        }
        if (this.selectedCards.find(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); })) {
            this.unselectCard(card);
        }
    };
    CardStock.prototype.removeCards = function (cards, settings) {
        return __awaiter(this, void 0, void 0, function () {
            var promises, results;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        promises = cards.map(function (card) { return _this.removeCard(card, settings); });
                        return [4, Promise.all(promises)];
                    case 1:
                        results = _a.sent();
                        return [2, results.some(function (result) { return result; })];
                }
            });
        });
    };
    CardStock.prototype.removeAll = function (settings) {
        var _this = this;
        var cards = this.getCards();
        cards.forEach(function (card) { return _this.removeCard(card, settings); });
    };
    CardStock.prototype.setSelectionMode = function (selectionMode, selectableCards) {
        var _this = this;
        if (selectionMode !== this.selectionMode) {
            this.unselectAll(true);
        }
        this.cards.forEach(function (card) { return _this.setSelectableCard(card, selectionMode != 'none'); });
        this.element.classList.toggle('bga-cards_selectable-stock', selectionMode != 'none');
        this.selectionMode = selectionMode;
        if (selectionMode === 'none') {
            this.getCards().forEach(function (card) { return _this.removeSelectionClasses(card); });
        }
        else {
            this.setSelectableCards(selectableCards !== null && selectableCards !== void 0 ? selectableCards : this.getCards());
        }
    };
    CardStock.prototype.setSelectableCard = function (card, selectable) {
        if (this.selectionMode === 'none') {
            return;
        }
        var element = this.getCardElement(card);
        var selectableCardsClass = this.getSelectableCardClass();
        var unselectableCardsClass = this.getUnselectableCardClass();
        if (selectableCardsClass) {
            element === null || element === void 0 ? void 0 : element.classList.toggle(selectableCardsClass, selectable);
        }
        if (unselectableCardsClass) {
            element === null || element === void 0 ? void 0 : element.classList.toggle(unselectableCardsClass, !selectable);
        }
        if (!selectable && this.isSelected(card)) {
            this.unselectCard(card, true);
        }
    };
    CardStock.prototype.setSelectableCards = function (selectableCards) {
        var _this = this;
        if (this.selectionMode === 'none') {
            return;
        }
        var selectableCardsIds = (selectableCards !== null && selectableCards !== void 0 ? selectableCards : this.getCards()).map(function (card) { return _this.manager.getId(card); });
        this.cards.forEach(function (card) {
            return _this.setSelectableCard(card, selectableCardsIds.includes(_this.manager.getId(card)));
        });
    };
    CardStock.prototype.selectCard = function (card, silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        if (this.selectionMode == 'none') {
            return;
        }
        var element = this.getCardElement(card);
        var selectableCardsClass = this.getSelectableCardClass();
        if (!element || !element.classList.contains(selectableCardsClass)) {
            return;
        }
        if (this.selectionMode === 'single') {
            this.cards.filter(function (c) { return _this.manager.getId(c) != _this.manager.getId(card); }).forEach(function (c) { return _this.unselectCard(c, true); });
        }
        var selectedCardsClass = this.getSelectedCardClass();
        element.classList.add(selectedCardsClass);
        this.selectedCards.push(card);
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), card);
        }
    };
    CardStock.prototype.unselectCard = function (card, silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        var element = this.getCardElement(card);
        var selectedCardsClass = this.getSelectedCardClass();
        element === null || element === void 0 ? void 0 : element.classList.remove(selectedCardsClass);
        var index = this.selectedCards.findIndex(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
        if (index !== -1) {
            this.selectedCards.splice(index, 1);
        }
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), card);
        }
    };
    CardStock.prototype.selectAll = function (silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        if (this.selectionMode == 'none') {
            return;
        }
        this.cards.forEach(function (c) { return _this.selectCard(c, true); });
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), null);
        }
    };
    CardStock.prototype.unselectAll = function (silent) {
        var _this = this;
        var _a;
        if (silent === void 0) { silent = false; }
        var cards = this.getCards();
        cards.forEach(function (c) { return _this.unselectCard(c, true); });
        if (!silent) {
            (_a = this.onSelectionChange) === null || _a === void 0 ? void 0 : _a.call(this, this.selectedCards.slice(), null);
        }
    };
    CardStock.prototype.bindClick = function () {
        var _this = this;
        var _a;
        (_a = this.element) === null || _a === void 0 ? void 0 : _a.addEventListener('click', function (event) {
            var cardDiv = event.target.closest('.card');
            if (!cardDiv) {
                return;
            }
            var card = _this.cards.find(function (c) { return _this.manager.getId(c) == cardDiv.id; });
            if (!card) {
                return;
            }
            _this.cardClick(card);
        });
    };
    CardStock.prototype.cardClick = function (card) {
        var _this = this;
        var _a;
        if (this.selectionMode != 'none') {
            var alreadySelected = this.selectedCards.some(function (c) { return _this.manager.getId(c) == _this.manager.getId(card); });
            if (alreadySelected) {
                this.unselectCard(card);
            }
            else {
                this.selectCard(card);
            }
        }
        (_a = this.onCardClick) === null || _a === void 0 ? void 0 : _a.call(this, card);
    };
    CardStock.prototype.animationFromElement = function (element, fromRect, settings) {
        var _a;
        return __awaiter(this, void 0, void 0, function () {
            var side, cardSides_1, animation, result;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        side = element.dataset.side;
                        if (settings.originalSide && settings.originalSide != side) {
                            cardSides_1 = element.getElementsByClassName('card-sides')[0];
                            cardSides_1.style.transition = 'none';
                            element.dataset.side = settings.originalSide;
                            setTimeout(function () {
                                cardSides_1.style.transition = null;
                                element.dataset.side = side;
                            });
                        }
                        animation = settings.animation;
                        if (animation) {
                            animation.settings.element = element;
                            animation.settings.fromRect = fromRect;
                        }
                        else {
                            animation = new BgaSlideAnimation({ element: element, fromRect: fromRect });
                        }
                        return [4, this.manager.animationManager.play(animation)];
                    case 1:
                        result = _b.sent();
                        return [2, (_a = result === null || result === void 0 ? void 0 : result.played) !== null && _a !== void 0 ? _a : false];
                }
            });
        });
    };
    CardStock.prototype.setCardVisible = function (card, visible, settings) {
        this.manager.setCardVisible(card, visible, settings);
    };
    CardStock.prototype.flipCard = function (card, settings) {
        this.manager.flipCard(card, settings);
    };
    CardStock.prototype.getSelectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectableCardClass) === undefined ? this.manager.getSelectableCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectableCardClass;
    };
    CardStock.prototype.getUnselectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.unselectableCardClass) === undefined ? this.manager.getUnselectableCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.unselectableCardClass;
    };
    CardStock.prototype.getSelectedCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectedCardClass) === undefined ? this.manager.getSelectedCardClass() : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectedCardClass;
    };
    CardStock.prototype.removeSelectionClasses = function (card) {
        this.removeSelectionClassesFromElement(this.getCardElement(card));
    };
    CardStock.prototype.removeSelectionClassesFromElement = function (cardElement) {
        var selectableCardsClass = this.getSelectableCardClass();
        var unselectableCardsClass = this.getUnselectableCardClass();
        var selectedCardsClass = this.getSelectedCardClass();
        cardElement === null || cardElement === void 0 ? void 0 : cardElement.classList.remove(selectableCardsClass, unselectableCardsClass, selectedCardsClass);
    };
    return CardStock;
}());
var SlideAndBackAnimation = (function (_super) {
    __extends(SlideAndBackAnimation, _super);
    function SlideAndBackAnimation(manager, element, tempElement) {
        var distance = (manager.getCardWidth() + manager.getCardHeight()) / 2;
        var angle = Math.random() * Math.PI * 2;
        var fromDelta = {
            x: distance * Math.cos(angle),
            y: distance * Math.sin(angle),
        };
        return _super.call(this, {
            animations: [
                new BgaSlideToAnimation({ element: element, fromDelta: fromDelta, duration: 250 }),
                new BgaSlideAnimation({ element: element, fromDelta: fromDelta, duration: 250, animationEnd: tempElement ? (function () { return element.remove(); }) : undefined }),
            ]
        }) || this;
    }
    return SlideAndBackAnimation;
}(BgaCumulatedAnimation));
var Deck = (function (_super) {
    __extends(Deck, _super);
    function Deck(manager, element, settings) {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l;
        var _this = _super.call(this, manager, element) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('deck');
        var cardWidth = _this.manager.getCardWidth();
        var cardHeight = _this.manager.getCardHeight();
        if (cardWidth && cardHeight) {
            _this.element.style.setProperty('--width', "".concat(cardWidth, "px"));
            _this.element.style.setProperty('--height', "".concat(cardHeight, "px"));
        }
        else {
            throw new Error("You need to set cardWidth and cardHeight in the card manager to use Deck.");
        }
        _this.fakeCardGenerator = (_a = settings === null || settings === void 0 ? void 0 : settings.fakeCardGenerator) !== null && _a !== void 0 ? _a : manager.getFakeCardGenerator();
        _this.thicknesses = (_b = settings.thicknesses) !== null && _b !== void 0 ? _b : [0, 2, 5, 10, 20, 30];
        _this.setCardNumber((_c = settings.cardNumber) !== null && _c !== void 0 ? _c : 0);
        _this.autoUpdateCardNumber = (_d = settings.autoUpdateCardNumber) !== null && _d !== void 0 ? _d : true;
        _this.autoRemovePreviousCards = (_e = settings.autoRemovePreviousCards) !== null && _e !== void 0 ? _e : true;
        var shadowDirection = (_f = settings.shadowDirection) !== null && _f !== void 0 ? _f : 'bottom-right';
        var shadowDirectionSplit = shadowDirection.split('-');
        var xShadowShift = shadowDirectionSplit.includes('right') ? 1 : (shadowDirectionSplit.includes('left') ? -1 : 0);
        var yShadowShift = shadowDirectionSplit.includes('bottom') ? 1 : (shadowDirectionSplit.includes('top') ? -1 : 0);
        _this.element.style.setProperty('--xShadowShift', '' + xShadowShift);
        _this.element.style.setProperty('--yShadowShift', '' + yShadowShift);
        if (settings.topCard) {
            _this.addCard(settings.topCard);
        }
        else if (settings.cardNumber > 0) {
            _this.addCard(_this.getFakeCard());
        }
        if (settings.counter && ((_g = settings.counter.show) !== null && _g !== void 0 ? _g : true)) {
            if (settings.cardNumber === null || settings.cardNumber === undefined) {
                console.warn("Deck card counter created without a cardNumber");
            }
            _this.createCounter((_h = settings.counter.position) !== null && _h !== void 0 ? _h : 'bottom', (_j = settings.counter.extraClasses) !== null && _j !== void 0 ? _j : 'round', settings.counter.counterId);
            if ((_k = settings.counter) === null || _k === void 0 ? void 0 : _k.hideWhenEmpty) {
                _this.element.querySelector('.bga-cards_deck-counter').classList.add('hide-when-empty');
            }
        }
        _this.setCardNumber((_l = settings.cardNumber) !== null && _l !== void 0 ? _l : 0);
        return _this;
    }
    Deck.prototype.createCounter = function (counterPosition, extraClasses, counterId) {
        var left = counterPosition.includes('right') ? 100 : (counterPosition.includes('left') ? 0 : 50);
        var top = counterPosition.includes('bottom') ? 100 : (counterPosition.includes('top') ? 0 : 50);
        this.element.style.setProperty('--bga-cards-deck-left', "".concat(left, "%"));
        this.element.style.setProperty('--bga-cards-deck-top', "".concat(top, "%"));
        this.element.insertAdjacentHTML('beforeend', "\n            <div ".concat(counterId ? "id=\"".concat(counterId, "\"") : '', " class=\"bga-cards_deck-counter ").concat(extraClasses, "\"></div>\n        "));
    };
    Deck.prototype.getCardNumber = function () {
        return this.cardNumber;
    };
    Deck.prototype.setCardNumber = function (cardNumber, topCard) {
        var _this = this;
        if (topCard === void 0) { topCard = undefined; }
        var promise = topCard === null || cardNumber == 0 ?
            Promise.resolve(false) :
            _super.prototype.addCard.call(this, topCard || this.getFakeCard(), undefined, { autoUpdateCardNumber: false });
        this.cardNumber = cardNumber;
        this.element.dataset.empty = (this.cardNumber == 0).toString();
        var thickness = 0;
        this.thicknesses.forEach(function (threshold, index) {
            if (_this.cardNumber >= threshold) {
                thickness = index;
            }
        });
        this.element.style.setProperty('--thickness', "".concat(thickness, "px"));
        var counterDiv = this.element.querySelector('.bga-cards_deck-counter');
        if (counterDiv) {
            counterDiv.innerHTML = "".concat(cardNumber);
        }
        return promise;
    };
    Deck.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a, _b;
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.autoUpdateCardNumber) !== null && _a !== void 0 ? _a : this.autoUpdateCardNumber) {
            this.setCardNumber(this.cardNumber + 1, null);
        }
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        if ((_b = settings === null || settings === void 0 ? void 0 : settings.autoRemovePreviousCards) !== null && _b !== void 0 ? _b : this.autoRemovePreviousCards) {
            promise.then(function () {
                var previousCards = _this.getCards().slice(0, -1);
                _this.removeCards(previousCards, { autoUpdateCardNumber: false });
            });
        }
        return promise;
    };
    Deck.prototype.cardRemoved = function (card, settings) {
        var _a;
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.autoUpdateCardNumber) !== null && _a !== void 0 ? _a : this.autoUpdateCardNumber) {
            this.setCardNumber(this.cardNumber - 1);
        }
        _super.prototype.cardRemoved.call(this, card, settings);
    };
    Deck.prototype.getTopCard = function () {
        var cards = this.getCards();
        return cards.length ? cards[cards.length - 1] : null;
    };
    Deck.prototype.shuffle = function (settings) {
        var _a, _b, _c;
        return __awaiter(this, void 0, void 0, function () {
            var animatedCardsMax, animatedCards, elements, getFakeCard, uid, i, newCard, newElement, pauseDelayAfterAnimation;
            var _this = this;
            return __generator(this, function (_d) {
                switch (_d.label) {
                    case 0:
                        animatedCardsMax = (_a = settings === null || settings === void 0 ? void 0 : settings.animatedCardsMax) !== null && _a !== void 0 ? _a : 10;
                        this.addCard((_b = settings === null || settings === void 0 ? void 0 : settings.newTopCard) !== null && _b !== void 0 ? _b : this.getFakeCard(), undefined, { autoUpdateCardNumber: false });
                        if (!this.manager.animationsActive()) {
                            return [2, Promise.resolve(false)];
                        }
                        animatedCards = Math.min(10, animatedCardsMax, this.getCardNumber());
                        if (!(animatedCards > 1)) return [3, 4];
                        elements = [this.getCardElement(this.getTopCard())];
                        getFakeCard = function (uid) {
                            var newCard;
                            if (settings === null || settings === void 0 ? void 0 : settings.fakeCardSetter) {
                                newCard = {};
                                settings === null || settings === void 0 ? void 0 : settings.fakeCardSetter(newCard, uid);
                            }
                            else {
                                newCard = _this.fakeCardGenerator("".concat(_this.element.id, "-shuffle-").concat(uid));
                            }
                            return newCard;
                        };
                        uid = 0;
                        for (i = elements.length; i <= animatedCards; i++) {
                            newCard = void 0;
                            do {
                                newCard = getFakeCard(uid++);
                            } while (this.manager.getCardElement(newCard));
                            newElement = this.manager.createCardElement(newCard, false);
                            newElement.dataset.tempCardForShuffleAnimation = 'true';
                            this.element.prepend(newElement);
                            elements.push(newElement);
                        }
                        return [4, this.manager.animationManager.playWithDelay(elements.map(function (element) { return new SlideAndBackAnimation(_this.manager, element, element.dataset.tempCardForShuffleAnimation == 'true'); }), 50)];
                    case 1:
                        _d.sent();
                        pauseDelayAfterAnimation = (_c = settings === null || settings === void 0 ? void 0 : settings.pauseDelayAfterAnimation) !== null && _c !== void 0 ? _c : 500;
                        if (!(pauseDelayAfterAnimation > 0)) return [3, 3];
                        return [4, this.manager.animationManager.play(new BgaPauseAnimation({ duration: pauseDelayAfterAnimation }))];
                    case 2:
                        _d.sent();
                        _d.label = 3;
                    case 3: return [2, true];
                    case 4: return [2, Promise.resolve(false)];
                }
            });
        });
    };
    Deck.prototype.getFakeCard = function () {
        return this.fakeCardGenerator(this.element.id);
    };
    return Deck;
}(CardStock));
var LineStock = (function (_super) {
    __extends(LineStock, _super);
    function LineStock(manager, element, settings) {
        var _a, _b, _c, _d;
        var _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('line-stock');
        element.dataset.center = ((_a = settings === null || settings === void 0 ? void 0 : settings.center) !== null && _a !== void 0 ? _a : true).toString();
        element.style.setProperty('--wrap', (_b = settings === null || settings === void 0 ? void 0 : settings.wrap) !== null && _b !== void 0 ? _b : 'wrap');
        element.style.setProperty('--direction', (_c = settings === null || settings === void 0 ? void 0 : settings.direction) !== null && _c !== void 0 ? _c : 'row');
        element.style.setProperty('--gap', (_d = settings === null || settings === void 0 ? void 0 : settings.gap) !== null && _d !== void 0 ? _d : '8px');
        return _this;
    }
    return LineStock;
}(CardStock));
var ManualPositionStock = (function (_super) {
    __extends(ManualPositionStock, _super);
    function ManualPositionStock(manager, element, settings, updateDisplay) {
        var _this = _super.call(this, manager, element, settings) || this;
        _this.manager = manager;
        _this.element = element;
        _this.updateDisplay = updateDisplay;
        element.classList.add('manual-position-stock');
        return _this;
    }
    ManualPositionStock.prototype.addCard = function (card, animation, settings) {
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        this.updateDisplay(this.element, this.getCards(), card, this);
        return promise;
    };
    ManualPositionStock.prototype.cardRemoved = function (card, settings) {
        _super.prototype.cardRemoved.call(this, card, settings);
        this.updateDisplay(this.element, this.getCards(), card, this);
    };
    return ManualPositionStock;
}(CardStock));
var VoidStock = (function (_super) {
    __extends(VoidStock, _super);
    function VoidStock(manager, element) {
        var _this = _super.call(this, manager, element) || this;
        _this.manager = manager;
        _this.element = element;
        element.classList.add('void-stock');
        return _this;
    }
    VoidStock.prototype.addCard = function (card, animation, settings) {
        var _this = this;
        var _a;
        var promise = _super.prototype.addCard.call(this, card, animation, settings);
        var cardElement = this.getCardElement(card);
        var originalLeft = cardElement.style.left;
        var originalTop = cardElement.style.top;
        cardElement.style.left = "".concat((this.element.clientWidth - cardElement.clientWidth) / 2, "px");
        cardElement.style.top = "".concat((this.element.clientHeight - cardElement.clientHeight) / 2, "px");
        if (!promise) {
            console.warn("VoidStock.addCard didn't return a Promise");
            promise = Promise.resolve(false);
        }
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.remove) !== null && _a !== void 0 ? _a : true) {
            return promise.then(function () {
                return _this.removeCard(card);
            });
        }
        else {
            cardElement.style.left = originalLeft;
            cardElement.style.top = originalTop;
            return promise;
        }
    };
    return VoidStock;
}(CardStock));
function sortFunction() {
    var sortedFields = [];
    for (var _i = 0; _i < arguments.length; _i++) {
        sortedFields[_i] = arguments[_i];
    }
    return function (a, b) {
        for (var i = 0; i < sortedFields.length; i++) {
            var direction = 1;
            var field = sortedFields[i];
            if (field[0] == '-') {
                direction = -1;
                field = field.substring(1);
            }
            else if (field[0] == '+') {
                field = field.substring(1);
            }
            var type = typeof a[field];
            if (type === 'string') {
                var compare = a[field].localeCompare(b[field]);
                if (compare !== 0) {
                    return compare;
                }
            }
            else if (type === 'number') {
                var compare = (a[field] - b[field]) * direction;
                if (compare !== 0) {
                    return compare * direction;
                }
            }
        }
        return 0;
    };
}
var CardManager = (function () {
    function CardManager(game, settings) {
        var _a;
        this.game = game;
        this.settings = settings;
        this.stocks = [];
        this.updateMainTimeoutId = [];
        this.updateFrontTimeoutId = [];
        this.updateBackTimeoutId = [];
        this.animationManager = (_a = settings.animationManager) !== null && _a !== void 0 ? _a : new AnimationManager(game);
    }
    CardManager.prototype.animationsActive = function () {
        return this.animationManager.animationsActive();
    };
    CardManager.prototype.addStock = function (stock) {
        this.stocks.push(stock);
    };
    CardManager.prototype.removeStock = function (stock) {
        var index = this.stocks.indexOf(stock);
        if (index !== -1) {
            this.stocks.splice(index, 1);
        }
    };
    CardManager.prototype.getId = function (card) {
        var _a, _b, _c;
        return (_c = (_b = (_a = this.settings).getId) === null || _b === void 0 ? void 0 : _b.call(_a, card)) !== null && _c !== void 0 ? _c : "card-".concat(card.id);
    };
    CardManager.prototype.createCardElement = function (card, visible) {
        var _a, _b, _c, _d, _e, _f;
        if (visible === void 0) { visible = true; }
        var id = this.getId(card);
        var side = visible ? 'front' : 'back';
        if (this.getCardElement(card)) {
            throw new Error('This card already exists ' + JSON.stringify(card));
        }
        var element = document.createElement("div");
        element.id = id;
        element.dataset.side = '' + side;
        element.innerHTML = "\n            <div class=\"card-sides\">\n                <div id=\"".concat(id, "-front\" class=\"card-side front\">\n                </div>\n                <div id=\"").concat(id, "-back\" class=\"card-side back\">\n                </div>\n            </div>\n        ");
        element.classList.add('card');
        document.body.appendChild(element);
        (_b = (_a = this.settings).setupDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element);
        (_d = (_c = this.settings).setupFrontDiv) === null || _d === void 0 ? void 0 : _d.call(_c, card, element.getElementsByClassName('front')[0]);
        (_f = (_e = this.settings).setupBackDiv) === null || _f === void 0 ? void 0 : _f.call(_e, card, element.getElementsByClassName('back')[0]);
        document.body.removeChild(element);
        return element;
    };
    CardManager.prototype.getCardElement = function (card) {
        return document.getElementById(this.getId(card));
    };
    CardManager.prototype.removeCard = function (card, settings) {
        var _a;
        var id = this.getId(card);
        var div = document.getElementById(id);
        if (!div) {
            return Promise.resolve(false);
        }
        div.id = "deleted".concat(id);
        div.remove();
        (_a = this.getCardStock(card)) === null || _a === void 0 ? void 0 : _a.cardRemoved(card, settings);
        return Promise.resolve(true);
    };
    CardManager.prototype.getCardStock = function (card) {
        return this.stocks.find(function (stock) { return stock.contains(card); });
    };
    CardManager.prototype.isCardVisible = function (card) {
        var _a, _b, _c, _d;
        return (_c = (_b = (_a = this.settings).isCardVisible) === null || _b === void 0 ? void 0 : _b.call(_a, card)) !== null && _c !== void 0 ? _c : ((_d = card.type) !== null && _d !== void 0 ? _d : false);
    };
    CardManager.prototype.setCardVisible = function (card, visible, settings) {
        var _this = this;
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o;
        var element = this.getCardElement(card);
        if (!element) {
            return;
        }
        var isVisible = visible !== null && visible !== void 0 ? visible : this.isCardVisible(card);
        element.dataset.side = isVisible ? 'front' : 'back';
        var stringId = JSON.stringify(this.getId(card));
        if ((_a = settings === null || settings === void 0 ? void 0 : settings.updateMain) !== null && _a !== void 0 ? _a : false) {
            if (this.updateMainTimeoutId[stringId]) {
                clearTimeout(this.updateMainTimeoutId[stringId]);
                delete this.updateMainTimeoutId[stringId];
            }
            var updateMainDelay = (_b = settings === null || settings === void 0 ? void 0 : settings.updateMainDelay) !== null && _b !== void 0 ? _b : 0;
            if (isVisible && updateMainDelay > 0 && this.animationsActive()) {
                this.updateMainTimeoutId[stringId] = setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element); }, updateMainDelay);
            }
            else {
                (_d = (_c = this.settings).setupDiv) === null || _d === void 0 ? void 0 : _d.call(_c, card, element);
            }
        }
        if ((_e = settings === null || settings === void 0 ? void 0 : settings.updateFront) !== null && _e !== void 0 ? _e : true) {
            if (this.updateFrontTimeoutId[stringId]) {
                clearTimeout(this.updateFrontTimeoutId[stringId]);
                delete this.updateFrontTimeoutId[stringId];
            }
            var updateFrontDelay = (_f = settings === null || settings === void 0 ? void 0 : settings.updateFrontDelay) !== null && _f !== void 0 ? _f : 500;
            if (!isVisible && updateFrontDelay > 0 && this.animationsActive()) {
                this.updateFrontTimeoutId[stringId] = setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupFrontDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element.getElementsByClassName('front')[0]); }, updateFrontDelay);
            }
            else {
                (_h = (_g = this.settings).setupFrontDiv) === null || _h === void 0 ? void 0 : _h.call(_g, card, element.getElementsByClassName('front')[0]);
            }
        }
        if ((_j = settings === null || settings === void 0 ? void 0 : settings.updateBack) !== null && _j !== void 0 ? _j : false) {
            if (this.updateBackTimeoutId[stringId]) {
                clearTimeout(this.updateBackTimeoutId[stringId]);
                delete this.updateBackTimeoutId[stringId];
            }
            var updateBackDelay = (_k = settings === null || settings === void 0 ? void 0 : settings.updateBackDelay) !== null && _k !== void 0 ? _k : 0;
            if (isVisible && updateBackDelay > 0 && this.animationsActive()) {
                this.updateBackTimeoutId[stringId] = setTimeout(function () { var _a, _b; return (_b = (_a = _this.settings).setupBackDiv) === null || _b === void 0 ? void 0 : _b.call(_a, card, element.getElementsByClassName('back')[0]); }, updateBackDelay);
            }
            else {
                (_m = (_l = this.settings).setupBackDiv) === null || _m === void 0 ? void 0 : _m.call(_l, card, element.getElementsByClassName('back')[0]);
            }
        }
        if ((_o = settings === null || settings === void 0 ? void 0 : settings.updateData) !== null && _o !== void 0 ? _o : true) {
            var stock = this.getCardStock(card);
            var cards = stock.getCards();
            var cardIndex = cards.findIndex(function (c) { return _this.getId(c) === _this.getId(card); });
            if (cardIndex !== -1) {
                stock.cards.splice(cardIndex, 1, card);
            }
        }
    };
    CardManager.prototype.flipCard = function (card, settings) {
        var element = this.getCardElement(card);
        var currentlyVisible = element.dataset.side === 'front';
        this.setCardVisible(card, !currentlyVisible, settings);
    };
    CardManager.prototype.updateCardInformations = function (card, settings) {
        var newSettings = __assign(__assign({}, (settings !== null && settings !== void 0 ? settings : {})), { updateData: true });
        this.setCardVisible(card, undefined, newSettings);
    };
    CardManager.prototype.getCardWidth = function () {
        var _a;
        return (_a = this.settings) === null || _a === void 0 ? void 0 : _a.cardWidth;
    };
    CardManager.prototype.getCardHeight = function () {
        var _a;
        return (_a = this.settings) === null || _a === void 0 ? void 0 : _a.cardHeight;
    };
    CardManager.prototype.getSelectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectableCardClass) === undefined ? 'bga-cards_selectable-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectableCardClass;
    };
    CardManager.prototype.getUnselectableCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.unselectableCardClass) === undefined ? 'bga-cards_disabled-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.unselectableCardClass;
    };
    CardManager.prototype.getSelectedCardClass = function () {
        var _a, _b;
        return ((_a = this.settings) === null || _a === void 0 ? void 0 : _a.selectedCardClass) === undefined ? 'bga-cards_selected-card' : (_b = this.settings) === null || _b === void 0 ? void 0 : _b.selectedCardClass;
    };
    CardManager.prototype.getFakeCardGenerator = function () {
        var _this = this;
        var _a, _b;
        return (_b = (_a = this.settings) === null || _a === void 0 ? void 0 : _a.fakeCardGenerator) !== null && _b !== void 0 ? _b : (function (deckId) { return ({ id: _this.getId({ id: "".concat(deckId, "-fake-top-card") }) }); });
    };
    return CardManager;
}());
var BayonetsAndTomahawks = (function () {
    function BayonetsAndTomahawks() {
        this.tooltipsToMap = [];
        this._helpMode = false;
        this._last_notif = null;
        this._last_tooltip_id = 0;
        this._notif_uid_to_log_id = {};
        this._notif_uid_to_mobile_log_id = {};
        this._selectableNodes = [];
        console.log("bayonetsandtomahawks constructor");
    }
    BayonetsAndTomahawks.prototype.setup = function (gamedatas) {
        dojo.place("<div id='customActions' style='display:inline-block'></div>", $("generalactions"), "after");
        this.setAlwaysFixTopActions();
        this.setupDontPreloadImages();
        this.gamedatas = gamedatas;
        debug("gamedatas", gamedatas);
        this.setupPlayerOrder({ playerOrder: gamedatas.playerOrder });
        this._connections = [];
        this.activeStates = {
            selectReserveCard: new SelectReserveCardState(this),
        };
        this.infoPanel = new InfoPanel(this);
        this.settings = new Settings(this);
        this.animationManager = new AnimationManager(this, {
            duration: this.settings.get({ id: PREF_SHOW_ANIMATIONS }) === DISABLED
                ? 0
                : 2100 - this.settings.get({ id: PREF_ANIMATION_SPEED }),
        });
        this.cardManager = new BTCardManager(this);
        this.discard = new VoidStock(this.cardManager, document.getElementById("bt_discard"));
        this.deck = new LineStock(this.cardManager, document.getElementById("bt_deck"));
        this.gameMap = new GameMap(this);
        this.pools = new Pools(this);
        this.tooltipManager = new TooltipManager(this);
        if (this.playerOrder.includes(this.getPlayerId())) {
            this.hand = new Hand(this);
        }
        this.playerManager = new PlayerManager(this);
        if (this.notificationManager != undefined) {
            this.notificationManager.destroy();
        }
        this.notificationManager = new NotificationManager(this);
        this.notificationManager.setupNotifications();
        this.tooltipManager.setupTooltips();
        debug("Ending game setup");
    };
    BayonetsAndTomahawks.prototype.setupPlayerOrder = function (_a) {
        var playerOrder = _a.playerOrder;
        var currentPlayerId = this.getPlayerId();
        var isInGame = playerOrder.includes(currentPlayerId);
        if (isInGame) {
            while (playerOrder[0] !== currentPlayerId) {
                var firstItem = playerOrder.shift();
                playerOrder.push(firstItem);
            }
        }
        this.playerOrder = playerOrder;
    };
    BayonetsAndTomahawks.prototype.setupDontPreloadImages = function () { };
    BayonetsAndTomahawks.prototype.onEnteringState = function (stateName, args) {
        var _this = this;
        console.log("Entering state: " + stateName, args);
        if (this.framework().isCurrentPlayerActive() &&
            this.activeStates[stateName]) {
            this.activeStates[stateName].onEnteringState(args.args);
        }
        if (args.args && args.args.previousSteps) {
            args.args.previousSteps.forEach(function (stepId) {
                var logEntry = $("logs").querySelector(".log.notif_newUndoableStep[data-step=\"".concat(stepId, "\"]"));
                if (logEntry) {
                    _this.onClick(logEntry, function () { return _this.undoToStep({ stepId: stepId }); });
                }
                logEntry = document.querySelector(".chatwindowlogs_zone .log.notif_newUndoableStep[data-step=\"".concat(stepId, "\"]"));
                if (logEntry) {
                    _this.onClick(logEntry, function () { return _this.undoToStep({ stepId: stepId }); });
                }
            });
        }
    };
    BayonetsAndTomahawks.prototype.onLeavingState = function (stateName) {
        this.clearPossible();
    };
    BayonetsAndTomahawks.prototype.onUpdateActionButtons = function (stateName, args) {
    };
    BayonetsAndTomahawks.prototype.addActionButtonClient = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses, _b = _a.color, color = _b === void 0 ? "none" : _b;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, "customActions", false, color);
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addCancelButton = function () {
        var _this = this;
        this.addDangerActionButton({
            id: "cancel_btn",
            text: _("Cancel"),
            callback: function () { return _this.onCancel(); },
        });
    };
    BayonetsAndTomahawks.prototype.addConfirmButton = function (_a) {
        var callback = _a.callback;
        this.addPrimaryActionButton({
            id: "confirm_btn",
            text: _("Confirm"),
            callback: callback,
        });
    };
    BayonetsAndTomahawks.prototype.addPassButton = function (_a) {
        var _this = this;
        var optionalAction = _a.optionalAction, text = _a.text;
        if (optionalAction) {
            this.addSecondaryActionButton({
                id: "pass_btn",
                text: text ? _(text) : _("Pass"),
                callback: function () { return _this.takeAction({ action: "actPassOptionalAction" }); },
            });
        }
    };
    BayonetsAndTomahawks.prototype.addPrimaryActionButton = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, "customActions", false, "blue");
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addSecondaryActionButton = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, "customActions", false, "gray");
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addDangerActionButton = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, "customActions", false, "red");
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addUndoButtons = function (_a) {
        var _this = this;
        var previousSteps = _a.previousSteps, previousEngineChoices = _a.previousEngineChoices;
        var lastStep = Math.max.apply(Math, __spreadArray([0], previousSteps, false));
        if (lastStep > 0) {
            this.addDangerActionButton({
                id: "undo_last_step_btn",
                text: _("Undo last step"),
                callback: function () {
                    return _this.takeAction({
                        action: "actUndoToStep",
                        args: {
                            stepId: lastStep,
                        },
                        checkAction: "actRestart",
                    });
                },
            });
        }
        if (previousEngineChoices > 0) {
            this.addDangerActionButton({
                id: "restart_btn",
                text: _("Restart turn"),
                callback: function () { return _this.takeAction({ action: "actRestart" }); },
            });
        }
    };
    BayonetsAndTomahawks.prototype.clearInterface = function () {
        console.log("clear interface");
        this.playerManager.clearInterface();
    };
    BayonetsAndTomahawks.prototype.clearPossible = function () {
        this.framework().removeActionButtons();
        dojo.empty("customActions");
        dojo.forEach(this._connections, dojo.disconnect);
        this._connections = [];
        dojo.query(".".concat(BT_SELECTABLE)).removeClass(BT_SELECTABLE);
        dojo.query(".".concat(BT_SELECTED)).removeClass(BT_SELECTED);
    };
    BayonetsAndTomahawks.prototype.getPlayerId = function () {
        return Number(this.framework().player_id);
    };
    BayonetsAndTomahawks.prototype.getCurrentPlayer = function () {
        return this.playerManager.getPlayer({ playerId: this.getPlayerId() });
    };
    BayonetsAndTomahawks.prototype.framework = function () {
        return this;
    };
    BayonetsAndTomahawks.prototype.onCancel = function () {
        this.clearPossible();
        this.framework().restoreServerGameState();
    };
    BayonetsAndTomahawks.prototype.clientUpdatePageTitle = function (_a) {
        var text = _a.text, args = _a.args, _b = _a.nonActivePlayers, nonActivePlayers = _b === void 0 ? false : _b;
        var title = this.format_string_recursive(_(text), args);
        if (nonActivePlayers) {
            this.gamedatas.gamestate.description = title;
        }
        else {
            this.gamedatas.gamestate.descriptionmyturn = title;
        }
        this.framework().updatePageTitle();
    };
    BayonetsAndTomahawks.prototype.setCardSelectable = function (_a) {
        var id = _a.id, callback = _a.callback;
        var node = $(id);
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTABLE);
        this._connections.push(dojo.connect(node, "onclick", this, function (event) {
            return callback(event);
        }));
    };
    BayonetsAndTomahawks.prototype.setCardSelected = function (_a) {
        var id = _a.id;
        var node = $(id);
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTED);
    };
    BayonetsAndTomahawks.prototype.connect = function (node, action, callback) {
        this._connections.push(dojo.connect($(node), action, callback));
    };
    BayonetsAndTomahawks.prototype.onClick = function (node, callback, temporary) {
        var _this = this;
        if (temporary === void 0) { temporary = true; }
        var safeCallback = function (evt) {
            evt.stopPropagation();
            if (_this.framework().isInterfaceLocked()) {
                return false;
            }
            if (_this._helpMode) {
                return false;
            }
            callback(evt);
        };
        if (temporary) {
            this.connect($(node), "click", safeCallback);
            dojo.removeClass(node, "unselectable");
            dojo.addClass(node, "selectable");
            this._selectableNodes.push(node);
        }
        else {
            dojo.connect($(node), "click", safeCallback);
        }
    };
    BayonetsAndTomahawks.prototype.undoToStep = function (_a) {
        var stepId = _a.stepId;
        this.takeAction({
            action: "actUndoToStep",
            args: {
                stepId: stepId,
            },
            checkAction: "actRestart",
        });
    };
    BayonetsAndTomahawks.prototype.updateLayout = function () {
        if (!this.settings) {
            return;
        }
        $("play_area_container").setAttribute("data-two-columns", this.settings.get({ id: "twoColumnsLayout" }));
        var ROOT = document.documentElement;
        var WIDTH = $("play_area_container").getBoundingClientRect()["width"] - 8;
        var LEFT_COLUMN = 1500;
        var RIGHT_COLUMN = 1500;
        if (this.settings.get({ id: "twoColumnsLayout" }) === PREF_ENABLED) {
            WIDTH = WIDTH - 8;
            var size = Number(this.settings.get({ id: "columnSizes" }));
            var proportions = [size, 100 - size];
            var LEFT_SIZE = (proportions[0] * WIDTH) / 100;
            var leftColumnScale = LEFT_SIZE / LEFT_COLUMN;
            ROOT.style.setProperty("--leftColumnScale", "".concat(leftColumnScale));
            var RIGHT_SIZE = (proportions[1] * WIDTH) / 100;
            var rightColumnScale = RIGHT_SIZE / RIGHT_COLUMN;
            ROOT.style.setProperty("--rightColumnScale", "".concat(rightColumnScale));
            $("play_area_container").style.gridTemplateColumns = "".concat(LEFT_SIZE, "px ").concat(RIGHT_SIZE, "px");
        }
        else {
            var LEFT_SIZE = WIDTH;
            var leftColumnScale = LEFT_SIZE / LEFT_COLUMN;
            ROOT.style.setProperty("--leftColumnScale", "".concat(leftColumnScale));
            var RIGHT_SIZE = WIDTH;
            var rightColumnScale = RIGHT_SIZE / RIGHT_COLUMN;
            ROOT.style.setProperty("--leftColumnScale", "".concat(rightColumnScale));
        }
    };
    BayonetsAndTomahawks.prototype.onAddingNewUndoableStepToLog = function (notif) {
        var _this = this;
        var _a;
        if (!$("log_".concat(notif.logId)))
            return;
        var stepId = notif.msg.args.stepId;
        $("log_".concat(notif.logId)).dataset.step = stepId;
        if ($("dockedlog_".concat(notif.mobileLogId)))
            $("dockedlog_".concat(notif.mobileLogId)).dataset.step = stepId;
        if ((_a = this.gamedatas.gamestate.args.previousSteps) === null || _a === void 0 ? void 0 : _a.includes(Number(stepId))) {
            this.onClick($("log_".concat(notif.logId)), function () { return _this.undoToStep({ stepId: stepId }); });
            if ($("dockedlog_".concat(notif.mobileLogId)))
                this.onClick($("dockedlog_".concat(notif.mobileLogId)), function () {
                    return _this.undoToStep({ stepId: stepId });
                });
        }
    };
    BayonetsAndTomahawks.prototype.onScreenWidthChange = function () {
        this.updateLayout();
    };
    BayonetsAndTomahawks.prototype.format_string_recursive = function (log, args) {
        var _this = this;
        try {
            if (log && args && !args.processed) {
                args.processed = true;
                Object.entries(args).forEach(function (_a) {
                    var key = _a[0], value = _a[1];
                    if (key.startsWith("tkn_")) {
                        args[key] = getTokenDiv({
                            key: key,
                            value: value,
                            game: _this,
                        });
                    }
                });
            }
        }
        catch (e) {
            console.error(log, args, "Exception thrown", e.stack);
        }
        return this.inherited(arguments);
    };
    BayonetsAndTomahawks.prototype.onPlaceLogOnChannel = function (msg) {
        var currentLogId = this.framework().notifqueue.next_log_id;
        var currentMobileLogId = this.framework().next_log_id;
        var res = this.framework().inherited(arguments);
        this._notif_uid_to_log_id[msg.uid] = currentLogId;
        this._notif_uid_to_mobile_log_id[msg.uid] = currentMobileLogId;
        this._last_notif = {
            logId: currentLogId,
            mobileLogId: currentMobileLogId,
            msg: msg,
        };
        return res;
    };
    BayonetsAndTomahawks.prototype.checkLogCancel = function (notifId) {
        if (this.gamedatas.canceledNotifIds != null &&
            this.gamedatas.canceledNotifIds.includes(notifId)) {
            this.cancelLogs([notifId]);
        }
    };
    BayonetsAndTomahawks.prototype.cancelLogs = function (notifIds) {
        var _this = this;
        notifIds.forEach(function (uid) {
            if (_this._notif_uid_to_log_id.hasOwnProperty(uid)) {
                var logId = _this._notif_uid_to_log_id[uid];
                if ($("log_" + logId))
                    dojo.addClass("log_" + logId, "cancel");
            }
            if (_this._notif_uid_to_mobile_log_id.hasOwnProperty(uid)) {
                var mobileLogId = _this._notif_uid_to_mobile_log_id[uid];
                if ($("dockedlog_" + mobileLogId))
                    dojo.addClass("dockedlog_" + mobileLogId, "cancel");
            }
        });
    };
    BayonetsAndTomahawks.prototype.addLogClass = function () {
        var _a;
        if (this._last_notif == null) {
            return;
        }
        var notif = this._last_notif;
        var type = notif.msg.type;
        if (type == "history_history") {
            type = notif.msg.args.originalType;
        }
        if ($("log_" + notif.logId)) {
            dojo.addClass("log_" + notif.logId, "notif_" + type);
            var methodName = "onAdding" + type.charAt(0).toUpperCase() + type.slice(1) + "ToLog";
            (_a = this[methodName]) === null || _a === void 0 ? void 0 : _a.call(this, notif);
        }
        if ($("dockedlog_" + notif.mobileLogId)) {
            dojo.addClass("dockedlog_" + notif.mobileLogId, "notif_" + type);
        }
    };
    BayonetsAndTomahawks.prototype.addLogTooltip = function (_a) {
        var tooltipId = _a.tooltipId, cardId = _a.cardId;
    };
    BayonetsAndTomahawks.prototype.setLoader = function (value, max) {
        this.framework().inherited(arguments);
        if (!this.framework().isLoadingComplete && value >= 100) {
            this.framework().isLoadingComplete = true;
            this.onLoadingComplete();
        }
    };
    BayonetsAndTomahawks.prototype.onLoadingComplete = function () {
        this.cancelLogs(this.gamedatas.canceledNotifIds);
        this.updateLayout();
    };
    BayonetsAndTomahawks.prototype.updatePlayerOrdering = function () {
        this.framework().inherited(arguments);
        var container = document.getElementById("player_boards");
        var infoPanel = document.getElementById("info_panel");
        if (!container) {
            return;
        }
        container.insertAdjacentElement("afterbegin", infoPanel);
    };
    BayonetsAndTomahawks.prototype.setAlwaysFixTopActions = function (alwaysFixed, maximum) {
        if (alwaysFixed === void 0) { alwaysFixed = true; }
        if (maximum === void 0) { maximum = 30; }
        this.alwaysFixTopActions = alwaysFixed;
        this.alwaysFixTopActionsMaximum = maximum;
        this.adaptStatusBar();
    };
    BayonetsAndTomahawks.prototype.adaptStatusBar = function () {
        this.inherited(arguments);
        if (this.alwaysFixTopActions) {
            var afterTitleElem = document.getElementById("after-page-title");
            var titleElem = document.getElementById("page-title");
            var zoom = getComputedStyle(titleElem).zoom;
            if (!zoom) {
                zoom = 1;
            }
            var titleRect = afterTitleElem.getBoundingClientRect();
            if (titleRect.top < 0 &&
                titleElem.offsetHeight <
                    (window.innerHeight * this.alwaysFixTopActionsMaximum) / 100) {
                var afterTitleRect = afterTitleElem.getBoundingClientRect();
                titleElem.classList.add("fixed-page-title");
                titleElem.style.width = (afterTitleRect.width - 10) / zoom + "px";
                afterTitleElem.style.height = titleRect.height + "px";
            }
            else {
                titleElem.classList.remove("fixed-page-title");
                titleElem.style.width = "auto";
                afterTitleElem.style.height = "0px";
            }
        }
    };
    BayonetsAndTomahawks.prototype.actionError = function (actionName) {
        this.framework().showMessage("cannot take ".concat(actionName, " action"), "error");
    };
    BayonetsAndTomahawks.prototype.takeAction = function (_a) {
        var action = _a.action, _b = _a.atomicAction, atomicAction = _b === void 0 ? true : _b, _c = _a.args, args = _c === void 0 ? {} : _c, checkAction = _a.checkAction;
        var actionName = atomicAction ? action : undefined;
        if (!this.framework().checkAction(checkAction || action)) {
            this.actionError(action);
            return;
        }
        var data = {
            lock: true,
            actionName: actionName,
            args: JSON.stringify(args),
        };
        var gameName = this.framework().game_name;
        this.framework().ajaxcall("/".concat(gameName, "/").concat(gameName, "/").concat(atomicAction ? "actTakeAtomicAction" : action, ".html"), data, this, function () { });
    };
    return BayonetsAndTomahawks;
}());
var BTCardManager = (function (_super) {
    __extends(BTCardManager, _super);
    function BTCardManager(game) {
        var _this = _super.call(this, game, {
            getId: function (card) { return card.id; },
            setupDiv: function (card, div) { return _this.setupDiv(card, div); },
            setupFrontDiv: function (card, div) { return _this.setupFrontDiv(card, div); },
            setupBackDiv: function (card, div) { return _this.setupBackDiv(card, div); },
            isCardVisible: function (card) { return _this.isCardVisible(card); },
            animationManager: game.animationManager,
        }) || this;
        _this.game = game;
        return _this;
    }
    BTCardManager.prototype.clearInterface = function () { };
    BTCardManager.prototype.setupDiv = function (card, div) {
        div.style.width = "calc(var(--btCardScale) * 250px)";
        div.style.height = "calc(var(--btCardScale) * 179px)";
        div.style.position = "relative";
    };
    BTCardManager.prototype.setupFrontDiv = function (card, div) {
        div.classList.add("bt_card");
        div.setAttribute("data-card-id", card.id);
        div.style.width = "calc(var(--btCardScale) * 250px)";
        div.style.height = "calc(var(--btCardScale) * 179px)";
    };
    BTCardManager.prototype.setupBackDiv = function (card, div) { };
    BTCardManager.prototype.isCardVisible = function (card) {
        if (card.location.startsWith("hand_")) {
            return true;
        }
        return false;
    };
    return BTCardManager;
}(CardManager));
var MIN_PLAY_AREA_WIDTH = 1500;
var DISABLED = "disabled";
var BT_SELECTABLE = "bt_selectable";
var BT_SELECTED = "bt_selected";
var PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY = "confirmEndOfTurnPlayerSwitchOnly";
var PREF_SHOW_ANIMATIONS = "showAnimations";
var PREF_ANIMATION_SPEED = "animationSpeed";
var PREF_DISABLED = "disabled";
var PREF_ENABLED = "enabled";
var POOL_FLEETS = "poolFleets";
var POOL_BRITISH_COMMANDERS = "poolBritishCommanders";
var POOL_BRITISH_LIGHT = "poolBritishLight";
var POOL_BRITISH_ARTILLERY = "poolBritishArtillery";
var POOL_BRITISH_FORTS = "poolBritishForts";
var POOL_BRITISH_METROPOLITAN_VOW = "poolBritishMetropolitanVoW";
var POOL_BRITISH_COLONIAL_VOW = "poolBritishColonialVoW";
var POOL_FRENCH_COMMANDERS = "poolFrenchCommanders";
var POOL_FRENCH_LIGHT = "poolFrenchLight";
var POOL_FRENCH_ARTILLERY = "poolFrenchArtillery";
var POOL_FRENCH_FORTS = "poolFrenchForts";
var POOL_FRENCH_METROPOLITAN_VOW = "poolFrenchMetropolitanVoW";
var POOL_NEUTRAL_INDIANS = "poolNeutralIndians";
var POOLS = [
    POOL_FLEETS,
    POOL_BRITISH_COMMANDERS,
    POOL_BRITISH_LIGHT,
    POOL_BRITISH_ARTILLERY,
    POOL_BRITISH_FORTS,
    POOL_BRITISH_METROPOLITAN_VOW,
    POOL_BRITISH_COLONIAL_VOW,
    POOL_FRENCH_COMMANDERS,
    POOL_FRENCH_LIGHT,
    POOL_FRENCH_ARTILLERY,
    POOL_FRENCH_FORTS,
    POOL_FRENCH_METROPOLITAN_VOW,
    POOL_NEUTRAL_INDIANS,
];
var YEAR_MARKER = "year_marker";
var ROUND_MARKER = "round_marker";
var VICTORY_MARKER = "victory_marker";
var OPEN_SEAS_MARKER = "open_seas_marker";
var FRENCH_RAID_MARKER = "french_raid_marker";
var BRITISH_RAID_MARKER = "british_raid_marker";
define([
    'dojo',
    'dojo/_base/declare',
    g_gamethemeurl + 'modules/js/vendor/nouislider.min.js',
    'dojo/fx',
    'dojox/fx/ext-dojo/complex',
    'ebg/core/gamegui',
    'ebg/counter',
], function (dojo, declare, noUiSliderDefined) {
    if (noUiSliderDefined) {
        noUiSlider = noUiSliderDefined;
    }
    return declare('bgagame.bayonetsandtomahawks', ebg.core.gamegui, new BayonetsAndTomahawks());
});
var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () { };
var capitalizeFirstLetter = function (string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
};
var YEAR_TRACK_CONFIG = [
    {
        year: 1755,
        top: 579,
        left: 1340,
    },
    {
        year: 1756,
        top: 579,
        left: 1385,
    },
    {
        year: 1757,
        top: 579,
        left: 1431,
    },
    {
        year: 1758,
        top: 630,
        left: 1340,
    },
    {
        year: 1759,
        top: 630,
        left: 1385,
    },
];
var ACTION_ROUND_TRACK_CONFIG = [
    {
        id: 'ar1',
        top: 717,
        left: 1340,
    },
    {
        id: 'ar2',
        top: 717,
        left: 1385,
    },
    {
        id: 'fleetsArrive',
        top: 717,
        left: 1431,
    },
    {
        id: 'ar3',
        top: 768,
        left: 1340,
    },
    {
        id: 'colonialsEnlist',
        top: 768,
        left: 1385,
    },
    {
        id: 'ar4',
        top: 831,
        left: 1340,
    },
    {
        id: 'ar5',
        top: 831,
        left: 1385,
    },
    {
        id: 'ar6',
        top: 831,
        left: 1431,
    },
    {
        id: 'ar7',
        top: 882,
        left: 1340,
    },
    {
        id: 'ar8',
        top: 882,
        left: 1385,
    },
    {
        id: 'ar9',
        top: 882,
        left: 1431,
    },
    {
        id: 'winterQuarters',
        top: 933,
        left: 1340,
    },
];
var GameMap = (function () {
    function GameMap(game) {
        this.game = game;
        var gamedatas = game.gamedatas;
        this.setupGameMap({ gamedatas: gamedatas });
    }
    GameMap.prototype.setupUnits = function (_a) {
        var gamedatas = _a.gamedatas;
        gamedatas.spaces.forEach(function (space) {
            var unit = gamedatas.units.find(function (unit) { return unit.location === space.id; });
            if (!unit) {
                return;
            }
            var node = document.querySelectorAll(".bt_space[data-space-id=\"".concat(space.id, "\"]"));
            if (node.length === 0) {
                return;
            }
            node[0].insertAdjacentHTML("afterbegin", tplUnit({
                faction: gamedatas.staticData.units[unit.counterId].faction,
                counterId: unit.counterId,
            }));
        });
    };
    GameMap.prototype.setupMarkers = function (_a) {
        var gamedatas = _a.gamedatas;
        var markers = gamedatas.markers;
        if (markers[YEAR_MARKER]) {
            document
                .getElementById("year_track_".concat(markers[YEAR_MARKER].location))
                .insertAdjacentHTML("beforeend", tplMarker({ id: markers[YEAR_MARKER].id }));
        }
        if (markers[ROUND_MARKER]) {
            document
                .getElementById("action_round_track_".concat(markers[ROUND_MARKER].location))
                .insertAdjacentHTML("beforeend", tplMarker({ id: markers[ROUND_MARKER].id }));
        }
    };
    GameMap.prototype.updateGameMap = function (_a) {
        var gamedatas = _a.gamedatas;
    };
    GameMap.prototype.setupGameMap = function (_a) {
        var gamedatas = _a.gamedatas;
        document
            .getElementById("play_area_container")
            .insertAdjacentHTML("afterbegin", tplGameMap({ gamedatas: gamedatas }));
        this.setupUnits({ gamedatas: gamedatas });
        this.setupMarkers({ gamedatas: gamedatas });
    };
    GameMap.prototype.clearInterface = function () { };
    return GameMap;
}());
var tplMarker = function (_a) {
    var id = _a.id;
    return "<div id=\"".concat(id, "\" class=\"bt_marker\"></div>");
};
var tplUnit = function (_a) {
    var faction = _a.faction, counterId = _a.counterId, style = _a.style;
    return "\n  <div class=\"bt_token\" data-counter-id=\"".concat(counterId, "\"").concat(style ? " style=\"".concat(style, "\"") : "", "></div>\n");
};
var tplSpaces = function (_a) {
    var spaces = _a.spaces;
    var filteredSpaces = spaces.filter(function (space) { return space.top && space.left; });
    var mappedSpaces = filteredSpaces.map(function (space) {
        return "<div data-space-id=\"".concat(space.id, "\" class=\"bt_space\" style=\"top: calc(var(--btMapScale) * ").concat(space.top - 26, "px); left: calc(var(--btMapScale) * ").concat(space.left - 26, "px);\"></div>");
    });
    var result = mappedSpaces.join("");
    return result;
};
var tplMarkerSpace = function (_a) {
    var id = _a.id, top = _a.top, left = _a.left;
    return "<div id=\"".concat(id, "\" class=\"bt_marker_space\" style=\"top: calc(var(--btMapScale) * ").concat(top, "px); left: calc(var(--btMapScale) * ").concat(left, "px);\"></div>");
};
var tplActionRoundTrack = function () { return ACTION_ROUND_TRACK_CONFIG.map(function (markerSpace) {
    return tplMarkerSpace({
        id: "action_round_track_".concat(markerSpace.id),
        top: markerSpace.top,
        left: markerSpace.left,
    });
}).join(""); };
var tplYearTrack = function () { return YEAR_TRACK_CONFIG.map(function (markerSpace) {
    return tplMarkerSpace({
        id: "year_track_".concat(markerSpace.year),
        top: markerSpace.top,
        left: markerSpace.left,
    });
}).join(""); };
var tplGameMap = function (_a) {
    var gamedatas = _a.gamedatas;
    var spaces = gamedatas.spaces;
    return "\n  <div id=\"bt_game_map\">\n    <div class=\"bt_marker_test\" data-marker-type=\"victory_point\"></div>\n    ".concat(tplSpaces({ spaces: spaces }), "\n    ").concat(tplYearTrack(), "\n    ").concat(tplActionRoundTrack(), "\n\n  </div>");
};
var Hand = (function () {
    function Hand(game) {
        this.game = game;
        this.setupHand();
    }
    Hand.prototype.clearInterface = function () {
        this.hand.removeAll();
    };
    Hand.prototype.updateHand = function () { };
    Hand.prototype.setupHand = function () {
        var node = $("game_play_area");
        node.insertAdjacentHTML("beforeend", tplHand());
        var handWrapper = $("floating_hand_wrapper");
        $("floating_hand_button").addEventListener("click", function () {
            if (handWrapper.dataset.open && handWrapper.dataset.open == "hand") {
                delete handWrapper.dataset.open;
            }
            else {
                handWrapper.dataset.open = "hand";
            }
        });
        this.hand = new LineStock(this.game.cardManager, document.getElementById("player_hand"), { wrap: "nowrap", gap: "12px", center: false });
    };
    Hand.prototype.addCard = function (card) {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0: return [4, this.hand.addCard(card)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    Hand.prototype.removeCard = function (card) {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0: return [4, this.hand.removeCard(card)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    Hand.prototype.getCards = function () {
        return this.hand.getCards();
    };
    Hand.prototype.getStock = function () {
        return this.hand;
    };
    return Hand;
}());
var tplHand = function () {
    return "<div id=\"floating_hand_wrapper\" class=\"active\">\n            <div id=\"floating_hand_button_container\">\n              <button id=\"floating_hand_button\" type=\"button\" class=\"bt_button\">\n                <div class=\"icon\"></div>\n              </button>  \n            </div>\n            <div id=\"floating_hand\">\n              <div id=\"player_hand\"\">\n              </div>\n            </div>\n          </div\n  ";
};
var InfoPanel = (function () {
    function InfoPanel(game) {
        this.game = game;
        var gamedatas = game.gamedatas;
        this.setup({ gamedatas: gamedatas });
    }
    InfoPanel.prototype.clearInterface = function () { };
    InfoPanel.prototype.updateInterface = function (_a) {
        var gamedatas = _a.gamedatas;
    };
    InfoPanel.prototype.setup = function (_a) {
        var gamedatas = _a.gamedatas;
        var node = document.getElementById("player_boards");
        if (!node) {
            return;
        }
        node.insertAdjacentHTML("afterbegin", tplInfoPanel());
    };
    return InfoPanel;
}());
var tplInfoPanel = function () { return "<div class='player-board' id=\"info_panel\"></div>"; };
var LOG_TOKEN_BOLD_TEXT = 'boldText';
var LOG_TOKEN_NEW_LINE = 'newLine';
var LOG_TOKEN_PLAYER_NAME = 'playerName';
var tooltipIdCounter = 0;
var getTokenDiv = function (_a) {
    var key = _a.key, value = _a.value, game = _a.game;
    var splitKey = key.split('_');
    var type = splitKey[1];
    switch (type) {
        case LOG_TOKEN_BOLD_TEXT:
            return tlpLogTokenBoldText({ text: value });
        case LOG_TOKEN_NEW_LINE:
            return '<br>';
        case LOG_TOKEN_PLAYER_NAME:
            var player = game.playerManager.getPlayers().find(function (player) { return player.getName() === value; });
            return player ? tplLogTokenPlayerName({ name: player.getName(), color: player.getHexColor() }) : value;
        default:
            return value;
    }
};
var tlpLogTokenBoldText = function (_a) {
    var text = _a.text;
    return "<span style=\"font-weight: 700;\">".concat(_(text), "</span>");
};
var tplLogTokenPlayerName = function (_a) {
    var name = _a.name, color = _a.color;
    return "<span class=\"playername\" style=\"color:#".concat(color, ";\">").concat(name, "</span>");
};
var NotificationManager = (function () {
    function NotificationManager(game) {
        this.game = game;
        this.subscriptions = [];
    }
    NotificationManager.prototype.setupNotifications = function () {
        var _this = this;
        console.log('notifications subscriptions setup');
        var notifs = [
            ['log', 1],
        ];
        notifs.forEach(function (notif) {
            _this.subscriptions.push(dojo.subscribe(notif[0], _this, function (notifDetails) {
                debug("notif_".concat(notif[0]), notifDetails);
                var promise = _this["notif_".concat(notif[0])](notifDetails);
                promise === null || promise === void 0 ? void 0 : promise.then(function () { return _this.game.framework().notifqueue.onSynchronousNotificationEnd(); });
            }));
            _this.game.framework().notifqueue.setSynchronous(notif[0], notif[1]);
        });
    };
    NotificationManager.prototype.notif_log = function (notif) {
        debug('notif_log', notif.args);
    };
    NotificationManager.prototype.notif_smallRefreshInterface = function (notif) {
        var updatedGamedatas = __assign(__assign({}, this.game.gamedatas), notif.args);
        this.game.clearInterface();
        this.game.gamedatas = updatedGamedatas;
        this.game.playerManager.updatePlayers({ gamedatas: updatedGamedatas });
    };
    NotificationManager.prototype.destroy = function () {
        dojo.forEach(this.subscriptions, dojo.unsubscribe);
    };
    NotificationManager.prototype.getPlayer = function (_a) {
        var playerId = _a.playerId;
        return this.game.playerManager.getPlayer({ playerId: playerId });
    };
    return NotificationManager;
}());
var PlayerManager = (function () {
    function PlayerManager(game) {
        console.log('Constructor PlayerManager');
        this.game = game;
        this.players = {};
        for (var playerId in game.gamedatas.players) {
            var player = game.gamedatas.players[playerId];
            this.players[playerId] = new BatPlayer({ player: player, game: this.game });
        }
    }
    PlayerManager.prototype.getPlayer = function (_a) {
        var playerId = _a.playerId;
        return this.players[playerId];
    };
    PlayerManager.prototype.getPlayers = function () {
        return Object.values(this.players);
    };
    PlayerManager.prototype.getPlayerIds = function () {
        return Object.keys(this.players).map(Number);
    };
    PlayerManager.prototype.updatePlayers = function (_a) {
        var gamedatas = _a.gamedatas;
        for (var playerId in gamedatas.players) {
            this.players[playerId].updatePlayer({ gamedatas: gamedatas });
        }
    };
    PlayerManager.prototype.clearInterface = function () {
        var _this = this;
        Object.keys(this.players).forEach(function (playerId) {
            _this.players[playerId].clearInterface();
        });
    };
    return PlayerManager;
}());
var BatPlayer = (function () {
    function BatPlayer(_a) {
        var game = _a.game, player = _a.player;
        this.game = game;
        var playerId = player.id;
        this.playerId = Number(playerId);
        this.player = player;
        this.playerName = player.name;
        this.playerColor = player.color;
        this.playerHexColor = player.hexColor;
        var gamedatas = game.gamedatas;
        this.setupPlayer({ gamedatas: gamedatas });
    }
    BatPlayer.prototype.updatePlayer = function (_a) {
        var gamedatas = _a.gamedatas;
    };
    BatPlayer.prototype.setupPlayer = function (_a) {
        var gamedatas = _a.gamedatas;
        var playerGamedatas = gamedatas.players[this.playerId];
        this.setupPlayerPanel({ playerGamedatas: playerGamedatas });
        this.setupHand({ playerGamedatas: playerGamedatas });
    };
    BatPlayer.prototype.setupHand = function (_a) {
        var playerGamedatas = _a.playerGamedatas;
        if (this.playerId === this.game.getPlayerId()) {
            this.game.hand.getStock().addCards(playerGamedatas.hand);
        }
    };
    BatPlayer.prototype.setupPlayerPanel = function (_a) {
        var playerGamedatas = _a.playerGamedatas;
        this.updatePlayerPanel({ playerGamedatas: playerGamedatas });
    };
    BatPlayer.prototype.updatePlayerPanel = function (_a) {
        var _b;
        var playerGamedatas = _a.playerGamedatas;
        if ((_b = this.game.framework().scoreCtrl) === null || _b === void 0 ? void 0 : _b[this.playerId]) {
            this.game
                .framework()
                .scoreCtrl[this.playerId].setValue(Number(playerGamedatas.score));
        }
    };
    BatPlayer.prototype.clearInterface = function () { };
    BatPlayer.prototype.getColor = function () {
        return this.playerColor;
    };
    BatPlayer.prototype.getHexColor = function () {
        return this.playerHexColor;
    };
    BatPlayer.prototype.getName = function () {
        return this.playerName;
    };
    BatPlayer.prototype.getPlayerId = function () {
        return this.playerId;
    };
    return BatPlayer;
}());
var Pools = (function () {
    function Pools(game) {
        this.game = game;
        var gamedatas = game.gamedatas;
        this.setupPools({ gamedatas: gamedatas });
    }
    Pools.prototype.setupUnits = function (_a) {
        var gamedatas = _a.gamedatas;
        POOLS.forEach(function (pool) {
            var units = gamedatas.units.filter(function (unit) { return unit.location === pool; });
            if (units.length === 0) {
                return;
            }
            var node = document.querySelectorAll("[data-pool-id=\"".concat(pool, "\"]"));
            if (node.length === 0) {
                return;
            }
            units.forEach(function (unit) {
                node[0].insertAdjacentHTML('beforeend', tplUnit({ counterId: unit.counterId, style: 'position: relative;' }));
            });
        });
    };
    Pools.prototype.updatePools = function (_a) {
        var gamedatas = _a.gamedatas;
    };
    Pools.prototype.setupPools = function (_a) {
        var gamedatas = _a.gamedatas;
        document
            .getElementById("play_area_container")
            .insertAdjacentHTML("beforeend", tplPoolsContainer());
        this.setupUnits({ gamedatas: gamedatas });
    };
    Pools.prototype.clearInterface = function () { };
    return Pools;
}());
var tplPoolsContainer = function () {
    return "\n  <div id=\"bt_pools_container\">\n    ".concat(tplPoolFleets(), "\n    ").concat(tplPoolNeutralIndians(), "\n    ").concat(tplPoolBritish(), "\n    ").concat(tplPoolFrench(), "\n  </div>");
};
var tplPoolFleets = function () { return "\n<div id=\"bt_pool_fleets\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('Fleets'), "</span></div>\n  <div data-pool-id=\"poolFleets\" class=\"bt_unit_pool\"></div>\n</div>"); };
var tplPoolNeutralIndians = function () { return "\n<div id=\"bt_pool_neutralIndians\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('Neutral Indians'), "</span></div>\n  <div data-pool-id=\"poolNeutralIndians\" class=\"bt_unit_pool\"></div>\n</div>"); };
var tplPoolBritish = function () { return "\n<div id=\"bt_pool_british\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('British'), "</span></div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Commanders'), "</span></div>\n    <div data-pool-id=\"poolBritishCommanders\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Light'), "</span></div>\n    <div data-pool-id=\"poolBritishLight\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Artillery'), "</span></div>\n    <div data-pool-id=\"poolBritishArtillery\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Forts'), "</span></div>\n    <div data-pool-id=\"poolBritishForts\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Metropolitan Brigades & VoW'), "</span></div>\n    <div data-pool-id=\"poolBritishMetropolitanVoW\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Colonial Brigades & VoW'), "</span></div>\n    <div data-pool-id=\"poolBritishColonialVoW\" class=\"bt_unit_pool\"></div>\n  </div>\n</div>\n"); };
var tplPoolFrench = function () { return "\n<div id=\"bt_pool_french\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('French'), "</span></div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Commanders'), "</span></div>\n    <div data-pool-id=\"poolFrenchCommanders\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Light'), "</span></div>\n    <div data-pool-id=\"poolFrenchLight\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Artillery'), "</span></div>\n    <div data-pool-id=\"poolFrenchArtillery\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Forts'), "</span></div>\n    <div data-pool-id=\"poolFrenchForts\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Metropolitan Brigades & VoW'), "</span></div>\n    <div data-pool-id=\"poolFrenchMetropolitanVoW\" class=\"bt_unit_pool\"></div>\n  </div>\n</div>\n"); };
var tplPool = function (_a) {
    var type = _a.type;
    return "<div id=\"bt_pool_".concat(type, "\" class=\"bt_unit_pool\">\n  </div>");
};
var getSettingsConfig = function () {
    var _a;
    return ({
        layout: {
            id: "layout",
            config: {
                twoColumnsLayout: {
                    id: "twoColumnsLayout",
                    onChangeInSetup: true,
                    defaultValue: "disabled",
                    label: _("Two column layout"),
                    type: "select",
                    options: [
                        {
                            label: _("Enabled"),
                            value: "enabled",
                        },
                        {
                            label: _("Disabled (single column)"),
                            value: "disabled",
                        },
                    ],
                },
                columnSizes: {
                    id: "columnSizes",
                    onChangeInSetup: true,
                    label: _("Column sizes"),
                    defaultValue: 50,
                    visibleCondition: {
                        id: "twoColumnsLayout",
                        values: [PREF_ENABLED],
                    },
                    sliderConfig: {
                        step: 5,
                        padding: 0,
                        range: {
                            min: 30,
                            max: 70,
                        },
                    },
                    type: "slider",
                },
            },
        },
        gameplay: {
            id: "gameplay",
            config: (_a = {},
                _a[PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY] = {
                    id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
                    onChangeInSetup: false,
                    defaultValue: DISABLED,
                    label: _("Confirm end of turn and player switch only"),
                    type: "select",
                    options: [
                        {
                            label: _("Enabled"),
                            value: PREF_ENABLED,
                        },
                        {
                            label: _("Disabled (confirm every move)"),
                            value: PREF_DISABLED,
                        },
                    ],
                },
                _a[PREF_SHOW_ANIMATIONS] = {
                    id: PREF_SHOW_ANIMATIONS,
                    onChangeInSetup: false,
                    defaultValue: PREF_ENABLED,
                    label: _("Show animations"),
                    type: "select",
                    options: [
                        {
                            label: _("Enabled"),
                            value: PREF_ENABLED,
                        },
                        {
                            label: _("Disabled"),
                            value: PREF_DISABLED,
                        },
                    ],
                },
                _a[PREF_ANIMATION_SPEED] = {
                    id: PREF_ANIMATION_SPEED,
                    onChangeInSetup: false,
                    label: _("Animation speed"),
                    defaultValue: 1600,
                    visibleCondition: {
                        id: PREF_SHOW_ANIMATIONS,
                        values: [PREF_ENABLED],
                    },
                    sliderConfig: {
                        step: 100,
                        padding: 0,
                        range: {
                            min: 100,
                            max: 2000,
                        },
                    },
                    type: "slider",
                },
                _a),
        },
    });
};
var Settings = (function () {
    function Settings(game) {
        this.settings = {};
        this.selectedTab = "layout";
        this.tabs = [
            {
                id: "layout",
                name: _("Layout"),
            },
            {
                id: "gameplay",
                name: _("Gameplay"),
            },
        ];
        this.game = game;
        var gamedatas = game.gamedatas;
        this.setup({ gamedatas: gamedatas });
    }
    Settings.prototype.clearInterface = function () { };
    Settings.prototype.updateInterface = function (_a) {
        var gamedatas = _a.gamedatas;
    };
    Settings.prototype.addButton = function (_a) {
        var gamedatas = _a.gamedatas;
        var configPanel = document.getElementById("info_panel");
        if (configPanel) {
            configPanel.insertAdjacentHTML("beforeend", tplSettingsButton());
        }
    };
    Settings.prototype.setupModal = function (_a) {
        var gamedatas = _a.gamedatas;
        this.modal = new Modal("settings_modal", {
            class: "settings_modal",
            closeIcon: "fa-times",
            titleTpl: '<h2 id="popin_${id}_title" class="${class}_title">${title}</h2>',
            title: _("Settings"),
            contents: tplSettingsModalContent({
                tabs: this.tabs,
            }),
            closeAction: "hide",
            verticalAlign: "flex-start",
            breakpoint: 740,
        });
    };
    Settings.prototype.setup = function (_a) {
        var _this = this;
        var gamedatas = _a.gamedatas;
        this.addButton({ gamedatas: gamedatas });
        this.setupModal({ gamedatas: gamedatas });
        this.setupModalContent();
        this.changeTab({ id: this.selectedTab });
        dojo.connect($("show_settings"), "onclick", function () { return _this.open(); });
        this.tabs.forEach(function (_a) {
            var id = _a.id;
            dojo.connect($("settings_modal_tab_".concat(id)), "onclick", function () {
                return _this.changeTab({ id: id });
            });
        });
    };
    Settings.prototype.setupModalContent = function () {
        var _this = this;
        var config = getSettingsConfig();
        var node = document.getElementById("setting_modal_content");
        if (!node) {
            return;
        }
        Object.entries(config).forEach(function (_a) {
            var tabId = _a[0], tabConfig = _a[1];
            node.insertAdjacentHTML("beforeend", tplSettingsModalTabContent({ id: tabId }));
            var tabContentNode = document.getElementById("settings_modal_tab_content_".concat(tabId));
            if (!tabContentNode) {
                return;
            }
            Object.values(tabConfig.config).forEach(function (setting) {
                var id = setting.id, type = setting.type, defaultValue = setting.defaultValue, visibleCondition = setting.visibleCondition;
                var localValue = localStorage.getItem(_this.getLocalStorageKey({ id: id }));
                _this.settings[id] = localValue || defaultValue;
                var methodName = _this.getMethodName({ id: id });
                if (setting.onChangeInSetup && localValue && _this[methodName]) {
                    _this[methodName](localValue);
                }
                if (setting.type === "select") {
                    var visible = !visibleCondition ||
                        (visibleCondition &&
                            visibleCondition.values.includes(_this.settings[visibleCondition.id]));
                    tabContentNode.insertAdjacentHTML("beforeend", tplPlayerPrefenceSelectRow({
                        setting: setting,
                        currentValue: _this.settings[setting.id],
                        visible: visible,
                    }));
                    var controlId_1 = "setting_".concat(setting.id);
                    $(controlId_1).addEventListener("change", function () {
                        var value = $(controlId_1).value;
                        _this.changeSetting({ id: setting.id, value: value });
                    });
                }
                else if (setting.type === "slider") {
                    var visible = !visibleCondition ||
                        (visibleCondition &&
                            visibleCondition.values.includes(_this.settings[visibleCondition.id]));
                    tabContentNode.insertAdjacentHTML("beforeend", tplPlayerPrefenceSliderRow({
                        id: setting.id,
                        label: setting.label,
                        visible: visible,
                    }));
                    var sliderConfig = __assign(__assign({}, setting.sliderConfig), { start: _this.settings[setting.id] });
                    noUiSlider.create($("setting_" + setting.id), sliderConfig);
                    $("setting_" + setting.id).noUiSlider.on("slide", function (arg) {
                        return _this.changeSetting({ id: setting.id, value: arg[0] });
                    });
                }
            });
        });
    };
    Settings.prototype.changeSetting = function (_a) {
        var id = _a.id, value = _a.value;
        var suffix = this.getSuffix({ id: id });
        this.settings[id] = value;
        localStorage.setItem(this.getLocalStorageKey({ id: id }), value);
        var methodName = this.getMethodName({ id: id });
        if (this[methodName]) {
            this[methodName](value);
        }
    };
    Settings.prototype.onChangeTwoColumnsLayoutSetting = function (value) {
        this.checkColumnSizesVisisble();
        var node = document.getElementById("play_area_container");
        if (node) {
            node.setAttribute("data-two-columns", value);
        }
        this.game.updateLayout();
    };
    Settings.prototype.onChangeColumnSizesSetting = function (value) {
        this.game.updateLayout();
    };
    Settings.prototype.onChangeCardSizeInLogSetting = function (value) {
        var ROOT = document.documentElement;
        ROOT.style.setProperty("--logCardScale", "".concat(Number(value) / 100));
    };
    Settings.prototype.onChangeAnimationSpeedSetting = function (value) {
        var duration = 2100 - value;
        debug("onChangeAnimationSpeedSetting", duration);
        this.game.animationManager.getSettings().duration = duration;
    };
    Settings.prototype.onChangeShowAnimationsSetting = function (value) {
        if (value === PREF_ENABLED) {
            this.game.animationManager.getSettings().duration = Number(this.settings[PREF_ANIMATION_SPEED]);
        }
        else {
            this.game.animationManager.getSettings().duration = 0;
        }
        this.checkAnmimationSpeedVisisble();
    };
    Settings.prototype.changeTab = function (_a) {
        var id = _a.id;
        var currentTab = document.getElementById("settings_modal_tab_".concat(this.selectedTab));
        var currentTabContent = document.getElementById("settings_modal_tab_content_".concat(this.selectedTab));
        currentTab.removeAttribute("data-state");
        if (currentTabContent) {
            currentTabContent.style.display = "none";
        }
        this.selectedTab = id;
        var tab = document.getElementById("settings_modal_tab_".concat(id));
        var tabContent = document.getElementById("settings_modal_tab_content_".concat(this.selectedTab));
        tab.setAttribute("data-state", "selected");
        if (tabContent) {
            tabContent.style.display = "";
        }
    };
    Settings.prototype.checkAnmimationSpeedVisisble = function () {
        var sliderNode = document.getElementById("setting_row_animationSpeed");
        if (!sliderNode) {
            return;
        }
        if (this.settings[PREF_SHOW_ANIMATIONS] === PREF_ENABLED) {
            sliderNode.style.display = "";
        }
        else {
            sliderNode.style.display = "none";
        }
    };
    Settings.prototype.checkColumnSizesVisisble = function () {
        var sliderNode = document.getElementById("setting_row_columnSizes");
        if (!sliderNode) {
            return;
        }
        if (this.settings["twoColumnsLayout"] === PREF_ENABLED) {
            sliderNode.style.display = "";
        }
        else {
            sliderNode.style.display = "none";
        }
    };
    Settings.prototype.getMethodName = function (_a) {
        var id = _a.id;
        return "onChange".concat(this.getSuffix({ id: id }), "Setting");
    };
    Settings.prototype.get = function (_a) {
        var id = _a.id;
        return this.settings[id] || null;
    };
    Settings.prototype.getSuffix = function (_a) {
        var id = _a.id;
        return id.charAt(0).toUpperCase() + id.slice(1);
    };
    Settings.prototype.getLocalStorageKey = function (_a) {
        var id = _a.id;
        return "".concat(this.game.framework().game_name, "-").concat(this.getSuffix({ id: id }));
    };
    Settings.prototype.open = function () {
        this.modal.show();
    };
    return Settings;
}());
var tplSettingsButton = function () {
    return "<div id=\"show_settings\">\n  <svg  xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 640 512\">\n    <g>\n      <path class=\"fa-secondary\" fill=\"currentColor\" d=\"M638.41 387a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4L602 335a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6 12.36 12.36 0 0 0-15.1 5.4l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 44.9c-29.6-38.5 14.3-82.4 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79zm136.8-343.8a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4l8.2-14.3a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6A12.36 12.36 0 0 0 552 7.19l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 45c-29.6-38.5 14.3-82.5 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79z\" opacity=\"0.4\"></path>\n      <path class=\"fa-primary\" fill=\"currentColor\" d=\"M420 303.79L386.31 287a173.78 173.78 0 0 0 0-63.5l33.7-16.8c10.1-5.9 14-18.2 10-29.1-8.9-24.2-25.9-46.4-42.1-65.8a23.93 23.93 0 0 0-30.3-5.3l-29.1 16.8a173.66 173.66 0 0 0-54.9-31.7V58a24 24 0 0 0-20-23.6 228.06 228.06 0 0 0-76 .1A23.82 23.82 0 0 0 158 58v33.7a171.78 171.78 0 0 0-54.9 31.7L74 106.59a23.91 23.91 0 0 0-30.3 5.3c-16.2 19.4-33.3 41.6-42.2 65.8a23.84 23.84 0 0 0 10.5 29l33.3 16.9a173.24 173.24 0 0 0 0 63.4L12 303.79a24.13 24.13 0 0 0-10.5 29.1c8.9 24.1 26 46.3 42.2 65.7a23.93 23.93 0 0 0 30.3 5.3l29.1-16.7a173.66 173.66 0 0 0 54.9 31.7v33.6a24 24 0 0 0 20 23.6 224.88 224.88 0 0 0 75.9 0 23.93 23.93 0 0 0 19.7-23.6v-33.6a171.78 171.78 0 0 0 54.9-31.7l29.1 16.8a23.91 23.91 0 0 0 30.3-5.3c16.2-19.4 33.7-41.6 42.6-65.8a24 24 0 0 0-10.5-29.1zm-151.3 4.3c-77 59.2-164.9-28.7-105.7-105.7 77-59.2 164.91 28.7 105.71 105.7z\"></path>\n    </g>\n  </svg>\n</div>";
};
var tplPlayerPrefenceSelectRow = function (_a) {
    var setting = _a.setting, currentValue = _a.currentValue, _b = _a.visible, visible = _b === void 0 ? true : _b;
    var values = setting.options
        .map(function (option) {
        return "<option value='".concat(option.value, "' ").concat(option.value === currentValue ? 'selected="selected"' : "", ">").concat(_(option.label), "</option>");
    })
        .join("");
    return "\n    <div id=\"setting_row_".concat(setting.id, "\" class=\"player_preference_row\"").concat(!visible ? " style=\"display: none;\"" : '', ">\n      <div class=\"player_preference_row_label\">").concat(_(setting.label), "</div>\n      <div class=\"player_preference_row_value\">\n        <select id=\"setting_").concat(setting.id, "\" class=\"\" style=\"display: block;\">\n        ").concat(values, "\n        </select>\n      </div>\n    </div>\n  ");
};
var tplSettingsModalTabContent = function (_a) {
    var id = _a.id;
    return "\n  <div id=\"settings_modal_tab_content_".concat(id, "\" style=\"display: none;\"></div>");
};
var tplSettingsModalTab = function (_a) {
    var id = _a.id, name = _a.name;
    return "\n  <div id=\"settings_modal_tab_".concat(id, "\" class=\"settings_modal_tab\">\n    <span>").concat(_(name), "</span>\n  </div>");
};
var tplSettingsModalContent = function (_a) {
    var tabs = _a.tabs;
    return "<div id=\"setting_modal_content\">\n    <div class=\"settings_modal_tabs\">\n  ".concat(tabs
        .map(function (_a) {
        var id = _a.id, name = _a.name;
        return tplSettingsModalTab({ id: id, name: name });
    })
        .join(""), "\n    </div>\n  </div>");
};
var tplPlayerPrefenceSliderRow = function (_a) {
    var label = _a.label, id = _a.id, _b = _a.visible, visible = _b === void 0 ? true : _b;
    return "\n  <div id=\"setting_row_".concat(id, "\" class=\"player_preference_row\"").concat(!visible ? " style=\"display: none;\"" : '', ">\n    <div class=\"player_preference_row_label\">").concat(_(label), "</div>\n    <div class=\"player_preference_row_value slider\">\n      <div id=\"setting_").concat(id, "\"></div>\n    </div>\n  </div>\n  ");
};
var SelectReserveCardState = (function () {
    function SelectReserveCardState(game) {
        this.game = game;
    }
    SelectReserveCardState.prototype.onEnteringState = function (args) {
        debug("Entering SelectReserveCardState");
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    SelectReserveCardState.prototype.onLeavingState = function () {
        debug("Leaving SelectReserveCardState");
    };
    SelectReserveCardState.prototype.setDescription = function (activePlayerId) {
    };
    SelectReserveCardState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _("${you} must select a Reserve Card"),
            args: {
                you: "${you}",
            },
        });
        this.args._private.forEach(function (card) {
            _this.game.setCardSelectable({
                id: card.id,
                callback: function () {
                    _this.updateInterfaceConfirm({ card: card });
                },
            });
        });
    };
    SelectReserveCardState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var card = _a.card;
        this.game.clearPossible();
        this.game.setCardSelected({ id: card.id });
        this.game.clientUpdatePageTitle({
            text: _("Select card?"),
            args: {},
        });
        var callback = function () {
            return _this.game.takeAction({
                action: "actSelectReserveCard",
                args: {
                    cardId: card.id,
                },
            });
        };
        if (this.game.settings.get({
            id: PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY,
        }) === PREF_ENABLED) {
            callback();
        }
        else {
            this.game.addConfirmButton({
                callback: callback,
            });
        }
        this.game.addCancelButton();
    };
    return SelectReserveCardState;
}());
var tplCardTooltipContainer = function (_a) {
    var card = _a.card, content = _a.content;
    return "<div class=\"bt_card_tooltip\">\n  <div class=\"bt_card_tooltip_inner_container\">\n    ".concat(content, "\n  </div>\n  ").concat(card, "\n</div>");
};
var TooltipManager = (function () {
    function TooltipManager(game) {
        this.idRegex = /id="[a-z]*_[0-9]*_[0-9]*"/;
        this.game = game;
    }
    TooltipManager.prototype.addTextToolTip = function (_a) {
        var nodeId = _a.nodeId, text = _a.text;
        this.game.framework().addTooltip(nodeId, _(text), '', 500);
    };
    TooltipManager.prototype.removeTooltip = function (nodeId) {
        this.game.framework().removeTooltip(nodeId);
    };
    TooltipManager.prototype.setupTooltips = function () {
    };
    return TooltipManager;
}());
