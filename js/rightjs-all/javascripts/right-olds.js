/**
 * The old browsers support module for RightJS
 *
 * Released under the terms of the MIT license
 * Visit http://rightjs.org for more details
 *
 * Copyright (C) 2008-2012 Nikolay Nemshilov
 */
(function (a) {
    function b(a, b) {
        var c = document.createElement(b);
        c.setAttribute(a, ";");
        return isFunction(c[a])
    }

    a.Browser.OLD && a.Browser.IE && (window.$ = a.$ = function (b) {
        return function (c) {
            var d = b(c);
            d && d instanceof a.Element && a.isString(c) && d._.id !== c && (d = a.$(document).first("#" + c));
            return d
        }
    }(a.$));
    if (!b("onsubmit", "form")) {
        var c = function (a) {
            var b = $(a), c = b.target._, d = c.type, e = c.form, f;
            e && (f = $(e).parent()) && (a.keyCode === 13 && (d === "text" || d === "password") || a.type === "click" && (d === "submit" || d === "image")) && (b.type = "submit", b.target = $(e), f.fire(b))
        };
        document.attachEvent("onclick", c), document.attachEvent("onkeypress", c)
    }
    if (!b("onchange", "input")) {
        var d = function (a) {
            var b = a._, c = b.type;
            return c === "radio" || c === "checkbox" ? b.checked : a.getValue()
        }, e = function (a, b) {
            var c = b.parent(), e = d(b);
            c && "" + b._prev_value !== "" + e && (b._prev_value = e, a.type = "change", c.fire(a))
        }, f = function (a) {
            var b = $(a), c = b.target, d = c._.type, f = c._.tagName, g = d === "radio" || d === "checkbox";
            (b.type === "click" && (g || f === "SELECT") || b.type === "keydown" && (b.keyCode == 13 && f !== "TEXTAREA" || d === "select-multiple")) && e(b, c)
        }, g = function (a) {
            var b = $(a), c = b.target;
            c instanceof Input && e(b, c)
        };
        document.attachEvent("onclick", f), document.attachEvent("onkeydown", f), document.attachEvent("onfocusout", g), document.attachEvent("onbeforeactivate", function (a) {
            var b = $(a).target;
            b instanceof Input && (b._prev_value = d(b))
        })
    }
    a.$E("p")._.getBoundingClientRect || a.Element.include({
        position: function () {
            var a = this._, b = a.offsetTop, c = a.offsetLeft, d = a.offsetParent;
            while (d)b += d.offsetTop, c += d.offsetLeft, d = d.offsetParent;
            return {x: c, y: b}
        }
    });
    var h = !!document.querySelector, i = !h;
    a.Browser.IE8L && (i = !0);
    if (i) {
        var j = {
            " ": function (b, c) {
                return a.$A(b.getElementsByTagName(c))
            }, ">": function (a, b) {
                var c = [], d = a.firstChild;
                while (d)(b === "*" || d.tagName === b) && c.push(d), d = d.nextSibling;
                return c
            }, "+": function (a, b) {
                while (a = a.nextSibling)if (a.tagName)return b === "*" || a.tagName === b ? [a] : [];
                return []
            }, "~": function (a, b) {
                var c = [];
                while (a = a.nextSibling)(b === "*" || a.tagName === b) && c.push(a);
                return c
            }
        }, k = {
            not: function (b, c) {
                return b.nodeType === 1 && !a.$(b).match(c)
            }, checked: function (a) {
                return a.checked === !0
            }, enabled: function (a) {
                return a.disabled === !1
            }, disabled: function (a) {
                return a.disabled === !0
            }, selected: function (a) {
                return a.selected === !0
            }, empty: function (a) {
                return !a.firstChild
            }, "first-child": function (a, b) {
                while (a = a.previousSibling)if (a.nodeType === 1 && (b === null || a.nodeName === b))return !1;
                return !0
            }, "first-of-type": function (a) {
                return k["first-child"](a, a.nodeName)
            }, "last-child": function (a, b) {
                while (a = a.nextSibling)if (a.nodeType === 1 && (b === null || a.nodeName === b))return !1;
                return !0
            }, "last-of-type": function (a) {
                return k["last-child"](a, a.nodeName)
            }, "only-child": function (a, b) {
                return k["first-child"](a, b) && k["last-child"](a, b)
            }, "only-of-type": function (a) {
                return k["only-child"](a, a.nodeName)
            }, "nth-child": function (a, b, c, d) {
                var e = 1, f = b[0], g = b[1];
                while (a = d === !0 ? a.nextSibling : a.previousSibling)a.nodeType === 1 && (c === undefined || a.nodeName === c) && e++;
                return g === undefined ? e === f : (e - g) % f === 0 && (e - g) / f >= 0
            }, "nth-of-type": function (a, b) {
                return k["nth-child"](a, b, a.nodeName)
            }, "nth-last-child": function (a, b) {
                return k["nth-child"](a, b, undefined, !0)
            }, "nth-last-of-type": function (a, b) {
                return k["nth-child"](a, b, a.nodeName, !0)
            }
        }, l = /((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?/g, m = /#([\w\-_]+)/, n = /^[\w\*]+/, o = /\.([\w\-\._]+)/, p = /:([\w\-]+)(\((.+?)\))*$/, q = /\[((?:[\w\-]*:)?[\w\-]+)\s*(?:([!\^$*~|]?=)\s*((['"])([^\4]*?)\4|([^'"][^\]]*?)))?\]/, r = {}, s = function (a) {
            if (!r[a]) {
                var b, c, d, e, f, g, h, i, j, l = {}, s = a;
                while (i = s.match(q))f = f || {}, f[i[1]] = {
                    o: i[2] || "",
                    v: i[5] || i[6] || ""
                }, s = s.replace(i[0], "");
                if (i = s.match(p)) {
                    g = i[1], h = i[3] === "" ? null : i[3];
                    if (g.startsWith("nth")) {
                        h = h.toLowerCase();
                        if (h === "n")g = null, h = null; else {
                            h === "odd" && (h = "2n+1"), h === "even" && (h = "2n");
                            var t = /^([+\-]?\d*)?n([+\-]?\d*)?$/.exec(h);
                            t ? h = [t[1] === "-" ? -1 : parseInt(t[1], 10) || 1, parseInt(t[2], 10) || 0] : h = [parseInt(h, 10), undefined]
                        }
                    }
                    s = s.replace(i[0], "")
                }
                b = (s.match(m) || [1, null])[1], c = (s.match(n) || "*").toString().toUpperCase(), d = (s.match(o) || [1, ""])[1].split(".").without(""), e = d.length, l.tag = c;
                if (b || d.length || f || g)b = b || !1, f = f || !1, g = g in k ? k[g] : !1, d = e ? d : !1, l.filter = function (a) {
                    var c, i = [], j = 0, k = 0, l = a.length, m;
                    for (; j < l; j++) {
                        c = a[j];
                        if (b !== !1 && c.id !== b)continue;
                        if (d !== !1) {
                            var n = c.className.split(" "), o = n.length, p = 0;
                            m = !1;
                            for (; p < e; p++) {
                                for (var q = 0, r = !1; q < o; q++)if (d[p] === n[q]) {
                                    r = !0;
                                    break
                                }
                                if (!r) {
                                    m = !0;
                                    break
                                }
                            }
                            if (m)continue
                        }
                        if (f !== !1) {
                            var s, t, u, v;
                            m = !1;
                            for (s in f) {
                                t = s === "class" ? c.className : c.getAttribute(s) || "", u = f[s].o, v = f[s].v;
                                if (u === "" && (s === "class" || s === "lang" ? t === "" : c.getAttributeNode(s) === null) || u === "=" && t !== v || u === "*=" && t.indexOf(v) === -1 || u === "^=" && t.indexOf(v) !== 0 || u === "$=" && t.substring(t.length - v.length) !== v || u === "~=" && t.split(" ").indexOf(v) === -1 || u === "|=" && t.split("-").indexOf(v) === -1) {
                                    m = !0;
                                    break
                                }
                            }
                            if (m)continue
                        }
                        if (g !== !1)if (!g(c, h))continue;
                        i[k++] = c
                    }
                    return i
                };
                r[a] = l
            }
            return r[a]
        }, t = {}, u = function (b) {
            var c = b.join("");
            if (!t[c]) {
                for (var d = 0; d < b.length; d++)b[d][1] = s(b[d][1]);
                var e = a.$uid, f = function (a) {
                    var b = [], c = [], d;
                    for (var f = 0, g = a.length; f < g; f++)d = e(a[f]), c[d] || (b.push(a[f]), c[d] = !0);
                    return b
                }, g = function (a, b) {
                    var c = j[b[0]](a, b[1].tag);
                    return b[1].filter ? b[1].filter(c) : c
                };
                t[c] = function (a) {
                    var c, d;
                    for (var e = 0, h = b.length; e < h; e++)if (e === 0)c = g(a, b[e]); else {
                        e > 1 && (c = f(c));
                        for (var i = 0; i < c.length; i++)d = g(c[i], b[e]), d.unshift(1), d.unshift(i), c.splice.apply(c, d), i += d.length - 3
                    }
                    return b.length > 1 ? f(c) : c
                }
            }
            return t[c]
        }, v = {}, w = {}, x = function (a) {
            if (!v[a]) {
                l.lastIndex = 0;
                var b = [], c = [], d = " ", e, f;
                while (e = l.exec(a))f = e[1], f === "+" || f === ">" || f === "~" ? d = f : (c.push([d, f]), d = " "), e[2] && (b.push(u(c)), c = []);
                b.push(u(c)), v[a] = b
            }
            return v[a]
        }, y = function (a, b) {
            var c = x(b), d = [];
            for (var e = 0, f = c.length; e < f; e++)d = d.concat(c[e](a));
            return d
        }, z = {
            first: function (a) {
                return this.find(a)[0]
            }, find: function (b, c) {
                var d, e = b || "*", f = this._, g = f.tagName;
                if (h)try {
                    d = $A(f.querySelectorAll(e))
                } catch (i) {
                    d = y(f, e)
                } else d = y(f, e);
                return c === !0 ? d : d.map(a.$)
            }
        };
        a.$ext(a.Element.prototype, z), a.$ext(a.Document.prototype, z)
    }
})(RightJS)