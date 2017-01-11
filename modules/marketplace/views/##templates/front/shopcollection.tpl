{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Collection' mod='marketplace'}</span>
{/capture}

<div class="main_block">
	<div id="wk_banner_block">
		{hook h="DisplayMpcollectionbannerhook"}
	</div>
	<div class="wk_left_col">
		{include file="./shopcollection-categoty-sort.tpl"}
		{hook h="DisplayMpcollectionlefthook"}
	</div>
	<div class="dashboard_content">
		{if isset($mp_shop_product)}
			<div class="content_sortPagiBar clearfix">
	        	<div class="sortPagiBar clearfix">
	        		{include file="./shopcollection-sort.tpl"}
	        		{include file="./shopcollection-nbr.tpl"}
				</div>
	            <div class="top-pagination-content clearfix">
	            	{include file="./shopcollection-pagination.tpl"}
	            </div>
			</div>
		{/if}
		<div class="wk_product_collection">
			{if isset($mp_shop_product)}
				{foreach $mp_shop_product as $key => $product}
					{if $product.active}
						<a href="{$link->getProductLink($product.product)|addslashes}" class="product_img_link" title="{$product.product_name|escape:'html':'UTF-8'}">
							<div class="wk_collection_data" {if ($key+1)%3 == 0}style="margin-right:0px;"{/if}>
								<div class="wk_img_block">
									{if $product.image}
										<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}"/>
									{else}
										<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.lang_iso|cat : '-default', 'home_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}"/>
									{/if}
								</div>
								{if isset($quick_view) && $quick_view}
									<!-- <div class="quick-view-wrapper-mobile">
										<a class="quick-view-mobile" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
											<i class="icon-eye-open"></i>
										</a>
									</div> -->
									<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
										<span>{l s='Quick view' mod='marketplace'}</span>
									</a>
								{/if}
								<div class="wk_collecion_details">
									<div class="name"><strong>{$product.product_name|truncate:45:'...'|escape:'html':'UTF-8'}</strong></div>
									{if $product.show_price}
										<div class="price"><strong>{convertPrice price=$product.product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}</strong></div>
									{/if}
									<div>
										{if !$PS_CATALOG_MODE} <!-- if catalog mode is disabled by config (by default)-->
											{if $product.qty_available > 0 && $product.available_for_order}
												<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='marketplace'}" data-id-product="{$product.id_product|intval}"> 
													<span>{l s='Add to cart' mod='marketplace'}</span>
												</a>
											{else}
												<span class="button ajax_add_to_cart_button btn btn-default disabled">
													<span>{l s='Add to cart' mod='marketplace'}</span>
												</span>
											{/if}
										{/if}
									</div>
								</div>
							</div>
						</a>
					{else}
						<div class="alert alert-info">{l s='No item found' mod='marketplace'}</div>
					{/if}
				{/foreach}
			{else}
				<div class="alert alert-info">{l s='No item found' mod='marketplace'}</div>
			{/if}
		</div>
		<div class="content_sortPagiBar">
			<div class="bottom-pagination-content clearfix">
				{include file="./shopcollection-pagination.tpl" paginationId='bottom'}
			</div>
		</div>
		{hook h="DisplayMpcollectionfooterhook"}
	</div>
</div>

{if isset($name_shop)}
{strip}
	{addJsDef requestSortProducts=$link->getModuleLink('marketplace', 'shopcollection', ['mp_shop_name' => $name_shop])}
{/strip}
{/if}