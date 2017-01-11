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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
jQuery(function ($) {
    "use strict";
	var productCarousel = $(".gallery-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = {$NUMBER_ITEMS|escape:'html'},
	    itemsDesktop = {$NUMBER_ITEMS|escape:'html'},
	    itemsDesktopSmall = 4,
	    itemsTablet = 2,
	    itemsMobile = 1;
	if ($("body").hasClass("noresponsive")) var items = {$NUMBER_ITEMS|escape:'html'},
	itemsDesktop = {$NUMBER_ITEMS|escape:'html'}, itemsDesktopSmall = 4, itemsTablet = 2, itemsMobile = 1;
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
		rtl: false,
	    nav: Boolean({$navigation|escape:''}),
	    dots: Boolean({$pagination|escape:''}),
		autoPlay: Boolean({$autoPlay|escape:''}),
	    rewindNav: Boolean({$rewindNav|escape:''}),
	    navigationText: ["", ""],
	    scrollPerPage: Boolean({$scrollPerPage|escape:''}),
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
$(document).ready(function() {

	/* Apply fancybox to multiple items */

	$("a.grouped_elements").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false
	});

});
</script>
{if $CONF_ACTIVE}
<div class="gallery-carousel">
	{foreach from=$jmsgallerys item=image name=jmsgallery }
		<div class="image-item">
			<div class="image-image">
				{if $image.image && $active}
					<img src="{$root_url|escape:'html'}modules/jmsgallery/views/img/resized_{$image.image|escape:''}" alt="{$image.title|escape:'html'}" class="img-responsive" />
				{elseif $image.image}
					<img src="{$root_url|escape:'html'}modules/jmsgallery/views/img/{$image.image|escape:''}" alt="{$image.title}" class="img-responsive"/>
				{/if}
			</div>
			<a class="grouped_elements" rel="group1" href="{$root_url|escape:'html'}modules/jmsgallery/views/img/{$image.image|escape:''}"	title="{$image.title}">
				<span class="image-info">
					<span class="gallery-info">
						<span class="gallery-content">
							<span class="title">
								<h3>
									{$image.title|escape:'html'}
								</h3>
							</span>
							<span class="image-description">
								<span>{$image.description|escape:'html'}</span>
							</span>
						</span>
					</span>
				</span>
			</a>
		</div>
	{/foreach}
</div>
{/if}