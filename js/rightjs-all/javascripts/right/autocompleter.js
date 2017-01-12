/**
 * RightJS-UI Autocompleter v2.2.2
 * http://rightjs.org/ui/autocompleter
 *
 * Copyright (C) 2010-2012 Nikolay Nemshilov
 */
var Autocompleter = RightJS.Autocompleter = function (a, b) {
    function g(a, c, d) {
        var e = this.reAnchor || (this.reAnchor = new b.Element("div", {"class": "rui-re-anchor"})).insert(this), f = e.insertTo(a, "after").position(), g = a.dimensions(), h = this, i = parseInt(a.getStyle("borderTopWidth")), j = parseInt(a.getStyle("borderLeftWidth")), k = parseInt(a.getStyle("borderRightWidth")), l = parseInt(a.getStyle("borderBottomWidth")), m = g.top - f.y + i, n = g.left - f.x + j, o = g.width - j - k, p = g.height - i - l;
        h.setStyle("visibility:hidden").show(null), c === "right" ? n += o - h.size().x : m += p, h.moveTo(n, m), d && (c === "left" || c === "right" ? h.setHeight(p) : h.setWidth(o)), h.setStyle("visibility:visible").hide(null)
    }

    function f(a, c, d, e) {
        if (c === "hide" && a.visible() || c === "show" && a.hidden()) {
            b.Fx ? (a.___fx = !0, d === undefined && (d = a.options.fxName, e === undefined && (e = {
                duration: a.options.fxDuration,
                onFinish: function () {
                    a.___fx = !1, a.fire(c)
                }
            }, c === "hide" && (e.duration = (b.Fx.Durations[e.duration] || e.duration) / 2)))) : (a.___fx = !1, d || a.fire(c));
            return a.$super(d, e)
        }
        return a
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

    var d = new b.Class(b.Element, {
        initialize: function (a) {
            this.$super("div", {"class": "rui-spinner"}), this.dots = [];
            for (var c = 0; c < (a || 4); c++)this.dots.push(new b.Element("div"));
            this.dots[0].addClass("glowing"), this.insert(this.dots), b(this.shift).bind(this).periodical(300)
        }, shift: function () {
            if (this.visible()) {
                var a = this.dots.pop();
                this.dots.unshift(a), this.insert(a, "top")
            }
        }
    }), e = {
        show: function (a, b) {
            this.constructor.current = this;
            return f(this, "show", a, b)
        }, hide: function (a, b) {
            this.constructor.current = null;
            return f(this, "hide", a, b)
        }, showAt: function (a, c, d) {
            this.hide(null).shownAt = a = b.$(a), g.call(this, a, c, d);
            return this.show()
        }, toggleAt: function (a, b, c) {
            return this.hidden() ? this.showAt(a, b, c) : this.hide()
        }
    }, h = b, i = b.$, j = b.$w, k = b.$E, l = b.Xhr, m = b.RegExp, n = b.isArray, o = new c("UL", {
        include: e,
        extend: {
            version: "2.2.2",
            EVENTS: j("show hide update load select done"),
            Options: {
                url: a.location.href,
                param: "search",
                method: "get",
                minLength: 1,
                threshold: 200,
                cache: !0,
                local: null,
                fxName: "slide",
                fxDuration: "short",
                spinner: "native",
                cssRule: "input[data-autocompleter]"
            }
        },
        initialize: function (a, b) {
            this.input = i(a), this.$super("autocompleter", b).addClass("rui-dd-menu").onMousedown(this.clicked), this.input.autocompleter = this
        },
        destroy: function () {
            delete this.input.autocompleter;
            return this
        },
        prev: function () {
            return this.pick("prev")
        },
        next: function () {
            return this.pick("next")
        },
        done: function (a) {
            a = a || this.first("li.current"), a && (a.radioClass("current"), this.input.setValue(a._.textContent || a._.innerText), this.fire("done"));
            return this.hide()
        },
        setOptions: function (a) {
            this.$super(a, this.input), a = this.options, h(a.url).includes("%{search}") || (a.url += (h(a.url).includes("?") ? "&" : "?") + a.param + "=%{search}")
        },
        pick: function (a) {
            var b = this.children(), c = b.first("hasClass", "current"), d = b.indexOf(c);
            a == "prev" ? c = d < 1 ? b.last() : b[d < 0 ? 0 : d - 1] : a == "next" && (c = d < 0 || d == b.length - 1 ? b.first() : b[d + 1]);
            return this.fire("select", {item: c.radioClass("current")})
        },
        clicked: function (a) {
            this.done(a.stop().find("li"))
        },
        keypressed: function (a) {
            if (this.input.value().length < this.options.minLength)return this.hide();
            this.timeout && this.timeout.cancel(), this.timeout = h(this.trigger).bind(this).delay(this.options.threshold)
        },
        trigger: function () {
            this.timeout = null, this.cache = this.cache || {};
            var a = this.input.value(), b = this.options;
            if (a.length < b.minLength)return this.hide();
            this.cache[a] ? this.suggest(this.cache[a], a) : n(b.local) ? this.suggest(this.findLocal(a), a) : this.request = l.load(b.url.replace("%{search}", encodeURIComponent(a)), {
                method: b.method,
                spinner: this.getSpinner(),
                onComplete: h(function (b) {
                    this.fire("load").suggest(b.text, a)
                }).bind(this)
            })
        },
        suggest: function (a, b) {
            this.options.cache && (this.cache[b] = a);
            if (h(a).blank())this.hide(); else {
                this.update(a.replace(/<ul[^>]*>|<\/ul>/im, "")), this.fire("update");
                if (!this._connected || this.hidden())this.showAt(this.input, "bottom", "resize"), this._connected = !0
            }
            return this
        },
        findLocal: function (a) {
            var b = new m("(" + m.escape(a) + ")", "ig");
            return h(this.options.local).map(function (a) {
                if (a.match(b))return "<li>" + a.replace(b, "<strong>$1</strong>") + "</li>"
            }).compact().join("")
        },
        getSpinner: function () {
            var a = this.options, b = a.spinner;
            b == "native" && (b = a.spinner = (new d(3)).insertTo(this), b.addClass("rui-autocompleter-spinner")), b instanceof d && g.call(b, this.input, "right", "resize");
            return b
        }
    });
    i(a).on({
        focus: function (a) {
            var c = a.target;
            c && c instanceof b.Element && (c.autocompleter || c.match(o.Options.cssRule)) && (c.autocompleter || new o(c))
        }, blur: function (a) {
            var b = a.target ? a.target.autocompleter : null;
            b && b.visible() && b.hide()
        }, keydown: function (a) {
            var b = a.target ? a.target.autocompleter : null;
            if (b && b.visible()) {
                var c = ({27: "hide", 38: "prev", 40: "next", 13: "done"})[a.keyCode];
                c && (a.stop(), b[c]())
            }
        }, keyup: function (a) {
            var b = a.target ? a.target.autocompleter : null;
            b && !h([9, 27, 37, 38, 39, 40, 13]).include(a.keyCode) && b.keypressed(a)
        }
    });
    var p = a.createElement("style"), q = a.createTextNode("*.rui-dd-menu, *.rui-dd-menu li{margin:0;padding:0;border:none;background:none;list-style:none;font-weight:normal;float:none} *.rui-dd-menu{display:none;position:absolute;z-index:9999;background:white;border:1px solid #BBB;border-radius:.2em;-moz-border-radius:.2em;-webkit-border-radius:.2em;box-shadow:#DDD .2em .2em .4em;-moz-box-shadow:#DDD .2em .2em .4em;-webkit-box-shadow:#DDD .2em .2em .4em} *.rui-dd-menu li{padding:.2em .4em;border-top:none;border-bottom:none;cursor:pointer} *.rui-dd-menu li.current{background:#DDD} *.rui-dd-menu li:hover{background:#EEE}dl.rui-dd-menu dt{padding:.3em .5em;cursor:default;font-weight:bold;font-style:italic;color:#444;background:#EEE}dl.rui-dd-menu dd li{padding-left:1.5em}div.rui-spinner,div.rui-spinner div{margin:0;padding:0;border:none;background:none;list-style:none;font-weight:normal;float:none;display:inline-block; *display:inline; *zoom:1;border-radius:.12em;-moz-border-radius:.12em;-webkit-border-radius:.12em}div.rui-spinner{text-align:center;white-space:nowrap;background:#EEE;border:1px solid #DDD;height:1.2em;padding:0 .2em}div.rui-spinner div{width:.4em;height:70%;background:#BBB;margin-left:1px}div.rui-spinner div:first-child{margin-left:0}div.rui-spinner div.glowing{background:#777}div.rui-re-anchor{margin:0;padding:0;background:none;border:none;float:none;display:inline;position:absolute;z-index:9999}.rui-autocompleter{border-top-color:#DDD !important;border-top-left-radius:0 !important;border-top-right-radius:0 !important;-moz-border-radius-topleft:0 !important;-moz-border-radius-topright:0 !important;-webkit-border-top-left-radius:0 !important;-webkit-border-top-right-radius:0 !important}.rui-autocompleter-spinner{border:none !important;background:none !important;position:absolute;z-index:9999}.rui-autocompleter-spinner div{margin-top:.2em !important; *margin-top:0.1em !important}");
    p.type = "text/css", a.getElementsByTagName("head")[0].appendChild(p), p.styleSheet ? p.styleSheet.cssText = q.nodeValue : p.appendChild(q);
    return o
}(document, RightJS)