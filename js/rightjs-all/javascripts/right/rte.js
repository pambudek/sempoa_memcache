/**
 * RightJS-UI RTE v2.2.1
 * http://rightjs.org/ui/rte
 *
 * Copyright (C) 2010-2011 Nikolay Nemshilov
 */
var Rte = RightJS.Rte = function (a, b, c) {
    function y(a) {
        var b = 0;
        while (a = a.previousSibling)b++;
        return b
    }

    function x(a) {
        a.collapsed = a.startContainer === a.endContainer && a.startOffset === a.endOffset
    }

    function w(a) {
        a.commonAncestorContainer = a._.parentElement()
    }

    function v(a) {
        var c = a.parentElement(), d, e, f, g, h;
        d = b.body.createTextRange(), d.moveToElementText(c), d.setEndPoint("EndToStart", a), e = d.text.length, e < c.innerText.length / 2 ? (f = 1, g = c.firstChild) : (f = -1, g = c.lastChild, d.moveToElementText(c), d.setEndPoint("StartToStart", a), e = d.text.length);
        while (g) {
            switch (g.nodeType) {
                case 3:
                    h = g.data.length;
                    if (h < e)e -= h, f === 1 ? d.moveStart("character", e) : d.moveEnd("character", -e); else return f === 1 ? {
                        node: g,
                        offset: e
                    } : {node: g, offset: h - e};
                    break;
                case 1:
                    h = g.innerText.length, f === 1 ? d.moveStart("character", h) : d.moveEnd("character", -h), e = e - h
            }
            g = f === 1 ? g.nextSibling : g.previousSibling
        }
        return {node: c, offset: 0}
    }

    function d(b, c) {
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

    var e = a, f = a.$, g = a.$$, h = a.$w, i = a.$E, j = a.$A, k = a.isArray, l = a.RegExp, m = a.Class, n = a.Element, o = a.Input, p = new d({
        extend: {
            version: "2.2.1",
            EVENTS: h("change focus blur"),
            supported: "contentEditable"in b.createElement("div"),
            Options: {
                toolbar: "small",
                autoresize: !0,
                showToolbar: !0,
                showStatus: !0,
                videoSize: "425x344",
                cssRule: "textarea[data-rte]"
            },
            Toolbars: {
                small: ["Bold Italic Underline Strike Ttext|Cut Copy Paste|Header Code Quote|Link Image Video|Source"],
                basic: ["Save Clear|Cut Copy Paste|Bold Italic Underline Strike Ttext|Left Center Right Justify", "Undo Redo|Header Code Quote|Link Image Video|Dotlist Numlist|Indent Outdent|Source"],
                extra: ["Save Clear|Cut Copy Paste|Bold Italic Underline Strike Ttext|Left Center Right Justify", "Undo Redo|Header Code Quote|Link Image Video|Subscript Superscript|Dotlist Numlist|Indent Outdent", "Format|Fontname Fontsize|Forecolor Backcolor|Source"]
            },
            Tools: {},
            Shortcuts: {
                Bold: "b",
                Italic: "i",
                Underline: "u",
                Header: "h",
                Link: "l",
                Cut: "x",
                Copy: "c",
                Paste: "v",
                Undo: "z",
                Redo: "shift+z",
                Source: "e",
                Code: "p",
                Save: "s"
            },
            Tags: {
                Bold: "b",
                Italic: "i",
                Underline: "u",
                Strike: "s",
                Ttext: "tt",
                Code: "pre",
                Quote: "blockquote",
                Header: "h2"
            },
            Formats: {
                h1: "Header 1",
                h2: "Header 2",
                h3: "Header 3",
                h4: "Header 4",
                p: "Paragraph",
                pre: "Preformatted",
                blockquote: "Blockquote",
                tt: "Typetext",
                address: "Address"
            },
            FontNames: {
                "Andale Mono": "andale mono,times",
                Arial: "arial,helvetica,sans-serif",
                "Arial Black": "arial black,avant garde",
                "Book Antiqua": "book antiqua,palatino",
                "Comic Sans MS": "comic sans ms,sans-serif",
                "Courier New": "courier new,courier",
                Georgia: "georgia,palatino",
                Helvetica: "helvetica",
                Impact: "impact,chicago",
                Symbol: "symbol",
                Tahoma: "tahoma,arial,helvetica,sans-serif",
                Terminal: "terminal,monaco",
                "Times New Roman": "times new roman,times",
                "Trebuchet MS": "trebuchet ms,geneva",
                Verdana: "verdana,geneva",
                Webdings: "webdings",
                Wingdings: "wingdings,zapf dingbats"
            },
            FontSizes: "6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 18pt 24pt 36pt",
            Videos: [[/(http:\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)/, "$1/v/$2"], [/(http:\/\/video.google.com)\/videoplay\?docid=([^&]+)/, "$1/googleplayer.swf?docId=$2"], [/(http:\/\/vimeo\.[a-z]+)\/([0-9]+).*?/, "$1/moogaloop.swf?clip_id=$2"]],
            i18n: {
                Clear: "Clear",
                Save: "Save",
                Source: "Source",
                Bold: "Bold",
                Italic: "Italic",
                Underline: "Underline",
                Strike: "Strike through",
                Ttext: "Typetext",
                Header: "Header",
                Cut: "Cut",
                Copy: "Copy",
                Paste: "Paste",
                Pastetext: "Paste as text",
                Left: "Left",
                Center: "Center",
                Right: "Right",
                Justify: "Justify",
                Undo: "Undo",
                Redo: "Redo",
                Code: "Code block",
                Quote: "Block quote",
                Link: "Add link",
                Image: "Insert image",
                Video: "Insert video",
                Dotlist: "List with dots",
                Numlist: "List with numbers",
                Indent: "Indent",
                Outdent: "Outdent",
                Forecolor: "Text color",
                Backcolor: "Background color",
                Select: "Select",
                Remove: "Remove",
                Format: "Format",
                Fontname: "Font name",
                Fontsize: "Size",
                Subscript: "Subscript",
                Superscript: "Superscript",
                UrlAddress: "URL Address"
            },
            current: null
        }, initialize: function (a, b) {
            this.$super("rte", {}).setOptions(b, a).append(this.toolbar = new p.Toolbar(this), this.editor = new p.Editor(this), this.status = new p.Status(this)), this.options.showToolbar || this.toolbar.hide(), this.options.showStatus || this.status.hide(), a && this.assignTo(a), this.undoer = new p.Undoer(this), this.selection = new p.Selection(this), this.selection.exec("styleWithCss", !1), this.status.update()
        }, setValue: function (a) {
            this.textarea && this.textarea.value(a), this.editor.update(a);
            return this
        }, getValue: function () {
            return this.editor._.innerHTML
        }, value: function (a) {
            return this[a === undefined ? "getValue" : "setValue"](a)
        }, disable: function () {
            this.disabled = !0;
            return this.addClass("rui-rte-disabled")
        }, enable: function () {
            this.disabled = !1;
            return this.removeClass("rui-rte-disabled")
        }, focus: function () {
            p.current !== this && (p.current = this, this.editor.focus());
            return this
        }, blur: function () {
            p.current = null, this.editor.blur();
            return this
        }, assignTo: function (a) {
            var b = f(a), c = b.size();
            p.supported ? (this.insertTo(b.setStyle("position:absolute;left:-9999em;"), "before"), this.editor.resize(c), this.setWidth(c.x), this.options.autoresize && this.editor.setStyle({
                minHeight: c.y + "px",
                height: "auto"
            })) : b.setStyle("visibility:visible"), this.setValue(b.value()), this.onChange(function () {
                b._.value = this.editor._.innerHTML
            }), this.textarea = b;
            return this
        }
    });
    p.Toolbar = new m(n, {
        initialize: function (a) {
            this.$super("div", {"class": "rui-rte-toolbar"}), this.rte = a, a.tools = {};
            var b = a.options, c = b.toolbar;
            e(p.Toolbars[c] || (k(c) ? c : [c])).each(function (b) {
                var c = i("div", {"class": "line"}).insertTo(this);
                e(b.split("|")).each(function (b) {
                    if (!e(b).blank()) {
                        var d = i("div", {"class": "bar"}).insertTo(c);
                        e(b.split(" ")).each(function (b) {
                            b = e(b).capitalize(), d.insert(new p.Tools[b](a))
                        })
                    }
                })
            }, this), a.tools.Undo || new p.Tools.Undo(a), a.tools.Redo || new p.Tools.Redo(a)
        }, shortcut: function (a) {
            var b = a._, c, d;
            for (c in this.rte.tools) {
                d = this.rte.tools[c];
                if (d.shortcut === b.keyCode && d.shiftKey === b.shiftKey)return d
            }
            return null
        }
    }), p.Editor = new m(n, {
        initialize: function (a) {
            this.$super(a.append('<div contenteditable="true" class="rui-rte-editor"></div>').first("div.rui-rte-editor")._), this.rte = a, this.on({
                focus: this._focus,
                blur: this._blur,
                mouseup: this._mouseup,
                keypress: this._keypress,
                keydown: this._keydown,
                keyup: this._keyup
            })
        }, update: function (a) {
            this.$super(a);
            return this
        }, focus: function () {
            this._.focus();
            return this
        }, blur: function () {
            this._.blur();
            return this
        }, removeElement: function (a) {
            if (a !== null) {
                var b = a.parentNode;
                while (a.firstChild)b.insertBefore(a.firstChild, a);
                b.removeChild(a)
            }
        }, _focus: function () {
            this.rte.selection.restore(), this.rte.status.update(), this.rte.focused = !0
        }, _blur: function () {
            this.rte.focused = !1, this.rte.status.update()
        }, _mouseup: function () {
            this._focus()
        }, _keypress: function (a) {
            this.__stopped && a.stop()
        }, _keydown: function (a) {
            var b = a._, c = !1, d;
            if (b.metaKey || b.ctrlKey)(d = this.rte.toolbar.shortcut(a)) && d.call(a), c = a.stopped;
            this.__stopped = c
        }, _keyup: function (a) {
            switch (a.keyCode) {
                case 37:
                case 38:
                case 39:
                case 40:
                    this.rte.status.update();
                    break;
                default:
                    var b = this.rte, d = this._;
                    this._timer !== !1 && c.clearTimeout(this._timer), this._timer = c.setTimeout(function () {
                        b.__old_value !== d.innerHTML && (b.__old_value = d.innerHTML, b.fire("change"))
                    }, this._delay)
            }
        }, _timer: !1, _delay: 400
    }), p.Status = new m(n, {
        initialize: function (a) {
            this.$super("div", {"class": "rui-rte-status"}), this.rte = a, this.nodes = [], this.tags = [], this.onMousedown(this._mousedown)
        }, update: function () {
            this._findNodes(), this._checkTools();
            return this.$super(this.nodes.map(function (a, b) {
                var c = a.tagName.toLowerCase();
                a.id && (c += "#" + a.id), a.className && (c += "." + a.className);
                return '<a href="" data-index="' + b + '" onclick="return false;" title="' + p.i18n.Select + '">' + c + "</a>"
            }).join(" &rsaquo; "))
        }, findElement: function (a, b) {
            if (a)for (var c = this.nodes.length - 1, d, e; c > -1; c--)if (this.nodes[c].tagName === a) {
                e = !0;
                for (d in b)b[d]instanceof l ? e &= b[d].test(this.nodes[c].getAttribute(d)) : e &= this.nodes[c].getAttribute(d) == b[d];
                if (e)return this.nodes[c]
            }
            return null
        }, _checkTools: function () {
            var a = this.rte.tools, b;
            for (b in a)a[b].check()
        }, _findNodes: function () {
            var a = this.rte.selection.element(), b = this.rte.editor._, c = this.rte._, d = [], e = [];
            this.nodes = [], this.tags = [];
            while (a && a !== c) {
                a.tagName && (d.unshift(a), e.unshift(a.tagName)), a = a.parentNode;
                if (a === b) {
                    this.nodes = d, this.tags = e;
                    break
                }
            }
        }, _mousedown: function (a) {
            var b = a.target;
            if (b._.tagName === "A") {
                a.stop();
                var c = b.get("data-index").toInt(), d = this.nodes[c];
                this.rte.selection.wrap(d)
            }
        }
    }), p.Undoer = new m({
        initialize: function (a) {
            function b() {
                this.undoer.save()
            }

            this.rte = a, this.rte.on({focus: b, change: b}), this.clear()
        }, clear: function () {
            this.stash = [], this.index = -1
        }, hasUndo: function () {
            return this.stash.length > 0 && this.index > 0
        }, hasRedo: function () {
            return this.stash.length - this.index > 1
        }, undo: function () {
            this.hasUndo() && this.set(--this.index)
        }, redo: function () {
            this.hasRedo() && this.set(++this.index)
        }, set: function (a) {
            this.stash[this.index] && (this.rte.editor.update(this.stash[this.index]), this.rte.selection.restore())
        }, save: function (a) {
            var b = a && a.tool, c = this.rte.tools, d, e, f;
            if (!b || b !== c.Undo && b !== c.Redo)this.rte.selection.store(), d = this.rte.editor._.innerHTML, e = d.replace(s, "").replace(t, ""), f = (this.stash[this.index] || "").replace(s, "").replace(t, ""), e !== f && (this.stash.length = this.index + 1, this.stash.push(d), this.index = this.stash.length - 1, c.Undo.check(), c.Redo.check()), this.rte.selection.restore()
        }
    }), p.Selection = new m({
        initialize: function (a) {
            this.rte = a
        }, range: function (a) {
            var d = c.getSelection && c.getSelection();
            if (a)d ? (d.removeAllRanges(), d.addRange(a)) : a._ && a._.select(); else {
                try {
                    a = d.getRangeAt(0)
                } catch (e) {
                    try {
                        a = b.createRange()
                    } catch (f) {
                        a = new u
                    }
                }
                return a
            }
        }, element: function () {
            var a = this.range(), b = a.commonAncestorContainer;
            a.collapsed || a.startContainer === b && a.startOffset - a.endOffset < 2 && a.startContainer.hasChildNodes() && (b = a.startContainer.childNodes[a.startOffset]), b = b && b.nodeType === 3 ? b.parentNode : b;
            return b || null
        }, wrap: function (a) {
            var b = this.range();
            b.selectNode(a), this.range(b)
        }, text: function () {
            return this.range().toString()
        }, empty: function () {
            return this.text() === ""
        }, html: function () {
            var a = this.range(), c, d;
            if (a._)return a._.htmlText;
            c = b.createElement("div"), d = a.cloneContents();
            while (d.firstChild)c.appendChild(d.firstChild);
            return c.innerHTML
        }, exec: function (a, c) {
            try {
                b.execCommand(a, !1, c)
            } catch (d) {
                a === "inserthtml" && this.range()._.pasteHTML(c)
            }
        }, store: function () {
            function c(c) {
                function j(a, b) {
                    b.nextSibling ? b.parentNode.insertBefore(a, b.nextSibling) : b.parentNode.appendChild(a)
                }

                var d = a[c + "Container"], e = a[c + "Offset"], f = b.createElement("span"), g = d.parentNode, h = d.nodeValue, i = b.createTextNode(("" + h).substr(e));
                f.setAttribute("rrte-" + c, "1"), d.nodeType === 3 ? e === 0 ? g.insertBefore(f, c === "start" && a.collapsed ? d.previousSibling : d) : e === h.length ? j(f, d) : (d.nodeValue = h.substr(0, e), j(i, d), g.insertBefore(f, i)) : d.nodeType === 1 && (e === 0 ? d.firstChild ? d.insertBefore(f, d.firstChild) : d.hasChildNodes() && d.appendChild(f) : e === d.childNodes.length ? d.appendChild(f) : d.insertBefore(f, d.childNodes[e]))
            }

            var a = this.range();
            a = {
                startContainer: a.startContainer,
                startOffset: a.startOffset,
                endContainer: a.endContainer,
                endOffset: a.endOffset,
                collapsed: a.collapsed
            }, c("end"), c("start")
        }, restore: function () {
            var a = j(this.rte.editor._.getElementsByTagName("span")), b = 0, c, d, e, f = this.range();
            for (; b < a.length; b++)c = a[b].getAttribute("rrte-start") ? "setStart" : a[b].getAttribute("rrte-end") ? "setEnd" : !1, c && (d = a[b].parentNode, f._ ? f[c](a[b]) : (e = y(a[b]), f[c](d, e)), d.removeChild(a[b]));
            this.range(f)
        }
    });
    var q = '<span rrte-start="1"></span>', r = '<span rrte-end="1"></span>', s = new l(l.escape(q), "i"), t = new l(l.escape(r), "i"), u = new m({
        collapsed: null,
        startContainer: null,
        startOffset: null,
        endContainer: null,
        endOffset: null,
        commonAncestorContainer: null,
        initialize: function () {
            this._ = b.selection.createRange();
            if (b.selection.type === "Control")this.startContainer = this.endContainer = this.commonAncestorContainer = this._(0), this.startOffset = this.endOffset = 0; else {
                var a = this._.duplicate();
                a.collapse(!0), a = v(a), this.startContainer = a.node, this.startOffset = a.offset, a = this._.duplicate(), a.collapse(!1), a = v(a), this.endContainer = a.node, this.endOffset = a.offset, w(this)
            }
            x(this)
        },
        setStart: function (a, b) {
            var c = this._.duplicate();
            c.moveToElementText(a), c.collapse(!0), this._.setEndPoint("StartToStart", c), this.startContainer = a, this.startOffset = b, this.endContainer === null && this.endOffset === null && (this.endContainer = a, this.endOffset = b), w(this), x(this)
        },
        setEnd: function (a, b) {
            var c = this._.duplicate();
            c.moveToElementText(a), c.collapse(!0), this._.setEndPoint("EndToEnd", c), this.endContainer = a, this.endOffset = b, this.startContainer === null && this.startOffset === null && (this.startContainer = a, this.startOffset = b), w(this), x(this)
        },
        selectNode: function (a) {
            this._.moveToElementText(a)
        },
        toString: function () {
            return "" + this._.text
        }
    });
    p.Tool = new m(n, {
        block: !0, blip: !1, changes: !0, shortuct: null, shiftKey: !1, initialize: function (a) {
            var b;
            for (b in p.Tools)if (p.Tools[b] === this.constructor)break;
            this.name = b, this.shortcut = this.shortcut || p.Shortcuts[b], this.$super("div", {
                html: '<div class="icon"></div>',
                "class": "tool " + b.toLowerCase(),
                title: (p.i18n[b] || b) + (this.shortcut ? " (" + this.shortcut + ")" : "")
            }), this.rte = a, a.tools[b] = this, this.shortcut = this.shortcut && this.shortcut.toLowerCase(), this.shiftKey = this.shortcut && this.shortcut.indexOf("shift") > -1, this.shortcut = this.shortcut && this.shortcut.toUpperCase().charCodeAt(this.shortcut.length - 1), this.onMousedown(function (a) {
                a.stop(), this.mousedown()
            });
            return this
        }, exec: function () {
        }, active: function () {
            return !1
        }, enabled: function () {
            return !0
        }, call: function (a) {
            this.disabled || (a && this.block && a.stop(), this.exec(), this.rte.status.update(), this.rte.fire("change", {tool: this}), this.blip && this.highlight())
        }, check: function () {
            this._.className = this._.className.replace(" disabled", ""), this.disabled = !1, this.name !== "Source" && this.rte.srcMode === !0 || !this.enabled() ? (this._.className += " disabled", this.disabled = !0) : (this._.className = this._.className.replace(" active", ""), this.active() && (this._.className += " active"))
        }, highlight: function () {
            e(this.addClass("highlight").removeClass).bind(this, "highlight").delay(100)
        }, mousedown: function () {
            this.call()
        }
    }), p.Tool.Command = new m(p.Tool, {
        command: null, value: null, exec: function () {
            this.rte.selection.exec(this.command, this.value)
        }, enabled: function () {
            try {
                return b.queryCommandEnabled(this.command)
            } catch (a) {
                return !1
            }
        }, active: function () {
            try {
                return this.value === null ? b.queryCommandState(this.command) : b.queryCommandValue(this.command) == this.value
            } catch (a) {
                return !1
            }
        }
    }), p.Tool.Format = new m(p.Tool, {
        tag: null, atts: {}, initialize: function (a) {
            this.$super(a), this.tag = (this.tag || p.Tags[this.name] || "").toUpperCase();
            return this
        }, exec: function () {
            this[this.active() ? "unformat" : "format"]()
        }, active: function () {
            return this.element() !== null
        }, element: function () {
            return this.rte.status.findElement(this.tag, this.attrs)
        }, unformat: function () {
            this._format(!1)
        }, format: function () {
            this._format(!0)
        }, _format: function (a) {
            var b = "<" + this.tag, c = "</" + this.tag + ">", d = this.rte.editor, e = this.rte.selection, f = e.range(), g = e.text(), h = this.element(), i = h && (h.textContent || h.innerText);
            for (var j in this.attrs)b += " " + j + '="' + this.attrs[j] + '"';
            b += ">", e.store(), !a && f._ && d.html(d.html().replace(new l(l.escape(q + b), "i"), b + q)), a ? d.html(d.html().replace(s, b + q).replace(t, r + c)) : h && g === i ? d.removeElement(h) : d.html(d.html().replace(s, c + q).replace(t, r + b).replace(/<([a-z]+)[^>]*?>\s*?<\/\1>/ig, "")), e.restore()
        }
    }), p.Tool.Options = {
        build: function (a) {
            this.trigger = i("div", {
                "class": "trigger",
                html: "&middot;"
            }), this.display = i("div", {"class": "display"}), this.options = i("ul", {"class": "options"}), this.addClass("with-options").append(this.display, this.options).insert(this.trigger, "top"), this.items = {};
            for (var c in a)this.items[c] = i("li").insert(a[c]), this.items[c].insertTo(this.options).value = c;
            this.items[""] = i("li", {
                "class": "remove",
                html: "--",
                title: p.i18n.Remove
            }), this.items[""].insertTo(this.options, "top").value = "", this.options.onMousedown(e(this.pick).bind(this));
            var d = e(this.options.hide).bind(this.options, null);
            f(b).on({
                mousedown: d, keydown: function (a) {
                    a.keyCode === 27 && d()
                }
            }), this.value = "", this.updateDisplay(null);
            return this
        }, pick: function (a) {
            var b = a.stop().target;
            b._.tagName !== "LI" && (b = b.parent("LI")), b.value !== undefined && (this.options.hide(), this.value = b.value, this.updateDisplay(this.value || null), this.markActive(), this.exec())
        }, mousedown: function () {
            this.disabled || (g(".rui-rte-toolbar div.with-options ul.options").without(this.options).each("hide"), this.options.hidden() && this.value !== null && this.markActive(), this.options.toggle("fade", {duration: "short"}))
        }, markActive: function () {
            for (var a in this.items)this.items[a][a === this.value ? "addClass" : "removeClass"]("active")
        }, updateDisplay: function (a) {
            this.display.update(a !== null && a in this.items ? this.items[a].text() : this._.title)
        }
    }, p.Tool.Style = new m(p.Tool.Format, {
        include: p.Tool.Options,
        tag: "span",
        style: null,
        initialize: function (a, b) {
            this.re = new l("(^|;)\\s*" + l.escape(this.style + ":") + "\\s*(.+?)\\s*(;|$)"), this.attrs = {style: this.re}, this.$super(a).build(b);
            return this
        },
        exec: function () {
            this.active() && (this.attrs = {style: this.style + ":" + this._value}, this.unformat()), this.value && (this.attrs = {style: this.style + ":" + this.value}, this.format()), this.attrs = {style: this.re}
        },
        active: function () {
            var a = this.element(), b = !1, c = null;
            a !== null && (this._value = c = this.getStyleValue(), b = !0), this.updateDisplay(c);
            return b
        },
        getStyleValue: function () {
            var a = this.element(), b = a !== null ? a.getAttribute("style") : null;
            b !== null && ((b = b.match(this.re)) !== null && (b = b[2]));
            return b
        }
    }), p.Tool.Color = new m(p.Tool.Style, {
        extend: {COLORS: e(["000000 444444 666666 999999 cccccc eeeeee f4f4f4 ffffff", "f24020 f79c33 fff84c 6af244 5ef9fd 0048f7 8950f7 ee5ff8", "e39e9b f5cba1 fee3a1 bcd3ab a6c3c8 a2c6e5 b1abd3 d0aabc d77169 f1b374 fdd675 9cbe83 7ca4ae 74aad8 8983bf bb839f cc0100 e79138 f1c332 69a84f 45818e 3d85c6 674ea7 a64d79 990000 b45f05 bf9000 38761c 134f5c 0b5394 351b75 751a47 660000 783e03 7f6000 264e13 0b333d 063763 1f124c 4c1030"])},
        initialize: function (a) {
            this.$super(a, {}).addClass("color"), this.display.clean(), p.Tool.Color.COLORS.each(function (a) {
                var b = i("li", {"class": "group"}), c = i("ul").insertTo(b), d = a.split(" "), e = 0, f, g;
                for (; e < d.length; e++) {
                    f = "#" + d[e], g = (parseInt("ffffff", 16) - parseInt(d[e], 16)).toString(16);
                    while (g.length < 6)g += "0";
                    this.items[f] = i("li", {
                        html: "&bull;",
                        style: {background: f, color: "#" + g}
                    }), this.items[f].insertTo(c).value = f
                }
                this.options.append(b)
            }, this);
            return this
        },
        getStyleValue: function () {
            var a = this.$super(), b;
            if (a !== null)if (b = /^#(\w)(\w)(\w)$/.exec(a))a = "#" + b[1] + b[1] + b[2] + b[2] + b[3] + b[3]; else if (b = /^\s*rgb\((\d+),\s*(\d+),\s*(\d+)\)\s*$/.exec(a))a = "#" + e(b.slice(1)).map(function (a) {
                a = (a - 0).toString(16);
                return a.length === 1 ? "0" + a : a
            }).join("");
            return a
        },
        updateDisplay: function (a) {
            this.display._.style.background = a === null ? "transparent" : a
        }
    }), p.Tool.Url = new m(p.Tool, {
        attr: null, exec: function (a) {
            a === undefined ? this.prompt() : a ? this[this.element() ? "url" : "create"](a) : this.rte.editor.removeElement(this.element())
        }, active: function () {
            return this.element() !== null
        }, prompt: function () {
            var a = prompt(p.i18n.UrlAddress, this.url() || "http://some.url.com");
            a !== null && this.exec(a)
        }, url: function (a) {
            if (this.element())if (a !== undefined)this.element()[this.attr] = a; else return this.element()[this.attr]
        }, create: function (a) {
            this.rte.selection.exec(this.command, a)
        }
    }), p.Tools.Bold = new m(p.Tool.Format, {}), p.Tools.Italic = new m(p.Tool.Format, {}), p.Tools.Underline = new m(p.Tool.Format, {}), p.Tools.Strike = new m(p.Tool.Format, {}), p.Tools.Cut = new m(p.Tool.Command, {
        command: "cut",
        block: !1,
        blip: !0
    }), p.Tools.Copy = new m(p.Tools.Cut, {command: "copy"}), p.Tools.Paste = new m(p.Tools.Cut, {command: "paste"}), p.Tools.Pastetext = new m(p.Tools.Command, {command: "paste"}), p.Tools.Left = new m(p.Tool.Command, {command: "justifyleft"}), p.Tools.Center = new m(p.Tool.Command, {command: "justifycenter"}), p.Tools.Right = new m(p.Tool.Command, {command: "justifyright"}), p.Tools.Justify = new m(p.Tool.Command, {command: "justifyfull"}), p.Tools.Undo = new m(p.Tool, {
        blip: !0,
        exec: function () {
            this.rte.undoer.undo()
        },
        enabled: function () {
            return this.rte.undoer.hasUndo()
        }
    }), p.Tools.Redo = new m(p.Tool, {
        blip: !0, exec: function () {
            this.rte.undoer.redo()
        }, enabled: function () {
            return this.rte.undoer.hasRedo()
        }
    }), p.Tools.Code = new m(p.Tool.Format, {}), p.Tools.Quote = new m(p.Tool.Format, {}), p.Tools.Ttext = new m(p.Tool.Format, {}), p.Tools.Header = new m(p.Tool.Format, {}), p.Tools.Link = new m(p.Tool.Url, {
        command: "createlink",
        attr: "href",
        enabled: function () {
            return !this.rte.selection.empty() || this.active()
        },
        element: function () {
            return this.rte.status.findElement("A", {})
        }
    }), p.Tools.Image = new m(p.Tool.Url, {
        command: "insertimage", attr: "src", element: function () {
            var a = this.rte.selection.element();
            return a !== null && a.tagName === "IMG" ? a : null
        }
    }), p.Tools.Video = new m(p.Tool.Url, {
        command: "inserthtml", enabled: function () {
            return !0
        }, element: function () {
            return this.rte.status.findElement("OBJECT", {})
        }, url: function (a) {
            var b = this.element() && this.element().getElementsByTagName("embed")[0];
            if (b)if (a !== undefined)b.src = this.swfUrl(a); else return b.src
        }, create: function (a) {
            var b = this.swfUrl(a), c = 'width="' + this.rte.options.videoSize.split("x")[0] + '" height="' + this.rte.options.videoSize.split("x")[1] + '"';
            this.$super("<object " + c + '><param name="src" value="' + b + '" /><embed src="' + b + '" type="application/x-shockwave-flash" ' + c + " /></object>")
        }, swfUrl: function (a) {
            return e(p.Videos).map(function (b) {
                return a.match(b[0]) ? a.replace(b[0], b[1]) : null
            }).compact()[0] || a
        }
    }), p.Tools.Dotlist = new m(p.Tool.Command, {command: "insertunorderedlist"}), p.Tools.Numlist = new m(p.Tool.Command, {command: "insertorderedlist"}), p.Tools.Indent = new m(p.Tool.Command, {command: "indent"}), p.Tools.Outdent = new m(p.Tool.Command, {command: "outdent"}), p.Tools.Forecolor = new m(p.Tool.Color, {style: "color"}), p.Tools.Backcolor = new m(p.Tool.Color, {style: "background-color"}), p.Tools.Source = new m(p.Tool, {
        source: !1,
        exec: function () {
            this[this.rte.srcMode ? "showPreview" : "showSource"](), this.rte.srcMode = !this.rte.srcMode
        },
        active: function () {
            return this.rte.srcMode
        },
        showPreview: function () {
            this.rte.editor.setStyle("visibility:visible"), this.source && (this.rte.value(this.source.value()), this.source.remove()), this.rte.editor.focus()
        },
        showSource: function () {
            this.rte.editor.setStyle("visibility:hidden;"), (this.source = this.source || i("textarea", {"class": "rui-rte-source"})).insertTo(this.rte.editor, "before").resize(this.rte.editor.size()).setValue("" + this.rte.value()).onKeydown(function (a) {
                var b = a._;
                if (b.metaKey || b.ctrlKey)b.keyCode == 69 && (a.stop(), this.exec())
            }.bind(this)).focus(), this.rte.focused = !0, this.rte.status.update();
            for (var a in this.rte.tools)this.rte.tools[a] !== this && this.rte.tools[a].addClass("disabled")
        }
    }), p.Tools.Clear = new m(p.Tool, {
        exec: function () {
            this.rte.editor.clean()
        }
    }), p.Tools.Save = new m(p.Tool, {
        initialize: function (a) {
            this.$super(a);
            if (!a.textarea || !a.textarea.form())this.disabled = !0, this.addClass("disabled")
        }, exec: function () {
            this.disabled || this.rte.textarea.form().submit()
        }, check: function () {
        }
    }), p.Tools.Format = new m(p.Tool.Format, {
        include: p.Tool.Options, initialize: function (a) {
            var b = {}, c, d, e;
            this.formats = {};
            for (c in p.Formats)if (e = c.match(/^([a-z0-9]+)(\.([a-z0-9_\-]+))?$/))d = e[1], this.formats[c] = {
                tag: d.toUpperCase(),
                attrs: {},
                match: {}
            }, e[3] && (this.formats[c].attrs["class"] = e[3], this.formats[c].match["class"] = new l("(^|\\s+)" + l.escape(e[3]) + "(\\s+|$)")), b[c] = "<" + d + ' class="' + e[3] + '">' + p.Formats[c] + "</" + d + ">";
            this.$super(a).build(b);
            return this
        }, exec: function () {
            this.active() && this.rule && (this.tag = this.formats[this.rule].tag, this.attrs = this.formats[this.rule].attrs, this.unformat()), this.value && this.formats[this.value] && (this.tag = this.formats[this.value].tag, this.attrs = this.formats[this.value].attrs, this.format())
        }, active: function () {
            var a = this.element() !== null;
            this.updateDisplay(this.rule);
            return a
        }, element: function () {
            var a, b, c = this.rte.status;
            for (a in this.formats) {
                b = c.findElement(this.formats[a].tag, this.formats[a].match);
                if (b !== null) {
                    this.rule = a;
                    return b
                }
            }
            return this.rule = null
        }
    }), p.Tools.Fontname = new m(p.Tool.Style, {
        style: "font-family", initialize: function (a) {
            var b = {}, c, d = p.FontNames;
            for (c in d)b[d[c]] = '<div style="font-family:' + d[c] + '">' + c + "</div>";
            return this.$super(a, b)
        }
    }), p.Tools.Fontsize = new m(p.Tool.Style, {
        style: "font-size", initialize: function (a) {
            var b = {}, c = 0, d = p.FontSizes.split(/\s+/);
            for (; c < d.length; c++)b[d[c]] = '<div style="font-size:' + d[c] + '">' + d[c] + "</div>";
            return this.$super(a, b)
        }
    }), p.Tools.Subscript = new m(p.Tool.Command, {command: "subscript"}), p.Tools.Superscript = new m(p.Tool.Command, {command: "superscript"}), f(b).onReady(function () {
        g(p.Options.cssRule).each("getRich")
    }), o.include({
        getRich: function (a) {
            this._.type === "textarea" && !this.rte && (this.rte = new p(this, a));
            return this.rte
        }
    });
    var z = b.createElement("style"), A = b.createTextNode("div.rui-rte,div.rui-rte-toolbar,div.rui-rte-toolbar *,div.rui-rte-editor,div.rui-rte-status,div.rui-rte-status *{margin:0;padding:0;background:none;border:none;width:auto;height:auto}textarea[data-rte]{visibility:hidden}div.rui-rte{display:inline-block; *display:inline; *zoom:1;position:relative}div.rui-rte-toolbar{padding:.15em .3em;background:#eee;border-radius:.25em .25em 0 0;-moz-border-radius:.25em .25em 0 0;-webkit-border-radius:.25em .25em 0 0;border:1px solid #ccc;border-bottom:none}div.rui-rte-toolbar div.line{display:inline-block; *display:inline; *zoom:1;margin-bottom:1px}div.rui-rte-toolbar div.bar{display:inline-block; *display:inline; *zoom:1;margin-right:2px}div.rui-rte-toolbar div.tool{display:inline-block; *display:inline; *zoom:1;margin-right:1px;vertical-align:middle;position:relative;cursor:pointer;border:1px solid #bbb;background-image:url(/images/rightjs-ui/rte.png);background-position:0px 0px;background-color:#fff;border-radius:.25em;-moz-border-radius:.25em;-webkit-border-radius:.25em}div.rui-rte-toolbar div.tool:hover{background-color:#ddd;border-color:#777}div.rui-rte-toolbar div.active{background-position:-20px 0px;background-color:#eee;border-color:#666;box-shadow:#aaa .025em .025em .5em;-moz-box-shadow:#aaa .025em .025em .5em;-webkit-box-shadow:#aaa .025em .025em .5em}div.rui-rte-toolbar div.disabled,div.rui-rte-toolbar div.disabled:hover{opacity:.4;filter:alpha(opacity=40);background-position:-40px 0px;background-color:#eee;border-color:#aaa;cursor:default}div.rui-rte-toolbar div.highlight{background-color:#BBB;border-color:#666}div.rui-rte-toolbar div.icon{height:20px;width:20px;background-image:url(/images/rightjs-ui/rte.png);background-repeat:no-repeat;background-position:20px 20px}div.rui-rte-toolbar div.save div.icon{background-position:0px -20px}div.rui-rte-toolbar div.clear div.icon{background-position:-20px -20px}div.rui-rte-toolbar div.source div.icon{background-position:-40px -20px}div.rui-rte-toolbar div.bold div.icon{background-position:0px -40px}div.rui-rte-toolbar div.italic div.icon{background-position:-20px -40px}div.rui-rte-toolbar div.underline div.icon{background-position:-40px -40px}div.rui-rte-toolbar div.strike div.icon{background-position:-60px -40px}div.rui-rte-toolbar div.cut div.icon{background-position:0px -60px}div.rui-rte-toolbar div.copy div.icon{background-position:-20px -60px}div.rui-rte-toolbar div.paste div.icon{background-position:-40px -60px}div.rui-rte-toolbar div.pastetext div.icon{background-position:-60px -60px}div.rui-rte-toolbar div.left div.icon{background-position:0px -80px}div.rui-rte-toolbar div.center div.icon{background-position:-20px -80px}div.rui-rte-toolbar div.right div.icon{background-position:-40px -80px}div.rui-rte-toolbar div.justify div.icon{background-position:-60px -80px}div.rui-rte-toolbar div.undo div.icon{background-position:0px -100px}div.rui-rte-toolbar div.redo div.icon{background-position:-20px -100px}div.rui-rte-toolbar div.quote div.icon{background-position:0px -120px}div.rui-rte-toolbar div.code div.icon{background-position:-20px -120px}div.rui-rte-toolbar div.ttext div.icon{background-position:-40px -120px}div.rui-rte-toolbar div.header div.icon{background-position:-60px -120px}div.rui-rte-toolbar div.image div.icon{background-position:0px -140px}div.rui-rte-toolbar div.link div.icon{background-position:-20px -140px}div.rui-rte-toolbar div.video div.icon{background-position:-40px -140px}div.rui-rte-toolbar div.dotlist div.icon{background-position:0px -160px}div.rui-rte-toolbar div.numlist div.icon{background-position:-20px -160px}div.rui-rte-toolbar div.indent div.icon{background-position:-40px -160px}div.rui-rte-toolbar div.outdent div.icon{background-position:-60px -160px}div.rui-rte-toolbar div.forecolor div.icon{background-position:0px -180px}div.rui-rte-toolbar div.backcolor div.icon{background-position:-20px -180px}div.rui-rte-toolbar div.symbol div.icon{background-position:0px -200px}div.rui-rte-toolbar div.subscript div.icon{background-position:-20px -200px}div.rui-rte-toolbar div.superscript div.icon{background-position:-40px -200px}div.rui-rte-toolbar div.with-options{padding-right:8px}div.rui-rte-toolbar div.with-options div.trigger{position:absolute;right:0;height:100%;width:7px;text-align:center;background:#ccc;border-left:1px solid #bbb}div.rui-rte-toolbar div.bar div:hover div.trigger,div.rui-rte-toolbar div.bar div.active div.trigger{background:#aaa;border-color:#888}div.rui-rte-toolbar div.with-options div.icon{display:none}div.rui-rte-toolbar div.with-options div.display{display:block;line-height:20px;padding:0 6px;margin:0;color:#222;font-size:12px;background:#f8f8f8}div.rui-rte-toolbar div.with-options ul.options,div.rui-rte-toolbar div.with-options ul.options li{list-style:none;margin:0;padding:0}div.rui-rte-toolbar div.with-options ul.options{display:none;cursor:default;position:absolute;margin-bottom:1px;margin-left:-1px;background:#fff;border:1px solid #aaa;border-radius:.25em;-moz-border-radius:.25em;-webkit-border-radius:.25em;box-shadow:#bbb .1em .1em .25em;-moz-box-shadow:#bbb .1em .1em .25em;-webkit-box-shadow:#bbb .1em .1em .25em}div.rui-rte-toolbar div.with-options ul.options li{padding:.2em .5em;white-space:nowrap;cursor:pointer}div.rui-rte-toolbar div.with-options ul.options li:hover{background-color:#eee}div.rui-rte-toolbar div.with-options ul.options li> *{margin:0;padding:0;border:none;position:static}div.rui-rte-toolbar div.color div.icon{display:block}div.rui-rte-toolbar div.color ul.options{padding-bottom:.5em}div.rui-rte-toolbar div.color ul.options li.group,div.rui-rte-toolbar div.color ul.options li.group:hover{background:none}div.rui-rte-toolbar div.color ul.options li.group ul{width:144px;clear:both;padding-top:.5em}div.rui-rte-toolbar div.color ul.options li.group ul li{float:left;width:16px;height:16px;line-height:16px;font-size:80%;text-align:center;text-indent:-9999em;padding:0;cursor:pointer;border:1px solid transparent}div.rui-rte-toolbar div.color ul.options li.group ul li:hover,div.rui-rte-toolbar div.color ul.options li.group ul li.active{background:none;border-color:#444;border-radius:.1em;-moz-border-radius:.1em;-webkit-border-radius:.1em}div.rui-rte-toolbar div.color ul.options li.group ul li.active{text-indent:0}div.rui-rte-toolbar div.color div.display{position:absolute;text-indent:-9999em;bottom:2px;left:3px;margin:0;padding:0;width:14px;height:4px;border-radius:.1em;-moz-border-radius:.1em;-webkit-border-radius:.1em}div.rui-rte-toolbar div.color ul.options li.group ul li.none{border-color:#444}div.rui-rte-toolbar div.color ul.options li.group ul li.label,div.rui-rte-toolbar div.color ul.options li.group ul li.label:hover{text-indent:0;border:none;margin-left:.5em;font-size:1em;cursor:default}div.rui-rte-editor{outline:none;outline:hidden;padding:.1em .3em;overflow:auto;background:white;border:1px solid #ccc}div.rui-rte-editor:focus{border-color:#aaa}div.rui-rte-editor> *:first-child{margin-top:0}div.rui-rte-editor> *:last-child{margin-bottom:0}div.rui-rte textarea.rui-rte-source{position:absolute;outline:none;resize:none}div.rui-rte-status{font-size:90%;height:1.4em;padding:0 .5em;color:#888;background:#eee;border:1px solid #ccc;border-top:none;border-radius:0 0 .25em .25em;-moz-border-radius:0 0 .25em .25em;-webkit-border-radius:0 0 .25em .25em}");
    z.type = "text/css", b.getElementsByTagName("head")[0].appendChild(z), z.styleSheet ? z.styleSheet.cssText = A.nodeValue : z.appendChild(A);
    return p
}(RightJS, document, window)