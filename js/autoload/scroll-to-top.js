$("#to_top").hide();
$(function () {$(window).scroll(function () {if ($(this).scrollTop() > 400) {$("#to_top").fadeIn();} else {$("#to_top").fadeOut(); } }); $("#to_top").click(function() {$("body,html").animate({scrollTop: 0}, 800); return false; }); });