/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

jQuery(function ($) {
    "use strict";
    var pageBody = $("body");

    function isTouchDevice() {
        return typeof window.ontouchstart != "undefined" ? true : false
    }
    if (isTouchDevice()) pageBody.addClass("touch");
    else pageBody.addClass("notouch")
});

jQuery(function ($) {
    "use strict";
	var productCarousel = $(".product-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = 3,
	    itemsDesktop = 3,
	    itemsDesktopSmall = 2,
	    itemsTablet = 2,
	    itemsMobile = 1;
	var rtl = false;
	if ($("body").hasClass("rtl")) rtl = true;			
	if ($("body").hasClass("noresponsive")) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 3, itemsTablet = 3, itemsMobile = 2;
	else if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-9").length > 0) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;	
	$(this).owlCarousel({
		responsiveClass:true,
		responsive:{
			1550:{
				items:items
			},
			1199:{
				items:itemsDesktop
			},
			991:{
				items:itemsDesktopSmall
			},
			768:{
				items:itemsTablet
			},
			318:{
				items:itemsMobile
			}
		},
		rtl: rtl,
	    nav: false,
	    dots: false,
	    rewindNav: true,
	    navigationText: ["", ""],
	    scrollPerPage: true,
	    slideSpeed: 500,
	    beforeInit: function rtlSwapItems(el) {
	        if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
	            $(e).parent().prepend($(e))
	        })
	    },
	    afterInit: function afterInit(el) {
	        if ($("body").hasClass("rtl")) this.jumpTo(1000)
	    }
	})
	});
});
jQuery(function ($) {
    "use strict";
	var productCarousel = $(".product-carousel2"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = 4,
	    itemsDesktop = 4,
	    itemsDesktopSmall = 3,
	    itemsTablet = 2,
	    itemsMobile = 1;
	var rtl = false;
	if ($("body").hasClass("rtl")) rtl = true;		
	if ($("body").hasClass("noresponsive")) var items = 4,
	itemsDesktop = 4, itemsDesktopSmall = 3, itemsTablet = 3, itemsMobile = 2;
	else if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-9").length > 0) var items = 4,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;	
	$(this).owlCarousel({
		responsiveClass:true,
		responsive:{
			1550:{
				items:items
			},
			1199:{
				items:itemsDesktop
			},
			991:{
				items:itemsDesktopSmall
			},
			768:{
				items:itemsTablet
			},
			318:{
				items:itemsMobile
			}
		},
		rtl: rtl,
	    nav: true,
	    dots: false,
	    rewindNav: true,
	    navigationText: ["", ""],
	    scrollPerPage: true,
	    slideSpeed: 500,
	    beforeInit: function rtlSwapItems(el) {
	        if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
	            $(e).parent().prepend($(e))
	        })
	    },
	    afterInit: function afterInit(el) {
	        if ($("body").hasClass("rtl")) this.jumpTo(1000)
	    }
	})
	});
});
jQuery(function ($) {
    "use strict";
	var productCarousel = $(".destinations-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = 3,
	    itemsDesktop = 3,
	    itemsDesktopSmall = 2,
	    itemsTablet = 2,
	    itemsMobile = 1;
		
	var rtl = false;
	if ($("body").hasClass("rtl")) rtl = true;
	if ($("body").hasClass("noresponsive")) var items = 4,
	itemsDesktop = 4, itemsDesktopSmall = 3, itemsTablet = 3, itemsMobile = 2;
	else if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-9").length > 0) var items = 4,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;	
	$(this).owlCarousel({
		responsiveClass:true,
		responsive:{
			1550:{
				items:items
			},
			1199:{
				items:itemsDesktop
			},
			991:{
				items:itemsDesktopSmall
			},
			768:{
				items:itemsTablet
			},
			318:{
				items:itemsMobile
			}
		},
		rtl: rtl,
	    nav: true,
	    dots: false,
	    rewindNav: true,
	    navigationText: ["", ""],
	    scrollPerPage: true,
	    slideSpeed: 500,
	    beforeInit: function rtlSwapItems(el) {
	        if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
	            $(e).parent().prepend($(e))
	        })
	    },
	    afterInit: function afterInit(el) {
	        if ($("body").hasClass("rtl")) this.jumpTo(1000)
	    }
	})
	});
});
jQuery(function ($) {
    "use strict";
	var productCarousel = $(".thumb-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = 4,
	    itemsDesktop = 3,
	    itemsDesktopSmall = 3,
	    itemsTablet = 2,
	    itemsMobile = 1;
	var rtl = false;
	if ($("body").hasClass("rtl")) rtl = true;		
	if ($("body").hasClass("noresponsive")) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 4,
	itemsDesktop = 4, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-9").length > 0) var items = 4,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 4, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 4, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	$(this).owlCarousel({
	    responsiveClass:true,
		responsive:{
			1550:{
				items:4
			},
			1199:{
				items:itemsDesktop
			},
			991:{
				items:itemsDesktopSmall
			},
			767:{
				items:itemsTablet
			},
			318:{
				items:itemsMobile
			}
		},
		rtl: rtl,
	    nav: true,
		dots: false,
	    navigationText: ["", ""],
	    scrollPerPage: true,
	    slideSpeed: 500,
	    beforeInit: function rtlSwapItems(el) {
	        if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
	            $(e).parent().prepend($(e))
	        })
	    },
	    afterInit: function afterInit(el) {
	        if ($("body").hasClass("rtl")) this.jumpTo(1000)
	    }
	})
	});
});
jQuery(function ($) {
    "use strict";
	var productCarousel = $(".brand-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = 6,
	    itemsDesktop = 6,
	    itemsDesktopSmall = 5,
	    itemsTablet = 3,
	    itemsMobile = 2;
	var rtl = false;
	if ($("body").hasClass("rtl")) rtl = true;		
	if ($("body").hasClass("noresponsive")) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 3, itemsTablet = 3, itemsMobile = 3;
	else if ($(this).closest("section.col-md-8.col-lg-9").length > 0) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-9").length > 0) var items = 3,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-6").length > 0) var items = 2,
	itemsDesktop = 2, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-sm-12.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 3, itemsTablet = 2, itemsMobile = 1;
	else if ($(this).closest("section.col-lg-3").length > 0) var items = 1,
	itemsDesktop = 1, itemsDesktopSmall = 2, itemsTablet = 2, itemsMobile = 1;
	$(this).owlCarousel({
	    responsiveClass:true,
		responsive:{
			1550:{
				items:6
			},
			1199:{
				items:itemsDesktop
			},
			991:{
				items:itemsDesktopSmall
			},
			768:{
				items:itemsTablet
			},
			318:{
				items:itemsMobile
			}
		},
		rtl: rtl,
	    nav: false,
		dots: false,
	    navigationText: ["", ""],
	    scrollPerPage: true,
	    slideSpeed: 500,
	    beforeInit: function rtlSwapItems(el) {
	        if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
	            $(e).parent().prepend($(e))
	        })
	    },
	    afterInit: function afterInit(el) {
	        if ($("body").hasClass("rtl")) this.jumpTo(1000)
	    }
	})
	});
});

