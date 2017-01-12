/**
 * RightJS-UI Resizable v2.2.4
 * http://rightjs.org/ui/resizable
 *
 * Copyright (C) 2010-2012 Nikolay Nemshilov
 */
var Resizable = RightJS.Resizable = function (a, b) {
    function m(a) {
        var b = j.current;
        b && (b.release(a), j.current = null)
    }

    function l(a) {
        var b = j.current;
        b && b.track(a)
    }

    function k(a) {
        var b = a.find(".rui-resizable-handle");
        if (b) {
            var c = b.parent();
            c instanceof j || (c = new j(c)), j.current = c.start(a.stop())
        }
    }

    function c(a, c) {
        c || (c = a, a = "DIV");
        var d = new b.Class(b.Element.Wrappers[a] || b.Element, {
            initialize: function (c, d) {
                this.key = c;
                var e = [{"class": "rui-" + c}];
                this instanceof b.Input || this instanceof b.Form || e.unshift(a), this.$super.apply(this, e), b.isString(d) && (d = b.$(d)), d instanceof b.Element && (this._ = d._, "$listeners"in d && (d.$listeners = d.$listeners), d = {}), this.setOptions(d, this);
                return b.Wrapper.Cache[b.$uid(this._)] = this
            }, setOptions: function (a, c) {
                c && (a = b.Object.merge(a, (new Function("return " + (c.get("data-" + this.key) || "{}")))())), a && b.Options.setOptions.call(this, b.Object.merge(this.options, a));
                return this
            }
        }), e = new b.Class(d, c);
        b.Observer.createShortcuts(e.prototype, e.EVENTS || b([]));
        return e
    }

    var d = b, e = b.$, f = b.$w, g = b.$E, h = b.Class, i = b.Element, j = new c({
        extend: {
            version: "2.2.4",
            EVENTS: f("resize start release"),
            Options: {direction: null, minWidth: null, maxWidth: null, minHeight: null, maxHeight: null}
        }, initialize: function (a, b) {
            this.$super("resizable", this.old_inst = e(a)).setOptions(b), this.options.direction ? this.addClass("rui-resizable-" + this.options.direction) : this.addClass("rui-resizable"), this.content = this.first(".rui-resizable-content") || g("div", {"class": "rui-resizable-content"}).insert(this.children()).insertTo(this), this.handle = this.first(".rui-resizable-handle") || g("div", {"class": "rui-resizable-handle"}).insertTo(this), this.content.setWidth(this.size().x - parseInt(this.getStyle("borderLeftWidth"), 10) - parseInt(this.getStyle("borderRightWidth"), 10)), this.options.direction !== "left" && this.options.direction !== "right" && this.content.setHeight(this.size().y - parseInt(this.getStyle("borderTopWidth"), 10) - parseInt(this.getStyle("borderBottomWidth"), 10))
        }, destroy: function () {
            this.removeClass("rui-resizable").removeClass("rui-resizable-top").removeClass("rui-resizable-left").removeClass("rui-resizable-right").removeClass("rui-resizable-bottom").insert(this.content._.childNodes), this.content.remove(), this.handle.remove(), this.old_inst && (Wrapper.Cache[$uid(this._)] = this.old_inst);
            return this
        }, setOptions: function (a, b) {
            a = a || {}, f("top left right bottom").each(function (b) {
                this.hasClass("rui-resizable-" + b) && (a.direction = b)
            }, this);
            return this.$super(a, b)
        }, start: function (a) {
            this.prevSizes = this.size(), this.prevEvPos = a.position(), this.contXDiff = this.size().x - this.content.size().x, this.contYDiff = this.size().y - this.content.size().y, f("minWidth maxWidth minHeight maxHeight").each(function (a) {
                this[a] = this.findDim(a)
            }, this);
            return this.fire("start", {original: a})
        }, track: function (a) {
            var b = a.position(), c = this.prevEvPos, d = this.handle.dimensions(), e = this.prevSizes, f = e.x, g = e.y, h = c.x - b.x, i = c.y - b.y, j = this.minWidth, k = this.maxWidth, l = this.minHeight, m = this.maxHeight, n = this.options, o = n.direction;
            f += (o === "left" ? 1 : -1) * h, g += (o === "top" ? 1 : -1) * i, f < j && (f = j), f > k && (f = k), g < l && (g = l), g > m && (g = m), e.x !== f && o !== "top" && o !== "bottom" && this.setWidth(f), e.y !== g && o !== "left" && o !== "right" && this.setHeight(g);
            if (f == j || f == k)b.x = d.left + d.width / 2;
            if (g == l || g == m)b.y = d.top + d.height / 2;
            this.prevEvPos = b, this.prevSizes = this.size(), this.fire("resize", {original: a})
        }, setWidth: function (a) {
            this.content.setWidth(a - this.contXDiff);
            return this.$super(a)
        }, setHeight: function (a) {
            this.content.setHeight(a - this.contYDiff);
            return this.$super(a)
        }, release: function (a) {
            return this.fire("release", {original: a})
        }, findDim: function (a) {
            var b = this.options[a] || this.getStyle(a);
            if (b && /\d+/.test(b) && parseFloat(b) > 0) {
                var c = d(a).include("Width") ? "width" : "height", e = (this._dummy || (this._dummy = g("div", {style: "visibility:hidden;z-index:-1"}))).setStyle(c, b).insertTo(this, "before"), f = e._["offset" + d(c).capitalize()];
                e.remove();
                return f
            }
        }
    });
    e(a).on({
        mousedown: k,
        touchstart: k,
        mousemove: l,
        touchmove: l,
        mouseup: m,
        touchend: m
    }), e(window).onBlur(function (a) {
        var b = j.current;
        b && (b.release(a), j.current = null)
    }), i.include({
        makeResizable: function (a) {
            return new j(this, a)
        }, undoResizable: function () {
            this instanceof j && this.destroy();
            return this
        }
    }), "Draggable"in b && b.Draggable.include({
        dragStart: function (a) {
            a.target.hasClass("rui-resizable-handle") || this.$super(a)
        }
    });
    var n = a.createElement("style"), o = a.createTextNode(".rui-resizable,.rui-resizable-top,.rui-resizable-left,.rui-resizable-right,.rui-resizable-bottom,.rui-resizable-content .rui-resizable-handle{margin:0;padding:0;overflow:none;border:none;background:none;width:auto;height:auto;min-width:none;max-width:none;min-height:none;max-height:none}.rui-resizable,.rui-resizable-top,.rui-resizable-left,.rui-resizable-right,.rui-resizable-bottom{position:relative;min-width:8em;min-height:8em;border:1px solid #DDD}.rui-resizable-content{overflow:auto;padding:.5em;position:relative}.rui-resizable-handle{position:absolute;background-image:url(/images/rightjs-ui/resizable.png);background-repeat:no-repeat;background-color:#DDD;cursor:move}.rui-resizable .rui-resizable-handle{right:0;bottom:0;background-position:-2px -2px;background-color:transparent;width:16px;height:16px}.rui-resizable-top .rui-resizable-handle,.rui-resizable-bottom .rui-resizable-handle{height:8px;width:100%;background-position:center -26px;cursor:row-resize}.rui-resizable-left .rui-resizable-handle,.rui-resizable-right .rui-resizable-handle{top:0px;width:8px;height:100%;background-position:-26px center;cursor:col-resize}.rui-resizable-top .rui-resizable-content{padding-top:1em}.rui-resizable-top .rui-resizable-handle{top:0}.rui-resizable-bottom .rui-resizable-content{padding-bottom:1em}.rui-resizable-bottom .rui-resizable-handle{bottom:0}.rui-resizable-left .rui-resizable-content{padding-left:1em}.rui-resizable-left .rui-resizable-handle{left:0}.rui-resizable-right .rui-resizable-content{padding-right:1em}.rui-resizable-right .rui-resizable-handle{right:0}");
    n.type = "text/css", a.getElementsByTagName("head")[0].appendChild(n), n.styleSheet ? n.styleSheet.cssText = o.nodeValue : n.appendChild(o);
    return j
}(document, RightJS)