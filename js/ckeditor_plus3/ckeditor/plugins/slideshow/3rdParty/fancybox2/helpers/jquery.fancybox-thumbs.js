﻿(function (d) {
    d.fancybox.helpers.thumbs = {
        defaults: {
            width: 50, height: 50, position: "bottom", source: function (b) {
                var a;
                b.element && (a = d(b.element).find("img").attr("src"));
                !a && ("image" === b.type && b.href) && (a = b.href);
                return a
            }
        }, wrap: null, list: null, width: 0, init: function (b, a) {
            var j = this, c, f = b.width, g = b.height, k = b.source;
            c = "";
            for (var e = 0; e < a.group.length; e++)c += '<li><a style="width:' + f + "px;height:" + g + 'px;" href="javascript:jQuery.fancybox.jumpto(' + e + ');"></a></li>';
            this.wrap = d('<div id="fancybox-thumbs"></div>').addClass(b.position).appendTo("body");
            this.list = d("<ul>" + c + "</ul>").appendTo(this.wrap);
            d.each(a.group, function (b) {
                var c = k(a.group[b]);
                c && d("<img />").load(function () {
                    var a = this.width, c = this.height, h, i, e;
                    j.list && (a && c) && (h = a / f, i = c / g, e = j.list.children().eq(b).find("a"), 1 <= h && 1 <= i && (h > i ? (a = Math.floor(a / i), c = g) : (a = f, c = Math.floor(c / h))), d(this).css({
                        width: a,
                        height: c,
                        top: Math.floor(g / 2 - c / 2),
                        left: Math.floor(f / 2 - a / 2)
                    }), e.width(f).height(g), d(this).hide().appendTo(e).fadeIn(300))
                }).attr("src", c)
            });
            this.width = this.list.children().eq(0).outerWidth(!0);
            this.list.width(this.width * (a.group.length + 1)).css("left", Math.floor(0.5 * d(window).width() - (a.index * this.width + 0.5 * this.width)))
        }, beforeLoad: function (b, a) {
            2 > a.group.length ? a.helpers.thumbs = !1 : a.margin["top" === b.position ? 0 : 2] += b.height + 15
        }, afterShow: function (b, a) {
            if (this.list)this.onUpdate(b, a); else this.init(b, a);
            this.list.children().removeClass("active").eq(a.index).addClass("active")
        }, onUpdate: function (b, a) {
            this.list && this.list.stop(!0).animate({
                left: Math.floor(0.5 * d(window).width() - (a.index * this.width +
                0.5 * this.width))
            }, 150)
        }, beforeClose: function () {
            this.wrap && this.wrap.remove();
            this.list = this.wrap = null;
            this.width = 0
        }
    }
})(jQuery);