jQuery(function ($) {
    "use strict";
    $(window).resize(function () {    		
			
    });
});	    

function view_as() {
	var viewGrid = $(".view-grid"),
        viewList = $(".view-list"),
        productList = $(".products-list");
    viewGrid.click(function (e) {    	
        productList.removeClass("products-list-in-row");
        productList.addClass("products-list-in-column");
        $(this).addClass('active');
        viewList.removeClass("active");
        e.preventDefault()
    });
    viewList.click(function (e) {    	
        productList.removeClass("products-list-in-column");
        productList.addClass("products-list-in-row");
        viewGrid.removeClass("active");
        $(this).addClass('active');        
        e.preventDefault()
    })
}
jQuery(function ($) {
    "use strict";
    view_as();
});
jQuery(function ($) {
    "use strict";
    $.initQuantity = function ($control) {
        $control.each(function () {
            var $this = $(this),
                data = $this.data("inited-control"),
                $plus = $(".input-group-addon:last", $this),
                $minus = $(".input-group-addon:first", $this),
                $value = $(".form-control", $this);
            if (!data) {
                $control.attr("unselectable", "on").css({
                    "-moz-user-select": "none",
                    "-o-user-select": "none",
                    "-khtml-user-select": "none",
                    "-webkit-user-select": "none",
                    "-ms-user-select": "none",
                    "user-select": "none"
                }).bind("selectstart", function () {
                    return false
                });
                $plus.click(function () {
                    var val =
                        parseInt($value.val()) + 1;
                    $value.val(val);
                    return false
                });
                $minus.click(function () {
                    var val = parseInt($value.val()) - 1;
                    $value.val(val > 0 ? val : 1);
                    return false
                });
                $value.blur(function () {
                    var val = parseInt($value.val());
                    $value.val(val > 0 ? val : 1)
                })
            }
        })
    };
    $.initQuantity($(".quantity-control"));
    $.initSelect = function ($select) {
        $select.each(function () {
            var $this = $(this),
                data = $this.data("inited-select"),
                $value = $(".value", $this),
                $hidden = $(".input-hidden", $this),
                $items = $(".dropdown-menu li > a", $this);
            if (!data) {
                $items.click(function (e) {
                    if ($(this).closest(".sort-isotope").length >
                        0) e.preventDefault();
                    var data = $(this).attr("data-value"),
                        dataHTML = $(this).html();
                    $this.trigger("change", {
                        value: data,
                        html: dataHTML
                    });
                    $value.html(dataHTML);
                    if ($hidden.length) $hidden.val(data)
                });
                $this.data("inited-select", true)
            }
        })
    };
    $.initSelect($(".btn-select"))
});
jQuery(function ($) {
    "use strict";
	$('.icon-main-menu-topbar').click(function (event) {
		$('.menu-topbar').toggleClass('op-main-menu-topbar');
		$(this).toggleClass('active');
		event.stopPropagation();	
    });
});
jQuery(function ($) {
    "use strict";
    $("header #off-canvas-menu-toggle").click(function (e) {		
        $("body").toggleClass("off-canvas-menu-open");        
		if ($(window).width() < 991)
			$("html, body").animate({
				scrollTop: 0
			}, "300");
			
        e.preventDefault()
    });
    $("#off-canvas-menu-close").bind("click", function (e) {
        $("body").removeClass("off-canvas-menu-open");        
    }); 
	$("#off-canvas-verticalmenu-toggle").click(function (e) {		
		$('body').toggleClass("off-canvas-verticalmenu-open");
		e.preventDefault()
	});
	$("#off-canvas-verticalmenu-close").click(function (e) {		
        $("body").removeClass("off-canvas-verticalmenu-open");  
    });
});
jQuery(function ($) {
    "use strict";
	$("#jms_search_btn").click(function (e) {
        $("#jms_ajax_search").toggleClass("open");
        e.stopPropagation();
        return false;
    });
	$("#languages-box-group").click(function (e) {
        $("#languages-dropdown-box").toggle(0);
        e.stopPropagation();
        return false;
    });
	$("#currency-box-group").click(function (e) {
        $("#currency-dropdown-box").toggle(0);
        e.stopPropagation();
        return false;
    });
});
jQuery(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});
jQuery(document).ready(function() {
	jQuery('.location-banner-content').addClass("a-hidden").viewportChecker({
	    classToAdd: 'a-visible animated fadeInUp', // Class to add to the elements when they are visible
	    offset: 30    
	});
	jQuery('.owl-item').addClass("a-hidden").viewportChecker({
	    classToAdd: 'a-visible animated fadeInUp', // Class to add to the elements when they are visible
	    offset: 30    
	});
	jQuery('.service-content').addClass("a-hidden").viewportChecker({
	    classToAdd: 'a-visible animated fadeInUp', // Class to add to the elements when they are visible
	    offset: 30    
	});
	jQuery('.blog-map-section .blog-section').addClass("a-hidden").viewportChecker({
	    classToAdd: 'a-visible animated fadeInLeft', // Class to add to the elements when they are visible
	    offset: 30    
	});
	jQuery('.blog-map-section .map-section').addClass("a-hidden").viewportChecker({
	    classToAdd: 'a-visible animated fadeInRight', // Class to add to the elements when they are visible
	    offset: 30    
	});
});    
jQuery(function ($) {
    "use strict";
    $(window).scroll(function () {
    	if ($(window).scrollTop() >= 30) {
    		$("#back-to-top").stop().fadeIn(300);
    	} else if ($(window).scrollTop() < $('header').outerHeight()) {
    		$("#back-to-top").stop().fadeOut(300);
    	}
    });
});  
function quick_view() {
	$(document).on('click', '.quick-view:visible', function(e) 
	{
		e.preventDefault();		
		var url = $(this).data('link');		
		if (url.indexOf('?') != -1)
			url += '&';
		else
			url += '?';


		if (!!$.prototype.fancybox)
			$.fancybox({
				'padding':  0,
				'width':    900,
				'height':   450,
				'type':     'iframe',
				'href':     url + 'content_only=1'
			});
	});
	$(document).on('click', '#show-map:visible', function(e) 
	{		
		e.preventDefault();		
		$('.map_location_bg').fadeOut("slow");
		$('.section_maplocation').css('height',$( window ).height());
		$('.section_maplocation').css('z-index',2001);
		$('html, body').animate({scrollTop: $('.section_maplocation').offset().top}, 500);
		$('.close-map').removeClass('hide');
	});
	$(document).on('click', '.close-map:visible', function(e) 
	{		
		e.preventDefault();		
		$('.map_location_bg').fadeIn("slow");
		$('.section_maplocation').css('height','130px');
		$('.section_maplocation').css('z-index','auto');		
		$('.close-map').addClass('hide');
	});
}
jQuery(function ($) {
    "use strict";
    $("#close_social").click(function (e) {
        $("#social_block").toggleClass("ov-close"); 
		e.stopPropagation();
		return false;
    });    
});
function back_to_top() {	  
    $('.back-to-top').click(function(event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, 500);
        return false;
    })
}

