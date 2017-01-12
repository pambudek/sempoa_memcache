/**
 * RubyOnRails Support Module v2.3.2
 * http://rightjs.org/plugins/rails
 *
 * Copyright (C) 2009-2012 Nikolay Nemshilov
 */
(function (a, b, c) {
    function q() {
        var a, b;
        a = f("meta[name=csrf-param]")[0], b = f("meta[name=csrf-token]")[0], a = a && a.get("content"), b = b && b.get("content");
        if (a && b)return [a, b]
    }

    function p(a, c) {
        var d = c.get("href"), e = c.get("data-method"), f = c.get("data-remote"), i = q();
        if (!k(a, c)) {
            (e || f) && a.stop();
            if (f)h.load(d, l(c, {
                method: e || "get",
                spinner: c.get("data-spinner"),
                params: (new Function('return {"' + i[0] + '": "' + i[1] + '"}'))()
            })); else if (e) {
                var j = g("form", {action: d, method: "post"});
                i && j.insert('<input type="hidden" name="' + i[0] + '" value="' + i[1] + '" />'), j.insert('<input type="hidden" name="_method" value="' + e + '"/>').insertTo(b.body).submit(), m(c)
            }
        }
    }

    function o(a) {
        return a.has("data-disable-with") ? d([a]) : a.find("*[data-disable-with]")
    }

    function n(a) {
        o(a).each(function (a) {
            if (a.__disable_with_html !== undefined) {
                var b = a instanceof j ? "value" : "html";
                a[b](a.__disable_with_html), delete a.__disable_with_html
            }
        })
    }

    function m(a) {
        o(a).each(function (a) {
            var b = a instanceof j ? "value" : "html";
            a.__disable_with_html = a[b](), a[b](a.get("data-disable-with"))
        })
    }

    function l(a, b) {
        return i.merge({
            onCreate: function () {
                m(a), a.fire("ajax:loading", {xhr: this})
            }, onComplete: function () {
                n(a), a.fire("ajax:complete", {xhr: this})
            }, onSuccess: function () {
                a.fire("ajax:success", {xhr: this})
            }, onFailure: function () {
                a.fire("ajax:failure", {xhr: this})
            }
        }, b)
    }

    function k(a, b) {
        var c = b.get("data-confirm");
        if (c && !confirm(c)) {
            a.stop();
            return !0
        }
    }

    var d = c, e = c.$, f = c.$$, g = c.$E, h = c.Xhr, i = c.Object, j = c.Input;
    c.Rails = {version: "2.3.2"}, d([c.String.prototype, c.Array.prototype, c.Function.prototype, c.Object, c.Options, c.Observer, c.Observer.prototype, c.Window.prototype, c.Document.prototype]).each(function (a) {
        for (var b in a)try {
            if (/[A-Z]/.test(b) && typeof a[b] === "function") {
                var c = d(b).underscored();
                if (a[c] === null || a[c] === undefined)a[c] = a[b]
            }
        } catch (e) {
        }
    }), d([c.Element, c.Event, c.Form, c.Input]).each(function (a) {
        if (a) {
            var b = {}, c = a.prototype;
            for (var e in c)/[A-Z]/.test(e) && typeof c[e] === "function" && (a.prototype[d(e).underscored()] = c[e])
        }
    }), c.$alias(c.String.prototype, {
        index_of: "indexOf",
        last_index_of: "lastIndexOf",
        to_f: "toFloat",
        to_i: "toInt",
        gsub: "replace",
        downcase: "toLowerCase",
        upcase: "toUpperCase",
        index: "indexOf",
        rindex: "lastIndexOf",
        strip: "trim"
    }), c.$alias(c.Array.prototype, {
        collect: "map",
        detect: "filter",
        index_of: "indexOf",
        last_index_of: "lastIndexOf",
        index: "indexOf",
        rindex: "lastIndexOf"
    }), e(b).on({
        ready: function () {
            var a = q(), b = 0, d, e = ["InEdit", "Rater", "Sortable"];
            if (a)for (; b < e.length; b++)e[b]in c && (d = c[e[b]].Options.Xhr, c.isHash(d) && (d.params = i.merge(d.params, {}), d.params[a[0]] = a[1]))
        }, click: function (a) {
            var b = a.find("a");
            b && p(a, b)
        }, submit: function (a) {
            var b = a.target;
            b.has("data-remote") && !k(a, b) && (a.stop(), b.send(l(b, {spinner: b.get("data-spinner") || b.first(".spinner")})))
        }
    });
    var r = {
        Options: {
            format: "js",
            flashId: "flashes",
            flashHideFx: "slide",
            flashHideDelay: 3200,
            highlightUpdates: !0,
            removeFx: "fade",
            insertFx: "fade",
            insertPosition: "bottom",
            linkToAjaxEdit: ".ajax_edit",
            linkToAjaxDelete: ".ajax_delete",
            rescanWithScopes: !0
        }, update_flash: function (a) {
            var b = e(this.Options.flashId);
            b && this.replace(b, a).hide_flash();
            return this
        }, hide_flash: function () {
            if (this.Options.flashHideDelay > -1) {
                var a = e(this.Options.flashId);
                a && a.visible() && a.hide.bind(a, this.Options.flashHideFx).delay(this.Options.flashHideDelay)
            }
            return this
        }, highlight: function (a) {
            e(a) && this.Options.highlightUpdates && e(a).highlight();
            return this
        }, insert: function (a, b, c) {
            var d = c || this.Options.insertPosition, f, g = e(a).insert(b, d);
            switch (d) {
                case"bottom":
                    f = g.children().last();
                    break;
                case"top":
                    f = g.first();
                    break;
                case"before":
                    f = g.prev();
                    break;
                case"after":
                    f = g.next()
            }
            f && this.Options.insertFx ? f.hide().show(this.Options.insertFx, {onFinish: this.highlight.bind(this, f)}) : this.highlight(f);
            return this.rescan(a)
        }, replace: function (a, b) {
            e(a).replace(b);
            return this.highlight(a).rescan(a)
        }, remove: function (a) {
            e(a) && e(a).remove(this.Options.removeFx)
        }, remotize_form: function (a) {
            var b = e(a);
            b && (b.remotize().enable()._.action += "." + this.Options.format);
            return this
        }, replace_form: function (a, b) {
            var c = e(a);
            c && (c.replace(b), this.remotize_form(a));
            return this.rescan(a)
        }, show_form_for: function (a, b) {
            e(a).find("form").each("remove"), e(a).insert(b);
            return this.remotize_form(e(a).first("form")).rescan(a)
        }, process_click: function (a) {
            var b;
            (b = a.find("a" + this.Options.linkToAjaxEdit)) ? (a.stop(), h.load(b.get("href") + "." + this.Options.format)) : (b = a.find("a" + this.Options.linkToAjaxDelete)) && b.has("onclick") && (a.stop(), (new Function("return " + b.onclick.toString().replace(".submit", ".send")))().call(b))
        }, rescan: function (b) {
            $w("Draggable Droppable Tabs Tags Selectable").each(function (c) {
                c in a && a[c].rescan(this.Options.rescanWithScopes ? b : null)
            }, this);
            return this
        }
    };
    e(b).on({
        ready: function () {
            r.hide_flash()
        }, click: function (a) {
            r.process_click(a)
        }
    }), a.RR = r
})(window, document, RightJS)