{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Collection' mod='marketplace'}</span>
{/capture}
<div class="main_block">
    {if isset($products)}
    <p class="center text_shopcollection">
    {l s='Bikes list available for' mod='marketplace'} {$nameshop}
    </p>
	{*<div id="wk_banner_block">
		{hook h="DisplayMpcollectionbannerhook"}
	</div>*}
	<div class="wk_left_col">
		{include file="./shopcollection-categoty-sort.tpl"}
		{hook h="DisplayMpcollectionlefthook"}
	</div>
    <div class="center text_shopcollection">
        {l s='From the' mod='marketplace'} {$date_depart} {l s='to the' mod='marketplace'} {$date_fin}, {l s='therefore' mod='marketplace'} {$quantite} {l s='day' mod='marketplace'}{if $quantite>1}s{/if}
        <div class="option center">
            <div id="quantity_wanted_p" class="input-group quantity-control" style="display:none">
                <span class="input-group-addon">-</span>
                <input type="text" name="qty" id="quantity_wanted" class="text form-control" value="{$quantite}" />&nbsp;<span>{l s='day' mod='marketplace'}{if $quantite>1}s{/if}	</span>
                <span class="input-group-addon">+</span>
            </div>
        </div>
    </div>
    {/if}
       
	<div class="dashboard_content">
		
        <div class="wk_left_col">
        </div>
        
        {if isset($products)}
            <input type="hidden" name="token" value="{$static_token}" />
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
			{if isset($products)}
            	{assign var=cpt value=0}
				{foreach $products as $key => $product}
					{if $product.active}
                        {if $product.count<8}
                        	{assign var=cpt value=$cpt+1} 
                                <div class="wk_collection_data" {if ($cpt)%2 == 0}style="margin-right:0px;"{/if} 
                                count="{$product.count}" velo="{$product.link_rewrite}">
                                 <a href="{$product.link|escape:'html'}" itemprop="url" target="_blank">
								 {*<a href="{$link->getImageLink($product.link_rewrite, $product.image, 'large_default')|escape:'html':'UTF-8'}" target="_blank" 
                                 class="fancybox jqzoom">*}
                                
                                 <div class="wk_img_block">
									{if $product.image}
										<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.image, 'large_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}"/>
									{else}
										<img class="img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.lang_iso|cat : '-default', 'large_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}"/>
									{/if}
								</div>
                                 
								<div class="wk_collecion_details name">
									<div class="name">{$product.product_name|truncate:45:'...'|escape:'html':'UTF-8'} {*/ n°{$product.id_product}*}</div>
								</div>
                                </a>
                                                                
								<div class="wk_collecion_details">
                                    {if !$PS_CATALOG_MODE} <!-- if catalog mode is disabled by config (by default)-->
                                        {if $product.show_price}
                                            <div id="price_{$product.id_product}" class="float_left price">
                                            {convertPrice price=$product.product->getPrice(true, 0, $priceDisplayPrecision, null, false, true, $quantite)*$quantite}
                                            </div>
                                        {/if}
                                        
                                        <div id="qty_{$product.id_product}" class="float_left qty">{$quantite} {l s='day' mod='marketplace'}{if $quantite>1}s{/if}</div>
                                        
                                        <!-- SELECTEUR DE QUANTITE -->
                                            <div id="quantity_{$product.id_product}" class="float_left quantity">
                                            <select id="quantity" name="quantity">
                                            {for $var=1 to $product.count}
                                                {foreach $products as $key => $productO}
                                                	{if $productO.product_name==$product.product_name}
                                                    	{if $productO.count==$var}
                                                        <option value="{$var}" id="{$productO.id_product}" count="{$productO.count}">{$var}</option>
                                                        {/if}
                                                    {/if}
                                                {/foreach}
                                                
                                            {/for}
                                            </select>
                                            </div>
                                            <!-- Cache les autres produits de meme nom -->
                                            <script>
											$(document).ready(function () { $('.wk_collection_data[velo=velo-vtt-electrique-modele-vitality-dice][count=1]').hide(); });
											</script>
                                        {if $product.count > 1}
                                        {else}    
                                        	
                                        {/if}
                                        
                                        <div id="img_{$product.id_product}" class="float_right img">
                                           {if $product.product_in_cart==0}
                                                <a class="button ajax_add_to_cart_button btn btn_loc" 
                                                 rel="nofollow" title="{l s='Add to cart' mod='marketplace'}" 
                                                data-id-product="{$product.id_product|intval}" > 
                                                    <span>{l s='Rent' mod='marketplace'}</span>
                                                </a>
                                           {else if $product.product_in_cart==1}
                                                <a class="button btn btn_loc_warning" href="/commande-rapide" >
                                                <span>{l s='In cart' mod='marketplace'}</span>
                                                </a>
                                           {else}
                                                <a class="button btn btn_loc_warning" >
                                                <span>{l s='Reserve' mod='marketplace'}</span>
                                                </a>
                                           {/if}
                                        </div>
                                        
                                        <div class="declinaisons" id="list_declinaisons_{$product.id_product}">
                                            <span id="title_declinaisons_{$product.id_product}" class="title_declinaisons"></span>
                                        </div>
                                        <ul id="declinaisons_{$product.id_product}" class="declinaisons_produit"></ul>
                                        
                                        <input type="hidden" class="id_product" value="{$product.id_product}" />
                                        <input type="hidden" id="idCombination_{$product.id_product}" pid="{$product.id_product}" value="" />

                                        
                                    {/if}
								</div>
                                
							</div>
						</a>
						{/if}
                    {else}
						<div class="alert alert-info">{l s='No bikes available...' mod='marketplace'}</div>
					{/if}
				{/foreach}
			{else}
				<div class="alert alert-info">{l s='No bikes available...' mod='marketplace'}</div>
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
<div id="name_shop" style="display:none">{$name_shop}</div>
<div id="s_date_depart" style="display:none">{$date_depart}</div>
<div id="s_date_fin" style="display:none">{$date_fin}</div>
{if isset($name_shop)}
{strip}
	{addJsDef requestSortProducts=$link->getModuleLink('marketplace', 'shopcollection', ['mp_shop_name' => $name_shop])}
	{addJsDef id_store=$id_store}
	<span style="display:none" id="message_confirm_delete">{l s='Rental added to cart, continue reservations' mod='marketplace'}</span>
{/strip}
{/if}
<script>
{literal}
$(document).ready(function () {
	$( ".id_product" ).each(function( index ) {
	  if ($('#title_declinaisons_'+$( this ).val()).html()=="")  setDeclinaisons($( this ).val() );
	});

	/*$('.wk_img_block .img-responsive').mouseover(function(){
	  	//console.log(this);
		$('#map-img').html('<img src="' + this.src + '">');
		$('#map-canvas').hide();
	});
	
	$('.wk_img_block .img-responsive').mouseout(function(){
	    //console.log(this.src):
		$('#map-img').html('');
		$('#map-canvas').show();
	});*/	

	
});

function setDeclinaisons(id){
	return false;
	$.ajax({
	  url: "/modules/marketplace/ajax_declinaisons.php?id_product="+id,
	  dataType: "json",
	  beforeSend: function( xhr ) {
	    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
	  }
	}).done(function( data ) {
		$('#ajax_id_product_'+id).hide();
		$.each(data, function(i, item) {
			// si la selection est chois on ignore la valeur par defaut
			// variable id_product_attribute passé en GET			
			if (true)
			{
				if (i==0) 
				{
					checked = "checked=checked"; 
					$('#idCombination_' + id).val(item.id);
				} 
				else checked = "";
			}
			/* retirer les declinaisons
			$('#declinaisons_'+id).append('<li><input type="radio" class="idCombination" name="radio_'+id+'" value="'+item.id+'" '+checked+' pid="'+id+'" ><label for="'+item.id+'">'+item.name+'</label></li>')
			*/
		});
		
		$('.idCombination[pid='+id+']').click(function(obj){
			$('#idCombination_' + $(this).attr('pid')).val(this.value);
			$.ajax({
			  	url: "/modules/marketplace/getprice.php?id_product="+id+"&id_product_attribute="+this.value+"&quantite="+$('#quantity_wanted').val()
			}).done(function( data ) 
			{
				$('#price_' + id).html(data);	
			});
		});
	});

}
{/literal}

</script>