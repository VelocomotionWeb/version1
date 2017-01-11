{capture name=path}
    {l s='Marketplace SellerList' mod='mpsellerlist'}
{/capture}
<div class="form-group wk_seller_top">
	<h1>{l s='Marketplace' mod='mpsellerlist'}</h1>
	<div class="wk_profiledata">
		<p class="wk_profile_text">{$mp_seller_text|escape:'htmlall':'UTF-8'}</p>
	</div>
	<p>
		<a class="btn btn-primary wk_btn_dash" href="{$gotoshop_link|escape:'htmlall':'UTF-8'}">{l s='Go To Dashboard' mod='mpsellerlist'}
		</a>
	</p>
</div>
<hr>
<div class="container wk_seller_container">
	<div class="row wk_seller_main">
    	<div class="col-lg-12 heading_list">
    		<div class="wk_seller_list">
            	<h1 class="wk_p_header">{l s='Seller List' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $total_active_seller==0}
        	{l s='No shop found' mod='mpsellerlist'}
        {else}
        	{assign var=k value=0}
		{foreach $all_active_seller as $act_seller}
        <div class="col-lg-3 col-md-4 col-xs-6 thumb">
            <a class="thumbnail" href="{$link->getModuleLink('marketplace','shopstore', ['flag'=>1,'mp_shop_name'=>{$act_seller['link_rewrite']|escape:'htmlall':'UTF-8'}])|escape:'htmlall':'UTF-8'}">
                <img class="img-responsive" src="{$modules_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/shop_img/{$shop_img[{$k|escape:'htmlall':'UTF-8'}]|escape:'htmlall':'UTF-8'}" alt="">
            </a>
            <div class="wk_seller_details">
                <p class="wk_seller_name">{$act_seller['shop_name']|escape:'htmlall':'UTF-8'}</p>
                <a href="{$link->getModuleLink('marketplace','shopstore', ['flag'=>1,'mp_shop_name'=>{$act_seller['link_rewrite']|escape:'htmlall':'UTF-8'}])|escape:'htmlall':'UTF-8'}" class="btn btn-default btn_seller_shop">View Shop</a>
            </div>
        </div>
        {assign var=k value=$k+1}
		{/foreach}
		{if $total_active_seller>1}
		<div class="col-lg-12 wk_view_more">
			<a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}" class="btn btn-default btn-all">
				{l s='View All Sellers' mod='mpsellerlist'}
			</a>
		</div>
        {/if}
		{/if}
    </div>
</div>
<div class="container wk_product_container">
	<div class="row wk_seller_main">
    	<div class="col-lg-12 heading_list">
    		<div class="wk_seller_list">
            	<h1 class="wk_p_header">{l s='Latest Products' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $active_seller_product==0}
        	{l s='No product found' mod='mpsellerlist'}
        {else}
        {assign var=k value=0}
        {foreach $seller_product_info as $seller_prod}
        <div class="col-lg-3 col-md-4 col-xs-6 thumb">
            <a class="thumbnail" href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}">
            {if $product_img_info[$k]['image']}
                <img class="img-responsive" src="{$link->getImageLink($product_img_info[$k]['link_rewrite'], $product_img_info[$k]['image'], 'home_default')|escape:'html':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="">
            {else}
                    <img class="img-responsive" src="{$link->getImageLink($product_img_info[$k]['link_rewrite'], $product_img_info[$k]['lang_iso']|cat : '-default', 'home_default')|escape:'html':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="">
            {/if}
            </a>
            <div class="wk_seller_details">
                <p class="wk_seller_name">{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}</p>
                <a href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}" class="btn btn-default btn_product_shop">View</a>
            </div>
        </div>
       	{assign var=k value=$k+1}
		{/foreach}
		{if $active_seller_product>1}
		<div class="col-sm-12 col-xs-12 wk_view_more">
			<a href="{$viewmoreproduct_link|escape:'htmlall':'UTF-8'}" class="btn btn-default btn-all">{l s='View All Products' mod='mpsellerlist'}</a>
		</div>
        {/if}
		{/if}
    </div>
</div>