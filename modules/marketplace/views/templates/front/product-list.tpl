{*
*}
{if isset($products)}
<!-- Products list -->
ICI
	<div id="product_list" class="products-list products-list-in-column row">			
	{$i=0}	
	{foreach from=$products item=product name=products}		
		<div class="item ajax_block_product" itemscope itemtype="http://schema.org/Product">
			<div class="product-preview {if $phover == 'image_swap'}image_swap{/if}">
				<div class="preview"> 
					<a href="{$product.link|escape:'html'}" class="preview-image product_img_link image-rollover" data-id-product="{$product.id_product}" itemprop="url">
						<img class="img-responsive product-img1" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="" itemprop="image" />
					</a>			
					{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
						<div class="wrapper-label wrapper-label-sale">
							<div class="label label-sale">
								{l s='Sale'}
							</div>
						</div>
					{elseif isset($product.new) && $product.new == 1}
						<div class="wrapper-label wrapper-label-new">
							<div class="label label-new">
								{l s='New'}
							</div>
						</div>
					{/if}
					<div class="preview-button">						
						<a data-link="{$product.link|escape:'html':'UTF-8'}" class="quick-view product-btn hidden-xs" title="{l s='Quick View'}">
							<span class="fa fa-external-link"></span>
						</a>
					</div>
				</div>
				<div class="product-info">
					<h3 class="title pull-left" itemprop="name">
						<a href="{$product.link|escape:'html'}" itemprop="url">{$product.name|escape:'html':'UTF-8'|truncate:18}</a>
					</h3>		
					<div class="content_price pull-right" itemprop="offers" itemscope itemtype="http://schema.org/Offer">				
						{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}					
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<!--<span class="old price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>-->							
							{/if}
							<span class="price new" itemprop="price">
								{hook h="displayProductPriceBlock" product=$product type="before_price"}
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>	
							{hook h="displayProductPriceBlock" product=$product type="price"}
							{hook h="displayProductPriceBlock" product=$product type="unit_price"}
						{/if}
						<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
					</div>
					{hook h='displayProductListReviews' product=$product}	
					<div class="product_description_short">
						{$product.description_short|strip_tags:'UTF-8'|truncate:80:''}	
					</div>
					{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
						{if ($product.quantity > 0 OR $product.allow_oosp)}
{*
						<a class="exclusive ajax_add_to_cart_button cart-button product-btn" data-id-product="{$product.id_product}" href="{$link->getPageLink('cart')|escape:'html':'UTF-8'}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart'}">
							<span>{l s='Add To Cart'}</span>
							<span class="lnr lnr-sync"></span>
						</a>
*}							
						{else}
							<a href="#" class="disable cart-button product-btn" title="{l s='Out of Stock'}">
								<<span>{l s='Add To Cart'}</span>
							</a>
						{/if}										
					{/if}
					{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
					{hook h="displayProductPriceBlock" product=$product type="weight"}
					<div class="list_info">
						<div class="row">
							<div class="info-left col-lg-4 col-md-4">
								<div class="list-info-left">
									<h3 class="title" itemprop="name">
										<a href="{$product.link|escape:'html'}" itemprop="url">{$product.name|escape:'html':'UTF-8'}</a>
									</h3>
									<div class="content_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">				
										{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}					
											{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
												{hook h="displayProductPriceBlock" product=$product type="old_price"}
												<!--<span class="old price">
													{displayWtPrice p=$product.price_without_reduction}
												</span>-->							
											{/if}
											<span class="price new" itemprop="price">
												{hook h="displayProductPriceBlock" product=$product type="before_price"}
												{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
											</span>	
											{hook h="displayProductPriceBlock" product=$product type="price"}
											{hook h="displayProductPriceBlock" product=$product type="unit_price"}
										{/if}
										<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
									</div>
									{hook h='displayProductListReviews' product=$product}	
									{if ($product.id_product_attribute == 0 OR (isset($add_prod_display) AND ($add_prod_display == 1))) AND $product.available_for_order AND !isset($restricted_country_mode) AND $product.minimal_quantity == 1 AND $product.customizable != 2 AND !$PS_CATALOG_MODE}
										{if ($product.quantity > 0 OR $product.allow_oosp)}
										<a class="exclusive ajax_add_to_cart_button cart-button product-btn" data-id-product="{$product.id_product}" href="{$link->getPageLink('cart')|escape:'html':'UTF-8'}?qty=1&amp;id_product={$product.id_product}&amp;token={$static_token}&amp;add" title="{l s='Add to cart'}">
											<span>{l s='Book now'}</span>
											<span class="lnr lnr-sync"></span>
										</a>							
										{else}
											<a href="#" class="disable cart-button product-btn" title="{l s='Out of Stock'}">
												<<span>{l s='Add To Cart'}</span>
											</a>
										{/if}										
									{/if}
									{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
									{hook h="displayProductPriceBlock" product=$product type="weight"}
								</div>
							</div>
							<div class="col-lg-8 col-md-8">
								<div class="list-info-right">
									<!--description-->
									{if isset($product.features)}
										<div class="features">
											{foreach from=$product.features item=feature}
												<span class="feature_content feature_{strtolower($feature.name)}">
													{$feature.value}
												</span>
											{/foreach}
										</div>
									{/if}
									<div class="product_description_short">
										{$product.description_short|strip_tags:'UTF-8'|truncate:130:''}	
									</div>
								</div>
							</div>
						</div>	
					</div>	
				</div>											 
			</div>
		</div>
		{$i=$i+1}
	{/foreach}
	</div>
	<!-- /Products list -->	
{/if}
