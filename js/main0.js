var myimages=new Array()
	function preloadimages(){
	for (i=0;i<preloadimages.arguments.length;i++){
	myimages[i]=new Image()
	myimages[i].src=preloadimages.arguments[i];
	 
	if (i == preloadimages.arguments.length - 1){
		myimages[i].onload = main(); //call main when last image is loaded
	}	 
}


//Enter path of images to be preloaded inside parenthesis. Extend list as desired.
preloadimages("/images/bg.jpg","/images/bg2.jpg","/images/bg3.jpg")

function bindSiders() {
    $(".bxslider").bxSlider({
        auto: !0,
        adaptiveHeight: !0,
        pager: !0,
        easing: "ease-in-out",
        mode: "fade"
    })
}

function scrollToPlace(e) {    
    var o = $("header").height();
    return e > 201 && (e -= 1), $("html,body").animate({
        scrollTop: $(e).offset().top - o - 80
    }, 900), !1
}

function main () {

document.body.className = "show-overflow", document.getElementById("page-load-overlay").className = "hide",
jQuery(document).ready(function() {
    "undefined" != typeof window.prices && $.each(window.prices, function(e, o) {
        $("." + e).html(o)
    }), $(".plan1").colorbox({
        rel: "plan1",
        width: "75%",
        height: "75%"
    }), $(".plan2").colorbox({
        rel: "plan2",
        width: "75%",
        height: "75%"
    }), $(".plane").colorbox({
        rel: "plane",
        width: "130%",
        height: "130%"
    }), $("#main_menu").children().click(function() {
        return scrollToPlace($(this).children().first().attr("href")), !1
    }), $(".btn-success, .take-call").click(function() {
        return scrollToPlace($(this).attr("href")), !1
    }), bindSiders()
});

}
 