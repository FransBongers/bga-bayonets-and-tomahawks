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
var MIN_PLAY_AREA_WIDTH = 1500;
var MIN_NOTIFICATION_MS = 1200;
var DISABLED = 'disabled';
var BT_SELECTABLE = 'bt_selectable';
var BT_SELECTED = 'bt_selected';
var DISCARD = 'discard';
var PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY = 'confirmEndOfTurnPlayerSwitchOnly';
var PREF_SHOW_ANIMATIONS = 'showAnimations';
var PREF_ANIMATION_SPEED = 'animationSpeed';
var PREF_CARD_SIZE_IN_LOG = 'cardSizeInLog';
var PREF_DISABLED = 'disabled';
var PREF_ENABLED = 'enabled';
var PREF_SINGLE_COLUMN_MAP_SIZE = 'singleColumnMapSize';
var BRITISH = 'british';
var FRENCH = 'french';
var INDIAN = 'indian';
var NEUTRAL = 'neutral';
var FACTIONS = [BRITISH, FRENCH, INDIAN];
var ARTILLERY = 'artillery';
var BASTION_UNIT_TYPE = 'bastion';
var BRIGADE = 'brigade';
var COMMANDER = 'commander';
var FLEET = 'fleet';
var FORT = 'fort';
var LIGHT = 'light';
var VAGARIES_OF_WAR = 'vagariesOfWar';
var REMOVED_FROM_PLAY = 'removedFromPlay';
var POOL_FLEETS = 'poolFleets';
var POOL_BRITISH_COMMANDERS = 'poolBritishCommanders';
var POOL_BRITISH_LIGHT = 'poolBritishLight';
var POOL_BRITISH_ARTILLERY = 'poolBritishArtillery';
var POOL_BRITISH_FORTS = 'poolBritishForts';
var POOL_BRITISH_METROPOLITAN_VOW = 'poolBritishMetropolitanVoW';
var POOL_BRITISH_COLONIAL_LIGHT = 'poolBritishColonialLight';
var POOL_BRITISH_COLONIAL_VOW = 'poolBritishColonialVoW';
var POOL_BRITISH_COLONIAL_VOW_BONUS = 'poolBritishColonialVoWBonus';
var POOL_FRENCH_COMMANDERS = 'poolFrenchCommanders';
var POOL_FRENCH_LIGHT = 'poolFrenchLight';
var POOL_FRENCH_ARTILLERY = 'poolFrenchArtillery';
var POOL_FRENCH_FORTS = 'poolFrenchForts';
var POOL_FRENCH_METROPOLITAN_VOW = 'poolFrenchMetropolitanVoW';
var POOL_NEUTRAL_INDIANS = 'poolNeutralIndians';
var REINFORCEMENTS_FLEETS = 'reinforcementsFleets';
var REINFORCEMENTS_BRITISH = 'reinforcementsBritish';
var REINFORCEMENTS_FRENCH = 'reinforcementsFrench';
var REINFORCEMENTS_COLONIAL = 'reinforcementsColonial';
var POOLS = [
    POOL_FLEETS,
    POOL_BRITISH_COMMANDERS,
    POOL_BRITISH_LIGHT,
    POOL_BRITISH_COLONIAL_LIGHT,
    POOL_BRITISH_ARTILLERY,
    POOL_BRITISH_FORTS,
    POOL_BRITISH_METROPOLITAN_VOW,
    POOL_BRITISH_COLONIAL_VOW,
    POOL_BRITISH_COLONIAL_VOW_BONUS,
    POOL_FRENCH_COMMANDERS,
    POOL_FRENCH_LIGHT,
    POOL_FRENCH_ARTILLERY,
    POOL_FRENCH_FORTS,
    POOL_FRENCH_METROPOLITAN_VOW,
    POOL_NEUTRAL_INDIANS,
    REINFORCEMENTS_FLEETS,
    REINFORCEMENTS_BRITISH,
    REINFORCEMENTS_FRENCH,
    REINFORCEMENTS_COLONIAL,
];
var YEAR_MARKER = 'year_marker';
var ROUND_MARKER = 'round_marker';
var VICTORY_MARKER = 'victory_marker';
var OPEN_SEAS_MARKER = 'openSeasMarker';
var FRENCH_RAID_MARKER = 'french_raid_marker';
var BRITISH_RAID_MARKER = 'british_raid_marker';
var FRENCH_BATTLE_MARKER = 'french_battle_marker';
var BRITISH_BATTLE_MARKER = 'british_battle_marker';
var RAID_TRACK_0 = 'raid_track_0';
var RAID_TRACK_1 = 'raid_track_1';
var RAID_TRACK_2 = 'raid_track_2';
var RAID_TRACK_3 = 'raid_track_3';
var RAID_TRACK_4 = 'raid_track_4';
var RAID_TRACK_5 = 'raid_track_5';
var RAID_TRACK_6 = 'raid_track_6';
var RAID_TRACK_7 = 'raid_track_7';
var RAID_TRACK_8 = 'raid_track_8';
var VICTORY_POINTS_FRENCH_10 = 'victory_points_french_10';
var VICTORY_POINTS_FRENCH_9 = 'victory_points_french_9';
var VICTORY_POINTS_FRENCH_8 = 'victory_points_french_8';
var VICTORY_POINTS_FRENCH_7 = 'victory_points_french_7';
var VICTORY_POINTS_FRENCH_6 = 'victory_points_french_6';
var VICTORY_POINTS_FRENCH_5 = 'victory_points_french_5';
var VICTORY_POINTS_FRENCH_4 = 'victory_points_french_4';
var VICTORY_POINTS_FRENCH_3 = 'victory_points_french_3';
var VICTORY_POINTS_FRENCH_2 = 'victory_points_french_2';
var VICTORY_POINTS_FRENCH_1 = 'victory_points_french_1';
var VICTORY_POINTS_BRITISH_1 = 'victory_points_british_1';
var VICTORY_POINTS_BRITISH_2 = 'victory_points_british_2';
var VICTORY_POINTS_BRITISH_3 = 'victory_points_british_3';
var VICTORY_POINTS_BRITISH_4 = 'victory_points_british_4';
var VICTORY_POINTS_BRITISH_5 = 'victory_points_british_5';
var VICTORY_POINTS_BRITISH_6 = 'victory_points_british_6';
var VICTORY_POINTS_BRITISH_7 = 'victory_points_british_7';
var VICTORY_POINTS_BRITISH_8 = 'victory_points_british_8';
var VICTORY_POINTS_BRITISH_9 = 'victory_points_british_9';
var VICTORY_POINTS_BRITISH_10 = 'victory_points_british_10';
var BATTLE_TRACK_ATTACKER_MINUS_5 = 'battle_track_attacker_minus_5';
var BATTLE_TRACK_ATTACKER_MINUS_4 = 'battle_track_attacker_minus_4';
var BATTLE_TRACK_ATTACKER_MINUS_3 = 'battle_track_attacker_minus_3';
var BATTLE_TRACK_ATTACKER_MINUS_2 = 'battle_track_attacker_minus_2';
var BATTLE_TRACK_ATTACKER_MINUS_1 = 'battle_track_attacker_minus_1';
var BATTLE_TRACK_ATTACKER_PLUS_0 = 'battle_track_attacker_plus_0';
var BATTLE_TRACK_ATTACKER_PLUS_1 = 'battle_track_attacker_plus_1';
var BATTLE_TRACK_ATTACKER_PLUS_2 = 'battle_track_attacker_plus_2';
var BATTLE_TRACK_ATTACKER_PLUS_3 = 'battle_track_attacker_plus_3';
var BATTLE_TRACK_ATTACKER_PLUS_4 = 'battle_track_attacker_plus_4';
var BATTLE_TRACK_ATTACKER_PLUS_5 = 'battle_track_attacker_plus_5';
var BATTLE_TRACK_ATTACKER_PLUS_6 = 'battle_track_attacker_plus_6';
var BATTLE_TRACK_ATTACKER_PLUS_7 = 'battle_track_attacker_plus_7';
var BATTLE_TRACK_ATTACKER_PLUS_8 = 'battle_track_attacker_plus_8';
var BATTLE_TRACK_ATTACKER_PLUS_9 = 'battle_track_attacker_plus_9';
var BATTLE_TRACK_ATTACKER_PLUS_10 = 'battle_track_attacker_plus_10';
var BATTLE_TRACK_DEFENDER_MINUS_5 = 'battle_track_defender_minus_5';
var BATTLE_TRACK_DEFENDER_MINUS_4 = 'battle_track_defender_minus_4';
var BATTLE_TRACK_DEFENDER_MINUS_3 = 'battle_track_defender_minus_3';
var BATTLE_TRACK_DEFENDER_MINUS_2 = 'battle_track_defender_minus_2';
var BATTLE_TRACK_DEFENDER_MINUS_1 = 'battle_track_defender_minus_1';
var BATTLE_TRACK_DEFENDER_PLUS_0 = 'battle_track_defender_plus_0';
var BATTLE_TRACK_DEFENDER_PLUS_1 = 'battle_track_defender_plus_1';
var BATTLE_TRACK_DEFENDER_PLUS_2 = 'battle_track_defender_plus_2';
var BATTLE_TRACK_DEFENDER_PLUS_3 = 'battle_track_defender_plus_3';
var BATTLE_TRACK_DEFENDER_PLUS_4 = 'battle_track_defender_plus_4';
var BATTLE_TRACK_DEFENDER_PLUS_5 = 'battle_track_defender_plus_5';
var BATTLE_TRACK_DEFENDER_PLUS_6 = 'battle_track_defender_plus_6';
var BATTLE_TRACK_DEFENDER_PLUS_7 = 'battle_track_defender_plus_7';
var BATTLE_TRACK_DEFENDER_PLUS_8 = 'battle_track_defender_plus_8';
var BATTLE_TRACK_DEFENDER_PLUS_9 = 'battle_track_defender_plus_9';
var BATTLE_TRACK_DEFENDER_PLUS_10 = 'battle_track_defender_plus_10';
var BATTLE_MARKERS_POOL = 'battle_markers_pool';
var COMMANDER_REROLLS_TRACK_ATTACKER_0 = 'commander_rerolls_track_attacker_0';
var COMMANDER_REROLLS_TRACK_ATTACKER_1 = 'commander_rerolls_track_attacker_1';
var COMMANDER_REROLLS_TRACK_ATTACKER_2 = 'commander_rerolls_track_attacker_2';
var COMMANDER_REROLLS_TRACK_ATTACKER_3 = 'commander_rerolls_track_attacker_3';
var COMMANDER_REROLLS_TRACK_DEFENDER_0 = 'commander_rerolls_track_defender_0';
var COMMANDER_REROLLS_TRACK_DEFENDER_1 = 'commander_rerolls_track_defender_1';
var COMMANDER_REROLLS_TRACK_DEFENDER_2 = 'commander_rerolls_track_defender_2';
var COMMANDER_REROLLS_TRACK_DEFENDER_3 = 'commander_rerolls_track_defender_3';
var OPEN_SEAS_MARKER_SAIL_BOX = 'openSeasMarkerSailBox';
var CHEROKEE_CONTROL = 'cherokeeControl';
var IROQUOIS_CONTROL = 'iroquoisControl';
var CHEROKEE = 'Cherokee';
var IROQUOIS = 'Iroquois';
var LOSSES_BOX_BRITISH = 'lossesBox_british';
var LOSSES_BOX_FRENCH = 'lossesBox_french';
var MARKERS = 'markers';
var UNITS = 'units';
var VOW_FRENCH_NAVY_LOSSES_PUT_BACK = 'VOWFrenchNavyLossedPutBack';
var VOW_FEWER_TROOPS_FRENCH = 'VOWFewerTroopsFrench';
var VOW_FEWER_TROOPS_PUT_BACK_FRENCH = 'VOWFewerTroopsPutBackFrench';
var VOW_PICK_ONE_ARTILLERY_FRENCH = 'VOWPickOneArtilleryFrench';
var VOW_FEWER_TROOPS_BRITISH = 'VOWFewerTroopsBritish';
var VOW_FEWER_TROOPS_PUT_BACK_BRITISH = 'VOWFewerTroopsPutBackBritish';
var VOW_PICK_TWO_ARTILLERY_BRITISH = 'VOWPickTwoArtilleryBritish';
var VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH = 'VOWPickTwoArtilleryOrLightBritish';
var VOW_PICK_ONE_COLONIAL_LIGHT = 'VOWPickOneColonialLight';
var VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK = 'VOWPickOneColonialLightPutBack';
var VOW_FEWER_TROOPS_COLONIAL = 'VOWFewerTroopsColonial';
var VOW_FEWER_TROOPS_PUT_BACK_COLONIAL = 'VOWFewerTroopsPutBackColonial';
var VOW_PENNSYLVANIA_MUSTERS = 'VOWPennsylvaniaMusters';
var VOW_PITT_SUBSIDIES = 'VOWPittSubsidies';
var ACTION_ROUND_INDIAN_ACTIONS = 'ACTION_ROUND_INDIAN_ACTIONS';
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
function sleep(ms) {
    return new Promise(function (r) { return setTimeout(r, ms); });
}
var BayonetsAndTomahawks = (function () {
    function BayonetsAndTomahawks() {
        this.tooltipsToMap = [];
        this._helpMode = false;
        this._last_notif = null;
        this._last_tooltip_id = 0;
        this._notif_uid_to_log_id = {};
        this._notif_uid_to_mobile_log_id = {};
        this._selectableNodes = [];
        console.log('bayonetsandtomahawks constructor');
    }
    BayonetsAndTomahawks.prototype.setup = function (gamedatas) {
        dojo.place("<div id='customActions' style='display:inline-block'></div>", $('generalactions'), 'after');
        this.setAlwaysFixTopActions();
        this.setupDontPreloadImages();
        this.gamedatas = gamedatas;
        debug('gamedatas', gamedatas);
        this.setupPlayerOrder({ playerOrder: gamedatas.playerOrder });
        this._connections = [];
        this.activeStates = {
            actionActivateStack: new ActionActivateStackState(this),
            actionRoundActionPhase: new ActionRoundActionPhaseState(this),
            actionRoundChooseCard: new ActionRoundChooseCardState(this),
            actionRoundChooseFirstPlayer: new ActionRoundChooseFirstPlayerState(this),
            actionRoundChooseReaction: new ActionRoundChooseReactionState(this),
            actionRoundSailBoxLanding: new ActionRoundSailBoxLandingState(this),
            armyMovement: new ArmyMovementState(this),
            armyMovementDestination: new ArmyMovementDestinationState(this),
            battleApplyHits: new BattleApplyHitsState(this),
            battleRetreat: new BattleRetreatState(this),
            battleRollsRerolls: new BattleRollsRerollsState(this),
            battleSelectCommander: new BattleSelectCommanderState(this),
            colonialsEnlistUnitPlacement: new ColonialsEnlistUnitPlacementState(this),
            confirmPartialTurn: new ConfirmPartialTurnState(this),
            confirmTurn: new ConfirmTurnState(this),
            eventDelayedSuppliesFromFrance: new EventDelayedSuppliesFromFranceState(this),
            eventDiseaseInBritishCamp: new EventDiseaseInBritishCampState(this),
            eventDiseaseInFrenchCamp: new EventDiseaseInFrenchCampState(this),
            eventPennsylvaniasPeacePromises: new EventPennsylvaniasPeacePromisesState(this),
            eventRoundUpMenAndEquipment: new EventRoundUpMenAndEquipmentState(this),
            eventSmallpoxInfectedBlankets: new EventSmallpoxInfectedBlanketsState(this),
            vagariesOfWarPickUnits: new VagariesOfWarPickUnitsState(this),
            fleetsArriveUnitPlacement: new FleetsArriveUnitPlacementState(this),
            lightMovement: new LightMovementState(this),
            lightMovementDestination: new LightMovementDestinationState(this),
            raid: new RaidState(this),
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
        this.tokenManager = new TokenManager(this);
        this.discard = new VoidStock(this.cardManager, document.getElementById('bt_discard'));
        this.deck = new LineStock(this.cardManager, document.getElementById('bt_deck'));
        this.gameMap = new GameMap(this);
        this.pools = new Pools(this);
        this.tooltipManager = new TooltipManager(this);
        this.cardsInPlay = new CardsInPlay(this);
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
        debug('Ending game setup');
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
        console.log('Entering state: ' + stateName, args);
        if (this.framework().isCurrentPlayerActive() &&
            this.activeStates[stateName]) {
            this.activeStates[stateName].onEnteringState(args.args);
        }
        else if (this.activeStates[stateName]) {
            this.activeStates[stateName].setDescription(Number(args.active_player), args.args);
        }
        if (args.args && args.args.previousSteps) {
            args.args.previousSteps.forEach(function (stepId) {
                var logEntry = $('logs').querySelector(".log.notif_newUndoableStep[data-step=\"".concat(stepId, "\"]"));
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
    BayonetsAndTomahawks.prototype.getUnitData = function (_a) {
        var counterId = _a.counterId;
        return this.gamedatas.staticData.units[counterId];
    };
    BayonetsAndTomahawks.prototype.addActionButtonClient = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses, _b = _a.color, color = _b === void 0 ? 'none' : _b;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, 'customActions', false, color);
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addCancelButton = function (_a) {
        var _this = this;
        var _b = _a === void 0 ? {} : _a, callback = _b.callback;
        this.addDangerActionButton({
            id: 'cancel_btn',
            text: _('Cancel'),
            callback: function () {
                if (callback) {
                    callback();
                }
                _this.onCancel();
            },
        });
    };
    BayonetsAndTomahawks.prototype.addConfirmButton = function (_a) {
        var callback = _a.callback;
        this.addPrimaryActionButton({
            id: 'confirm_btn',
            text: _('Confirm'),
            callback: callback,
        });
    };
    BayonetsAndTomahawks.prototype.addPassButton = function (_a) {
        var _this = this;
        var optionalAction = _a.optionalAction, text = _a.text;
        if (optionalAction) {
            this.addSecondaryActionButton({
                id: 'pass_btn',
                text: text ? _(text) : _('Pass'),
                callback: function () {
                    return _this.takeAction({
                        action: 'actPassOptionalAction',
                        atomicAction: false,
                    });
                },
            });
        }
    };
    BayonetsAndTomahawks.prototype.addPlayerButton = function (_a) {
        var player = _a.player, callback = _a.callback;
        var id = "select_".concat(player.id);
        this.addPrimaryActionButton({
            id: id,
            text: player.name,
            callback: callback,
        });
        var node = document.getElementById(id);
        node.style.backgroundColor = "#".concat(player.color);
    };
    BayonetsAndTomahawks.prototype.addPrimaryActionButton = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, 'customActions', false, 'blue');
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addSecondaryActionButton = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, 'customActions', false, 'gray');
        if (extraClasses) {
            dojo.addClass(id, extraClasses);
        }
    };
    BayonetsAndTomahawks.prototype.addDangerActionButton = function (_a) {
        var id = _a.id, text = _a.text, callback = _a.callback, extraClasses = _a.extraClasses;
        if ($(id)) {
            return;
        }
        this.framework().addActionButton(id, text, callback, 'customActions', false, 'red');
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
                id: 'undo_last_step_btn',
                text: _('Undo last step'),
                callback: function () {
                    return _this.takeAction({
                        action: 'actUndoToStep',
                        args: {
                            stepId: lastStep,
                        },
                        checkAction: 'actRestart',
                        atomicAction: false,
                    });
                },
            });
        }
        if (previousEngineChoices > 0) {
            this.addDangerActionButton({
                id: 'restart_btn',
                text: _('Restart turn'),
                callback: function () {
                    return _this.takeAction({ action: 'actRestart', atomicAction: false });
                },
            });
        }
    };
    BayonetsAndTomahawks.prototype.clearInterface = function () {
        console.log('clear interface');
        this.playerManager.clearInterface();
        this.gameMap.clearInterface();
    };
    BayonetsAndTomahawks.prototype.clearPossible = function () {
        this.framework().removeActionButtons();
        dojo.empty('customActions');
        dojo.forEach(this._connections, dojo.disconnect);
        this._connections = [];
        this._selectableNodes.forEach(function (node) {
            if ($(node))
                dojo.removeClass(node, 'selectable selected');
        });
        this._selectableNodes = [];
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
    BayonetsAndTomahawks.prototype.openUnitStack = function (unit) {
        var unitStack = this.gameMap.stacks[unit.location][unit.faction];
        unitStack.open();
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
        this._connections.push(dojo.connect(node, 'onclick', this, function (event) {
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
    BayonetsAndTomahawks.prototype.setLocationSelectable = function (_a) {
        var id = _a.id, callback = _a.callback;
        var node = $(id);
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTABLE);
        this._connections.push(dojo.connect(node, 'onclick', this, function (event) {
            return callback(event);
        }));
    };
    BayonetsAndTomahawks.prototype.setLocationSelected = function (_a) {
        var id = _a.id;
        var node = $(id);
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTED);
    };
    BayonetsAndTomahawks.prototype.setUnitSelectable = function (_a) {
        var id = _a.id, callback = _a.callback;
        var node = $(id);
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTABLE);
        this._connections.push(dojo.connect(node, 'onclick', this, function (event) {
            event.stopPropagation();
            callback(event);
        }));
    };
    BayonetsAndTomahawks.prototype.setStackSelected = function (_a) {
        var spaceId = _a.spaceId, faction = _a.faction;
        var node = $("".concat(spaceId, "_").concat(faction, "_stack"));
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTED);
    };
    BayonetsAndTomahawks.prototype.setStackSelectable = function (_a) {
        var id = _a.id, callback = _a.callback;
        var node = $(id);
        if (node === null) {
            return;
        }
        node.classList.add(BT_SELECTABLE);
        this._connections.push(dojo.connect(node, 'onclick', this, function (event) {
            return callback(event);
        }));
    };
    BayonetsAndTomahawks.prototype.setUnitSelected = function (_a) {
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
            this.connect($(node), 'click', safeCallback);
            dojo.removeClass(node, 'unselectable');
            dojo.addClass(node, 'selectable');
            this._selectableNodes.push(node);
        }
        else {
            dojo.connect($(node), 'click', safeCallback);
        }
    };
    BayonetsAndTomahawks.prototype.undoToStep = function (_a) {
        var stepId = _a.stepId;
        this.takeAction({
            action: 'actUndoToStep',
            args: {
                stepId: stepId,
            },
            checkAction: 'actRestart',
        });
    };
    BayonetsAndTomahawks.prototype.updateLayout = function () {
        if (!this.settings) {
            return;
        }
        $('play_area_container').setAttribute('data-two-columns', this.settings.get({ id: 'twoColumnsLayout' }));
        var ROOT = document.documentElement;
        var WIDTH = $('play_area_container').getBoundingClientRect()['width'] - 8;
        var LEFT_COLUMN = 1500;
        var RIGHT_COLUMN = 1500;
        if (this.settings.get({ id: 'twoColumnsLayout' }) === PREF_ENABLED) {
            WIDTH = WIDTH - 8;
            var size = Number(this.settings.get({ id: 'columnSizes' }));
            var proportions = [size, 100 - size];
            var LEFT_SIZE = (proportions[0] * WIDTH) / 100;
            var leftColumnScale = LEFT_SIZE / LEFT_COLUMN;
            ROOT.style.setProperty('--leftColumnScale', "".concat(leftColumnScale));
            ROOT.style.setProperty('--mapSizeMultiplier', '1');
            var RIGHT_SIZE = (proportions[1] * WIDTH) / 100;
            var rightColumnScale = RIGHT_SIZE / RIGHT_COLUMN;
            ROOT.style.setProperty('--rightColumnScale', "".concat(rightColumnScale));
            $('play_area_container').style.gridTemplateColumns = "".concat(LEFT_SIZE, "px ").concat(RIGHT_SIZE, "px");
        }
        else {
            var LEFT_SIZE = WIDTH;
            var leftColumnScale = LEFT_SIZE / LEFT_COLUMN;
            ROOT.style.setProperty('--leftColumnScale', "".concat(leftColumnScale));
            ROOT.style.setProperty('--mapSizeMultiplier', "".concat(Number(this.settings.get({ id: PREF_SINGLE_COLUMN_MAP_SIZE })) / 100));
            var RIGHT_SIZE = WIDTH;
            var rightColumnScale = RIGHT_SIZE / RIGHT_COLUMN;
            ROOT.style.setProperty('--rightColumnScale', "".concat(rightColumnScale));
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
                    if (key.startsWith('tkn_')) {
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
            console.error(log, args, 'Exception thrown', e.stack);
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
                if ($('log_' + logId))
                    dojo.addClass('log_' + logId, 'cancel');
            }
            if (_this._notif_uid_to_mobile_log_id.hasOwnProperty(uid)) {
                var mobileLogId = _this._notif_uid_to_mobile_log_id[uid];
                if ($('dockedlog_' + mobileLogId))
                    dojo.addClass('dockedlog_' + mobileLogId, 'cancel');
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
        if (type == 'history_history') {
            type = notif.msg.args.originalType;
        }
        if ($('log_' + notif.logId)) {
            dojo.addClass('log_' + notif.logId, 'notif_' + type);
            var methodName = 'onAdding' + type.charAt(0).toUpperCase() + type.slice(1) + 'ToLog';
            (_a = this[methodName]) === null || _a === void 0 ? void 0 : _a.call(this, notif);
        }
        if ($('dockedlog_' + notif.mobileLogId)) {
            dojo.addClass('dockedlog_' + notif.mobileLogId, 'notif_' + type);
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
        var container = document.getElementById('player_boards');
        var infoPanel = document.getElementById('info_panel');
        if (!container) {
            return;
        }
        container.insertAdjacentElement('afterbegin', infoPanel);
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
            var afterTitleElem = document.getElementById('after-page-title');
            var titleElem = document.getElementById('page-title');
            var zoom = getComputedStyle(titleElem).zoom;
            if (!zoom) {
                zoom = 1;
            }
            var titleRect = afterTitleElem.getBoundingClientRect();
            if (titleRect.top < 0 &&
                titleElem.offsetHeight <
                    (window.innerHeight * this.alwaysFixTopActionsMaximum) / 100) {
                var afterTitleRect = afterTitleElem.getBoundingClientRect();
                titleElem.classList.add('fixed-page-title');
                titleElem.style.width = (afterTitleRect.width - 10) / zoom + 'px';
                afterTitleElem.style.height = titleRect.height + 'px';
            }
            else {
                titleElem.classList.remove('fixed-page-title');
                titleElem.style.width = 'auto';
                afterTitleElem.style.height = '0px';
            }
        }
    };
    BayonetsAndTomahawks.prototype.actionError = function (actionName) {
        this.framework().showMessage("cannot take ".concat(actionName, " action"), 'error');
    };
    BayonetsAndTomahawks.prototype.takeAction = function (_a) {
        var action = _a.action, _b = _a.atomicAction, atomicAction = _b === void 0 ? true : _b, _c = _a.args, args = _c === void 0 ? {} : _c, checkAction = _a.checkAction;
        var actionName = atomicAction ? action : undefined;
        console.log('action error', checkAction || action);
        if (!this.framework().checkAction(checkAction || action)) {
            this.actionError(action);
            return;
        }
        this.framework().bgaPerformAction(atomicAction ? 'actTakeAtomicAction' : action, { args: JSON.stringify(args), actionName: actionName }, { lock: true, checkAction: false });
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
        div.classList.add('bt_card_container');
    };
    BTCardManager.prototype.setupFrontDiv = function (card, div) {
        div.classList.add("bt_card");
        div.setAttribute("data-card-id", card.id);
        div.style.width = "calc(var(--btCardScale) * 250px)";
        div.style.height = "calc(var(--btCardScale) * 179px)";
    };
    BTCardManager.prototype.setupBackDiv = function (card, div) {
        div.classList.add("bt_card");
        div.setAttribute("data-card-id", "".concat(card.faction, "_back"));
        div.style.width = "calc(var(--btCardScale) * 250px)";
        div.style.height = "calc(var(--btCardScale) * 179px)";
    };
    BTCardManager.prototype.isCardVisible = function (card) {
        if (card.location.startsWith("hand_") || card.location.startsWith("cardInPlay_") || card.location.startsWith("selected_")) {
            return true;
        }
        return false;
    };
    return BTCardManager;
}(CardManager));
var CardsInPlay = (function () {
    function CardsInPlay(game) {
        this.game = game;
        this.setupCardsInPlay({ gamedatas: game.gamedatas });
    }
    CardsInPlay.prototype.clearInterface = function () { };
    CardsInPlay.prototype.updateCardsInPlay = function (_a) {
        var _this = this;
        var gamedatas = _a.gamedatas;
        FACTIONS.forEach(function (faction) {
            if (!gamedatas.cardsInPlay[faction]) {
                return;
            }
            _this.addCard({ faction: faction, card: gamedatas.cardsInPlay[faction] });
        });
    };
    CardsInPlay.prototype.setupCardsInPlay = function (_a) {
        var _b;
        var gamedatas = _a.gamedatas;
        var node = $("bt_right_column");
        node.insertAdjacentHTML("afterbegin", tplCardsInPlay());
        this.cards = (_b = {},
            _b[BRITISH] = new LineStock(this.game.cardManager, document.getElementById("british_card_in_play"), { direction: "column", center: false }),
            _b[FRENCH] = new LineStock(this.game.cardManager, document.getElementById("french_card_in_play"), { direction: "column", center: false }),
            _b[INDIAN] = new LineStock(this.game.cardManager, document.getElementById("indian_card_in_play"), { direction: "column", center: false }),
            _b);
        this.updateCardsInPlay({ gamedatas: gamedatas });
    };
    CardsInPlay.prototype.addCard = function (_a) {
        var card = _a.card, faction = _a.faction;
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0: return [4, this.cards[faction].addCard(card)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    CardsInPlay.prototype.removeCard = function (_a) {
        var card = _a.card, faction = _a.faction;
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0: return [4, this.cards[faction].removeCard(card)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    CardsInPlay.prototype.getCards = function (_a) {
        var faction = _a.faction;
        return this.cards[faction].getCards();
    };
    CardsInPlay.prototype.getStock = function (_a) {
        var faction = _a.faction;
        return this.cards[faction];
    };
    return CardsInPlay;
}());
var tplCardsInPlay = function () {
    return "<div id=\"bt_cards_in_play\">\n            <span>Cards in play</span>\n            <div class=\"bt_cards_in_play_container\">\n              <div id=\"british_card_in_play\" class=\"bt_card_in_play\">\n                <div class=\"bt_card_in_play_border\"></div>\n              </div>\n              <div id=\"french_card_in_play\" class=\"bt_card_in_play\">\n                <div class=\"bt_card_in_play_border\"></div>\n              </div>\n              <div id=\"indian_card_in_play\" class=\"bt_card_in_play\">\n                <div class=\"bt_card_in_play_border\"></div>\n              </div>\n            </div>\n          </div\n  ";
};
var isDebug = window.location.host == 'studio.boardgamearena.com' ||
    window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () { };
var capitalizeFirstLetter = function (string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
};
var createUnitsLog = function (units) {
    var unitsLog = '';
    var unitsLogArgs = {};
    units.forEach(function (unit, index) {
        var key = "tkn_unit_".concat(index);
        unitsLog += '${' + key + '}';
        unitsLogArgs[key] = "".concat(unit.counterId, ":").concat(unit.reduced ? 'reduced' : 'full');
    });
    return {
        log: unitsLog,
        args: unitsLogArgs,
    };
};
var YEAR_TRACK_CONFIG = [
    {
        id: 1755,
        top: 579,
        left: 1340,
    },
    {
        id: 1756,
        top: 579,
        left: 1385,
    },
    {
        id: 1757,
        top: 579,
        left: 1431,
    },
    {
        id: 1758,
        top: 630,
        left: 1340,
    },
    {
        id: 1759,
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
var RAID_TRACK_CONFIG = [
    {
        id: RAID_TRACK_0,
        top: 98,
        left: 25,
    },
    {
        id: RAID_TRACK_1,
        top: 143.5,
        left: 25,
    },
    {
        id: RAID_TRACK_2,
        top: 189,
        left: 25,
    },
    {
        id: RAID_TRACK_3,
        top: 234.5,
        left: 25,
    },
    {
        id: RAID_TRACK_4,
        top: 279.5,
        left: 25,
    },
    {
        id: RAID_TRACK_5,
        top: 325,
        left: 25,
    },
    {
        id: RAID_TRACK_6,
        top: 370.5,
        left: 25,
    },
    {
        id: RAID_TRACK_7,
        top: 416,
        left: 25,
    },
    {
        id: RAID_TRACK_8,
        top: 461.5,
        left: 25,
    },
];
var VICTORY_POINTS_TRACK_CONFIG = [
    {
        id: VICTORY_POINTS_FRENCH_10,
        top: 24,
        left: 159.5,
    },
    {
        id: VICTORY_POINTS_FRENCH_9,
        top: 24,
        left: 205.5,
    },
    {
        id: VICTORY_POINTS_FRENCH_8,
        top: 24,
        left: 250.5,
    },
    {
        id: VICTORY_POINTS_FRENCH_7,
        top: 24,
        left: 296,
    },
    {
        id: VICTORY_POINTS_FRENCH_6,
        top: 24,
        left: 341.5,
    },
    {
        id: VICTORY_POINTS_FRENCH_5,
        top: 24,
        left: 387,
    },
    {
        id: VICTORY_POINTS_FRENCH_4,
        top: 24,
        left: 432,
    },
    {
        id: VICTORY_POINTS_FRENCH_3,
        top: 24,
        left: 477.5,
    },
    {
        id: VICTORY_POINTS_FRENCH_2,
        top: 24,
        left: 522.5,
    },
    {
        id: VICTORY_POINTS_FRENCH_1,
        top: 24,
        left: 568.5,
    },
    {
        id: VICTORY_POINTS_BRITISH_1,
        top: 24,
        left: 614,
    },
    {
        id: VICTORY_POINTS_BRITISH_2,
        top: 24,
        left: 659.5,
    },
    {
        id: VICTORY_POINTS_BRITISH_3,
        top: 24,
        left: 705,
    },
    {
        id: VICTORY_POINTS_BRITISH_4,
        top: 24,
        left: 750.5,
    },
    {
        id: VICTORY_POINTS_BRITISH_5,
        top: 24,
        left: 796,
    },
    {
        id: VICTORY_POINTS_BRITISH_6,
        top: 24,
        left: 841.5,
    },
    {
        id: VICTORY_POINTS_BRITISH_7,
        top: 24,
        left: 887,
    },
    {
        id: VICTORY_POINTS_BRITISH_8,
        top: 24,
        left: 932.5,
    },
    {
        id: VICTORY_POINTS_BRITISH_9,
        top: 24,
        left: 977.5,
    },
    {
        id: VICTORY_POINTS_BRITISH_10,
        top: 24,
        left: 1023,
    },
];
var BATTLE_TRACK_CONFIG = [
    {
        id: BATTLE_TRACK_ATTACKER_MINUS_5,
        top: 94,
        left: 111,
    },
    {
        id: BATTLE_TRACK_ATTACKER_MINUS_4,
        top: 94,
        left: 153,
    },
    {
        id: BATTLE_TRACK_ATTACKER_MINUS_3,
        top: 94,
        left: 196,
    },
    {
        id: BATTLE_TRACK_ATTACKER_MINUS_2,
        top: 94,
        left: 240,
    },
    {
        id: BATTLE_TRACK_ATTACKER_MINUS_1,
        top: 94,
        left: 282,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_0,
        top: 94,
        left: 325,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_1,
        top: 94,
        left: 372,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_2,
        top: 94,
        left: 415,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_3,
        top: 94,
        left: 458,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_4,
        top: 94,
        left: 501,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_5,
        top: 94,
        left: 544,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_6,
        top: 94,
        left: 587,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_7,
        top: 94,
        left: 630,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_8,
        top: 94,
        left: 673,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_9,
        top: 94,
        left: 715,
    },
    {
        id: BATTLE_TRACK_ATTACKER_PLUS_10,
        top: 94,
        left: 759,
    },
    {
        id: BATTLE_TRACK_DEFENDER_MINUS_5,
        top: 138,
        left: 111,
    },
    {
        id: BATTLE_TRACK_DEFENDER_MINUS_4,
        top: 138,
        left: 153,
    },
    {
        id: BATTLE_TRACK_DEFENDER_MINUS_3,
        top: 138,
        left: 196,
    },
    {
        id: BATTLE_TRACK_DEFENDER_MINUS_2,
        top: 138,
        left: 240,
    },
    {
        id: BATTLE_TRACK_DEFENDER_MINUS_1,
        top: 138,
        left: 282,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_0,
        top: 138,
        left: 325,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_1,
        top: 138,
        left: 372,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_2,
        top: 138,
        left: 415,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_3,
        top: 138,
        left: 458,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_4,
        top: 138,
        left: 501,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_5,
        top: 138,
        left: 544,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_6,
        top: 138,
        left: 587,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_7,
        top: 138,
        left: 630,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_8,
        top: 138,
        left: 673,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_9,
        top: 138,
        left: 715,
    },
    {
        id: BATTLE_TRACK_DEFENDER_PLUS_10,
        top: 138,
        left: 759,
    },
];
var COMMANDER_REROLLS_TRACK_CONFIG = [
    {
        id: COMMANDER_REROLLS_TRACK_ATTACKER_0,
        top: 94,
        left: 816,
    },
    {
        id: COMMANDER_REROLLS_TRACK_ATTACKER_1,
        top: 94,
        left: 859,
    },
    {
        id: COMMANDER_REROLLS_TRACK_ATTACKER_2,
        top: 94,
        left: 902,
    },
    {
        id: COMMANDER_REROLLS_TRACK_ATTACKER_3,
        top: 94,
        left: 945,
    },
    {
        id: COMMANDER_REROLLS_TRACK_DEFENDER_0,
        top: 138,
        left: 816,
    },
    {
        id: COMMANDER_REROLLS_TRACK_DEFENDER_1,
        top: 138,
        left: 859,
    },
    {
        id: COMMANDER_REROLLS_TRACK_DEFENDER_2,
        top: 138,
        left: 902,
    },
    {
        id: COMMANDER_REROLLS_TRACK_DEFENDER_3,
        top: 138,
        left: 945,
    },
];
var GameMap = (function () {
    function GameMap(game) {
        this.stacks = {};
        this.yearTrack = {};
        this.actionRoundTrack = {};
        this.victoryPointsTrack = {};
        this.battleTrack = {};
        this.raidTrack = {};
        this.commanderRerollsTrack = {};
        this.game = game;
        var gamedatas = game.gamedatas;
        this.setupGameMap({ gamedatas: gamedatas });
    }
    GameMap.prototype.clearInterface = function () {
        var _this = this;
        this.losses.lossesBox_british.removeAll();
        this.losses.lossesBox_french.removeAll();
        Object.keys(this.stacks).forEach(function (spaceId) {
            _this.stacks[spaceId][BRITISH].removeAll();
            _this.stacks[spaceId][FRENCH].removeAll();
            var element = document.getElementById("".concat(spaceId, "_markers"));
            if (!element) {
                return;
            }
            element.replaceChildren();
        });
        [
            YEAR_MARKER,
            ROUND_MARKER,
            BRITISH_RAID_MARKER,
            FRENCH_RAID_MARKER,
            VICTORY_MARKER,
            OPEN_SEAS_MARKER,
        ].forEach(function (markerId) {
            var node = document.getElementById(markerId);
            if (node) {
                node.remove();
            }
        });
    };
    GameMap.prototype.updateInterface = function (_a) {
        var gamedatas = _a.gamedatas;
        this.setupUnitsAndSpaces({ gamedatas: gamedatas });
        this.setupMarkers({ gamedatas: gamedatas });
    };
    GameMap.prototype.setupUnitsAndSpaces = function (_a) {
        var _b;
        var _this = this;
        var gamedatas = _a.gamedatas;
        if (!this.losses) {
            this.losses = (_b = {},
                _b[LOSSES_BOX_BRITISH] = new LineStock(this.game.tokenManager, document.getElementById(LOSSES_BOX_BRITISH), {
                    center: false,
                }),
                _b[LOSSES_BOX_FRENCH] = new LineStock(this.game.tokenManager, document.getElementById(LOSSES_BOX_FRENCH), {
                    center: false,
                }),
                _b);
        }
        [LOSSES_BOX_BRITISH, LOSSES_BOX_FRENCH].forEach(function (box) {
            var units = gamedatas.units.filter(function (unit) { return unit.location === box; });
            _this.losses[box].addCards(units);
        });
        gamedatas.spaces.forEach(function (space) {
            var _a;
            if (space.raided) {
                var element = document.getElementById("".concat(space.id, "_markers"));
                if (!element) {
                    return;
                }
                element.insertAdjacentHTML('beforeend', tplMarkerOfType({ type: "".concat(space.raided, "_raided_marker") }));
            }
            if (space.control !== space.homeSpace &&
                (space.control === BRITISH || space.control === FRENCH)) {
                _this.addMarkerToSpace({
                    spaceId: space.id,
                    type: "".concat(space.control, "_control_marker"),
                });
            }
            if (space.battle) {
                _this.addMarkerToSpace({
                    spaceId: space.id,
                    type: 'battle_marker',
                });
            }
            if (!_this.stacks[space.id]) {
                _this.stacks[space.id] = (_a = {},
                    _a[BRITISH] = new UnitStack(_this.game.tokenManager, document.getElementById("".concat(space.id, "_british_stack")), {}, BRITISH),
                    _a[FRENCH] = new UnitStack(_this.game.tokenManager, document.getElementById("".concat(space.id, "_french_stack")), {}, FRENCH),
                    _a);
            }
            gamedatas.units
                .filter(function (unit) { return unit.location === space.id; })
                .forEach(function (unit) {
                if (unit.faction === BRITISH) {
                    _this.stacks[space.id][BRITISH].addUnit(unit);
                }
                else if (unit.faction === FRENCH) {
                    _this.stacks[space.id][FRENCH].addUnit(unit);
                }
                else if (unit.faction === INDIAN) {
                    _this.stacks[space.id][FRENCH].addUnit(unit);
                }
            });
        });
    };
    GameMap.prototype.setupMarkers = function (_a) {
        var _this = this;
        var gamedatas = _a.gamedatas;
        [1755, 1756, 1757, 1758, 1759].forEach(function (year) {
            _this.yearTrack["year_track_".concat(year)] = new LineStock(_this.game.tokenManager, document.getElementById("year_track_".concat(year)));
        });
        for (var i = 1; i <= 9; i++) {
            this.actionRoundTrack["action_round_track_ar".concat(i)] =
                new LineStock(this.game.tokenManager, document.getElementById("action_round_track_ar".concat(i)), {
                    gap: '0px',
                    center: false,
                });
        }
        this.actionRoundTrack.action_round_track_fleetsArrive =
            new LineStock(this.game.tokenManager, document.getElementById('action_round_track_fleetsArrive'));
        this.actionRoundTrack.action_round_track_colonialsEnlist =
            new LineStock(this.game.tokenManager, document.getElementById('action_round_track_colonialsEnlist'));
        this.actionRoundTrack.action_round_track_winterQuarters =
            new LineStock(this.game.tokenManager, document.getElementById('action_round_track_winterQuarters'));
        var _loop_3 = function (j) {
            [BRITISH, FRENCH].forEach(function (faction) {
                _this.victoryPointsTrack["victory_points_".concat(faction, "_").concat(j)] =
                    new LineStock(_this.game.tokenManager, document.getElementById("victory_points_".concat(faction, "_").concat(j)));
            });
        };
        for (var j = 1; j <= 10; j++) {
            _loop_3(j);
        }
        var _loop_4 = function (i) {
            ['attacker', 'defender'].forEach(function (side) {
                var sideId = "battle_track_".concat(side, "_").concat(i < 0 ? 'minus' : 'plus', "_").concat(Math.abs(i));
                _this.battleTrack[sideId] = new LineStock(_this.game.tokenManager, document.getElementById(sideId));
            });
        };
        for (var i = -5; i <= 10; i++) {
            _loop_4(i);
        }
        this.battleTrack[BATTLE_MARKERS_POOL] = new LineStock(this.game.tokenManager, document.getElementById(BATTLE_MARKERS_POOL), {
            gap: '4px',
            center: false,
            wrap: 'nowrap',
        });
        for (var k = 0; k <= 8; k++) {
            this.raidTrack["raid_track_".concat(k)] = new LineStock(this.game.tokenManager, document.getElementById("raid_track_".concat(k)), {
                wrap: 'nowrap',
                gap: '0px',
            });
        }
        var _loop_5 = function (l) {
            ['attacker', 'defender'].forEach(function (side) {
                _this.commanderRerollsTrack["commander_rerolls_track_".concat(side, "_").concat(l)] =
                    new LineStock(_this.game.tokenManager, document.getElementById("commander_rerolls_track_".concat(side, "_").concat(l)), {
                        center: false,
                    });
            });
        };
        for (var l = 0; l <= 3; l++) {
            _loop_5(l);
        }
        this.openSeasMarkerSailBox = new LineStock(this.game.tokenManager, document.getElementById(OPEN_SEAS_MARKER_SAIL_BOX), {
            wrap: 'nowrap',
            gap: '0px',
        });
        this.updateMarkers({ gamedatas: gamedatas });
    };
    GameMap.prototype.updateMarkers = function (_a) {
        var _this = this;
        var gamedatas = _a.gamedatas;
        var markers = gamedatas.markers;
        var yearMarker = markers[YEAR_MARKER];
        if (yearMarker && this.yearTrack[yearMarker.location]) {
            this.yearTrack[yearMarker.location].addCard(yearMarker);
        }
        var roundMarker = markers[ROUND_MARKER];
        if (roundMarker && this.actionRoundTrack[roundMarker.location]) {
            this.actionRoundTrack[roundMarker.location].addCard(roundMarker);
        }
        var britishRaidMarker = markers[BRITISH_RAID_MARKER];
        if (britishRaidMarker && this.raidTrack[britishRaidMarker.location]) {
            this.raidTrack[britishRaidMarker.location].addCard(britishRaidMarker);
        }
        var frenchRaidMarker = markers[FRENCH_RAID_MARKER];
        if (frenchRaidMarker && this.raidTrack[frenchRaidMarker.location]) {
            this.raidTrack[frenchRaidMarker.location].addCard(frenchRaidMarker);
        }
        var bBattleMarker = markers[BRITISH_BATTLE_MARKER];
        if (bBattleMarker && this.battleTrack[bBattleMarker.location]) {
            this.battleTrack[bBattleMarker.location].addCard(bBattleMarker);
        }
        var fBattleMarker = markers[FRENCH_BATTLE_MARKER];
        if (fBattleMarker && this.battleTrack[fBattleMarker.location]) {
            this.battleTrack[fBattleMarker.location].addCard(fBattleMarker);
        }
        if (markers[OPEN_SEAS_MARKER]) {
            this.openSeasMarkerSailBox.addCard(markers[OPEN_SEAS_MARKER]);
        }
        var victoryMarker = markers[VICTORY_MARKER];
        if (victoryMarker && this.victoryPointsTrack[victoryMarker.location]) {
            this.victoryPointsTrack[victoryMarker.location].addCard(victoryMarker);
        }
        Object.entries(markers)
            .filter(function (_a) {
            var id = _a[0], marker = _a[1];
            return id.startsWith('routeMarker') && !marker.location.startsWith('supply');
        })
            .forEach(function (_a) {
            var id = _a[0], marker = _a[1];
            _this.addMarkerToStack(marker);
        });
        gamedatas.units
            .filter(function (unit) {
            return unit.location.startsWith('commander_rerolls_track');
        })
            .forEach(function (commander) {
            _this.commanderRerollsTrack[commander.location].addCard(commander);
        });
        [CHEROKEE, IROQUOIS].forEach(function (indianNation) {
            var control = gamedatas.constrolIndianNations[indianNation];
            if ([BRITISH, FRENCH].includes(control)) {
                _this.addMarkerToSpace({
                    spaceId: indianNation === CHEROKEE ? CHEROKEE_CONTROL : IROQUOIS_CONTROL,
                    type: "".concat(control, "_control_marker"),
                });
            }
        });
    };
    GameMap.prototype.updateGameMap = function (_a) {
        var gamedatas = _a.gamedatas;
    };
    GameMap.prototype.setupGameMap = function (_a) {
        var gamedatas = _a.gamedatas;
        document
            .getElementById('play_area_container')
            .insertAdjacentHTML('afterbegin', tplGameMap({ gamedatas: gamedatas }));
        this.setupUnitsAndSpaces({ gamedatas: gamedatas });
        this.setupMarkers({ gamedatas: gamedatas });
    };
    GameMap.prototype.moveRoundMarker = function (_a) {
        var nextRoundStep = _a.nextRoundStep;
        return __awaiter(this, void 0, void 0, function () {
            var marker, toNode;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        marker = document.getElementById('round_marker');
                        toNode = document.getElementById(nextRoundStep);
                        if (!(marker && toNode)) {
                            console.error('Unable to move round marker');
                            return [2];
                        }
                        return [4, this.game.animationManager.attachWithAnimation(new BgaSlideAnimation({ element: marker }), toNode)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    GameMap.prototype.moveYearMarker = function (_a) {
        var year = _a.year;
        return __awaiter(this, void 0, void 0, function () {
            var marker, toNode;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        marker = document.getElementById('year_marker');
                        toNode = document.getElementById("year_track_".concat(year));
                        if (!(marker && toNode)) {
                            console.error('Unable to move year marker');
                            return [2];
                        }
                        return [4, this.game.animationManager.attachWithAnimation(new BgaSlideAnimation({ element: marker }), toNode)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    GameMap.prototype.addMarkerToStack = function (marker) {
        return __awaiter(this, void 0, void 0, function () {
            var splitLocation;
            return __generator(this, function (_a) {
                splitLocation = marker.location.split('_');
                this.stacks[splitLocation[0]][splitLocation[1]].addCard(marker);
                return [2];
            });
        });
    };
    GameMap.prototype.addMarkerToSpace = function (_a) {
        var spaceId = _a.spaceId, type = _a.type;
        var element = document.getElementById("".concat(spaceId, "_markers"));
        if (!element) {
            return;
        }
        element.insertAdjacentHTML('beforeend', tplMarkerOfType({ id: "".concat(spaceId, "_").concat(type), type: type }));
    };
    GameMap.prototype.removeMarkerFromSpace = function (_a) {
        var spaceId = _a.spaceId, type = _a.type;
        var element = document.getElementById("".concat(spaceId, "_").concat(type));
        if (!element) {
            return;
        }
        element.remove();
    };
    return GameMap;
}());
var tplMarker = function (_a) {
    var id = _a.id;
    return "<div id=\"".concat(id, "\" class=\"bt_marker\"></div>");
};
var tplMarkerSide = function (_a) {
    var id = _a.id;
    return "<div id=\"".concat(id, "\" class=\"bt_marker_side\" data-type=\"").concat(id, "\" data-side=\"front\"></div>");
};
var tplMarkerOfType = function (_a) {
    var id = _a.id, type = _a.type;
    return "<div ".concat(id ? "id=\"".concat(id, "\"") : '', " class=\"bt_marker_side\" data-type=\"").concat(type, "\" data-side=\"front\"></div>");
};
var tplUnit = function (_a) {
    var faction = _a.faction, counterId = _a.counterId, style = _a.style;
    return "\n  <div class=\"bt_token_side\" data-counter-id=\"".concat(counterId, "\"").concat(style ? " style=\"".concat(style, "\"") : '', "></div>\n");
};
var tplSpaces = function (_a) {
    var spaces = _a.spaces;
    var filteredSpaces = spaces.filter(function (space) { return space.top && space.left; });
    var mappedSpaces = filteredSpaces.map(function (space) {
        return "<div id=\"".concat(space.id, "\" class=\"bt_space\" style=\"top: calc(var(--btMapScale) * ").concat(space.top - 26, "px); left: calc(var(--btMapScale) * ").concat(space.left - 26, "px);\">\n        <div id=\"").concat(space.id, "_french_stack\"></div>\n        <div id=\"").concat(space.id, "_markers\"></div>\n        <div id=\"").concat(space.id, "_british_stack\"></div>\n      </div>");
    });
    var result = mappedSpaces.join('');
    return result;
};
var tplMarkerSpace = function (_a) {
    var id = _a.id, top = _a.top, left = _a.left, extraClasses = _a.extraClasses;
    return "<div id=\"".concat(id, "\" class=\"bt_marker_space").concat(extraClasses ? " ".concat(extraClasses) : '', "\" style=\"top: calc(var(--btMapScale) * ").concat(top, "px); left: calc(var(--btMapScale) * ").concat(left, "px);\"></div>");
};
var tplLossesBox = function () {
    return "\n    <div id=\"lossesBox_french\" class=\"bt_losses_box\"></div>\n    <div id=\"lossesBox_british\" class=\"bt_losses_box\"></div>\n  ";
};
var tplActionRoundTrack = function () {
    return ACTION_ROUND_TRACK_CONFIG.map(function (markerSpace) {
        return tplMarkerSpace({
            id: "action_round_track_".concat(markerSpace.id),
            top: markerSpace.top,
            left: markerSpace.left,
        });
    }).join('');
};
var tplRaidTrack = function () {
    return RAID_TRACK_CONFIG.map(function (markerSpace) {
        return tplMarkerSpace({
            id: "".concat(markerSpace.id),
            top: markerSpace.top,
            left: markerSpace.left,
            extraClasses: 'bt_raid_track',
        });
    }).join('');
};
var tplYearTrack = function () {
    return YEAR_TRACK_CONFIG.map(function (markerSpace) {
        return tplMarkerSpace({
            id: "year_track_".concat(markerSpace.id),
            top: markerSpace.top,
            left: markerSpace.left,
        });
    }).join('');
};
var tplVictoryPointsTrack = function () {
    return VICTORY_POINTS_TRACK_CONFIG.map(function (markerSpace) {
        return tplMarkerSpace({
            id: "".concat(markerSpace.id),
            top: markerSpace.top,
            left: markerSpace.left,
        });
    }).join('');
};
var tplBattleTrack = function () {
    return BATTLE_TRACK_CONFIG.map(function (markerSpace) {
        return tplMarkerSpace({
            id: markerSpace.id,
            top: markerSpace.top,
            left: markerSpace.left,
        });
    }).join('');
};
var tplCommanderTrack = function () {
    return COMMANDER_REROLLS_TRACK_CONFIG.map(function (markerSpace) {
        return tplMarkerSpace({
            id: markerSpace.id,
            top: markerSpace.top,
            left: markerSpace.left,
            extraClasses: 'bt_commander_rerolls_track',
        });
    }).join('');
};
var tplBattleMarkersPool = function () { return '<div id="battle_markers_pool"></div>'; };
var tplGameMap = function (_a) {
    var gamedatas = _a.gamedatas;
    var spaces = gamedatas.spaces;
    return "\n  <div id=\"bt_game_map\">\n    ".concat(tplMarkerSpace({
        id: OPEN_SEAS_MARKER_SAIL_BOX,
        top: 77.5,
        left: 1374.5,
    }), "\n    ").concat(tplLossesBox(), "\n    ").concat(tplSpaces({ spaces: spaces }), "\n    ").concat(tplVictoryPointsTrack(), "\n    ").concat(tplBattleTrack(), "\n    ").concat(tplBattleMarkersPool(), "\n    ").concat(tplCommanderTrack(), "\n    ").concat(tplRaidTrack(), "\n    ").concat(tplYearTrack(), "\n    ").concat(tplActionRoundTrack(), "\n    ").concat(tplMarkerSpace({ id: "".concat(CHEROKEE_CONTROL, "_markers"), top: 2120, left: 863.5 }), "\n    ").concat(tplMarkerSpace({ id: "".concat(IROQUOIS_CONTROL, "_markers"), top: 1711.5, left: 585.5 }), "\n  </div>");
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
    Hand.prototype.open = function () {
        var handWrapper = $("floating_hand_wrapper");
        if (handWrapper) {
            handWrapper.dataset.open = "hand";
        }
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
var LOG_TOKEN_CARD = 'card';
var LOG_TOKEN_MARKER = 'marker';
var LOG_TOKEN_UNIT = 'unit';
var LOG_TOKEN_DIE_RESULT = 'dieResult';
var tooltipIdCounter = 0;
var getTokenDiv = function (_a) {
    var _b;
    var key = _a.key, value = _a.value, game = _a.game;
    var splitKey = key.split('_');
    var type = splitKey[1];
    switch (type) {
        case LOG_TOKEN_BOLD_TEXT:
            return tlpLogTokenBoldText({ text: value });
        case LOG_TOKEN_CARD:
            return tplLogTokenCard(value);
        case LOG_TOKEN_MARKER:
            return tplLogTokenMarker(value);
        case LOG_TOKEN_NEW_LINE:
            return '<br>';
        case LOG_TOKEN_DIE_RESULT:
            return tplLogDieResult(value);
        case LOG_TOKEN_UNIT:
            var splitCounterId = value.split(':');
            var counterId = splitCounterId[0];
            var reduced = (splitCounterId === null || splitCounterId === void 0 ? void 0 : splitCounterId[1]) === 'reduced';
            return tplLogTokenUnit(counterId, (_b = game.gamedatas.staticData.units[counterId]) === null || _b === void 0 ? void 0 : _b.type, reduced);
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
var tplLogTokenCard = function (id) {
    return "<div class=\"bt_log_card bt_card\" data-card-id=\"".concat(id, "\"></div>");
};
var tplLogTokenMarker = function (type) {
    return "<div class=\"bt_marker_side\" data-type=\"".concat(type, "\"></div>");
};
var tplLogTokenUnit = function (counterId, type, reduced) {
    return "<div class=\"bt_token_side\" data-counter-id=\"".concat(counterId).concat(reduced ? '_reduced' : '', "\"").concat(type === COMMANDER ? ' data-commander="true"' : '', "></div>");
};
var tplLogDieResult = function (dieResult) {
    return "<div class=\"bt_log_die\" data-die-result=\"".concat(dieResult, "\"></div>");
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
            'log',
            'message',
            'advanceBattleVictoryMarker',
            'battle',
            'battleCleanup',
            'battleReroll',
            'battleReturnCommander',
            'battleStart',
            'battleSelectCommander',
            'commanderDraw',
            'discardCardFromHand',
            'discardCardFromHandPrivate',
            'discardCardInPlay',
            'drawCardPrivate',
            'drawnReinforcements',
            'eliminateUnit',
            'indianNationControl',
            'loseControl',
            'moveRaidPointsMarker',
            'moveRoundMarker',
            'moveStack',
            'moveYearMarker',
            'moveUnit',
            'placeStackMarker',
            'placeUnitInLosses',
            'placeUnits',
            'raidPoints',
            'flipUnit',
            'removeMarkersEndOfActionRound',
            'returnToPool',
            'revealCardsInPlay',
            'scoreVictoryPoints',
            'selectReserveCard',
            'selectReserveCardPrivate',
            'takeControl',
            'vagariesOfWarPickUnits',
        ];
        notifs.forEach(function (notifName) {
            _this.subscriptions.push(dojo.subscribe(notifName, _this, function (notifDetails) {
                debug("notif_".concat(notifName), notifDetails);
                var promise = _this["notif_".concat(notifName)](notifDetails);
                var promises = promise ? [promise] : [];
                var minDuration = 1;
                var msg = _this.game.format_string_recursive(notifDetails.log, notifDetails.args);
                if (msg != '') {
                    $('gameaction_status').innerHTML = msg;
                    $('pagemaintitletext').innerHTML = msg;
                    $('generalactions').innerHTML = '';
                    minDuration = MIN_NOTIFICATION_MS;
                }
                if (_this.game.animationManager.animationsActive()) {
                    Promise.all(__spreadArray(__spreadArray([], promises, true), [sleep(minDuration)], false)).then(function () {
                        return _this.game.framework().notifqueue.onSynchronousNotificationEnd();
                    });
                }
                else {
                    _this.game.framework().notifqueue.setSynchronousDuration(0);
                }
            }));
            _this.game.framework().notifqueue.setSynchronous(notifName, undefined);
            _this.game
                .framework()
                .notifqueue.setIgnoreNotificationCheck('discardCardFromHand', function (notif) {
                return notif.args.playerId == _this.game.getPlayerId();
            });
        });
    };
    NotificationManager.prototype.destroy = function () {
        dojo.forEach(this.subscriptions, dojo.unsubscribe);
    };
    NotificationManager.prototype.getPlayer = function (_a) {
        var playerId = _a.playerId;
        return this.game.playerManager.getPlayer({ playerId: playerId });
    };
    NotificationManager.prototype.notif_log = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                debug('notif_log', notif.args);
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_message = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            return __generator(this, function (_a) {
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_smallRefreshInterface = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var updatedGamedatas;
            return __generator(this, function (_a) {
                updatedGamedatas = __assign(__assign({}, this.game.gamedatas), notif.args);
                this.game.clearInterface();
                this.game.gamedatas = updatedGamedatas;
                this.game.playerManager.updatePlayers({ gamedatas: updatedGamedatas });
                this.game.gameMap.updateInterface({ gamedatas: updatedGamedatas });
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_advanceBattleVictoryMarker = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var marker;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        marker = notif.args.marker;
                        return [4, this.game.gameMap.battleTrack[marker.location].addCard(marker)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_battle = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var space;
            return __generator(this, function (_a) {
                space = notif.args.space;
                this.game.gameMap.addMarkerToSpace({
                    spaceId: space.id,
                    type: 'battle_marker',
                });
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_battleCleanup = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, attackerMarker, defenderMarker, space;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, attackerMarker = _a.attackerMarker, defenderMarker = _a.defenderMarker, space = _a.space;
                        return [4, Promise.all([
                                this.game.gameMap.battleTrack[attackerMarker.location].addCard(attackerMarker),
                                this.game.gameMap.battleTrack[defenderMarker.location].addCard(defenderMarker),
                            ])];
                    case 1:
                        _b.sent();
                        this.game.gameMap.removeMarkerFromSpace({
                            spaceId: space.id,
                            type: 'battle_marker',
                        });
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_battleReroll = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var commander;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        commander = notif.args.commander;
                        if (commander === null) {
                            return [2];
                        }
                        return [4, this.game.gameMap.commanderRerollsTrack[commander.location].addCard(commander)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_battleReturnCommander = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var commander, unitStack;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        commander = notif.args.commander;
                        unitStack = this.game.gameMap.stacks[commander.location][commander.faction];
                        if (!unitStack) return [3, 2];
                        return [4, unitStack.addUnit(commander)];
                    case 1:
                        _a.sent();
                        _a.label = 2;
                    case 2: return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_battleSelectCommander = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var commander;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        commander = notif.args.commander;
                        return [4, this.game.gameMap.commanderRerollsTrack[commander.location].addCard(commander)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_battleStart = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, attackerMarker, defenderMarker;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, attackerMarker = _a.attackerMarker, defenderMarker = _a.defenderMarker;
                        return [4, Promise.all([
                                this.game.gameMap.battleTrack[attackerMarker.location].addCard(attackerMarker),
                                this.game.gameMap.battleTrack[defenderMarker.location].addCard(defenderMarker),
                            ])];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_discardCardFromHand = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, faction, playerId, fakeCard, fromElement;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, faction = _a.faction, playerId = _a.playerId;
                        fakeCard = {
                            id: "bt_tempCardDiscard_".concat(faction),
                            faction: faction,
                            location: DISCARD,
                        };
                        fromElement = document.getElementById("overall_player_board_".concat(playerId));
                        return [4, this.game.discard.addCard(fakeCard, { fromElement: fromElement })];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_commanderDraw = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var commander;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        commander = notif.args.commander;
                        return [4, this.game.pools.stocks[commander.location].addCard(commander)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_discardCardFromHandPrivate = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, card, playerId;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, card = _a.card, playerId = _a.playerId;
                        card.location = 'hand_';
                        return [4, this.game.discard.addCard(card)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_discardCardInPlay = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var card;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        card = notif.args.card;
                        return [4, this.game.discard.addCard(card)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_drawCardPrivate = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var card;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        card = notif.args.card;
                        return [4, this.game.deck.addCard(card)];
                    case 1:
                        _a.sent();
                        return [4, this.game.hand.addCard(card)];
                    case 2:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_drawnReinforcements = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, units, location;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, units = _a.units, location = _a.location;
                        return [4, this.game.pools.stocks[location].addCards(units)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_eliminateUnit = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var unit;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        unit = notif.args.unit;
                        if (!unit.location.startsWith('lossesBox_')) return [3, 2];
                        return [4, this.game.gameMap.losses[unit.location].addCard(unit)];
                    case 1:
                        _a.sent();
                        return [3, 5];
                    case 2:
                        if (!(unit.location === REMOVED_FROM_PLAY)) return [3, 4];
                        return [4, this.game.tokenManager.removeCard(unit)];
                    case 3:
                        _a.sent();
                        return [3, 5];
                    case 4:
                        if (unit.location === POOL_FLEETS) {
                        }
                        _a.label = 5;
                    case 5: return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_indianNationControl = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, indianNation, faction;
            return __generator(this, function (_b) {
                _a = notif.args, indianNation = _a.indianNation, faction = _a.faction;
                this.game.gameMap.addMarkerToSpace({
                    spaceId: indianNation === CHEROKEE ? CHEROKEE_CONTROL : IROQUOIS_CONTROL,
                    type: "".concat(faction, "_control_marker"),
                });
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_loseControl = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, space, faction;
            return __generator(this, function (_b) {
                _a = notif.args, space = _a.space, faction = _a.faction;
                this.game.gameMap.removeMarkerFromSpace({
                    spaceId: space.id,
                    type: "".concat(faction, "_control_marker"),
                });
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_moveRaidPointsMarker = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var marker;
            return __generator(this, function (_a) {
                marker = notif.args.marker;
                this.game.gameMap.raidTrack[marker.location].addCard(marker);
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_moveRoundMarker = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, nextRoundStep, marker;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, nextRoundStep = _a.nextRoundStep, marker = _a.marker;
                        return [4, this.game.gameMap.actionRoundTrack[nextRoundStep].addCard(marker)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_moveStack = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, stack, destination, faction, markers, unitStack;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, stack = _a.stack, destination = _a.destination, faction = _a.faction, markers = _a.markers;
                        unitStack = this.game.gameMap.stacks[destination.id][faction];
                        if (!unitStack) return [3, 2];
                        return [4, Promise.all([
                                unitStack.addUnits(stack),
                                unitStack.addUnits(markers),
                            ])];
                    case 1:
                        _b.sent();
                        _b.label = 2;
                    case 2: return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_moveUnit = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, unit, destination, faction, unitStack, element;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, unit = _a.unit, destination = _a.destination, faction = _a.faction;
                        unitStack = this.game.gameMap.stacks[destination.id][faction];
                        if (!unitStack) return [3, 2];
                        return [4, unitStack.addUnit(unit)];
                    case 1:
                        _b.sent();
                        _b.label = 2;
                    case 2:
                        if (unit.spent === 1) {
                            element = document.getElementById("spent_marker_".concat(unit.id));
                            if (element) {
                                element.setAttribute('data-spent', 'true');
                            }
                        }
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_moveYearMarker = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, location, marker;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, location = _a.location, marker = _a.marker;
                        return [4, this.game.gameMap.yearTrack[location].addCard(marker)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_placeUnits = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, units, spaceId, faction, unitStack;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, units = _a.units, spaceId = _a.spaceId, faction = _a.faction;
                        unitStack = this.game.gameMap.stacks[spaceId][faction];
                        if (!unitStack) {
                            return [2];
                        }
                        return [4, unitStack.addUnits(units)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_placeStackMarker = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var marker;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        marker = notif.args.marker;
                        return [4, this.game.gameMap.addMarkerToStack(marker)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_placeUnitInLosses = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var unit;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        unit = notif.args.unit;
                        return [4, this.game.gameMap.losses[unit.location].addCard(unit)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_raidPoints = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, space, faction, element;
            return __generator(this, function (_b) {
                _a = notif.args, space = _a.space, faction = _a.faction;
                element = document.getElementById("".concat(space.id, "_markers"));
                if (!element) {
                    return [2];
                }
                element.insertAdjacentHTML('beforeend', tplMarkerOfType({ type: "".concat(faction, "_raided_marker") }));
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_flipUnit = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var unit;
            return __generator(this, function (_a) {
                unit = notif.args.unit;
                this.game.tokenManager.updateCardInformations(unit);
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_removeMarkersEndOfActionRound = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var spentUnits;
            return __generator(this, function (_a) {
                spentUnits = notif.args.spentUnits;
                spentUnits.forEach(function (unit) {
                    var element = document.getElementById("spent_marker_".concat(unit.id));
                    if (element) {
                        element.setAttribute('data-spent', 'false');
                    }
                });
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_returnToPool = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var unit;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        unit = notif.args.unit;
                        return [4, this.game.pools.stocks[unit.location].addCard(unit)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_revealCardsInPlay = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var factions, _i, factions_1, faction;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        factions = [BRITISH, FRENCH, INDIAN];
                        _i = 0, factions_1 = factions;
                        _a.label = 1;
                    case 1:
                        if (!(_i < factions_1.length)) return [3, 4];
                        faction = factions_1[_i];
                        return [4, this.game.cardsInPlay.addCard({
                                card: notif.args[faction],
                                faction: faction,
                            })];
                    case 2:
                        _a.sent();
                        _a.label = 3;
                    case 3:
                        _i++;
                        return [3, 1];
                    case 4: return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_scoreVictoryPoints = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, marker, points;
            var _this = this;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, marker = _a.marker, points = _a.points;
                        Object.entries(points).forEach(function (_a) {
                            var _b;
                            var playerId = _a[0], score = _a[1];
                            if ((_b = _this.game.framework().scoreCtrl) === null || _b === void 0 ? void 0 : _b[playerId]) {
                                _this.game.framework().scoreCtrl[playerId].setValue(Number(score));
                            }
                        });
                        return [4, this.game.gameMap.victoryPointsTrack[marker.location].addCard(marker)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_selectReserveCard = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var faction;
            return __generator(this, function (_a) {
                faction = notif.args.faction;
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_selectReserveCardPrivate = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var discardedCard;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        discardedCard = notif.args.discardedCard;
                        return [4, this.game.discard.addCard(discardedCard)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    NotificationManager.prototype.notif_takeControl = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, space, playerId, faction;
            return __generator(this, function (_b) {
                _a = notif.args, space = _a.space, playerId = _a.playerId, faction = _a.faction;
                if (space.control !== space.homeSpace) {
                    this.game.gameMap.addMarkerToSpace({
                        spaceId: space.id,
                        type: "".concat(faction, "_control_marker"),
                    });
                }
                else {
                    this.game.gameMap.removeMarkerFromSpace({
                        spaceId: space.id,
                        type: "".concat(faction, "_control_marker"),
                    });
                }
                return [2];
            });
        });
    };
    NotificationManager.prototype.notif_vagariesOfWarPickUnits = function (notif) {
        return __awaiter(this, void 0, void 0, function () {
            var _a, units, location;
            return __generator(this, function (_b) {
                switch (_b.label) {
                    case 0:
                        _a = notif.args, units = _a.units, location = _a.location;
                        return [4, this.game.pools.stocks[location].addCards(units)];
                    case 1:
                        _b.sent();
                        return [2];
                }
            });
        });
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
        this.playerData = player;
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
        this.stocks = {};
        this.game = game;
        var gamedatas = game.gamedatas;
        this.setupPools({ gamedatas: gamedatas });
    }
    Pools.prototype.setupPoolsStocks = function (_a) {
        var _this = this;
        var gamedatas = _a.gamedatas;
        POOLS.forEach(function (poolId) {
            _this.stocks[poolId] = new LineStock(_this.game.tokenManager, document.getElementById(poolId), { center: false, gap: '2px' });
        });
        this.updatePools({ gamedatas: gamedatas });
    };
    Pools.prototype.updatePools = function (_a) {
        var _this = this;
        var gamedatas = _a.gamedatas;
        POOLS.forEach(function (poolId) {
            var units = gamedatas.units.filter(function (unit) { return unit.location === poolId; });
            if (units.length === 0) {
                return;
            }
            _this.stocks[poolId].addCards(units);
        });
    };
    Pools.prototype.setupPools = function (_a) {
        var gamedatas = _a.gamedatas;
        document
            .getElementById('play_area_container')
            .insertAdjacentHTML('beforeend', tplPoolsContainer());
        this.setupPoolsStocks({ gamedatas: gamedatas });
    };
    Pools.prototype.clearInterface = function () { };
    return Pools;
}());
var tplPoolsContainer = function () {
    return "\n  <div id=\"bt_right_column\">\n  ".concat(tplDrawnReinforcements(), "\n    ").concat(tplPoolFleets(), "\n    ").concat(tplPoolNeutralIndians(), "\n    ").concat(tplPoolBritish(), "\n    ").concat(tplPoolFrench(), "\n  </div>");
};
var tplDrawnReinforcements = function () { return "\n<div id=\"bt_drawn_reinforcements\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('Drawn Reinforcements'), "</span></div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Fleets'), "</span></div>\n    <div id=\"reinforcementsFleets\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('British'), "</span></div>\n    <div id=\"reinforcementsBritish\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('French'), "</span></div>\n    <div id=\"reinforcementsFrench\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Colonial'), "</span></div>\n    <div id=\"reinforcementsColonial\" class=\"bt_unit_pool\"></div>\n  </div>\n</div>\n"); };
var tplPoolFleets = function () { return "\n<div id=\"bt_pool_fleets\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('Fleets'), "</span></div>\n  <div id=\"poolFleets\" class=\"bt_unit_pool\"></div>\n</div>"); };
var tplPoolNeutralIndians = function () { return "\n<div id=\"bt_pool_neutralIndians\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('Neutral Indians'), "</span></div>\n  <div id=\"poolNeutralIndians\" class=\"bt_unit_pool\"></div>\n</div>"); };
var tplPoolBritish = function () { return "\n<div id=\"bt_pool_british\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('British'), "</span></div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Commanders'), "</span></div>\n    <div id=\"poolBritishCommanders\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Light'), "</span></div>\n    <div id=\"poolBritishLight\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Colonial Light'), "</span></div>\n    <div id=\"poolBritishColonialLight\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Artillery'), "</span></div>\n    <div id=\"poolBritishArtillery\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Forts'), "</span></div>\n    <div id=\"poolBritishForts\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Metropolitan Brigades & VoW'), "</span></div>\n    <div id=\"poolBritishMetropolitanVoW\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Colonial Brigades & VoW'), "</span></div>\n    <div id=\"poolBritishColonialVoW\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Colonial VoW Bonus'), "</span></div>\n    <div id=\"poolBritishColonialVoWBonus\" class=\"bt_unit_pool\"></div>\n  </div>\n</div>\n"); };
var tplPoolFrench = function () { return "\n<div id=\"bt_pool_french\" class=\"bt_unit_pool_container\">\n  <div><span>".concat(_('French'), "</span></div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Commanders'), "</span></div>\n    <div id=\"poolFrenchCommanders\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Light'), "</span></div>\n    <div id=\"poolFrenchLight\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Artillery'), "</span></div>\n    <div id=\"poolFrenchArtillery\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Forts'), "</span></div>\n    <div id=\"poolFrenchForts\" class=\"bt_unit_pool\"></div>\n  </div>\n  <div>\n    <div class=\"bt_unit_pool_section_title\"><span>").concat(_('Metropolitan Brigades & VoW'), "</span></div>\n    <div id=\"poolFrenchMetropolitanVoW\" class=\"bt_unit_pool\"></div>\n  </div>\n</div>\n"); };
var tplPool = function (_a) {
    var type = _a.type;
    return "<div id=\"bt_pool_".concat(type, "\" class=\"bt_unit_pool\">\n  </div>");
};
var getSettingsConfig = function () {
    var _a, _b;
    return ({
        layout: {
            id: "layout",
            config: (_a = {
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
                    }
                },
                _a[PREF_SINGLE_COLUMN_MAP_SIZE] = {
                    id: PREF_SINGLE_COLUMN_MAP_SIZE,
                    onChangeInSetup: true,
                    label: _("Map size"),
                    defaultValue: 100,
                    visibleCondition: {
                        id: "twoColumnsLayout",
                        values: [DISABLED],
                    },
                    sliderConfig: {
                        step: 5,
                        padding: 0,
                        range: {
                            min: 30,
                            max: 100,
                        },
                    },
                    type: "slider",
                },
                _a[PREF_CARD_SIZE_IN_LOG] = {
                    id: PREF_CARD_SIZE_IN_LOG,
                    onChangeInSetup: true,
                    label: _("Size of cards in log"),
                    defaultValue: 0,
                    sliderConfig: {
                        step: 5,
                        padding: 0,
                        range: {
                            min: 0,
                            max: 90,
                        },
                    },
                    type: "slider",
                },
                _a),
        },
        gameplay: {
            id: "gameplay",
            config: (_b = {},
                _b[PREF_CONFIRM_END_OF_TURN_AND_PLAYER_SWITCH_ONLY] = {
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
                _b[PREF_SHOW_ANIMATIONS] = {
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
                _b[PREF_ANIMATION_SPEED] = {
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
                _b),
        },
    });
};
var Settings = (function () {
    function Settings(game) {
        this.settings = {};
        this.selectedTab = 'layout';
        this.tabs = [
            {
                id: 'layout',
                name: _('Layout'),
            },
            {
                id: 'gameplay',
                name: _('Gameplay'),
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
        var configPanel = document.getElementById('info_panel');
        if (configPanel) {
            configPanel.insertAdjacentHTML('beforeend', tplSettingsButton());
        }
    };
    Settings.prototype.setupModal = function (_a) {
        var gamedatas = _a.gamedatas;
        this.modal = new Modal("settings_modal", {
            class: 'settings_modal',
            closeIcon: 'fa-times',
            titleTpl: '<h2 id="popin_${id}_title" class="${class}_title">${title}</h2>',
            title: _('Settings'),
            contents: tplSettingsModalContent({
                tabs: this.tabs,
            }),
            closeAction: 'hide',
            verticalAlign: 'flex-start',
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
        dojo.connect($("show_settings"), 'onclick', function () { return _this.open(); });
        this.tabs.forEach(function (_a) {
            var id = _a.id;
            dojo.connect($("settings_modal_tab_".concat(id)), 'onclick', function () {
                return _this.changeTab({ id: id });
            });
        });
    };
    Settings.prototype.setupModalContent = function () {
        var _this = this;
        var config = getSettingsConfig();
        var node = document.getElementById('setting_modal_content');
        if (!node) {
            return;
        }
        Object.entries(config).forEach(function (_a) {
            var tabId = _a[0], tabConfig = _a[1];
            node.insertAdjacentHTML('beforeend', tplSettingsModalTabContent({ id: tabId }));
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
                if (setting.type === 'select') {
                    var visible = !visibleCondition ||
                        (visibleCondition &&
                            visibleCondition.values.includes(_this.settings[visibleCondition.id]));
                    tabContentNode.insertAdjacentHTML('beforeend', tplPlayerPrefenceSelectRow({
                        setting: setting,
                        currentValue: _this.settings[setting.id],
                        visible: visible,
                    }));
                    var controlId_1 = "setting_".concat(setting.id);
                    $(controlId_1).addEventListener('change', function () {
                        var value = $(controlId_1).value;
                        _this.changeSetting({ id: setting.id, value: value });
                    });
                }
                else if (setting.type === 'slider') {
                    var visible = !visibleCondition ||
                        (visibleCondition &&
                            visibleCondition.values.includes(_this.settings[visibleCondition.id]));
                    tabContentNode.insertAdjacentHTML('beforeend', tplPlayerPrefenceSliderRow({
                        id: setting.id,
                        label: setting.label,
                        visible: visible,
                    }));
                    var sliderConfig = __assign(__assign({}, setting.sliderConfig), { start: _this.settings[setting.id] });
                    noUiSlider.create($('setting_' + setting.id), sliderConfig);
                    $('setting_' + setting.id).noUiSlider.on('slide', function (arg) {
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
        var node = document.getElementById('play_area_container');
        if (node) {
            node.setAttribute('data-two-columns', value);
        }
        this.game.updateLayout();
    };
    Settings.prototype.onChangeColumnSizesSetting = function (value) {
        this.game.updateLayout();
    };
    Settings.prototype.onChangeSingleColumnMapSizeSetting = function (value) {
        this.game.updateLayout();
    };
    Settings.prototype.onChangeCardSizeInLogSetting = function (value) {
        var ROOT = document.documentElement;
        ROOT.style.setProperty('--logCardScale', "".concat(Number(value) / 100));
    };
    Settings.prototype.onChangeAnimationSpeedSetting = function (value) {
        var duration = 2100 - value;
        debug('onChangeAnimationSpeedSetting', duration);
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
        currentTab.removeAttribute('data-state');
        if (currentTabContent) {
            currentTabContent.style.display = 'none';
        }
        this.selectedTab = id;
        var tab = document.getElementById("settings_modal_tab_".concat(id));
        var tabContent = document.getElementById("settings_modal_tab_content_".concat(this.selectedTab));
        tab.setAttribute('data-state', 'selected');
        if (tabContent) {
            tabContent.style.display = '';
        }
    };
    Settings.prototype.checkAnmimationSpeedVisisble = function () {
        var sliderNode = document.getElementById('setting_row_animationSpeed');
        if (!sliderNode) {
            return;
        }
        if (this.settings[PREF_SHOW_ANIMATIONS] === PREF_ENABLED) {
            sliderNode.style.display = '';
        }
        else {
            sliderNode.style.display = 'none';
        }
    };
    Settings.prototype.checkColumnSizesVisisble = function () {
        var sliderNode = document.getElementById('setting_row_columnSizes');
        var mapSizeSliderNode = document.getElementById('setting_row_singleColumnMapSize');
        if (!(sliderNode && mapSizeSliderNode)) {
            return;
        }
        if (this.settings['twoColumnsLayout'] === PREF_ENABLED) {
            sliderNode.style.display = '';
            mapSizeSliderNode.style.display = 'none';
        }
        else {
            sliderNode.style.display = 'none';
            mapSizeSliderNode.style.display = '';
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
var ActionActivateStackState = (function () {
    function ActionActivateStackState(game) {
        this.game = game;
    }
    ActionActivateStackState.prototype.onEnteringState = function (args) {
        debug('Entering ActionActivateStackState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ActionActivateStackState.prototype.onLeavingState = function () {
        debug('Leaving ActionActivateStackState');
    };
    ActionActivateStackState.prototype.setDescription = function (activePlayerId) { };
    ActionActivateStackState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a stack to activate'),
            args: {
                you: '${you}',
            },
        });
        this.setStacksSelectable();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    ActionActivateStackState.prototype.updateInterfaceSelectAction = function (_a) {
        var _this = this;
        var stackId = _a.stackId, stackActions = _a.stackActions;
        this.game.clearPossible();
        this.game.setLocationSelected({ id: "".concat(stackId, "_").concat(this.args.faction, "_stack") });
        this.game.clientUpdatePageTitle({
            text: _('${you} must choose an action to perform'),
            args: {
                you: '${you}',
            },
        });
        stackActions.forEach(function (action) {
            _this.game.addPrimaryActionButton({
                text: _(action.name),
                id: "".concat(action.id, "_btn"),
                callback: function () { return _this.updateInterfaceConfirm({ stackAction: action, stackId: stackId }); },
            });
        });
        this.game.addCancelButton();
    };
    ActionActivateStackState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var stackAction = _a.stackAction, stackId = _a.stackId;
        this.game.clearPossible();
        this.game.setLocationSelected({ id: "".concat(stackId, "_").concat(this.args.faction, "_stack") });
        this.game.clientUpdatePageTitle({
            text: _('Perform ${actionName} with stack in ${locationName}?'),
            args: {
                actionName: _(stackAction.name),
                locationName: stackId,
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actActionActivateStack',
                args: {
                    action: stackAction.id,
                    stack: stackId,
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
    ActionActivateStackState.prototype.setStacksSelectable = function () {
        var _this = this;
        Object.entries(this.args.stacks).forEach(function (_a, index) {
            var stackId = _a[0], stackActions = _a[1];
            _this.game.setLocationSelectable({
                id: "".concat(stackId, "_").concat(_this.args.faction, "_stack"),
                callback: function () { return _this.updateInterfaceSelectAction({ stackId: stackId, stackActions: stackActions }); },
            });
        });
    };
    return ActionActivateStackState;
}());
var ActionRoundActionPhaseState = (function () {
    function ActionRoundActionPhaseState(game) {
        this.game = game;
    }
    ActionRoundActionPhaseState.prototype.onEnteringState = function (args) {
        debug('Entering ActionRoundActionPhaseState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ActionRoundActionPhaseState.prototype.onLeavingState = function () {
        debug('Leaving ActionRoundActionPhaseState');
    };
    ActionRoundActionPhaseState.prototype.setDescription = function (activePlayerId) { };
    ActionRoundActionPhaseState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.setCardSelected({ id: this.args.card.id });
        this.game.clientUpdatePageTitle({
            text: this.args.isIndianActions
                ? _('${you} may use the Indian card for actions')
                : _('${you} may perform actions'),
            args: {
                you: '${you}',
            },
        });
        this.addActionButtons();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    ActionRoundActionPhaseState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var actionPointId = _a.actionPointId;
        this.game.clearPossible();
        this.game.setCardSelected({ id: this.args.card.id });
        this.game.clientUpdatePageTitle({
            text: _('Use ${tkn_actionPoint} to perform an Action?'),
            args: {
                tkn_actionPoint: actionPointId,
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actActionRoundActionPhase',
                args: {
                    actionPointId: actionPointId,
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
    ActionRoundActionPhaseState.prototype.addActionButtons = function () {
        var _this = this;
        this.args.availableActionPoints.forEach(function (actionPointId, index) {
            _this.game.addPrimaryActionButton({
                id: "ap_".concat(actionPointId, "_").concat(index),
                text: actionPointId,
                callback: function () { return _this.updateInterfaceConfirm({ actionPointId: actionPointId }); },
            });
        });
    };
    return ActionRoundActionPhaseState;
}());
var ActionRoundChooseCardState = (function () {
    function ActionRoundChooseCardState(game) {
        this.game = game;
    }
    ActionRoundChooseCardState.prototype.onEnteringState = function (args) {
        debug('Entering ActionRoundChooseCardState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ActionRoundChooseCardState.prototype.onLeavingState = function () {
        debug('Leaving ActionRoundChooseCardState');
    };
    ActionRoundChooseCardState.prototype.setDescription = function (activePlayerId, args) {
        this.args = args;
        this.game.hand.open();
        if (this.args._private.selectedCard) {
            this.game.setCardSelected({ id: this.args._private.selectedCard.id });
        }
    };
    ActionRoundChooseCardState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must choose your card for this Action Round'),
            args: {
                you: '${you}',
            },
        });
        this.game.hand.open();
        var cards = this.args._private.cards;
        cards.forEach(function (card) {
            _this.game.setCardSelectable({
                id: card.id,
                callback: function () {
                    _this.updateInterfaceConfirm({ card: card });
                },
            });
        });
        this.setIndianCardSelected();
    };
    ActionRoundChooseCardState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var card = _a.card;
        this.game.clearPossible();
        this.game.setCardSelected({ id: card.id });
        this.setIndianCardSelected();
        this.game.clientUpdatePageTitle({
            text: _('Select card?'),
            args: {},
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actActionRoundChooseCard',
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
    ActionRoundChooseCardState.prototype.setIndianCardSelected = function () {
        var indianCard = this.args._private.indianCard;
        if (indianCard) {
            this.game.setCardSelected({ id: indianCard.id });
        }
    };
    return ActionRoundChooseCardState;
}());
var ActionRoundChooseFirstPlayerState = (function () {
    function ActionRoundChooseFirstPlayerState(game) {
        this.game = game;
    }
    ActionRoundChooseFirstPlayerState.prototype.onEnteringState = function (args) {
        debug("Entering ActionRoundChooseFirstPlayerState");
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ActionRoundChooseFirstPlayerState.prototype.onLeavingState = function () {
        debug("Leaving ActionRoundChooseFirstPlayerState");
    };
    ActionRoundChooseFirstPlayerState.prototype.setDescription = function (activePlayerId) { };
    ActionRoundChooseFirstPlayerState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _("${you} must choose the First Player for this Action Round"),
            args: {
                you: "${you}",
            },
        });
        this.game.hand.open();
        this.game.playerManager.getPlayers().forEach(function (player) {
            _this.game.addPlayerButton({
                player: player.playerData,
                callback: function () {
                    return _this.game.takeAction({
                        action: "actActionRoundChooseFirstPlayer",
                        args: {
                            playerId: player.getPlayerId(),
                        },
                    });
                },
            });
        });
        this.game.addUndoButtons(this.args);
    };
    return ActionRoundChooseFirstPlayerState;
}());
var ActionRoundChooseReactionState = (function () {
    function ActionRoundChooseReactionState(game) {
        this.game = game;
    }
    ActionRoundChooseReactionState.prototype.onEnteringState = function (args) {
        debug('Entering ActionRoundChooseReactionState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ActionRoundChooseReactionState.prototype.onLeavingState = function () {
        debug('Leaving ActionRoundChooseReactionState');
    };
    ActionRoundChooseReactionState.prototype.setDescription = function (activePlayerId) { };
    ActionRoundChooseReactionState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} may choose an Action Point to hold for Reaction'),
            args: {
                you: '${you}',
            },
        });
        this.addActionPointButtons();
        this.game.addPassButton({ optionalAction: this.args.optionalAction });
        this.game.addUndoButtons(this.args);
    };
    ActionRoundChooseReactionState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var actionPoint = _a.actionPoint;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('Hold ${ap} for Reaction?'),
            args: {
                ap: actionPoint.id,
            },
        });
        this.game.addConfirmButton({
            callback: function () {
                return _this.game.takeAction({
                    action: 'actActionRoundChooseReaction',
                    args: {
                        actionPointId: actionPoint.id,
                    },
                });
            },
        });
        this.game.addCancelButton();
    };
    ActionRoundChooseReactionState.prototype.addActionPointButtons = function () {
        var _this = this;
        this.args.actionPoints.forEach(function (ap, index) {
            return _this.game.addPrimaryActionButton({
                text: _(ap.id),
                id: "action_point_btn_".concat(index),
                callback: function () { return _this.updateInterfaceConfirm({ actionPoint: ap }); },
            });
        });
    };
    return ActionRoundChooseReactionState;
}());
var ActionRoundSailBoxLandingState = (function () {
    function ActionRoundSailBoxLandingState(game) {
        this.game = game;
    }
    ActionRoundSailBoxLandingState.prototype.onEnteringState = function (args) {
        debug("Entering ActionRoundSailBoxLandingState");
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ActionRoundSailBoxLandingState.prototype.onLeavingState = function () {
        debug("Leaving ActionRoundSailBoxLandingState");
    };
    ActionRoundSailBoxLandingState.prototype.setDescription = function (activePlayerId) { };
    ActionRoundSailBoxLandingState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _("${you} must perform landing"),
            args: {
                you: "${you}",
            },
        });
        this.game.addConfirmButton({
            callback: function () {
                return _this.game.takeAction({
                    action: "actActionRoundSailBoxLanding",
                    args: {},
                });
            },
        });
        this.game.addUndoButtons(this.args);
    };
    return ActionRoundSailBoxLandingState;
}());
var ArmyMovementState = (function () {
    function ArmyMovementState(game) {
        this.selectedUnits = [];
        this.game = game;
    }
    ArmyMovementState.prototype.onEnteringState = function (args) {
        debug('Entering ArmyMovementState');
        this.args = args;
        this.selectedUnits = [];
        this.updateInterfaceInitialStep();
    };
    ArmyMovementState.prototype.onLeavingState = function () {
        debug('Leaving ArmyMovementState');
    };
    ArmyMovementState.prototype.setDescription = function (activePlayerId) { };
    ArmyMovementState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select units to move'),
            args: {
                you: '${you}',
            },
        });
        var stack = this.game.gameMap.stacks[this.args.origin.id][this.args.faction];
        stack.open();
        this.setUnitsSelectable();
        this.game.addConfirmButton({
            callback: function () {
                _this.game.clearPossible();
                _this.game.takeAction({
                    action: 'actArmyMovement',
                    args: {
                        unitIds: _this.selectedUnits.map(function (unit) { return unit.id; }),
                    },
                });
            },
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
        this.checkConfirmDisabled();
    };
    ArmyMovementState.prototype.setUnitsSelectable = function () {
        var _this = this;
        this.args.units.forEach(function (unit) {
            _this.game.setLocationSelectable({
                id: '' + unit.id,
                callback: function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    _this.handleUnitClick({ unit: unit });
                },
            });
        });
    };
    ArmyMovementState.prototype.handleUnitClick = function (_a) {
        var unit = _a.unit;
        var index = this.selectedUnits.findIndex(function (item) { return item.id === unit.id; });
        var element = document.getElementById(unit.id + '');
        if (!element) {
            return;
        }
        if (index < 0) {
            this.selectedUnits.push(unit);
            element.classList.add(BT_SELECTED);
            element.classList.remove(BT_SELECTABLE);
            this.game.setUnitSelected({ id: '' + unit.id });
        }
        else {
            this.selectedUnits.splice(index, 1);
            element.classList.add(BT_SELECTABLE);
            element.classList.remove(BT_SELECTED);
        }
        this.checkConfirmDisabled();
    };
    ArmyMovementState.prototype.checkConfirmDisabled = function () {
        var button = document.getElementById('confirm_btn');
        if (!button) {
            return;
        }
        if (this.selectedUnits.length === 0) {
            button.classList.add(DISABLED);
        }
        else {
            button.classList.remove(DISABLED);
        }
    };
    return ArmyMovementState;
}());
var ArmyMovementDestinationState = (function () {
    function ArmyMovementDestinationState(game) {
        this.selectedUnits = [];
        this.game = game;
    }
    ArmyMovementDestinationState.prototype.onEnteringState = function (args) {
        debug('Entering ArmyMovementDestinationState');
        this.args = args;
        this.selectedUnits = [];
        this.updateInterfaceInitialStep();
    };
    ArmyMovementDestinationState.prototype.onLeavingState = function () {
        debug('Leaving ArmyMovementDestinationState');
    };
    ArmyMovementDestinationState.prototype.setDescription = function (activePlayerId) { };
    ArmyMovementDestinationState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Space to move your units to'),
            args: {
                you: '${you}',
            },
        });
        this.setUnitsSelected();
        this.setSpacesSelectable();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    ArmyMovementDestinationState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var space = _a.space;
        this.game.clearPossible();
        this.setUnitsSelected();
        this.game.setLocationSelected({ id: space.id });
        this.game.clientUpdatePageTitle({
            text: this.args.units.length === 1
                ? _('Move ${unitName} to ${spaceName}?')
                : _('Move selected units to ${spaceName}?'),
            args: {
                you: '${you}',
                spaceName: _(space.name),
                unitName: _(this.game.gamedatas.staticData.units[this.args.units[0].counterId]
                    .counterText),
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actArmyMovementDestination',
                args: {
                    spaceId: space.id,
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
    };
    ArmyMovementDestinationState.prototype.setSpacesSelectable = function () {
        var _this = this;
        Object.values(this.args.destinations).forEach(function (destination) {
            console.log('destination', destination);
            _this.game.setLocationSelectable({
                id: destination.space.id,
                callback: function () {
                    return _this.updateInterfaceConfirm({ space: destination.space });
                },
            });
        });
    };
    ArmyMovementDestinationState.prototype.setUnitsSelected = function () {
        var _this = this;
        this.args.units.forEach(function (unit) {
            _this.game.setUnitSelected({ id: unit.id });
        });
    };
    return ArmyMovementDestinationState;
}());
var BattleApplyHitsState = (function () {
    function BattleApplyHitsState(game) {
        this.game = game;
    }
    BattleApplyHitsState.prototype.onEnteringState = function (args) {
        debug('Entering BattleApplyHitsState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    BattleApplyHitsState.prototype.onLeavingState = function () {
        debug('Leaving BattleApplyHitsState');
    };
    BattleApplyHitsState.prototype.setDescription = function (activePlayerId) { };
    BattleApplyHitsState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: this.args.eliminate ? _('${you} must select a unit to eliminate') : _('${you} must select a unit to apply a Hit to'),
            args: {
                you: '${you}',
            },
        });
        var stack = this.game.gameMap.stacks[this.args.spaceId][this.args.faction];
        stack.open();
        this.setUnitsSelectable();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    BattleApplyHitsState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var unit = _a.unit;
        this.game.clearPossible();
        this.game.setUnitSelected({ id: unit.id });
        this.game.clientUpdatePageTitle({
            text: this.args.eliminate ? _('Eliminate ${unitName}?') : _('Apply Hit to ${unitName}?'),
            args: {
                unitName: _(this.game.gamedatas.staticData.units[unit.counterId].counterText),
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actBattleApplyHits',
                args: {
                    unitId: unit.id,
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
    BattleApplyHitsState.prototype.setUnitsSelectable = function () {
        var _this = this;
        this.args.units.forEach(function (unit) {
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    _this.updateInterfaceConfirm({ unit: unit });
                },
            });
        });
    };
    return BattleApplyHitsState;
}());
var BattleRetreatState = (function () {
    function BattleRetreatState(game) {
        this.game = game;
    }
    BattleRetreatState.prototype.onEnteringState = function (args) {
        debug('Entering BattleRetreatState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    BattleRetreatState.prototype.onLeavingState = function () {
        debug('Leaving BattleRetreatState');
    };
    BattleRetreatState.prototype.setDescription = function (activePlayerId) { };
    BattleRetreatState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Space to retreat to'),
            args: {
                you: '${you}',
            },
        });
        this.args.retreatOptions.forEach(function (space) {
            _this.game.setLocationSelectable({
                id: space.id,
                callback: function () { return _this.updateInterfaceConfirm({ space: space }); },
            });
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    BattleRetreatState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var space = _a.space;
        this.game.clearPossible();
        this.game.setLocationSelected({ id: space.id });
        this.game.clientUpdatePageTitle({
            text: _('Retreat to ${spaceName}?'),
            args: {
                spaceName: _(space.name),
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actBattleRetreat',
                args: {
                    spaceId: space.id,
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
    return BattleRetreatState;
}());
var BattleRollsRerollsState = (function () {
    function BattleRollsRerollsState(game) {
        this.game = game;
    }
    BattleRollsRerollsState.prototype.onEnteringState = function (args) {
        debug('Entering BattleRollsRerollsState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    BattleRollsRerollsState.prototype.onLeavingState = function () {
        debug('Leaving BattleRollsRerollsState');
    };
    BattleRollsRerollsState.prototype.setDescription = function (activePlayerId) { };
    BattleRollsRerollsState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} may select a die to reroll'),
            args: {
                you: '${you}',
            },
        });
        this.args.diceResults.forEach(function (dieResult) {
            return _this.game.addPrimaryActionButton({
                id: "die_result_".concat(dieResult.index, "_btn"),
                text: _this.game.format_string_recursive('${tkn_dieResult}', {
                    tkn_dieResult: dieResult.result,
                }),
                callback: function () { return _this.updateInterfaceConfirm({ dieResult: dieResult }); },
            });
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    BattleRollsRerollsState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var dieResult = _a.dieResult;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('Reroll ${tkn_dieResult} ?'),
            args: {
                tkn_dieResult: dieResult.result,
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actBattleRollsRerolls',
                args: {
                    dieResult: dieResult,
                    rerollSource: dieResult.availableRerollSources[0]
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
    return BattleRollsRerollsState;
}());
var BattleSelectCommanderState = (function () {
    function BattleSelectCommanderState(game) {
        this.game = game;
    }
    BattleSelectCommanderState.prototype.onEnteringState = function (args) {
        debug('Entering BattleSelectCommanderState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    BattleSelectCommanderState.prototype.onLeavingState = function () {
        debug('Leaving BattleSelectCommanderState');
    };
    BattleSelectCommanderState.prototype.setDescription = function (activePlayerId) { };
    BattleSelectCommanderState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Commander'),
            args: {
                you: '${you}',
            },
        });
        var stack = this.game.gameMap.stacks[this.args.space.id][this.args.faction];
        stack.open();
        this.setUnitsSelectable();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    BattleSelectCommanderState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var unit = _a.unit;
        this.game.clearPossible();
        this.game.setUnitSelected({ id: unit.id });
        this.game.clientUpdatePageTitle({
            text: _('Select ${unitName}?'),
            args: {
                you: '${you}',
                unitName: _(this.game.gamedatas.staticData.units[unit.counterId].counterText),
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actBattleSelectCommander',
                args: {
                    unitId: unit.id,
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
    BattleSelectCommanderState.prototype.setUnitsSelectable = function () {
        var _this = this;
        this.args.commanders.forEach(function (unit) {
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    _this.updateInterfaceConfirm({ unit: unit });
                },
            });
        });
    };
    return BattleSelectCommanderState;
}());
var ColonialsEnlistUnitPlacementState = (function () {
    function ColonialsEnlistUnitPlacementState(game) {
        this.placedUnits = null;
        this.game = game;
    }
    ColonialsEnlistUnitPlacementState.prototype.onEnteringState = function (args) {
        debug('Entering ColonialsEnlistUnitPlacementState');
        this.args = args;
        this.localMoves = {};
        this.placedUnits = {};
        this.updateInterfaceInitialStep();
    };
    ColonialsEnlistUnitPlacementState.prototype.onLeavingState = function () {
        debug('Leaving ColonialsEnlistUnitPlacementState');
    };
    ColonialsEnlistUnitPlacementState.prototype.setDescription = function (activePlayerId) { };
    ColonialsEnlistUnitPlacementState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        var unitsToPlace = this.args.units.filter(function (unit) {
            return !Object.keys(_this.placedUnits).includes(unit.id);
        });
        if (unitsToPlace.length === 0) {
            this.updateInterfaceConfirm();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a unit to place'),
            args: {
                you: '${you}',
            },
        });
        unitsToPlace.forEach(function (unit) {
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    _this.updateInterfaceSelectSpace({ unit: unit });
                },
            });
        });
        if (Object.keys(this.placedUnits).length === 0) {
            this.game.addPassButton({
                optionalAction: this.args.optionalAction,
            });
            this.game.addUndoButtons(this.args);
        }
        else {
            this.addCancelButton();
        }
    };
    ColonialsEnlistUnitPlacementState.prototype.updateInterfaceSelectSpace = function (_a) {
        var _this = this;
        var unit = _a.unit;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Space to place ${tkn_unit}'),
            args: {
                you: '${you}',
                tkn_unit: unit.counterId,
            },
        });
        var spacesToPlaceUnit = this.getPossibleSpacesToPlaceUnit(unit);
        spacesToPlaceUnit.forEach(function (id) {
            _this.game.setLocationSelectable({
                id: id,
                callback: function () { return __awaiter(_this, void 0, void 0, function () {
                    return __generator(this, function (_a) {
                        switch (_a.label) {
                            case 0:
                                this.placedUnits[unit.id] = id;
                                this.addLocalMove({ fromSpaceId: unit.location, unit: unit });
                                return [4, this.game.gameMap.stacks[id][unit.faction].addUnit(unit)];
                            case 1:
                                _a.sent();
                                this.updateInterfaceInitialStep();
                                return [2];
                        }
                    });
                }); },
            });
        });
        this.addCancelButton();
    };
    ColonialsEnlistUnitPlacementState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('Confirm unit placement?'),
            args: {},
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actColonialsEnlistUnitPlacement',
                args: {
                    placedUnits: _this.placedUnits,
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
        this.addCancelButton();
    };
    ColonialsEnlistUnitPlacementState.prototype.addLocalMove = function (_a) {
        var fromSpaceId = _a.fromSpaceId, unit = _a.unit;
        if (this.localMoves[fromSpaceId]) {
            this.localMoves[fromSpaceId].push(unit);
        }
        else {
            this.localMoves[fromSpaceId] = [unit];
        }
    };
    ColonialsEnlistUnitPlacementState.prototype.addCancelButton = function () {
        var _this = this;
        this.game.addDangerActionButton({
            id: 'cancel_btn',
            text: _('Cancel'),
            callback: function () { return __awaiter(_this, void 0, void 0, function () {
                return __generator(this, function (_a) {
                    switch (_a.label) {
                        case 0: return [4, this.revertLocalMoves()];
                        case 1:
                            _a.sent();
                            this.game.onCancel();
                            return [2];
                    }
                });
            }); },
        });
    };
    ColonialsEnlistUnitPlacementState.prototype.revertLocalMoves = function () {
        return __awaiter(this, void 0, void 0, function () {
            var promises;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        promises = [];
                        Object.entries(this.localMoves).forEach(function (_a) {
                            var spaceId = _a[0], units = _a[1];
                            promises.push(_this.game.pools.stocks[spaceId].addCards(units));
                        });
                        return [4, Promise.all(promises)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    ColonialsEnlistUnitPlacementState.prototype.getPossibleSpacesToPlaceUnit = function (unit) {
        var _this = this;
        if (this.game.gamedatas.staticData.units[unit.counterId].type === LIGHT) {
            return this.args.spaces.map(function (space) { return space.id; });
        }
        else {
            return this.args.spaces
                .filter(function (space) {
                return space.colony ===
                    _this.game.gamedatas.staticData.units[unit.counterId].colony;
            })
                .map(function (space) { return space.id; });
        }
    };
    return ColonialsEnlistUnitPlacementState;
}());
var ConfirmPartialTurnState = (function () {
    function ConfirmPartialTurnState(game) {
        this.game = game;
    }
    ConfirmPartialTurnState.prototype.onEnteringState = function (args) {
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ConfirmPartialTurnState.prototype.onLeavingState = function () {
        debug("Leaving ConfirmTurnState");
    };
    ConfirmPartialTurnState.prototype.setDescription = function (activePlayerId) {
    };
    ConfirmPartialTurnState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _("${you} must confirm the switch of player. You will not be able to restart your turn"),
            args: {
                you: "${you}",
            },
        });
        this.game.addConfirmButton({
            callback: function () {
                return _this.game.takeAction({
                    action: "actConfirmPartialTurn",
                    atomicAction: false,
                });
            },
        });
        this.game.addUndoButtons(this.args);
    };
    return ConfirmPartialTurnState;
}());
var ConfirmTurnState = (function () {
    function ConfirmTurnState(game) {
        this.game = game;
    }
    ConfirmTurnState.prototype.onEnteringState = function (args) {
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    ConfirmTurnState.prototype.onLeavingState = function () {
        debug("Leaving ConfirmTurnState");
    };
    ConfirmTurnState.prototype.setDescription = function (activePlayerId) {
    };
    ConfirmTurnState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _("${you} must confirm or restart your turn"),
            args: {
                you: "${you}",
            },
        });
        this.game.addConfirmButton({
            callback: function () {
                return _this.game.takeAction({ action: "actConfirmTurn", atomicAction: false });
            },
        });
        this.game.addUndoButtons(this.args);
    };
    return ConfirmTurnState;
}());
var EventDelayedSuppliesFromFranceState = (function () {
    function EventDelayedSuppliesFromFranceState(game) {
        this.frenchAP = null;
        this.indianAP = null;
        this.game = game;
    }
    EventDelayedSuppliesFromFranceState.prototype.onEnteringState = function (args) {
        debug('Entering EventDelayedSuppliesFromFranceState');
        this.args = args;
        this.frenchAP = null;
        this.indianAP = null;
        this.updateInterfaceInitialStep();
    };
    EventDelayedSuppliesFromFranceState.prototype.onLeavingState = function () {
        debug('Leaving EventDelayedSuppliesFromFranceState');
    };
    EventDelayedSuppliesFromFranceState.prototype.setDescription = function (activePlayerId) { };
    EventDelayedSuppliesFromFranceState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select an Indian AP to lose'),
            args: {
                you: '${you}',
            },
        });
        this.args.indianAP.forEach(function (actionPoint, index) {
            _this.game.addPrimaryActionButton({
                id: "ap_".concat(actionPoint, "_").concat(index),
                text: actionPoint.id,
                callback: function () {
                    _this.indianAP = actionPoint.id;
                    _this.updateInterfaceSelectFrenchAP();
                },
            });
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    EventDelayedSuppliesFromFranceState.prototype.updateInterfaceSelectFrenchAP = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a French AP to lose'),
            args: {
                you: '${you}',
            },
        });
        this.args.frenchAP.forEach(function (actionPoint, index) {
            _this.game.addPrimaryActionButton({
                id: "ap_".concat(actionPoint, "_").concat(index),
                text: actionPoint.id,
                callback: function () {
                    _this.frenchAP = actionPoint.id;
                    _this.updateInterfaceConfirm();
                },
            });
        });
    };
    EventDelayedSuppliesFromFranceState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        var text = _('Lose ${indianAP} and ${frenchAP}?');
        this.game.clientUpdatePageTitle({
            text: text,
            args: {
                indianAP: this.indianAP,
                frenchAP: this.frenchAP,
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actEventDelayedSuppliesFromFrance',
                args: {
                    frenchAP: _this.frenchAP,
                    indianAP: _this.indianAP,
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
    return EventDelayedSuppliesFromFranceState;
}());
var EventDiseaseInBritishCampState = (function () {
    function EventDiseaseInBritishCampState(game) {
        this.selectedUnits = [];
        this.game = game;
    }
    EventDiseaseInBritishCampState.prototype.onEnteringState = function (args) {
        debug('Entering EventDiseaseInBritishCampState');
        this.args = args;
        this.selectedUnits = [];
        this.updateInterfaceInitialStep();
    };
    EventDiseaseInBritishCampState.prototype.onLeavingState = function () {
        debug('Leaving EventDiseaseInBritishCampState');
    };
    EventDiseaseInBritishCampState.prototype.setDescription = function (activePlayerId) { };
    EventDiseaseInBritishCampState.prototype.updateInterfaceInitialStep = function () {
        var remaining = this.args.year <= 1756
            ? 1 - this.selectedUnits.length
            : 2 - this.selectedUnits.length;
        if (remaining === 0) {
            this.updateInterfaceConfirm();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: this.args.year <= 1756
                ? _('${you} must select 1 Brigade')
                : _('${you} must select 1 Colonial Brigade and 1 Metropolitan Brigade (${number} remaining)'),
            args: {
                you: '${you}',
                number: remaining,
            },
        });
        this.setUnitsSelectable();
        this.setUnitsSelected();
        if (this.selectedUnits.length === 0) {
            this.game.addPassButton({
                optionalAction: this.args.optionalAction,
            });
            this.game.addUndoButtons(this.args);
        }
        else {
            this.game.addCancelButton();
        }
    };
    EventDiseaseInBritishCampState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        var text = _('Eliminate ${unitsLog}?');
        this.game.clientUpdatePageTitle({
            text: text,
            args: {
                unitsLog: createUnitsLog(this.selectedUnits),
            },
        });
        this.selectedUnits.forEach(function (_a) {
            var id = _a.id;
            return _this.game.setUnitSelected({ id: id });
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actEventDiseaseInBritishCamp',
                args: {
                    selectedUnitIds: _this.selectedUnits.map(function (_a) {
                        var id = _a.id;
                        return id;
                    }),
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
    EventDiseaseInBritishCampState.prototype.setUnitsSelectable = function () {
        var _this = this;
        var units = [];
        if (this.args.year <= 1756) {
            units = this.args.brigades;
        }
        else {
            var colonyBrigadeSelected = this.selectedUnits.some(function (unit) { return !!_this.game.gamedatas.staticData.units[unit.counterId].colony; });
            var metropolitanBrigadeSelected = this.selectedUnits.some(function (unit) {
                return !!_this.game.gamedatas.staticData.units[unit.counterId].metropolitan;
            });
            if (!colonyBrigadeSelected) {
                units = this.args.colonialBrigades;
            }
            if (!metropolitanBrigadeSelected) {
                units = units.concat(this.args.metropolitanBrigades);
            }
        }
        units.forEach(function (unit) {
            _this.game.openUnitStack(unit);
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    if (_this.selectedUnits.some(function (selectedUnit) { return selectedUnit.id === unit.id; })) {
                        _this.selectedUnits = _this.selectedUnits.filter(function (selectedUnit) { return selectedUnit.id !== unit.id; });
                    }
                    else {
                        _this.selectedUnits.push(unit);
                    }
                    _this.updateInterfaceInitialStep();
                },
            });
        });
    };
    EventDiseaseInBritishCampState.prototype.setUnitsSelected = function () {
        var _this = this;
        this.selectedUnits.forEach(function (_a) {
            var id = _a.id;
            return _this.game.setUnitSelected({ id: id });
        });
    };
    return EventDiseaseInBritishCampState;
}());
var EventDiseaseInFrenchCampState = (function () {
    function EventDiseaseInFrenchCampState(game) {
        this.game = game;
    }
    EventDiseaseInFrenchCampState.prototype.onEnteringState = function (args) {
        debug('Entering EventDiseaseInFrenchCampState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    EventDiseaseInFrenchCampState.prototype.onLeavingState = function () {
        debug('Leaving EventDiseaseInFrenchCampState');
    };
    EventDiseaseInFrenchCampState.prototype.setDescription = function (activePlayerId) { };
    EventDiseaseInFrenchCampState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select 1 Brigade to eliminate'),
            args: {
                you: '${you}',
            },
        });
        this.args.options.forEach(function (unit) {
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () { return _this.updateInterfaceConfirm({ unit: unit }); },
            });
            var stack = _this.game.gameMap.stacks[unit.location][unit.faction];
            stack.open();
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    EventDiseaseInFrenchCampState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var unit = _a.unit;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('Eliminate ${tkn_unit} ?'),
            args: {
                tkn_unit: unit.counterId,
            },
        });
        this.game.setUnitSelected({ id: unit.id });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actEventDiseaseInFrenchCamp',
                args: {
                    unitId: unit.id,
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
    return EventDiseaseInFrenchCampState;
}());
var EventPennsylvaniasPeacePromisesState = (function () {
    function EventPennsylvaniasPeacePromisesState(game) {
        this.selectedUnits = [];
        this.game = game;
    }
    EventPennsylvaniasPeacePromisesState.prototype.onEnteringState = function (args) {
        debug('Entering EventPennsylvaniasPeacePromisesState');
        this.args = args;
        this.selectedUnits = [];
        this.updateInterfaceInitialStep();
    };
    EventPennsylvaniasPeacePromisesState.prototype.onLeavingState = function () {
        debug('Leaving EventPennsylvaniasPeacePromisesState');
    };
    EventPennsylvaniasPeacePromisesState.prototype.setDescription = function (activePlayerId) { };
    EventPennsylvaniasPeacePromisesState.prototype.updateInterfaceInitialStep = function () {
        var remaining = Math.min(2, this.args.units.length) - this.selectedUnits.length;
        if (remaining === 0) {
            this.updateInterfaceConfirm();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select an Indian unit (${number} remaining)'),
            args: {
                you: '${you}',
                number: remaining,
            },
        });
        this.setUnitsSelectable(this.args.units);
        this.setUnitsSelected();
        if (this.selectedUnits.length === 0) {
            this.game.addPassButton({
                optionalAction: this.args.optionalAction,
            });
            this.game.addUndoButtons(this.args);
        }
        else {
            this.game.addCancelButton();
        }
    };
    EventPennsylvaniasPeacePromisesState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        var text = _('Send ${unitsLog} to the Losses Box?');
        this.game.clientUpdatePageTitle({
            text: text,
            args: {
                unitsLog: createUnitsLog(this.selectedUnits),
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actEventPennsylvaniasPeacePromises',
                args: {
                    selectedUnitIds: _this.selectedUnits.map(function (_a) {
                        var id = _a.id;
                        return id;
                    }),
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
    EventPennsylvaniasPeacePromisesState.prototype.setUnitsSelectable = function (units) {
        var _this = this;
        units.forEach(function (unit) {
            _this.game.openUnitStack(unit);
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    if (_this.selectedUnits.some(function (selectedUnit) { return selectedUnit.id === unit.id; })) {
                        _this.selectedUnits = _this.selectedUnits.filter(function (selectedUnit) { return selectedUnit.id !== unit.id; });
                    }
                    else {
                        _this.selectedUnits.push(unit);
                    }
                    _this.updateInterfaceInitialStep();
                },
            });
        });
    };
    EventPennsylvaniasPeacePromisesState.prototype.setUnitsSelected = function () {
        var _this = this;
        this.selectedUnits.forEach(function (_a) {
            var id = _a.id;
            return _this.game.setUnitSelected({ id: id });
        });
    };
    return EventPennsylvaniasPeacePromisesState;
}());
var EventRoundUpMenAndEquipmentState = (function () {
    function EventRoundUpMenAndEquipmentState(game) {
        this.selectedReducedUnits = [];
        this.game = game;
    }
    EventRoundUpMenAndEquipmentState.prototype.onEnteringState = function (args) {
        debug('Entering EventRoundUpMenAndEquipmentState');
        this.args = args;
        this.selectedReducedUnits = [];
        this.updateInterfaceInitialStep();
    };
    EventRoundUpMenAndEquipmentState.prototype.onLeavingState = function () {
        debug('Leaving EventRoundUpMenAndEquipmentState');
    };
    EventRoundUpMenAndEquipmentState.prototype.setDescription = function (activePlayerId) { };
    EventRoundUpMenAndEquipmentState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select an option'),
            args: {
                you: '${you}',
            },
        });
        if (this.args.options.reduced.length > 0) {
            this.game.addPrimaryActionButton({
                id: 'flip_reduced_btn',
                text: _('Flip Reduced units'),
                callback: function () { return _this.updateInterfaceFlipReducedUnits(); },
            });
        }
        if (Object.keys(this.args.options.lossesBox).length > 0) {
            this.game.addPrimaryActionButton({
                id: 'place_from_losses_box_btn',
                text: _('Place 1 unit from Losses Box'),
                callback: function () { return _this.updateInterfacePlaceUnitFromLossesBox(); },
            });
        }
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    EventRoundUpMenAndEquipmentState.prototype.updateInterfaceFlipReducedUnits = function () {
        var _this = this;
        if (this.selectedReducedUnits.length === 2) {
            this.updateInterfaceConfirm({ flipReduced: true });
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select up to 2 Reduced units to flip (${number} remaining)'),
            args: {
                you: '${you}',
                number: 2 - this.selectedReducedUnits.length,
            },
        });
        this.args.options.reduced.forEach(function (unit) {
            _this.game.openUnitStack(unit);
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    if (_this.selectedReducedUnits.some(function (selectedUnit) { return selectedUnit.id === unit.id; })) {
                        _this.selectedReducedUnits = _this.selectedReducedUnits.filter(function (selectedUnit) { return selectedUnit.id !== unit.id; });
                    }
                    else {
                        _this.selectedReducedUnits.push(unit);
                    }
                    _this.updateInterfaceFlipReducedUnits();
                },
            });
        });
        this.selectedReducedUnits.forEach(function (_a) {
            var id = _a.id;
            return _this.game.setUnitSelected({ id: id });
        });
        this.game.addPrimaryActionButton({
            id: 'done_btn',
            text: _('Done'),
            callback: function () { return _this.updateInterfaceConfirm({ flipReduced: true }); },
            extraClasses: this.selectedReducedUnits.length === 0 ? DISABLED : '',
        });
        this.game.addCancelButton();
    };
    EventRoundUpMenAndEquipmentState.prototype.updateInterfacePlaceUnitFromLossesBox = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select 1 unit from the Losses Box'),
            args: {
                you: '${you}',
            },
        });
        Object.entries(this.args.options.lossesBox).forEach(function (_a) {
            var id = _a[0], option = _a[1];
            _this.game.setUnitSelectable({
                id: id,
                callback: function () { return _this.updateInterfaceSelectSpace(option); },
            });
        });
        this.game.addCancelButton();
    };
    EventRoundUpMenAndEquipmentState.prototype.updateInterfaceSelectSpace = function (_a) {
        var _this = this;
        var unit = _a.unit, spaceIds = _a.spaceIds;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a friendly Home Space to place ${tkn_unit}'),
            args: {
                you: '${you}',
                tkn_unit: unit.counterId,
            },
        });
        this.game.setUnitSelected({ id: unit.id });
        spaceIds.forEach(function (spaceId) {
            return _this.game.setLocationSelectable({
                id: spaceId,
                callback: function () {
                    _this.updateInterfaceConfirm({ unit: unit, spaceId: spaceId });
                },
            });
        });
        this.game.addCancelButton();
    };
    EventRoundUpMenAndEquipmentState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var _b = _a.flipReduced, flipReduced = _b === void 0 ? false : _b, unit = _a.unit, spaceId = _a.spaceId;
        this.game.clearPossible();
        var text = flipReduced
            ? _('Flip ${unitsLog} ?')
            : _('Place ${tkn_unit} in ${spaceName}?');
        this.game.clientUpdatePageTitle({
            text: text,
            args: {
                unitsLog: createUnitsLog(this.selectedReducedUnits),
                tkn_unit: unit ? unit.counterId : '',
                spaceName: spaceId
                    ? _(this.game.gamedatas.staticData.spaces[spaceId].name)
                    : '',
            },
        });
        if (flipReduced) {
            this.selectedReducedUnits.forEach(function (_a) {
                var id = _a.id;
                return _this.game.setUnitSelected({ id: id });
            });
        }
        if (unit) {
            this.game.setUnitSelected({ id: unit.id });
        }
        if (spaceId) {
            this.game.setLocationSelected({ id: spaceId });
        }
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actEventRoundUpMenAndEquipment',
                args: {
                    selectedReducedUnitIds: _this.selectedReducedUnits.map(function (_a) {
                        var id = _a.id;
                        return id;
                    }),
                    placedUnit: unit ? { unitId: unit.id, spaceId: spaceId } : null,
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
    return EventRoundUpMenAndEquipmentState;
}());
var EventSmallpoxInfectedBlanketsState = (function () {
    function EventSmallpoxInfectedBlanketsState(game) {
        this.selectedUnits = [];
        this.game = game;
    }
    EventSmallpoxInfectedBlanketsState.prototype.onEnteringState = function (args) {
        debug('Entering EventSmallpoxInfectedBlanketsState');
        this.args = args;
        this.selectedUnits = [];
        this.updateInterfaceInitialStep();
    };
    EventSmallpoxInfectedBlanketsState.prototype.onLeavingState = function () {
        debug('Leaving EventSmallpoxInfectedBlanketsState');
    };
    EventSmallpoxInfectedBlanketsState.prototype.setDescription = function (activePlayerId) { };
    EventSmallpoxInfectedBlanketsState.prototype.updateInterfaceInitialStep = function () {
        var remaining = 2 - this.selectedUnits.length;
        if (remaining === 0) {
            this.updateInterfaceConfirm();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a French-controlled Indian unit (${number} remaining)'),
            args: {
                you: '${you}',
                number: remaining,
            },
        });
        this.setUnitsSelectable(this.args.units);
        this.setUnitsSelected();
        if (this.selectedUnits.length === 0) {
            this.game.addPassButton({
                optionalAction: this.args.optionalAction,
            });
            this.game.addUndoButtons(this.args);
        }
        else {
            this.game.addCancelButton();
        }
    };
    EventSmallpoxInfectedBlanketsState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        var text = _('Place ${unitsLog} on the Losses Box?');
        this.game.clientUpdatePageTitle({
            text: text,
            args: {
                unitsLog: createUnitsLog(this.selectedUnits),
            },
        });
        this.selectedUnits.forEach(function (_a) {
            var id = _a.id;
            return _this.game.setUnitSelected({ id: id });
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actEventSmallpoxInfectedBlankets',
                args: {
                    selectedUnitIds: _this.selectedUnits.map(function (_a) {
                        var id = _a.id;
                        return id;
                    }),
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
    EventSmallpoxInfectedBlanketsState.prototype.setUnitsSelectable = function (units) {
        var _this = this;
        units.forEach(function (unit) {
            _this.game.openUnitStack(unit);
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    if (_this.selectedUnits.some(function (selectedUnit) { return selectedUnit.id === unit.id; })) {
                        _this.selectedUnits = _this.selectedUnits.filter(function (selectedUnit) { return selectedUnit.id !== unit.id; });
                    }
                    else {
                        _this.selectedUnits.push(unit);
                    }
                    _this.updateInterfaceInitialStep();
                },
            });
        });
    };
    EventSmallpoxInfectedBlanketsState.prototype.setUnitsSelected = function () {
        var _this = this;
        this.selectedUnits.forEach(function (_a) {
            var id = _a.id;
            return _this.game.setUnitSelected({ id: id });
        });
    };
    return EventSmallpoxInfectedBlanketsState;
}());
var FleetsArriveUnitPlacementState = (function () {
    function FleetsArriveUnitPlacementState(game) {
        this.placedFleets = null;
        this.placedUnits = null;
        this.placedCommanders = null;
        this.game = game;
    }
    FleetsArriveUnitPlacementState.prototype.onEnteringState = function (args) {
        debug('Entering FleetsArriveUnitPlacementState');
        this.args = args;
        this.localMoves = {};
        this.placedFleets = {};
        this.placedUnits = {};
        this.placedCommanders = {};
        this.updateInterfaceInitialStep();
    };
    FleetsArriveUnitPlacementState.prototype.onLeavingState = function () {
        debug('Leaving FleetsArriveUnitPlacementState');
    };
    FleetsArriveUnitPlacementState.prototype.setDescription = function (activePlayerId) { };
    FleetsArriveUnitPlacementState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        var fleetsToPlace = this.args.fleets.filter(function (fleet) {
            return !Object.keys(_this.placedFleets).includes(fleet.id);
        });
        if (fleetsToPlace.length === 0) {
            this.updateInterfacePlaceUnits();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Fleet to place'),
            args: {
                you: '${you}',
            },
        });
        fleetsToPlace.forEach(function (fleet) {
            _this.game.setUnitSelectable({
                id: fleet.id,
                callback: function () {
                    _this.updateInterfaceSelectSpace({ unit: fleet, isFleet: true });
                },
            });
        });
        if (Object.keys(this.placedFleets).length === 0) {
            this.game.addPassButton({
                optionalAction: this.args.optionalAction,
            });
            this.game.addUndoButtons(this.args);
        }
        else {
            this.addCancelButton();
        }
    };
    FleetsArriveUnitPlacementState.prototype.updateInterfaceSelectSpace = function (_a) {
        var _this = this;
        var _b;
        var unit = _a.unit, _c = _a.isFleet, isFleet = _c === void 0 ? false : _c;
        this.game.clearPossible();
        var commanderId = this.args.commandersPerUnit[unit.id] || null;
        this.game.clientUpdatePageTitle({
            text: commanderId
                ? _('${you} must select a Space to place ${tkn_unit}${tkn_unit_commander}')
                : _('${you} must select a Space to place ${tkn_unit}'),
            args: {
                you: '${you}',
                tkn_unit: unit.counterId,
                tkn_unit_commander: commanderId
                    ? (_b = this.args.commanders[commanderId]) === null || _b === void 0 ? void 0 : _b.counterId
                    : '',
            },
        });
        if (isFleet) {
            this.args.spaces.forEach(function (space) {
                _this.game.setLocationSelectable({
                    id: space.id,
                    callback: function () { return __awaiter(_this, void 0, void 0, function () {
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    this.placedFleets[unit.id] = space.id;
                                    this.addLocalMove({ fromSpaceId: unit.location, unit: unit });
                                    return [4, this.game.gameMap.stacks[space.id][unit.faction].addUnit(unit)];
                                case 1:
                                    _a.sent();
                                    this.updateInterfaceInitialStep();
                                    return [2];
                            }
                        });
                    }); },
                });
            });
        }
        else {
            var spacesToPlaceUnit = this.getPossibleSpacesToPlaceUnit();
            spacesToPlaceUnit.forEach(function (id) {
                _this.game.setLocationSelectable({
                    id: id,
                    callback: function () { return __awaiter(_this, void 0, void 0, function () {
                        var units, commander;
                        return __generator(this, function (_a) {
                            switch (_a.label) {
                                case 0:
                                    this.placedUnits[unit.id] = id;
                                    this.addLocalMove({ fromSpaceId: unit.location, unit: unit });
                                    units = [unit];
                                    if (commanderId) {
                                        commander = this.args.commanders[commanderId];
                                        this.addLocalMove({
                                            fromSpaceId: unit.location,
                                            unit: commander,
                                        });
                                        units.push(commander);
                                    }
                                    return [4, this.game.gameMap.stacks[id][unit.faction].addUnits(units)];
                                case 1:
                                    _a.sent();
                                    this.updateInterfacePlaceUnits();
                                    return [2];
                            }
                        });
                    }); },
                });
            });
        }
        this.addCancelButton();
    };
    FleetsArriveUnitPlacementState.prototype.updateInterfacePlaceUnits = function () {
        var _this = this;
        var unitsToPlace = this.args.units.filter(function (unit) {
            return (!Object.keys(_this.placedFleets).includes(unit.id) &&
                !Object.keys(_this.placedUnits).includes(unit.id));
        });
        if (unitsToPlace.length === 0) {
            this.updateInterfaceConfirm();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a unit to place'),
            args: {
                you: '${you}',
            },
        });
        unitsToPlace.forEach(function (unit) {
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    _this.updateInterfaceSelectSpace({ unit: unit });
                },
            });
        });
        this.addCancelButton();
    };
    FleetsArriveUnitPlacementState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('Confirm unit placement?'),
            args: {},
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actFleetsArriveUnitPlacement',
                args: {
                    placedFleets: _this.placedFleets,
                    placedUnits: _this.placedUnits,
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
        this.addCancelButton();
    };
    FleetsArriveUnitPlacementState.prototype.addLocalMove = function (_a) {
        var fromSpaceId = _a.fromSpaceId, unit = _a.unit;
        if (this.localMoves[fromSpaceId]) {
            this.localMoves[fromSpaceId].push(unit);
        }
        else {
            this.localMoves[fromSpaceId] = [unit];
        }
    };
    FleetsArriveUnitPlacementState.prototype.addCancelButton = function () {
        var _this = this;
        this.game.addDangerActionButton({
            id: 'cancel_btn',
            text: _('Cancel'),
            callback: function () { return __awaiter(_this, void 0, void 0, function () {
                return __generator(this, function (_a) {
                    switch (_a.label) {
                        case 0: return [4, this.revertLocalMoves()];
                        case 1:
                            _a.sent();
                            this.game.onCancel();
                            return [2];
                    }
                });
            }); },
        });
    };
    FleetsArriveUnitPlacementState.prototype.revertLocalMoves = function () {
        return __awaiter(this, void 0, void 0, function () {
            var promises;
            var _this = this;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        promises = [];
                        Object.entries(this.localMoves).forEach(function (_a) {
                            var spaceId = _a[0], units = _a[1];
                            promises.push(_this.game.pools.stocks[spaceId].addCards(units));
                        });
                        return [4, Promise.all(promises)];
                    case 1:
                        _a.sent();
                        return [2];
                }
            });
        });
    };
    FleetsArriveUnitPlacementState.prototype.getPossibleSpacesToPlaceUnit = function () {
        var fleetLocations = Object.values(this.placedFleets);
        if (fleetLocations.length > 0) {
            return fleetLocations.reduce(function (carry, current) {
                if (carry.includes(current)) {
                    return carry;
                }
                else {
                    carry.push(current);
                    return carry;
                }
            }, []);
        }
        var unitLocations = Object.values(this.placedUnits);
        if (unitLocations.length > 0) {
            return [unitLocations[0]];
        }
        else {
            return this.args.spaces.map(function (space) { return space.id; });
        }
    };
    return FleetsArriveUnitPlacementState;
}());
var VagariesOfWarPickUnitsState = (function () {
    function VagariesOfWarPickUnitsState(game) {
        var _a;
        this.selectedUnitIds = [];
        this.selectedVoWToken = null;
        this.vowTokenNumberOfUnitsMap = (_a = {},
            _a[VOW_PICK_ONE_ARTILLERY_FRENCH] = 1,
            _a[VOW_PICK_TWO_ARTILLERY_BRITISH] = 2,
            _a[VOW_PICK_TWO_ARTILLERY_OR_LIGHT_BRITISH] = 2,
            _a[VOW_PICK_ONE_COLONIAL_LIGHT] = 1,
            _a[VOW_PICK_ONE_COLONIAL_LIGHT_PUT_BACK] = 1,
            _a);
        this.game = game;
    }
    VagariesOfWarPickUnitsState.prototype.onEnteringState = function (args) {
        debug('Entering VagariesOfWarPickUnitsState');
        this.args = args;
        this.selectedUnitIds = [];
        this.selectedVoWToken = null;
        this.updateInterfaceInitialStep();
    };
    VagariesOfWarPickUnitsState.prototype.onLeavingState = function () {
        debug('Leaving VagariesOfWarPickUnitsState');
    };
    VagariesOfWarPickUnitsState.prototype.setDescription = function (activePlayerId) { };
    VagariesOfWarPickUnitsState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        if (Object.keys(this.args.options).length === 1) {
            this.selectedVoWToken = Object.keys(this.args.options)[0];
            this.updateInterfaceSelectUnits();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Vagaries of War token to resolve'),
            args: {
                you: '${you}',
            },
        });
        Object.keys(this.args.options).forEach(function (counterId) {
            _this.game.addSecondaryActionButton({
                id: "".concat(counterId, "_btn"),
                text: _this.game.format_string_recursive('${tkn_unit}', {
                    tkn_unit: counterId,
                }),
                callback: function () {
                    _this.selectedVoWToken = counterId;
                    _this.updateInterfaceSelectUnits();
                },
            });
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    VagariesOfWarPickUnitsState.prototype.updateInterfaceSelectUnits = function () {
        var _this = this;
        var numberOfUnitsToSelect = this.vowTokenNumberOfUnitsMap[this.selectedVoWToken];
        if (this.selectedUnitIds.length === numberOfUnitsToSelect) {
            this.updateInterfaceConfirm();
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a unit for ${tkn_unit} (${number} remaining)'),
            args: {
                you: '${you}',
                tkn_unit: this.selectedVoWToken,
                number: numberOfUnitsToSelect - this.selectedUnitIds.length,
            },
        });
        this.selectedUnitIds.forEach(function (id) { return _this.game.setUnitSelected({ id: id }); });
        this.args.options[this.selectedVoWToken].forEach(function (unit) {
            return _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () {
                    if (_this.selectedUnitIds.includes(unit.id)) {
                        _this.selectedUnitIds = _this.selectedUnitIds.filter(function (unitId) { return unitId !== unit.id; });
                    }
                    else {
                        _this.selectedUnitIds.push(unit.id);
                    }
                    _this.updateInterfaceSelectUnits();
                },
            });
        });
        this.game.addCancelButton();
    };
    VagariesOfWarPickUnitsState.prototype.updateInterfaceConfirm = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('Pick ${unitsLog} ?'),
            args: {
                unitsLog: createUnitsLog(this.args.options[this.selectedVoWToken].filter(function (unit) {
                    return _this.selectedUnitIds.includes(unit.id);
                })),
            },
        });
        this.selectedUnitIds.forEach(function (id) { return _this.game.setUnitSelected({ id: id }); });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actVagariesOfWarPickUnits',
                args: {
                    vowTokenId: _this.selectedVoWToken,
                    selectedUnitIds: _this.selectedUnitIds,
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
    return VagariesOfWarPickUnitsState;
}());
var LightMovementState = (function () {
    function LightMovementState(game) {
        this.selectedUnits = [];
        this.game = game;
    }
    LightMovementState.prototype.onEnteringState = function (args) {
        debug('Entering LightMovementState');
        this.args = args;
        this.selectedUnits = [];
        this.updateInterfaceInitialStep();
    };
    LightMovementState.prototype.onLeavingState = function () {
        debug('Leaving LightMovementState');
    };
    LightMovementState.prototype.setDescription = function (activePlayerId) { };
    LightMovementState.prototype.updateInterfaceInitialStep = function () {
        var _this = this;
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select units to move'),
            args: {
                you: '${you}',
            },
        });
        var stack = this.game.gameMap.stacks[this.args.origin.id][this.args.faction];
        stack.open();
        this.setUnitsSelectable();
        this.game.addConfirmButton({
            callback: function () {
                _this.game.clearPossible();
                _this.game.takeAction({
                    action: 'actLightMovement',
                    args: {
                        unitIds: _this.selectedUnits.map(function (unit) { return unit.id; }),
                    },
                });
            },
        });
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
        this.checkConfirmDisabled();
    };
    LightMovementState.prototype.setUnitsSelectable = function () {
        var _this = this;
        this.args.lightUnits.forEach(function (unit) {
            _this.game.setLocationSelectable({
                id: '' + unit.id,
                callback: function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    _this.handleUnitClick({ unit: unit });
                },
            });
        });
    };
    LightMovementState.prototype.handleUnitClick = function (_a) {
        var unit = _a.unit;
        var index = this.selectedUnits.findIndex(function (item) { return item.id === unit.id; });
        var element = document.getElementById(unit.id + '');
        if (!element) {
            return;
        }
        if (index < 0) {
            this.selectedUnits.push(unit);
            element.classList.add(BT_SELECTED);
            element.classList.remove(BT_SELECTABLE);
            this.game.setUnitSelected({ id: '' + unit.id });
        }
        else {
            this.selectedUnits.splice(index, 1);
            element.classList.add(BT_SELECTABLE);
            element.classList.remove(BT_SELECTED);
        }
        this.checkConfirmDisabled();
    };
    LightMovementState.prototype.checkConfirmDisabled = function () {
        var button = document.getElementById('confirm_btn');
        if (!button) {
            return;
        }
        if (this.selectedUnits.length === 0) {
            button.classList.add(DISABLED);
        }
        else {
            button.classList.remove(DISABLED);
        }
    };
    return LightMovementState;
}());
var LightMovementDestinationState = (function () {
    function LightMovementDestinationState(game) {
        this.game = game;
    }
    LightMovementDestinationState.prototype.onEnteringState = function (args) {
        debug('Entering LightMovementDestinationState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    LightMovementDestinationState.prototype.onLeavingState = function () {
        debug('Leaving LightMovementDestinationState');
    };
    LightMovementDestinationState.prototype.setDescription = function (activePlayerId) { };
    LightMovementDestinationState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a Space to move your units to'),
            args: {
                you: '${you}',
            },
        });
        this.setUnitsSelected();
        this.setSpacesSelectable();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    LightMovementDestinationState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var space = _a.space;
        this.game.clearPossible();
        this.setUnitsSelected();
        this.game.setLocationSelected({ id: space.id });
        this.game.clientUpdatePageTitle({
            text: this.args.units.length === 1
                ? _('Move ${unitName} to ${spaceName}?')
                : _('Move selected units to ${spaceName}?'),
            args: {
                you: '${you}',
                spaceName: _(space.name),
                unitName: _(this.game.gamedatas.staticData.units[this.args.units[0].counterId]
                    .counterText),
            },
        });
        var callback = function () {
            _this.game.clearPossible();
            _this.game.takeAction({
                action: 'actLightMovementDestination',
                args: {
                    spaceId: space.id,
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
    };
    LightMovementDestinationState.prototype.setSpacesSelectable = function () {
        var _this = this;
        Object.values(this.args.destinations).forEach(function (destination) {
            _this.game.setLocationSelectable({
                id: destination.space.id,
                callback: function () {
                    return _this.updateInterfaceConfirm({ space: destination.space });
                },
            });
        });
    };
    LightMovementDestinationState.prototype.setUnitsSelected = function () {
        var _this = this;
        this.args.units.forEach(function (unit) {
            _this.game.setUnitSelected({ id: unit.id });
        });
    };
    return LightMovementDestinationState;
}());
var RaidState = (function () {
    function RaidState(game) {
        this.game = game;
    }
    RaidState.prototype.onEnteringState = function (args) {
        debug('Entering RaidState');
        this.args = args;
        this.updateInterfaceInitialStep();
    };
    RaidState.prototype.onLeavingState = function () {
        debug('Leaving RaidState');
    };
    RaidState.prototype.setDescription = function (activePlayerId) { };
    RaidState.prototype.updateInterfaceInitialStep = function () {
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a target Space to raid'),
            args: {
                you: '${you}',
            },
        });
        this.setTargetsSelectable();
        this.game.addPassButton({
            optionalAction: this.args.optionalAction,
        });
        this.game.addUndoButtons(this.args);
    };
    RaidState.prototype.updateInterfaceSelectPath = function (_a) {
        var _this = this;
        var space = _a.space, paths = _a.paths;
        if (paths.length === 1) {
            this.updateInterfaceSelectUnit({ space: space, path: paths[0] });
            return;
        }
        this.game.clearPossible();
        var counts = {};
        paths.forEach(function (path, index) {
            path.forEach(function (spaceId) {
                if (counts[spaceId]) {
                    counts[spaceId].count += 1;
                    counts[spaceId].paths.push(index);
                }
                else {
                    counts[spaceId] = {
                        count: 1,
                        paths: [index],
                    };
                }
            });
        });
        Object.entries(counts).forEach(function (_a) {
            var id = _a[0], count = _a[1];
            if (count.count > 1) {
                _this.game.setLocationSelected({ id: id });
            }
            else {
                _this.game.setLocationSelectable({
                    id: id,
                    callback: function () {
                        _this.updateInterfaceSelectUnit({
                            path: paths[count.paths[0]],
                            space: space,
                        });
                    },
                });
            }
        });
        this.game.clientUpdatePageTitle({
            text: _('${you} must select the path to the target of the Raid'),
            args: {
                you: '${you}',
            },
        });
    };
    RaidState.prototype.updateInterfaceSelectUnit = function (_a) {
        var _this = this;
        var space = _a.space, path = _a.path;
        if (this.args.units.length === 1) {
            this.updateInterfaceConfirm({ space: space, path: path, unit: this.args.units[0] });
            return;
        }
        this.game.clearPossible();
        this.game.clientUpdatePageTitle({
            text: _('${you} must select a unit to Raid with'),
            args: {
                you: '${you}',
            },
        });
        this.game.setLocationSelected({ id: space.id });
        path.forEach(function (spaceId) {
            _this.game.setLocationSelected({ id: spaceId });
        });
        var stack = this.game.gameMap.stacks[this.args.originId][this.args.faction];
        stack.open();
        this.args.units.forEach(function (unit) {
            _this.game.setUnitSelectable({
                id: unit.id,
                callback: function () { return _this.updateInterfaceConfirm({ space: space, path: path, unit: unit }); },
            });
        });
        this.game.addCancelButton();
    };
    RaidState.prototype.updateInterfaceConfirm = function (_a) {
        var _this = this;
        var space = _a.space, path = _a.path, unit = _a.unit;
        this.game.clearPossible();
        this.game.setLocationSelected({ id: space.id });
        this.game.setUnitSelected({ id: unit.id });
        path.forEach(function (spaceId) {
            _this.game.setLocationSelected({ id: spaceId });
        });
        this.game.clientUpdatePageTitle({
            text: _('Raid ${spaceName}?'),
            args: {
                spaceName: _(space.name),
            },
        });
        this.game.addConfirmButton({
            callback: function () {
                _this.game.clearPossible();
                _this.game.takeAction({
                    action: 'actRaid',
                    args: {
                        path: path,
                        spaceId: space.id,
                        unitId: unit.id,
                    },
                });
            },
        });
        this.game.addCancelButton();
    };
    RaidState.prototype.setTargetsSelectable = function () {
        var _this = this;
        Object.values(this.args.raidTargets).forEach(function (target) {
            _this.game.setLocationSelectable({
                id: target.space.id,
                callback: function () {
                    _this.updateInterfaceSelectPath(target);
                },
            });
        });
    };
    return RaidState;
}());
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
        this.game.hand.open();
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
            _this.game.clearPossible();
            _this.game.takeAction({
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
var TokenManager = (function (_super) {
    __extends(TokenManager, _super);
    function TokenManager(game) {
        var _this = _super.call(this, game, {
            getId: function (card) { return "".concat(card.id); },
            setupDiv: function (card, div) { return _this.setupDiv(card, div); },
            setupFrontDiv: function (card, div) { return _this.setupFrontDiv(card, div); },
            setupBackDiv: function (card, div) { return _this.setupBackDiv(card, div); },
            isCardVisible: function (card) { return _this.isCardVisible(card); },
            animationManager: game.animationManager,
        }) || this;
        _this.game = game;
        return _this;
    }
    TokenManager.prototype.clearInterface = function () { };
    TokenManager.prototype.setupDiv = function (token, div) {
        var _a;
        if (token.manager === UNITS) {
            div.classList.add('bt_token');
            div.insertAdjacentHTML('beforeend', "<div id=\"spent_marker_".concat(token.id, "\" data-spent=\"").concat(token.spent === 1 ? 'true' : 'false', "\" class=\"bt_spent_marker\"></div>"));
            var isCommander = ((_a = this.game.gamedatas.staticData.units[token.counterId]) === null || _a === void 0 ? void 0 : _a.type) ===
                COMMANDER;
            if (isCommander) {
                div.setAttribute('data-commander', 'true');
            }
        }
        else if (token.manager === MARKERS) {
            div.classList.add('bt_marker');
        }
    };
    TokenManager.prototype.setupFrontDiv = function (token, div) {
        var _a;
        if (token.manager === UNITS) {
            div.classList.add('bt_token_side');
            div.setAttribute('data-counter-id', token.counterId);
            var isCommander = ((_a = this.game.gamedatas.staticData.units[token.counterId]) === null || _a === void 0 ? void 0 : _a.type) ===
                COMMANDER;
            if (isCommander) {
                div.setAttribute('data-commander', 'true');
            }
        }
        else if (token.manager === MARKERS) {
            div.classList.add('bt_marker_side');
            div.setAttribute('data-side', 'front');
            div.setAttribute('data-type', token.type);
        }
    };
    TokenManager.prototype.setupBackDiv = function (token, div) {
        var _a;
        if (token.manager === UNITS) {
            div.classList.add('bt_token_side');
            div.setAttribute('data-counter-id', "".concat(token.counterId, "_reduced"));
            var isCommander = ((_a = this.game.gamedatas.staticData.units[token.counterId]) === null || _a === void 0 ? void 0 : _a.type) ===
                COMMANDER;
            if (isCommander) {
                div.setAttribute('data-commander', 'true');
            }
        }
        else if (token.manager === MARKERS) {
            div.classList.add('bt_marker_side');
            div.setAttribute('data-side', 'back');
            div.setAttribute('data-type', token.type);
        }
    };
    TokenManager.prototype.isCardVisible = function (token) {
        if (token.manager === UNITS) {
            return !token.reduced;
        }
        else if (token.manager === MARKERS) {
            return token.side === 'front';
        }
    };
    return TokenManager;
}(CardManager));
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
var UnitStack = (function (_super) {
    __extends(UnitStack, _super);
    function UnitStack(manager, element, settings, faction) {
        var _this = _super.call(this, manager, element, settings, function (element, cards, lastCard, stock) { return _this.updateStackDisplay(element, cards, stock); }) || this;
        _this.manager = manager;
        _this.element = element;
        _this.hovering = false;
        _this.isOpen = false;
        _this.element.classList.add('bt_stack');
        _this.faction = faction;
        _this.element.addEventListener('mouseover', function () { return _this.onMouseOver(); });
        _this.element.addEventListener('mouseout', function () { return _this.onMouseOut(); });
        _this.element.addEventListener('click', function () {
            _this.isOpen = !_this.isOpen;
            _this.updateStackDisplay(_this.element, _this.getCards(), _this);
        });
        return _this;
    }
    UnitStack.prototype.addUnit = function (unit, animation, settings) {
        var promise = _super.prototype.addCard.call(this, unit, animation, settings);
        this.element.setAttribute('data-has-unit', 'true');
        return promise;
    };
    UnitStack.prototype.addUnits = function (units, animation, settings) {
        var promise = _super.prototype.addCards.call(this, units, animation, settings);
        this.element.setAttribute('data-has-unit', 'true');
        return promise;
    };
    UnitStack.prototype.cardRemoved = function (unit, settings) {
        var unitDiv = this.getCardElement(unit);
        unitDiv.style.top = undefined;
        unitDiv.style.left = undefined;
        _super.prototype.cardRemoved.call(this, unit, settings);
        if (this.getCards().length === 0) {
            this.element.removeAttribute('data-has-unit');
        }
    };
    UnitStack.prototype.onMouseOver = function () {
        this.hovering = true;
        this.updateStackDisplay(this.element, this.getCards(), this);
    };
    UnitStack.prototype.onMouseOut = function () {
        this.hovering = false;
        this.updateStackDisplay(this.element, this.getCards(), this);
    };
    UnitStack.prototype.open = function () {
        this.isOpen = true;
        this.updateStackDisplay(this.element, this.getCards(), this);
    };
    UnitStack.prototype.updateStackDisplay = function (element, cards, stock) {
        var _this = this;
        var expanded = this.isOpen || this.hovering;
        if (expanded) {
            this.element.setAttribute('data-expanded', 'true');
        }
        cards.forEach(function (card, index) {
            var unitDiv = stock.getCardElement(card);
            unitDiv.style.top = "calc(var(--btTokenScale) * ".concat(index * (expanded ? 0 : -8), "px)");
            var offset = expanded ? 52 : 8;
            unitDiv.style.left = "calc(var(--btTokenScale) * ".concat(index * (_this.faction === FRENCH ? -1 * offset : offset), "px)");
        });
        if (!expanded) {
            this.element.removeAttribute('data-expanded');
        }
    };
    return UnitStack;
}(ManualPositionStock));
