/**
 * Additional Visual Effects v2.2.0
 * http://rightjs.org/plugins/effects
 *
 * Copyright (C) 2009-2011 Nikolay Nemshilov
 */
(function (a) {
    function n(a, b, c) {
        var d = e(c).compact(), g = k(d.last()) ? d.pop() : {}, h = new (f[b.capitalize()])(a, g);
        h.start.apply(h, d);
        return a
    }

    function m(a, b) {
        for (var c = 0, d = b.length, e = a.computedStyles(), f = {}, g; c < d; c++)g = b[c], g in e && (f[g] = "" + e[g]);
        return f
    }

    if (!a.Fx)throw"RightJS Fx is missing";
    var b = a, c = a.$, d = a.$w, e = a.$A, f = a.Fx, g = a.Class, h = a.Object, i = a.Element, j = a.defined, k = a.isHash, l = a.isString;
    a.Effects = {version: "2.2.0"}, f.Move = new g(f.Morph, {
        extend: {
            Options: h.merge(f.Options, {
                duration: "long",
                position: "absolute"
            })
        }, prepare: function (a) {
            return this.$super(this.getEndPosition(a))
        }, getEndPosition: function (a) {
            var b = this.element.getStyle("position"), c = {};
            if (b != "absolute" || b != "relative")this.element._.style.position = b = b == "fixed" ? "absolute" : "relative";
            a.top && (a.y = a.top.toInt()), a.left && (a.x = a.left.toInt());
            var d = this.element.position(), e = this.getParentPosition(), f = d.x - e.x, g = d.y - e.y;
            this.options.position == "relative" ? b == "absolute" ? (j(a.x) && (a.x += d.x), j(a.y) && (a.y += d.x)) : (j(a.x) && (a.x += f), j(a.y) && (a.y += g)) : b == "relative" && (j(a.x) && (a.x += f - d.x), j(a.y) && (a.y += g - d.y));
            for (var h in a)switch (h) {
                case"top":
                case"left":
                    break;
                case"y":
                    c.top = a.y + "px";
                    break;
                case"x":
                    c.left = a.x + "px";
                    break;
                default:
                    c[h] = a[h]
            }
            return c
        }, getParentPosition: function () {
            f.Move.Dummy = f.Move.Dummy || new i("div", {style: "width:0;height:0;visibility:hidden"}), this.element.insert(f.Move.Dummy, "before");
            var a = f.Move.Dummy.position();
            f.Move.Dummy.remove();
            return a
        }
    }), f.Zoom = new g(f.Move, {
        PROPERTIES: d("width height lineHeight paddingTop paddingRight paddingBottom paddingLeft fontSize borderWidth"),
        extend: {Options: h.merge(f.Move.Options, {position: "relative", duration: "normal", from: "center"})},
        prepare: function (a, b) {
            return this.$super(this._getZoomedStyle(a, b))
        },
        _getZoomedStyle: function (a, b) {
            var c = this._getProportion(a);
            return h.merge(this._getBasicStyle(c), this._getEndPosition(c), b)
        },
        _getProportion: function (a) {
            if (k(a)) {
                var c = $E("div").insertTo($E("div", {style: "visibility:hidden;float:left;height:0;width:0"}).insertTo(document.body)).setStyle(a);
                a = a.height ? c.size().y / this.element.size().y : c.size().x / this.element.size().x, c.remove()
            } else l(a) && (a = b(a).endsWith("%") ? b(a).toFloat() / 100 : b(a).toFloat());
            return a
        },
        _getBasicStyle: function (a) {
            function e(c) {
                return "" + b(c).toFloat() * a
            }

            var c = m(this.element, this.PROPERTIES), d = /([\d\.]+)/g;
            for (var f in c) {
                if (f === "width" || f === "height")c[f] = c[f] || this.element["offset" + b(f).capitalize()] + "px";
                c[f].match(d) ? c[f] = c[f].replace(d, e) : delete c[f]
            }
            c.borderWidth && b(c.borderWidth).toFloat() < 1 && (c.borderWidth = "1px");
            return c
        },
        _getEndPosition: function (a) {
            var b = {}, c = this.element.size(), d = c.x * (a - 1), e = c.y * (a - 1);
            switch (this.options.from.replace("-", " ").split(" ").sort().join("_")) {
                case"top":
                    b.x = -d / 2;
                    break;
                case"right":
                    b.x = -d, b.y = -e / 2;
                    break;
                case"bottom":
                    b.x = -d / 2;
                case"bottom_left":
                    b.y = -e;
                    break;
                case"bottom_right":
                    b.y = -e;
                case"right_top":
                    b.x = -d;
                    break;
                case"center":
                    b.x = -d / 2;
                case"left":
                    b.y = -e / 2;
                    break;
                default:
            }
            return b
        }
    }), f.Bounce = new g(f, {
        extend: {Options: h.merge(f.Options, {duration: "short", direction: "top", value: 16})},
        prepare: function (a) {
            a = a || this.options.value;
            var b = this.element.position(), c = f.Durations[this.options.duration] || this.options.duration, d = {
                duration: c,
                position: "relative"
            }, e = "y";
            switch (this.options.direction) {
                case"right":
                    a = -a;
                case"left":
                    e = "x";
                    break;
                case"bottom":
                    a = -a
            }
            var g = {}, h = {};
            g[e] = -a, h[e] = a, (new f.Move(this.element, d)).start(g), (new f.Move(this.element, d)).start(h), this.finish.bind(this).delay(1);
            return this
        }
    }), f.Run = new g(f.Move, {
        extend: {Options: h.merge(f.Move.Options, {direction: "left"})}, prepare: function (a) {
            var b = a || "toggle", c = {}, d = this.element.dimensions(), e = 80;
            if (b == "out" || b == "toggle" && this.element.visible())this.options.direction == "left" ? c.x = -d.width - e : c.y = -d.height - e, this.onFinish(function () {
                this.element.hide().setStyle(this.getEndPosition({x: d.left, y: d.top}))
            }); else {
                d = this.element.setStyle("visibility: hidden").show().dimensions();
                var f = {};
                this.options.direction == "left" ? (f.x = -d.width - e, c.x = d.left) : (f.y = -d.height - e, c.y = d.top), this.element.setStyle(this.getEndPosition(f)).setStyle("visibility: visible")
            }
            return this.$super(c)
        }
    }), f.Puff = new g(f.Zoom, {
        extend: {Options: h.merge(f.Zoom.Options, {size: 1.4})}, prepare: function (a) {
            var b = a || "toggle", c = 0, d = this.options.size, e;
            if (b == "out" || b == "toggle" && this.element.visible())e = this.getEndPosition(this._getZoomedStyle(1)), this.onFinish(function () {
                e.opacity = 1, this.element.hide().setStyle(e)
            }); else {
                this.element.setStyle("visibility: visible").show();
                var f = this.element.offsetWidth;
                e = this.getEndPosition(this._getZoomedStyle(1)), this.onFinish(function () {
                    this.element.setStyle(e)
                }), this.element.setStyle(h.merge(this.getEndPosition(this._getZoomedStyle(d)), {
                    opacity: 0,
                    visibility: "visible"
                })), d = f / this.element.offsetWidth, c = 1
            }
            return this.$super(d, {opacity: c})
        }
    }), f.Glow = new g(f.Morph, {
        extend: {Options: h.merge(f.Options, {color: "#FF8", transition: "Exp"})},
        prepare: function (a, b) {
            var c = this.element, d = c._.style, e = "color", f = b || c.getStyle(e);
            f = [c].concat(c.parents()).map("getStyle", e).compact().first() || "#FFF", d[e] = a || this.options.color;
            return this.$super({color: f})
        }
    }), a.Element.include({
        move: function (a, b) {
            return n(this, "move", [a, b || {}])
        }, bounce: function () {
            return n(this, "bounce", arguments)
        }, zoom: function (a, b) {
            return n(this, "zoom", [a, b || {}])
        }, run: function () {
            return n(this, "run", arguments)
        }, puff: function () {
            return n(this, "puff", arguments)
        }, glow: function () {
            return n(this, "glow", arguments)
        }
    })
})(RightJS)