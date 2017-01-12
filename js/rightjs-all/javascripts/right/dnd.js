/**
 * Drag'n'Drop module v2.2.4
 * http://rightjs.org/plugins/drag-n-drop
 *
 * Copyright (C) 2009-2012 Nikolay Nemshilov
 */
(function (a, b, c) {
    function o(a) {
        l.current !== null && l.current.dragStop(a)
    }

    function n(a) {
        l.current !== null && (l.current.dragProcess(a), m.checkHover(a, l.current))
    }

    var d = c, e = c.$, f = c.$w, g = c.Class, h = c.isHash, i = c.isArray, j = c.Element, k = c.Observer, l = new g(k, {
        extend: {
            version: "2.2.4",
            EVENTS: f("before start drag stop drop"),
            Options: {
                handle: null,
                snap: 0,
                axis: null,
                range: null,
                dragClass: "dragging",
                clone: !1,
                revert: !1,
                revertDuration: "normal",
                scroll: !0,
                scrollSensitivity: 32,
                zIndex: 1e7,
                moveOut: !1,
                relName: "draggable"
            },
            current: null,
            rescan: function (a) {
                var c = this.Options.relName, d = this === l ? "draggable" : "droppable";
                (e(a) || e(b)).find('*[rel^="' + c + '"]').each(function (a) {
                    a[d] || new this(a, (new Function("return " + a.get("data-" + c)))() || {})
                }, this)
            }
        }, initialize: function (a, b) {
            this.element = e(a), this.$super(b), this._dragStart = d(function (a) {
                a.which === 1 && this.dragStart(a)
            }).bind(this), this.handle.on({
                mousedown: this._dragStart,
                touchstart: this._dragStart
            }), this.element.draggable = this
        }, destroy: function () {
            this.handle.stopObserving("mousedown", this._dragStart).stopObserving("touchstart", this._dragStart), delete this.element.draggable;
            return this
        }, setOptions: function (a) {
            this.$super(a), this.handle = this.options.handle ? e(this.options.handle) : this.element, i(this.options.snap) ? (this.snapX = this.options.snap[0], this.snapY = this.options.snap[1]) : this.snapX = this.snapY = this.options.snap;
            return this
        }, revert: function () {
            var a = this.clone.position(), b = {top: a.y + this.ryDiff + "px", left: a.x + this.rxDiff + "px"};
            this.options.revertDuration && this.element.morph ? this.element.morph(b, {
                duration: this.options.revertDuration,
                onFinish: d(this.swapBack).bind(this)
            }) : (this.element.setStyle(b), this.swapBack());
            return this
        }, dragStart: function (c) {
            if (this._drag)return !1;
            this._drag = !0, this.fire("before", this, c.stop());
            var d = this.element.position();
            this.xDiff = c.pageX - d.x, this.yDiff = c.pageY - d.y, this.rxDiff = this.ryDiff = 0, this.element.parents().reverse().each(function (a) {
                a.getStyle("position") !== "static" && (a = a.position(), this.rxDiff = -a.x, this.ryDiff = -a.y)
            }, this);
            var f = {x: this.element.getStyle("width"), y: this.element.getStyle("height")};
            f.x == "auto" && (f.x = this.element._.offsetWidth + "px"), f.y == "auto" && (f.y = this.element._.offsetHeight + "px");
            if (this.options.clone || this.options.revert)this.clone = (new j(this.element._.cloneNode(!0))).setStyle({visibility: this.options.clone ? "visible" : "hidden"}).insertTo(this.element, "before");
            this.element.setStyle({
                position: "absolute",
                zIndex: l.Options.zIndex++,
                top: d.y + this.ryDiff + "px",
                left: d.x + this.rxDiff + "px",
                width: f.x,
                height: f.y
            }).addClass(this.options.dragClass), this.options.moveOut && this.element.insertTo(b.body), this.winScrolls = e(a).scrolls(), this.winSizes = e(a).size(), l.current = this.calcConstraints().fire("start", this, c), this.style = this.element._.style
        }, dragProcess: function (b) {
            var c = b.pageX, d = b.pageY, f = c - this.xDiff, g = d - this.yDiff;
            this.ranged && (this.minX > f && (f = this.minX), this.maxX < f && (f = this.maxX), this.minY > g && (g = this.minY), this.maxY < g && (g = this.maxY));
            if (this.options.scroll) {
                var h = {x: this.winScrolls.x, y: this.winScrolls.y}, i = this.options.scrollSensitivity;
                d - h.y < i ? h.y = d - i : h.y + this.winSizes.y - d < i && (h.y = d - this.winSizes.y + i), c - h.x < i ? h.x = c - i : h.x + this.winSizes.x - c < i && (h.x = c - this.winSizes.x + i), h.y < 0 && (h.y = 0), h.x < 0 && (h.x = 0), (h.y < this.winScrolls.y || h.y > this.winScrolls.y || h.x < this.winScrolls.x || h.x > this.winScrolls.x) && e(a).scrollTo(this.winScrolls = h)
            }
            this.snapX && (f = f - f % this.snapX), this.snapY && (g = g - g % this.snapY), this.axisY || (this.style.left = f + this.rxDiff + "px"), this.axisX || (this.style.top = g + this.ryDiff + "px"), this.fire("drag", this, b)
        }, dragStop: function (a) {
            this.element.removeClass(this.options.dragClass), m.checkDrop(a, this), this.options.revert ? this.revert() : this._drag = !1, l.current = null, this.fire("stop", this, a)
        }, swapBack: function () {
            this.clone && this.clone.replace(this.element.setStyle({
                width: this.clone.getStyle("width"),
                height: this.clone.getStyle("height"),
                position: this.clone.getStyle("position"),
                zIndex: this.clone.getStyle("zIndex") || ""
            })), this._drag = !1
        }, calcConstraints: function () {
            var a = this.options.axis;
            this.axisX = d(["x", "horizontal"]).include(a), this.axisY = d(["y", "vertical"]).include(a), this.ranged = !1;
            var b = this.options.range;
            if (b) {
                this.ranged = !0;
                var c = e(b);
                if (c instanceof j) {
                    var f = c.dimensions();
                    b = {x: [f.left, f.left + f.width], y: [f.top, f.top + f.height]}
                }
                if (h(b)) {
                    var g = this.element.size();
                    b.x && (this.minX = b.x[0], this.maxX = b.x[1] - g.x), b.y && (this.minY = b.y[0], this.maxY = b.y[1] - g.y)
                }
            }
            return this
        }
    }), m = new g(k, {
        extend: {
            EVENTS: f("drop hover leave"),
            Options: {
                accept: "*",
                containment: null,
                overlap: null,
                overlapSize: .5,
                allowClass: "droppable-allow",
                denyClass: "droppable-deny",
                relName: "droppable"
            },
            rescan: l.rescan,
            checkHover: function (a, b) {
                for (var c = 0, d = this.active.length; c < d; c++)this.active[c].checkHover(a, b)
            },
            checkDrop: function (a, b) {
                for (var c = 0, d = this.active.length; c < d; c++)this.active[c].checkDrop(a, b)
            },
            active: []
        }, initialize: function (a, b) {
            this.element = e(a), this.$super(b), m.active.push(this.element._droppable = this)
        }, destroy: function () {
            m.active = m.active.without(this), delete this.element.droppable;
            return this
        }, checkHover: function (a, b) {
            this.hoveredBy(a, b) ? this._hovered || (this._hovered = !0, this.element.addClass(this.options[this.allows(b) ? "allowClass" : "denyClass"]), this.fire("hover", b, this, a)) : this._hovered && (this._hovered = !1, this.reset().fire("leave", b, this, a))
        }, checkDrop: function (a, b) {
            this.reset(), this.hoveredBy(a, b) && this.allows(b) && (b.fire("drop", this, b, a), this.fire("drop", b, this, a))
        }, reset: function () {
            this.element.removeClass(this.options.allowClass).removeClass(this.options.denyClass);
            return this
        }, hoveredBy: function (a, b) {
            var c = this.element.dimensions(), d = c.top, e = c.left, f = c.left + c.width, g = c.top + c.height, h = a.pageX, i = a.pageY;
            if (!this.options.overlap)return h > e && h < f && i > d && i < g;
            var j = b.element.dimensions(), k = this.options.overlapSize, l = j.top, m = j.left, n = j.left + j.width, o = j.top + j.height;
            switch (this.options.overlap) {
                case"x":
                case"horizontal":
                    return (l > d && l < g || o > d && o < g) && (m > e && m < f - c.width * k || n < f && n > e + c.width * k);
                case"y":
                case"vertical":
                    return (m > e && m < f || n > e && n < f) && (l > d && l < g - c.height * k || o < g && o > d + c.height * k);
                default:
                    return (m > e && m < f - c.width * k || n < f && n > e + c.width * k) && (l > d && l < g - c.height * k || o < g && o > d + c.height * k)
            }
        }, allows: function (a) {
            this.options.containment && !this._scanned && (this.options.containment = d(this.options.containment).map(e), this._scanned = !0);
            var b = this.options.containment ? this.options.containment.includes(a.element) : !0;
            return b && (this.options.accept == "*" ? !0 : a.element.match(this.options.accept))
        }
    });
    e(b).on({
        ready: function () {
            l.rescan(), m.rescan()
        }, mousemove: n, touchmove: n, mouseup: o, touchend: o
    }), j.include({
        makeDraggable: function (a) {
            new l(this, a);
            return this
        }, undoDraggable: function () {
            "draggable"in this && this.draggable.destroy();
            return this
        }, makeDroppable: function (a) {
            new m(this, a);
            return this
        }, undoDroppable: function () {
            "droppable"in this && this.droppable.destroy();
            return this
        }
    }), a.Draggable = c.Draggable = l, a.Droppable = c.Droppable = m
})(window, document, RightJS)