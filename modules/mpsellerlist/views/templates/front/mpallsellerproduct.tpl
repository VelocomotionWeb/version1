{capture name=path}
    <a {if $logged}href="{$link->getModuleLink('mpsellerlist', 'sellerlist')|escape:'html':'UTF-8'}"{/if}>
        {l s='Marketplace SellerList' mod='mpsellerlist'}
    </a>
    <span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
    <span class="navigation_page">{l s='ViewMoreProduct' mod='mpsellerlist'}</span>
{/capture}
<div class="form-group">
        <label for="sel1" class="wk_sel_label"><strong>Sort By</strong></label>
        <select name="wk_orderby" class="form-control" id="wk_orderby">
            <option>--</option>
            <option {if isset($orderby) && $orderby == 1}selected="selected"{/if} id="1">{l s='Lowest price' mod='mpsellerlist'}</option>
            <option {if isset($orderby) && $orderby == 2}selected="selected"{/if} id="2">{l s='Higest price' mod='mpsellerlist'}</option>
            <option {if isset($orderby) && $orderby == 3}selected="selected"{/if} id="3">{l s='A To Z' mod='mpsellerlist'}</option>
            <option {if isset($orderby) && $orderby == 4}selected="selected"{/if} id="4">{l s='Z To A' mod='mpsellerlist'}</option>
        </select>
</div>
<div class="container wk_product_container">
    <input type="hidden" id="orderby" name="orderby" value="{if isset($orderby)|escape:'htmlall':'UTF-8'}{$sortby|escape:'htmlall':'UTF-8'}{/if}">
    <input type="hidden" id="orderway" name="orderway" value="{if isset($orderway)|escape:'htmlall':'UTF-8'}{$orderway|escape:'htmlall':'UTF-8'}{/if}">
    <div class="row wk_seller_main">
        <div class="col-lg-12 heading_list">
            <div class="wk_seller_list">
                <h1 class="wk_p_header">{l s='All Products' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $active_seller_product==0}
            {l s='No product found' mod='mpsellerlist'}
        {else}
        {assign var=k value=0}
        {foreach $seller_product_info as $seller_prod}
        <div class="col-lg-3 col-md-4 col-xs-6 thumb" id="{$seller_prod['id']|escape:'htmlall':'UTF-8'}">
            <a class="thumbnail" href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}">
                {if $product_img_info[$k]['image']}
                    <img class="img-responsive" src="{$link->getImageLink($product_img_info[$k]['link_rewrite'], $product_img_info[$k]['image'], 'home_default')|escape:'html':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="">
                {else}
                    <img class="img-responsive" src="{$link->getImageLink($product_img_info[$k]['link_rewrite'], $product_img_info[$k]['lang_iso']|cat : '-default', 'home_default')|escape:'html':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="">
                {/if}
            </a>
            <div class="wk_seller_details">
                <p class="wk_seller_name">{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}</p>
                <div><strong>{convertPrice price={$seller_prod['price']|escape:'htmlall':'UTF-8'}}</strong></div>
                <a href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}" class="btn btn-default btn_product_shop">View</a>
            </div>
        </div>
        {assign var=k value=$k+1}
        {/foreach}
        {if $active_seller_product>1}
        <div class="col-lg-12 wk_view_more" id="{$k|escape:'htmlall':'UTF-8'}">
            <img class="view-more-img" src="{$modules_dir|escape:'htmlall':'UTF-8'}/mpsellerlist/views/img/ajax-loader.gif">
            <a href="" class="btn btn-default btn-all" id="wk-more-product">{l s='View More Products' mod='mpsellerlist'}</a>
        </div>
        {/if}
    {/if}
    </div>
</div>
{strip}
    {addJsDef ajaxsort_url = $ajaxsort_url}
    {addJsDef viewmore_url = $link->getModuleLink('mpsellerlist', 'moreproduct')}
{/strip}