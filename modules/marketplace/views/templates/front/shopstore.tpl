<style type="text/css">
#left_column{
	display:none;
}
</style>

{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Shop' mod='marketplace'}</span>
{/capture}

<div class="main_block">
<div class="wk_left_sidebar">
	<div style="margin-bottom:10px;">
		{if $no_shop_img == 0}
			<img class="left_img" src="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/shop_img/{$seller_id|escape:'html':'UTF-8'}-{$name_shop|escape:'html':'UTF-8'}.jpg" alt="{l s='Seller Image' mod='marketplace'}"/>
		{else}
			<img class="left_img" src="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/shop_img/defaultshopimage.jpg" alt="Seller Image"/>
		{/if}
	</div>
	<div style="float:left;width:100%;">
	<a class="button btn btn-default button-medium" href="{$link->getModuleLink('marketplace','shopcollection',['mp_shop_name'=>{$shop_link_rewrite|escape:'html':'UTF-8'}])|escape:'html':'UTF-8'}">
		<span>{l s='View Collection' mod='marketplace'}</span>
	</a>
	</div>
	{hook h='DisplayMpshoplefthook'}
</div>
<div class="dashboard_content">
	<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Shop' mod='marketplace'}</span>
	</div>
	<div class="wk_right_col">
		{if $MP_SELLER_DETAILS_ACCESS_11 || $MP_SELLER_DETAILS_ACCESS_2}
			<div class="box-account">
				<div class="box-head">
					<h2>
                    	<a href="/module/marketplace/list?mp_shop_name={$shop_link_rewrite|escape:'html':'UTF-8'}&city={$city_name}&latitude={$latitude}&longitude={$longitude}&distance=0.1" target="_blank">{l s='Recent Products' mod='marketplace'}
                    	</a>
                    </h2>
					<div class="wk_border_line"></div>
				</div>
				<div class="box-content" style="background-color:#F6F6F6;border-bottom: 3px solid #D5D3D4;">
					{if $MP_SELLER_DETAILS_ACCESS_2}
						<div class="seller_name">{$name_shop|escape:'html':'UTF-8'}</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_11}
						<div>{$mp_shop_details.about_us}</div>
					{/if}
					{hook h="displayExtraShopDetails"}
				</div>
			</div>
		{/if}
        {*
        {$mp_shop_details|var_dump} {$mp_seller_info|var_dump}
        *}
            <iframe
              width="100%"
              height="450"
              frameborder="0" style="border:0"
              src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDAAx0cPh2MP4DbPzmVs_V2HslsSAoUSaQ&q={$mp_seller_info.address}" allowfullscreen>
            </iframe>

		{if $MP_SHOW_SELLER_DETAILS}
			<div class="box-account">
				<div class="box-head">
					<h2>{l s='About Seller' mod='marketplace'}</h2>
					<div class="wk_border_line"></div>
				</div>
				<div class="box-content" style="background-color:#F6F6F6;border-bottom: 3px solid #D5D3D4;">
					<div class="wk-left-label">
						{if isset($mp_seller_info)}
							{if $MP_SELLER_DETAILS_ACCESS_1}
								<div class="wk_row">
									<label class="wk-person-icon">{l s='Seller Name -' mod='marketplace'}</label>
									<span>{$mp_seller_info.seller_name|escape:'html':'UTF-8'}</span>
								</div>
								<div class="wk_row">
									<label class="wk-address-icon">{l s='Seller address -' mod='marketplace'}</label>
									<span class="">{$mp_seller_info.address|escape:'html':'UTF-8'}</span>
								</div>
							{/if}
							{if $MP_SELLER_DETAILS_ACCESS_3}
								<div class="wk_row">
									<label class="wk-mail-icon">{l s='Business Email -' mod='marketplace'}</label>
									<span>{$mp_seller_info.business_email|escape:'html':'UTF-8'}</span>
								</div>
							{/if}
							{if $MP_SELLER_DETAILS_ACCESS_12}
								{*
                                <div class="wk_row">
									<label class="wk-rating-icon">{l s='Seller rating -' mod='marketplace'}</label>
									<span class="avg_rating"></span>
								</div>
                                *}
							{/if}
						{/if}
					</div>
				</div>
			</div>
		{/if}
		{*<div class="box-account">
			<div class="box-head">
				
				
			</div>
			<div class="box-content">
				{if isset($mp_shop_product)}
					<div id="product-slider_block_center" class="wk-product-slider">
						<ul class="mp-prod-slider {if $mp_shop_product|@count > 3}mp-bx-slider{/if}">
							{foreach $mp_shop_product as $key => $product}
								<a href="{$link->getProductLink($product.product)|addslashes}" class="product_img_link" title="{$product.name|escape:'html':'UTF-8'}">
									<li {if $mp_shop_product|@count <= 3}class="wk-product-out-slider"{/if} {if $key == 2}style="margin-right:0;"{/if}>
										<div class="wk-slider-product-img">
											{if $product.image}
												<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}">
											{else}
												<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.lang_iso|cat : '-default', 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}">
											{/if}
										</div>
										<div class="wk-slider-product-info">
											<div style="margin-bottom:5px;">{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}</div>
											{if $product.show_price}
												<div style="font-weight:bold;">{convertPrice price=$product.product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}</div>
											{/if}
										</div>
									</li>
								</a>
							{/foreach}
						</ul> 
					</div>
				{else}
					<div class="alert alert-info">
						{l s='No recent products' mod='marketplace'}
					</div>
				{/if}
			</div>*}
		</div>
		{hook h='DisplayMpshopcontentbottomhook'}
	</div>
</div>
</div>			
<script type="text/javascript">
{if isset($avg_rating)}
	$(document).ready(function(){
		$('.avg_rating').raty({						
			path: '{$modules_dir|escape:'html':'UTF-8'}marketplace/libs/rateit/lib/img',
			score: {$avg_rating|escape:'html':'UTF-8'},
			readOnly: true,
		});
	});
{/if}
</script>