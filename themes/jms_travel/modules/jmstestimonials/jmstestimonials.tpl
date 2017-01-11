{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
jQuery(function ($) {
    "use strict";
	var numbercat = {$jmsitems|escape:'html':'UTF-8'};
	var productCarousel = $(".testimonials-new-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = numbercat,
	    itemsDesktop = 1,
	    itemsDesktopSmall = 1,
	    itemsTablet = 1,
	    itemsMobile = 1;
	if ($("body").hasClass("noresponsive")) var items = 1,
	itemsDesktop = 3, itemsDesktopSmall = 2, itemsTablet = 1, itemsMobile = 1;
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
		rtl: {if $rtl=='1'}true{else}false{/if},
	    nav: true,
	    dots: true,
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
</script>

<div class="proverbs">
	<div class="pr_left_bottom testimonials-new-carousel">
		
		{foreach from=$Testimonials item=product name=myLoop}	
			<div class="item-test">
				<div class="testimonials-preview" {if $show_image == 0} class="no-image"{/if}>
					<div class="pr_bt">
					{if $show_image}
						<div class="testimonials_img" >
							<a href="#" class="preview-image product_img_link">
								<img class="img-responsive" src="{$image_url|escape:'html':'UTF-8'}resized_{$product.image|escape:'html':'UTF-8'}" />
							</a>
						</div>
					{/if}
						<div class="testimonials_info">
							<div class="testimonials_date" {if $show_date == 0} class="no-date"{/if}>
								{if $show_date}
									{$product.posttime|escape:'html':'UTF-8'}
								{/if}
							</div>
							<div class="testimonials_comment" >			
								&ldquo; {$product.comment|escape:'html':'UTF-8'|truncate:150:''} &rdquo;
							</div>
							<div class="anchor-text">
								<span class="fa fa-anchor"></span>
							</div>
							<div class="testimonials_author">
								{$product.author|escape:'html':'UTF-8'}
							</div>
							<div class="testimonials_office {if $show_office == 0}no-office{/if}">
								{if $show_office}{$product.office|escape:'html':'UTF-8'} {/if}
							</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}	
	</div>
</div>