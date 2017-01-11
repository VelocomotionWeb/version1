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
	var dealCarousel = $(".deal-carousel"),
	container = $(".container");	
	if (dealCarousel.length > 0) dealCarousel.each(function () {
	var items = 1,
	    itemsDesktop = 1,
	    itemsDesktopSmall = 1,
	    itemsTablet = 1,
	    itemsMobile = 1;
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
</script>
<div class="deal-carousel">
	{foreach from=$products item=product key=k name=jmsdeals}
		<div class="item ajax_block_product animation">
			<div class="product-preview {if $phover == 'image_swap'}image_swap{/if}">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6">
						<div class="product-info">							
							<h3 class="title"><a href="{$product.link|escape:'html'}">{$product.name|escape:'html':'UTF-8'}</a></h3>								
							{hook h='displayProductListReviews' product=$product}
							<div class="product_description_short">
								{$product.description_short|strip_tags:'UTF-8'|truncate:200:''}	
							</div>
							{if isset($product.features)}
								<div class="features">
									<em class="fa fa-clock-o"></em>
									{foreach from=$product.features item=feature}
										<span class="feature_content">
											{$feature.value}
										</span>
									{/foreach}
								</div>
							{/if}
							<div class="price-product-home">
								{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}								
								<!--<span class="old price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>-->
								{/if}
								{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<span class="price new ">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{else}{/if}	
								{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
								
									{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
										{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
											{hook h="displayProductPriceBlock" product=$product type="old_price"}
											{if $product.specific_prices.reduction_type == 'percentage'}
											<span class="percentage-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
												-{$product.specific_prices.reduction * 100}%
											</span>
											{/if}
										{/if}
									{/if}
								{/if}
							</div>
							<div class="countdown" id="countdown-{$deals[$k].id_deal|escape:'html'}">{$deals[$k].expire_time|escape:'html'}</div>		
							{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
								{if ($product.quantity > 0 OR $product.allow_oosp)}
								<a class="ajax_add_to_cart_button product-btn cart-button" rel="ajax_id_product_{$product.id_product}" href="{$link->getPageLink('cart')|escape:'html'}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart' mod='jmsdeals'}">
									<span>{l s='Add To Cart' mod='jmsdeals'}</span>
								</a>
								{else}
								<a href="#" class="disable product-btn cart-button" title="{l s='Add to cart' mod='jmsdeals'}">
									<span>{l s='Add To Cart' mod='jmsdeals'}</span>
								</a>
								{/if}																														
							{/if}				
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6">
						<div class="preview"> 
							<a href="{$product.link|escape:'html'}" class="preview-image product_img_link image-rollover" data-id-product="{$product.id_product}" itemprop="url">
								<img class="img-responsive product-img1" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox_default')|escape:'html'}" alt=""  itemprop="image" />
							</a>									
							{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<div class="wrapper-label wrapper-label-sale">
									<div class="label label-sale">
										{l s='Sale' mod='jmsdeals'}
									</div>
								</div>
							{elseif isset($product.new) && $product.new == 1}
								<div class="wrapper-label wrapper-label-new">
									<div class="label label-new">
										{l s='New' mod='jmsdeals'}
									</div>
								</div>									
							{/if}	
							<div class="preview-button">								
								<a data-link="{$product.link|escape:'html'}" class="quick-view product-btn hidden-xs" title="{l s='Quick View' mod='jmsdeals'}">
									<i class="fa fa-external-link"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	{/foreach}
</div>