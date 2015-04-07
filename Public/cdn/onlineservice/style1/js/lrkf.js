(function(jQuery){ 

if(jQuery.browser) return; 

jQuery.browser = {}; 
jQuery.browser.mozilla = false; 
jQuery.browser.webkit = false; 
jQuery.browser.opera = false; 
jQuery.browser.msie = false; 

var nAgt = navigator.userAgent; 
jQuery.browser.name = navigator.appName; 
jQuery.browser.fullVersion = ''+parseFloat(navigator.appVersion); 
jQuery.browser.majorVersion = parseInt(navigator.appVersion,10); 
var nameOffset,verOffset,ix; 

// In Opera, the true version is after "Opera" or after "Version" 
if ((verOffset=nAgt.indexOf("Opera"))!=-1) { 
jQuery.browser.opera = true; 
jQuery.browser.name = "Opera"; 
jQuery.browser.fullVersion = nAgt.substring(verOffset+6); 
if ((verOffset=nAgt.indexOf("Version"))!=-1) 
jQuery.browser.fullVersion = nAgt.substring(verOffset+8); 
} 
// In MSIE, the true version is after "MSIE" in userAgent 
else if ((verOffset=nAgt.indexOf("MSIE"))!=-1) { 
jQuery.browser.msie = true; 
jQuery.browser.name = "Microsoft Internet Explorer"; 
jQuery.browser.fullVersion = nAgt.substring(verOffset+5); 
} 
// In Chrome, the true version is after "Chrome" 
else if ((verOffset=nAgt.indexOf("Chrome"))!=-1) { 
jQuery.browser.webkit = true; 
jQuery.browser.name = "Chrome"; 
jQuery.browser.fullVersion = nAgt.substring(verOffset+7); 
} 
// In Safari, the true version is after "Safari" or after "Version" 
else if ((verOffset=nAgt.indexOf("Safari"))!=-1) { 
jQuery.browser.webkit = true; 
jQuery.browser.name = "Safari"; 
jQuery.browser.fullVersion = nAgt.substring(verOffset+7); 
if ((verOffset=nAgt.indexOf("Version"))!=-1) 
jQuery.browser.fullVersion = nAgt.substring(verOffset+8); 
} 
// In Firefox, the true version is after "Firefox" 
else if ((verOffset=nAgt.indexOf("Firefox"))!=-1) { 
jQuery.browser.mozilla = true; 
jQuery.browser.name = "Firefox"; 
jQuery.browser.fullVersion = nAgt.substring(verOffset+8); 
} 
// In most other browsers, "name/version" is at the end of userAgent 
else if ( (nameOffset=nAgt.lastIndexOf(' ')+1) < 
(verOffset=nAgt.lastIndexOf('/')) ) 
{ 
jQuery.browser.name = nAgt.substring(nameOffset,verOffset); 
jQuery.browser.fullVersion = nAgt.substring(verOffset+1); 
if (jQuery.browser.name.toLowerCase()==jQuery.browser.name.toUpperCase()) { 
jQuery.browser.name = navigator.appName; 
} 
} 
// trim the fullVersion string at semicolon/space if present 
if ((ix=jQuery.browser.fullVersion.indexOf(";"))!=-1) 
jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,ix); 
if ((ix=jQuery.browser.fullVersion.indexOf(" "))!=-1) 
jQuery.browser.fullVersion=jQuery.browser.fullVersion.substring(0,ix); 

jQuery.browser.majorVersion = parseInt(''+jQuery.browser.fullVersion,10); 
if (isNaN(jQuery.browser.majorVersion)) { 
jQuery.browser.fullVersion = ''+parseFloat(navigator.appVersion); 
jQuery.browser.majorVersion = parseInt(navigator.appVersion,10); 
} 
jQuery.browser.version = jQuery.browser.majorVersion; 
})(jQuery); 
(function ($) {
    $.fn.lrkf = function (options) {
        var opts = {
            position: "fixed",
            btntext: "\u5ba2\u670d\u5728\u7ebf",
            qqs: [{
                    name: "\u61d2\u4eba\u5efa\u7ad9",
                    qq: "191221838"
                }],
            tel: "",
            more: null,
            kftop: "120",
            z: "99999",
            defshow: true,
            Event: "",
            callback: function () {}
        }, $body = $("body"),
            $url = "";
        $.extend(opts, options);
        if (!$("#lrkfwarp").length > 0) {
            $body.append("<div id='lrkfwarp' class='lrkf lrkfshow' style=" + opts.position +
                "><a href='#' class='lrkf_btn lrkf_btn_hide' id='lrkf_btn' onfocus='this.blur()'>" + opts.btntext +
                "</a><div class='lrkf_box'><div class='lrkf_header'><a href='#' title='\u5173\u95ed' class='x' id='lrkf_x'></a></div><div class='lrkf_con' id='lrkf_con'><ul class='kflist'></ul></div><div class='lrkf_foot'></div></div></div>")
        }
        var $lrkfwarp = $("#lrkfwarp"),
            $lrkf_con = $("#lrkf_con"),
            $kflist = $lrkf_con.children("ul"),
            $lrkf_x = $("#lrkf_x"),
            $lrkf_btn = $("#lrkf_btn"),
            $lrkfwarp_w = $lrkfwarp.outerWidth() * 1 + 1;
        $lrkfwarp.css({
            top: opts.kftop + "px",
            "z-index": opts.z
        });
        if (!opts.defshow) {
            $lrkfwarp.removeClass("lrkfshow").css({
                right: -$lrkfwarp_w
            })
        }
        var json = {
            options: opts.qqs
        };
        json = eval(json.options);
        $.each(json, function (i, o) {
            $kflist.append("<li class=qq><a target=_blank href=http://wpa.qq.com/msgrd?v=3&uin=" + o.qq +
                "&site=qq&menu=yes><img src=http://wpa.qq.com/pa?p=2:" + o.qq + ":45>" + o.name + "</a></li>")
        });
        if (opts.tel) {
            $kflist.append("<li class=hr></li>");
            var json_tel = {
                options: opts.tel
            };
            json_tel = eval(json_tel.options);
            $.each(json_tel, function (i, o) {
                $kflist.append("<li class=tel>" + o.name + ":<b>" + o.tel + "</b></li>")
            })
        }
        if (opts.more) {
            $kflist.append("<li class=hr></li><li class=more><a href='" + opts.more +
                "'>>>\u66f4\u591a\u65b9\u5f0f</a></li>")
        }
        var $lrkfwarptop = $lrkfwarp.offset().top;
        if ($.browser.msie && $.browser.version == 6 || opts.position == "absolute") {
            $(window).scroll(function () {
                var offsetTop = $lrkfwarptop + $(window).scrollTop() + "px";
                $lrkfwarp.animate({
                    top: offsetTop
                }, {
                    duration: 600,
                    queue: false
                })
            })
        }
        $lrkf_x.click(function () {
            $lrkfwarp.hide();
            return false
        });
        if (opts.Event == "") {
            $lrkfwarp.mouseenter(function () {
                $(this).stop().animate({
                    right: 0
                })
            }).mouseleave(function () {
                $(this).stop().animate({
                    right: -$lrkfwarp_w
                })
            })
        } else {
            $lrkf_btn.on("click", function () {
                if ($lrkfwarp.hasClass("lrkfshow")) {
                    $lrkfwarp.animate({
                        right: -$lrkfwarp_w
                    }, function () {
                        $lrkfwarp.removeClass("lrkfshow")
                    })
                } else {
                    $lrkfwarp.addClass("lrkfshow").animate({
                        right: 0
                    })
                }
                return false
            })
        }
    }
})(jQuery);