/**
 * RightJS-UI Uploader v2.2.1
 * http://rightjs.org/ui/uploader
 *
 * Copyright (C) 2010-2011 Nikolay Nemshilov
 */
var Uploader = RightJS.Uploader = function (a) {
    function b(b, c) {
        c || (c = b, b = "DIV");
        var d = new a.Class(a.Element.Wrappers[b] || a.Element, {
            initialize: function (c, d) {
                this.key = c;
                var e = [{"class": "rui-" + c}];
                this instanceof a.Input || this instanceof a.Form || e.unshift(b), this.$super.apply(this, e), a.isString(d) && (d = a.$(d)), d instanceof a.Element && (this._ = d._, "$listeners"in d && (d.$listeners = d.$listeners), d = {}), this.setOptions(d, this);
                return a.Wrapper.Cache[a.$uid(this._)] = this
            }, setOptions: function (b, c) {
                c && (b = a.Object.merge(b, (new Function("return " + (c.get("data-" + this.key) || "{}")))())), b && a.Options.setOptions.call(this, a.Object.merge(this.options, b));
                return this
            }
        }), e = new a.Class(d, c);
        a.Observer.createShortcuts(e.prototype, e.EVENTS || a([]));
        return e
    }

    var c = a, d = a.$, e = a.$w, f = a.$E, g = a.Xhr, h = a.Form, j = a.RegExp, k = new b({
        extend: {
            version: "2.2.1",
            EVENTS: e("start update finish error"),
            Options: {
                url: "/progress",
                param: "X-Progress-ID",
                timeout: 1e3,
                round: 0,
                fxDuration: 400,
                cssRule: "[data-uploader]"
            }
        }, initialize: function (a, b) {
            this.form = a = d(a);
            var c = a.first(".rui-uploader");
            this.$super("uploader", c).setOptions(b, this.form).addClass("rui-progress-bar").insert([this.bar = this.first(".bar") || f("div", {"class": "bar"}), this.num = this.first(".num") || f("div", {"class": "num"})]), c || this.insertTo(a)
        }, start: function () {
            var a = {state: "starting"};
            return this.paint(a).prepare().request().fire("start", {data: a})
        }, update: function (a) {
            this.paint(a).fire("update", {data: a});
            switch (a.state) {
                case"starting":
                case"uploading":
                    c(this.request).bind(this).delay(this.options.timeout);
                    break;
                case"done":
                    this.fire("finish", {data: a});
                    break;
                case"error":
                    this.fire("error", {data: a})
            }
            return this
        }, paint: function (b) {
            var d = (this.percent || 0) / 100;
            switch (b.state) {
                case"starting":
                    d = 0;
                    break;
                case"done":
                    d = 1;
                    break;
                case"uploading":
                    d = b.received / (b.size || 1)
            }
            this.percent = c(d * 100).round(this.options.round), this.percent !== 0 && a.Fx && this.options.fxDuration ? (this.bar.morph({width: this.percent + "%"}, {duration: this.options.fxDuration}), c(function () {
                this.num._.innerHTML = this.percent + "%"
            }).bind(this).delay(this.options.fxDuration / 2)) : (this.bar._.style.width = this.percent + "%", this.num._.innerHTML = this.percent + "%"), this[b.state === "error" ? "addClass" : "removeClass"]("rui-progress-bar-failed");
            return this
        }, request: function () {
            g.load(this.options.url + "?" + this.options.param + "=" + this.uid, {
                evalJS: !1,
                evalJSON: !1,
                onSuccess: c(function (a) {
                    this.update((new Function("return " + a.text))())
                }).bind(this)
            });
            return this
        }, prepare: function () {
            this.uid = "";
            for (i = 0; i < 32; i++)this.uid += Math.random(0, 15).toString(16);
            var a = this.options.param, b = this.form.get("action").replace(new j("(\\?|&)" + j.escape(a) + "=[^&]*", "i"), "");
            this.form.set("action", b + (c(b).includes("?") ? "&" : "?") + a + "=" + this.uid), this.show();
            return this
        }
    }), l = h.prototype.send;
    h.include({
        send: function () {
            !this.uploader && (this.match(k.Options.cssRule) || this.first(".rui-uploader")) && (this.uploader = new k(this)), this.uploader && this.uploader.start();
            return l.apply(this, arguments)
        }
    });
    var m = document.createElement("style"), n = document.createTextNode("div.rui-progress-bar,div.rui-progress-bar *{margin:0;padding:0;border:none;background:none}div.rui-progress-bar{position:relative;height:1.4em;line-height:1.4em;width:20em;border:1px solid #999}div.rui-progress-bar,div.rui-progress-bar div.bar{border-radius:0.25em;-moz-border-radius:0.25em;-webkit-border-radius:0.25em}div.rui-progress-bar div.bar{position:absolute;left:0;top:0;width:0%;height:100%;background:#CCC;z-index:1}div.rui-progress-bar div.num{position:absolute;width:100%;height:100%;z-index:2;text-align:center}div.rui-progress-bar-failed{border-color:red;color:red;background:pink}.rui-uploader{display:none}");
    m.type = "text/css", document.getElementsByTagName("head")[0].appendChild(m), m.styleSheet ? m.styleSheet.cssText = n.nodeValue : m.appendChild(n);
    return k
}(RightJS)