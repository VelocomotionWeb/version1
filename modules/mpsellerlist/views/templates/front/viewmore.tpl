{if isset($seller_product_info)}
{foreach $seller_product_info as $seller_prod}
<div class="col-lg-3 col-md-4 col-xs-6 thumb" id="{$seller_prod['id']|escape:'htmlall':'UTF-8'}">
    <a class="thumbnail" href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}">
        <img class="img-responsive" src="{$product_img_info[$k]|escape:'htmlall':'UTF-8'}" title="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}" alt="">
    </a>
    <div class="wk_seller_details">
        <p class="wk_seller_name">{$seller_prod['product_name']|escape:'htmlall':'UTF-8'}</p>
        <div><strong>{convertPrice price={$seller_prod['price']|escape:'htmlall':'UTF-8'}}</strong></div>
        <a href="{$link->getProductLink($seller_prod['main_id_product'])|escape:'htmlall':'UTF-8'}" class="btn btn-default btn_product_shop">View</a>
    </div>
</div>
{assign var=k value=$k+1}
{/foreach}
{/if}