jQuery(function ($) {
    "use strict";
	$('.title_verticalmenu .fa-bars').click(function (event) {
		$('.jms-vermegamenu').toggle(500);
		$(this).toggleClass('active');
		event.stopPropagation();	
	});
	$(".menu-footer").click(function (e) {	
		$("footer").toggleClass("off-canvas-menu-footer-close"); 
		$(this).toggleClass('active');		
        $(".footer-navbar").toggle();  
		$('html, body').animate({scrollTop:$(document).height()}, 'slow');
    }); 
});

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
};
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') 
			c = c.substring(1);
		if (c.indexOf(name) == 0) 
			return c.substring(name.length, c.length);
	}
	return "";
};

function eqheight_footer() 
{ 
 var max_h = 250;
 $.each( $('.footer-navbar .footer-position'), function( key, value ) {
  if($(this).height() > max_h) max_h = $(this).height();
 }); 
 $('.footer-navbar .footer-position').height(max_h);
}
function eqheight_contact() 
{ 
 var max_h = 250;
 $.each( $('.contact-box'), function( key, value ) {
  if($(this).height() > max_h) max_h = $(this).height();
 }); 
 $('.contact-box').height(max_h);
}
$(window).load(function () {      	
    $('.dropdown-menu input, .dropdown-menu label').click(function(e) {
        e.stopPropagation();
    });     
    quick_view();
    back_to_top();	
	eqheight_contact();
    $(".view-grid").addClass('active');
    
});

