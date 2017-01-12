/**
 * RightJS-UI Selectable v2.2.3
 * http://rightjs.org/ui/selectable
 *
 * Copyright (C) 2009-2012 Nikolay Nemshilov
 */
var Selectable = RightJS.Selectable = function (a, b) {
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

    var d = {
        assignTo: function (b) {
            var c = e(function (a, b) {
                (a = f(a)) && a[a.setValue ? "setValue" : "update"](b.target.getValue())
            }).curry(b), d = e(function (a, b) {
                a = f(a), a && a.onChange && a.onChange(e(function () {
                    this.setValue(a.value())
                }).bind(b))
            }).curry(b);
            f(b) ? (c({target: this}), d(this)) : f(a).onReady(e(function () {
                c({target: this}), d(this)
            }.bind(this)));
            return this.onChange(c)
        }
    }, e = b, f = b.$, g = b.$$, h = b.$w, i = b.$E, j = b.$A, k = b.Object, l = b.isHash, m = b.isArray, n = b.isString, o = b.isNumber, p = b.defined, q = b.Input, r = b.Element, s = new c("UL", {
        include: d,
        extend: {
            version: "2.2.3",
            EVENTS: h("change select unselect disable enable hover leave show hide"),
            Options: {
                options: null,
                selected: null,
                disabled: null,
                multiple: !0,
                fxName: "slide",
                fxDuration: "short",
                update: null,
                parseIds: !1,
                limit: null,
                hCont: "&bull;"
            },
            rescan: function (b) {
                f(b || a).find(".rui-selectable").each(function (a) {
                    a instanceof s || new s(a)
                })
            }
        },
        initialize: function () {
            var a = j(arguments).compact(), b = a.pop(), c = a.pop(), d;
            if (!l(b) || b instanceof r)c = f(c || b), b = null;
            c && (c = f(c))instanceof q && (this.selectbox = d = c, b = k.merge(this.harvestOptions(c), b), c = null), this.$super("selectable", c).setOptions(b).on({
                mousedown: this._mousedown,
                touchstart: this._mousedown,
                mouseover: this._mouseover,
                mouseout: this._mouseout,
                mouseup: this._mouseup,
                touchend: this._mouseup,
                click: this._click,
                select: this._change,
                unselect: this._change
            }), this.empty() && this.build(), b = this.options;
            if (!b.multiple || this.hasClass("rui-selectable-single"))this.isSingle = !0, this.addClass("rui-selectable-single"), this.buildSingle(), b.selected === null && this.select(this.items()[0]);
            b.disabled && this.disable(b.disabled), b.selected && this.select(b.selected), b.update && this.assignTo(b.update), d && (this.assignTo(d).insertTo(d, "before"), d.wrap(i("div", {style: "position:absolute;z-index:-1;visibility:hidden;width:0;height:0;overflow:hidden"})))
        },
        setValue: function (a) {
            n(a) && (a = a.split(",").map("trim").filter(function (a) {
                return !a.blank()
            })), this.items().each("removeClass", "rui-selectable-selected");
            return this.select(a)
        },
        getValue: function () {
            if (this.isSingle) {
                var a = this.items().first("hasClass", "rui-selectable-selected");
                return a ? this.itemValue(a) : null
            }
            return this.items().filter("hasClass", "rui-selectable-selected").map(function (a) {
                return this.itemValue(a)
            }, this)
        },
        disable: function (a) {
            this.mapOrAll(a).each(function (a) {
                this.fire("disable", a.addClass("rui-selectable-disabled"))
            }, this);
            return this
        },
        enable: function (a) {
            this.mapOrAll(a).each(function (a) {
                this.fire("enable", a.removeClass("rui-selectable-disabled"))
            }, this);
            return this
        },
        disabled: function (a) {
            return this.mapOrAll(a).every("hasClass", "rui-selectable-disabled")
        },
        select: function (a) {
            var b = this.mapEnabled(a), c = "rui-selectable-selected";
            this.isSingle && b && (this.items().each("removeClass", c), b = e([b[0]]));
            if (!this.isSingle && this.options.limit) {
                var d = this.items().filter("hasClass", c), f = [];
                while (b.length && d.length + f.length < this.options.limit) {
                    var g = b.shift();
                    d.include(g) || f.push(g)
                }
                b = f
            }
            b.compact().each(function (a) {
                this.fire("select", a.addClass(c))
            }, this);
            return this
        },
        unselect: function (a) {
            var b = this.getValue();
            this.mapEnabled(a).each(function (a) {
                this.fire("unselect", a.removeClass("rui-selectable-selected"))
            }, this);
            return this
        },
        selected: function (a) {
            return this.mapEnabled(a).every("hasClass", "rui-selectable-selected")
        },
        insertTo: function (a, b) {
            this.$super.call(this.isSingle ? this.container : this, a, b);
            return this
        },
        remove: function () {
            this.$super.call(this.isSingle ? this.container : this);
            return this
        },
        fire: function (a, b) {
            b && b instanceof r ? this.$super(a, {
                item: b,
                index: this.items().indexOf(b)
            }) : this.$super.apply(this, arguments);
            return this
        },
        itemValue: function (a) {
            var b = e([a._value, a.get("id") || a.get("val")]).compact()[0];
            return b !== undefined ? this.options.parseIds ? b.match(/\d+/) : b : this.items().indexOf(a)
        },
        items: function () {
            return this.find("li")
        },
        mapOrAll: function (a) {
            var b = this.items();
            p(a) && (m(a) || (a = [a]), b = e(a).map(function (a) {
                var c = n(a) && /^\d+$/.test(a) ? parseInt(a, 10) : a, d = a;
                o(c) ? d = b[c] : n(a) && (d = b.first(function (b) {
                    return b.id == a || b.val == a
                }));
                return d
            }, this).compact());
            return b
        },
        mapEnabled: function (a) {
            return this.mapOrAll(a).filter(function (a) {
                return !a.hasClass("rui-selectable-disabled")
            }, this)
        },
        _mousedown: function (a) {
            a.stop();
            var b = a.target, c = this.items();
            if (c.include(b) && !this.disabled(b)) {
                this.isSingle ? this.select(b) : this.selected(b) ? (this.unselect(b), this._massRemove = !0) : (this.select(b), this._massSelect = !0);
                if ((a.shiftKey || a.metaKey) && this._prevItem) {
                    var d = c.indexOf(this._prevItem), e = c.indexOf(b);
                    if (d != e) {
                        if (d > e) {
                            var f = d;
                            d = e, e = d
                        }
                        for (var g = d; g < e; g++)this[this._prevItem.hasClass("rui-selectable-selected") ? "select" : "unselect"](c[g])
                    }
                }
                this._prevItem = b
            }
        },
        _mouseup: function (a) {
            a.stop(), this._massRemove = this._massSelect = !1
        },
        _mouseover: function (a) {
            var b = a.target;
            this.fire("hover", b), this.isSingle || (this._massSelect ? this.select(b) : this._massRemove && this.unselect(b))
        },
        _mouseout: function (a) {
            this.fire("leave", a.target)
        },
        _click: function (a) {
            a.stop()
        },
        _change: function () {
            "" + this.value != "" + this.getValue() && (this.value = this.getValue(), this.fire("change"))
        },
        build: function () {
            var a = this.options.options, b = e([]);
            if (m(a))a.each(function (a) {
                b.push(m(a) ? a : [a, a])
            }); else for (var c in a)b.push([a[c], c]);
            b.each(function (a) {
                var b = i("li", {val: a[1], html: a[0]});
                b._value = a[1], this.insert(b)
            }, this);
            return this
        },
        buildSingle: function () {
            this.container = i("div", {"class": "rui-selectable-container"}).insert([this.trigger = i("div", {
                html: this.options.hCont,
                "class": "rui-selectable-handle"
            }), this.display = i("ul", {"class": "rui-selectable-display"})]).onClick(e(this.toggleList).bind(this)), this.parent() && this.container.insertTo(this, "instead"), this.container.insert(this), f(a).onClick(e(this.hideList).bind(this));
            return this.onSelect("showItem").onSelect("hideList").addClass("rui-dd-menu")
        },
        toggleList: function (a) {
            a.stop();
            return this.visible() ? this.hideList() : this.showList(a)
        },
        showList: function (a) {
            a.stop(), g(".rui-selectable-single").without(this).each("hide");
            var b = this.container.dimensions(), c = this.container.position();
            this.setStyle({
                top: b.top + b.height - c.y - 1 + "px",
                left: b.left - c.x + "px",
                width: b.width + "px"
            }).show(this.options.fxName, {
                duration: this.options.fxDuration,
                onFinish: this.fire.bind(this, "show", this)
            }), this.options.fxName || this.fire("show", this)
        },
        hideList: function () {
            this.isSingle && this.visible() && (this.hide(this.options.fxName, {
                duration: this.options.fxDuration,
                onFinish: this.fire.bind(this, "hide")
            }), this.options.fxName || this.fire("hide"))
        },
        showItem: function () {
            var a = this.items().first("hasClass", "rui-selectable-selected") || this.items().first();
            this.display.html("<li>" + (a ? a.html() : "&nbsp;") + "</li>")
        },
        harvestOptions: function (a) {
            var b = (new Function("return " + a.get("data-selectable")))() || {};
            b.multiple = a._.type == "select-multiple", b.options = e([]), b.selected = e([]), b.disabled = e([]), j(a._.getElementsByTagName("OPTION")).each(function (c, d) {
                var e = c.innerHTML, f = c.getAttribute("value");
                b.options.push([e, f === null ? e : f]), c.selected && !a._.disabled && b.selected.push(d), (c.disabled || a._.disabled) && b.disabled.push(d)
            }), b.selected.empty() && (b.selected = 0);
            return b
        }
    });
    f(a).onReady(function () {
        s.rescan()
    });
    var t = a.createElement("style"), u = a.createTextNode("*.rui-dd-menu, *.rui-dd-menu li{margin:0;padding:0;border:none;background:none;list-style:none;font-weight:normal;float:none} *.rui-dd-menu{display:none;position:absolute;z-index:9999;background:white;border:1px solid #BBB;border-radius:.2em;-moz-border-radius:.2em;-webkit-border-radius:.2em;box-shadow:#DDD .2em .2em .4em;-moz-box-shadow:#DDD .2em .2em .4em;-webkit-box-shadow:#DDD .2em .2em .4em} *.rui-dd-menu li{padding:.2em .4em;border-top:none;border-bottom:none;cursor:pointer} *.rui-dd-menu li.current{background:#DDD} *.rui-dd-menu li:hover{background:#EEE}dl.rui-dd-menu dt{padding:.3em .5em;cursor:default;font-weight:bold;font-style:italic;color:#444;background:#EEE}dl.rui-dd-menu dd li{padding-left:1.5em} *.rui-selectable, *.rui-selectable li, *.rui-selectable dt, *.rui-selectable dd, *.rui-selectable ul,div.rui-selectable-container ul.rui-selectable-display,div.rui-selectable-container ul.rui-selectable-display li{margin:0;padding:0;border:none;background:none;list-style:none} *.rui-selectable{border:1px solid #CCC;border-bottom:none;display:inline-block; *display:inline; *zoom:1;min-width:10em;border-radius:.2em;-moz-border-radius:.2em;-webkit-border-radius:.2em;user-select:none;-moz-user-select:none;-webkit-user-select:none} *.rui-selectable li{padding:.3em 1em;cursor:pointer;border-bottom:1px solid #CCC} *.rui-selectable li:hover{background:#EEE} *.rui-selectable li.rui-selectable-selected{font-weight:bold;background:#DDD} *.rui-selectable li.rui-selectable-disabled, *.rui-selectable li.rui-selectable-disabled:hover{background:#CCC;color:#777;cursor:default}dl.rui-selectable dt{padding:.3em .5em;cursor:default;font-weight:bold;font-style:italic;color:#444;background:#EEE;border-bottom:1px solid #CCC}dl.rui-selectable dd li{padding-left:1.5em} *.rui-selectable-single{background:#FFF;display:none} *.rui-selectable-single li{overflow:hidden}div.rui-selectable-container{border:1px solid #CCC;border-radius:.2em;-moz-border-radius:.2em;-webkit-border-radius:.2em;display:inline-block; *display:inline; *zoom:1; *width:10em;vertical-align:middle;min-width:10em;cursor:pointer;height:1.6em;position:relative}div.rui-selectable-container div.rui-selectable-handle{font-family:Arial;position:absolute;right:0;width:0.8em;background:#DDD;text-align:center;height:100%;line-height:0.8em;font-size:200%;color:#888;border-left:1px solid #CCC}div.rui-selectable-container:hover div.rui-selectable-handle{color:#666}div.rui-selectable-container ul.rui-selectable-display{display:block;width:auto;overflow:hidden;margin-right:2em}div.rui-selectable-container ul.rui-selectable-display li{line-height:1.6em;padding:0 .5em;overflow:hidden;width:100%;white-space:nowrap}select.rui-selectable{visibility:hidden}");
    t.type = "text/css", a.getElementsByTagName("head")[0].appendChild(t), t.styleSheet ? t.styleSheet.cssText = u.nodeValue : t.appendChild(u);
    return s
}(document, RightJS)