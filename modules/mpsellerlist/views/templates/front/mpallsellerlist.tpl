{capture name=path}
    <a {if $logged}href="{$link->getModuleLink('mpsellerlist', 'sellerlist')|escape:'html':'UTF-8'}"{/if}>
        {l s='Marketplace SellerList' mod='mpsellerlist'}
    </a>
    <span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
    <span class="navigation_page">{l s='Viewmorelist' mod='mpsellerlist'}</span>
{/capture}
<div class="row margin-btm-10">
    <div class="col-md-offset-0 col-md-12 col-sm-offset-0 col-sm-12">
        <div class="pull-right">
            <div class="dropdown pull-left">
                <button class="btn btn-primary dropdown-toggle search_value" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" data-value="1">
                    <span class="text-capitalize" id="search_for">Seller Name</span>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="search_category" data-value="1">{l s='Seller Name' mod='mpsellerlist'}</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="search_category" data-value="2">{l s='Shop Name' mod='mpsellerlist'}</a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#" class="search_category" data-value="3">{l s='Shop Location' mod='mpsellerlist'}</a></li>
                </ul>
            </div>
            <div class="search_container pull-left">
                <label class="input-group pull-left">
                    <input type="text" class="mp_search_box form-control" aria-describedby="sizing-addon1" id="seller-search">
                    <span class="input-group-addon" id="mpseller-search"><i class="icon-search"></i></span>
                </label>
                <ul class="mp_search_sugg"></ul>
            </div>
        </div>
    </div>
</div>
<div class="brand_search">
    <label>{l s='Select Seller' mod='mpsellerlist'}:</label>
    <span class="content">
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=a" {if $alph=='a'}class="btn btn-default btn_seller_selected"{/if}>{l s='A' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=b" {if $alph=='b'}class="btn btn-default btn_seller_selected"{/if}>{l s='B' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=c" {if $alph=='c'}class="btn btn-default btn_seller_selected"{/if}>{l s='C' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=d" {if $alph=='d'}class="btn btn-default btn_seller_selected"{/if}>{l s='D' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=e" {if $alph=='e'}class="btn btn-default btn_seller_selected"{/if}>{l s='E' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=f" {if $alph=='f'}class="btn btn-default btn_seller_selected"{/if}>{l s='F' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=g" {if $alph=='g'}class="btn btn-default btn_seller_selected"{/if}>{l s='G' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=h" {if $alph=='h'}class="btn btn-default btn_seller_selected"{/if}>{l s='H' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=i" {if $alph=='i'}class="btn btn-default btn_seller_selected"{/if}>{l s='I' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=j" {if $alph=='j'}class="btn btn-default btn_seller_selected"{/if}>{l s='J' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=k" {if $alph=='k'}class="btn btn-default btn_seller_selected"{/if}>{l s='K' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=l" {if $alph=='l'}class="btn btn-default btn_seller_selected"{/if}>{l s='L' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=m" {if $alph=='m'}class="btn btn-default btn_seller_selected"{/if}>{l s='M' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=n" {if $alph=='n'}class="btn btn-default btn_seller_selected"{/if}>{l s='N' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=o" {if $alph=='o'}class="btn btn-default btn_seller_selected"{/if}>{l s='O' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=p" {if $alph=='p'}class="btn btn-default btn_seller_selected"{/if}>{l s='P' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=q" {if $alph=='q'}class="btn btn-default btn_seller_selected"{/if}>{l s='Q' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=r" {if $alph=='r'}class="btn btn-default btn_seller_selected"{/if}>{l s='R' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=s" {if $alph=='s'}class="btn btn-default btn_seller_selected"{/if}>{l s='S' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=t" {if $alph=='t'}class="btn btn-default btn_seller_selected"{/if}>{l s='T' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=u" {if $alph=='u'}class="btn btn-default btn_seller_selected"{/if}>{l s='U' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=v" {if $alph=='v'}class="btn btn-default btn_seller_selected"{/if}>{l s='V' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=w" {if $alph=='w'}class="btn btn-default btn_seller_selected"{/if}>{l s='W' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=x" {if $alph=='x'}class="btn btn-default btn_seller_selected"{/if}>{l s='X' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=y" {if $alph=='y'}class="btn btn-default btn_seller_selected"{/if}>{l s='Y' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}&alp=z" {if $alph=='z'}class="btn btn-default btn_seller_selected"{/if}>{l s='Z' mod='mpsellerlist'}</a>
        <a href="{$viewmorelist_link|escape:'htmlall':'UTF-8'}" class="btn btn-default btn_seller_shop">{l s='All seller' mod='mpsellerlist'}</a>
    </span>
</div>
<div class="container wk_seller_container">
    <div class="row wk_seller_main">
        <div class="col-lg-12 heading_list">
            <div class="wk_seller_list">
                <h1 class="wk_p_header">{l s='Seller List' mod='mpsellerlist'}</h1>
            </div>
        </div>
        {if $total_active_seller==0}  
        <h1 style="padding:40px;">{l s='No Shop Available' mod='mpsellerlist'}</h1>
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
        {/if}
    </div>
</div>
{strip}
    {addJsDef ajaxsearch_url = $ajaxsearch_url}
    {addJsDef shop_store_link = $shop_store_link}
    {addJsDef viewmorelist_link = $viewmorelist_link}
{/strip}