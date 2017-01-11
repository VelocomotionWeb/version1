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
	    items: items,
	    itemsDesktop: [1199, itemsDesktop],
	    itemsDesktopSmall: [980, itemsDesktopSmall],
	    itemsTablet: [768, itemsTablet],
	    itemsTabletSmall: false,
	    itemsMobile: [480, itemsMobile],
		autoPlay: Boolean({$autoPlay|escape:''}),
	    navigation: Boolean({$navigation|escape:''}),
	    pagination: Boolean({$pagination|escape:''}),
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
				<a class="grouped_elements" rel="group1" href="{$root_url|escape:'html'}modules/jmsgallery/views/img/{$image.image|escape:''}"	title="{$image.title}">
				{if $image.image && $active}
					<img src="{$root_url|escape:'html'}modules/jmsgallery/views/img/resized_{$image.image|escape:''}" alt="{$image.title|escape:'html'}" class="img-thumbnail" />
				{elseif $image.image}
					<img src="{$root_url|escape:'html'}modules/jmsgallery/views/img/{$image.image|escape:''}" alt="{$image.title}"/>
				{/if}
				</a>
			</div>

		</div>
	{/foreach}
</div>
{/if}