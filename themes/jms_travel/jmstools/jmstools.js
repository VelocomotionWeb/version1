/*
* 2007-2014 PrestaShop
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
	$(".skin-box").click(function() {		
		var skin =  $(this).attr('data-color');		
		$('#jmsskin').val(skin);
		$(".skin-box").removeClass('active');
		$(this).addClass('active');
	});		
}); 
jQuery(function ($) {
    "use strict";
	$("#jmstools-arrow").click(function() {
		if($('#jmstools').hasClass('jmsclose')) {
			$("#jmstools").removeClass('jmsclose');
			$("#jmstools").addClass('jmsopen');		
			$('#jmstools').animate({left:'0px'}, 500);
			$('#setting-overlay').addClass('overlay-animate');
		} else {
			$("#jmstools").removeClass('jmsopen');
			$("#jmstools").addClass('jmsclose');		
			$('#jmstools').animate({left:'-270px'}, 500);
			$('#setting-overlay').removeClass('overlay-animate');
		}
	});
	$("#setting-overlay").click(function() {
		$("#jmstools").removeClass('jmsopen');
		$("#jmstools").addClass('jmsclose');		
		$('#jmstools').animate({left:'-270px'}, 500);
		$('#setting-overlay').removeClass('overlay-animate');
	});

	$('#accordion input.color').each( function(){
	 	 var input = this;	 	 
	 	 $(input).ColorPicker({	 		 
	 	 	onChange:function (hsb, hex, rgb) {
	 	 		$(input).css('backgroundColor', '#' + hex);
	 	 		$(input).val( hex );	 	 		
	 	 	}
	 	 }).bind('keyup', function(){	 		 
	 		$(input).css('backgroundColor', '#' + this.value); 
	 		$(this).ColorPickerSetColor(this.value);
	 	});
	});
	$('#accordion .clear-btn').click( function(){
		
		if($(this).hasClass('clear-img')) {			
			var $imgs_box = $(this).parent().find('.imgs-box');		
			$imgs_box.find('.img-box').removeClass('active');
			$imgs_box.find('.pick_img_hidden').val('');
		} else {
			var $parent = $(this).parent();		
			var $input  = $(".color", $parent ); 
			if( $input.val('') ) {			
				$input.attr( 'style','' );
			}	
			$input.val('');
		}
		return false;
	});	
	$(".img-box").click(function() {		
		var img =  $(this).attr('data-pattern');				
		$(this).parent().find('.pick_img_hidden').val(img);
		$(this).parent().find('.img-box').removeClass('active');		
		$(this).addClass('active');
	});		
	
	$(".info .fa-info").hover(function() {		
		$(this).next('.info-content').toggle();
	});
}); 
$(window).load(function() {
	$.uniform.restore(".noUniform");
});