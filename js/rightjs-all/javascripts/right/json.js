/**
 * JSON support module v2.2.1
 * http://rightjs.org/plugins/json
 *
 * Copyright (C) 2009-2011 Nikolay Nemshilov
 */
var JSON = function (a, b) {
    function h(a) {
        return (a < 10 ? "0" : "") + a
    }

    function g(a) {
        return a.replace(f, function (a) {
            return e[a] || "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
        })
    }

    a.JSON = b.JSON || {}, a.JSON.version = "2.2.1";
    var c = a.JSON, d = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g, e = {
        "\b": "\\b",
        "\t": "\\t",
        "\n": "\\n",
        "\f": "\\f",
        "\r": "\\r",
        '"': '\\"',
        "\\": "\\\\"
    }, f = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
    "stringify"in c || (c.stringify = function (a) {
        if (a === null)return "null";
        if (a.toJSON)return a.toJSON();
        switch (typeof a) {
            case"boolean":
                return String(a);
            case"number":
                return String(a + 0);
            case"string":
                return '"' + g(a) + '"';
            case"object":
                if (a instanceof Array)return "[" + a.map(c.stringify).join(",") + "]";
                if (a instanceof Date)return '"' + a.getUTCFullYear() + "-" + h(a.getUTCMonth() + 1) + "-" + h(a.getUTCDate()) + "T" + h(a.getUTCHours()) + ":" + h(a.getUTCMinutes()) + ":" + h(a.getUTCSeconds()) + "." + h(a.getMilliseconds()) + 'Z"';
                var b = [];
                for (var d in a)b.push(c.encode(d) + ":" + c.encode(a[d]));
                return "{" + b.join(",") + "}"
        }
    }), "parse"in c || (c.parse = function (a) {
        if (isString(a) && a) {
            a = a.replace(d, function (a) {
                return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4)
            });
            if (/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, "")))return (new Function("return " + a))()
        }
        throw"JSON parse error: " + a
    }), a.$alias(c, {encode: "stringify", decode: "parse"});
    if (a.Cookie) {
        var i = a.Cookie.prototype.set, j = a.Cookie.prototype.get;
        a.Cookie.include({
            set: function (a) {
                return i.call(this, c.stringify(a))
            }, get: function () {
                return c.parse(j.call(this) || "null")
            }
        })
    }
    return c
}(RightJS, window)