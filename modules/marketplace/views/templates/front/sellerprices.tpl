{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Price list' mod='marketplace'}</span>
{/capture}
<div class="main_block" >
	{include file="$tpl_dir./errors.tpl"}
	{if isset($updated)}
	{/if}
	<p class="alert alert-success" style="display:none">{l s='Validated list' mod='marketplace'}</p>
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="dashboard">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Price list' mod='marketplace'} {$mp_seller_info['seller_name']|escape:'html':'UTF-8'}</span>
			</div>
			<div class="wk_right_col">
				<div class="profile_content">
					<form action="{$link->getModuleLink('marketplace', 'editprofile')|escape:'html':'UTF-8'}" method="post"   enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" id="wk_edit_profile_form">
						<fieldset>
							<div class="form-group">	
                                <p>
                                {l s="Enter bikes price by the day, and the price for each package." mod="marketplace"}
                                <br>
                                {l s="[ENTER] to move at the next area" mod="marketplace"}
                                <p>
                                {l s="Don\'t enter a price if you don\'t want to change a bike price" mod="marketplace"}

                                <select  name="tax" id="tax" class="form-control">
                                    {foreach $taxs as $tax}
                                        <option value="{$tax.rate}" {if 1 == $tax.name}selected{/if}>{$tax.name}</option>
                                    {/foreach}
                                </select>
							</div>

							<div class="form-group left_floated">
                                <table class="table_price" border="1" width=100%>
                                <thead>
                                <tr>
                                <td>{l s='Bikes list' mod='marketplace'}</td>
                                <td>
                                	{l s='Starting price incl tax' mod='marketplace'}<br>
                                	{l s='day' mod='marketplace'}
                                    </td>
                                    {foreach from=$colonnes key=pos item=colonne}
		                                <td>{$colonne[$id_lang]}</td>
                                    {/foreach}
                                </tr>                            
                                </thead>
                                <tbody>
                                {assign var=pos value='1'}
                                {foreach from=$products item=product1} 
                                <tr>
                                    <td>{$product1.product_name}</td>
                                    <td><input name="p_{$product1.id_product}" id="p_{$product1.id_product}" id_product="{$product1.id_product}" jour="1" 
                                    class="saisie" value="" pos="{$pos}"
                                            onKeyUp = "if (document.all) e = window.event; else e = event;  if (e.keyCode == 13) $('.saisie[pos={$pos+1}]').focus(); "></td>
                                    {assign var=pos value=$pos+1}
                                    {foreach from=$colonnes item=colonne}
                                    	<td><input name="p_{$product1.id_product}" id="p_{$product1.id_product}" id_product="{$product1.id_product}" jour="{$colonne[0]}"
                                        	class="saisie" pos="{$pos}" disabled 
                                            onKeyUp = "if (document.all) e = window.event; else e = event;  if (e.keyCode == 13) $('.saisie[pos={$pos+1}]').focus(); "></td>
                                        {assign var=pos value=$pos+1}
                                    {/foreach}
                                    
                                </tr>                            
                                {/foreach}
                                <tbody>
                                </table>
							</div>
							<div class="form-group left_floated">
								<div {if isset($shop_img_path)}class="wk_hover_img"{/if}>
								</div>
							</div>
                            
							{hook h="DisplayMpshopaddfooterhook"}
							<div class="submit-button">
								<button type="button" id="update_seller" name="updateSeller" class="btn btn-default button button-medium">
									<span>{l s='Save' mod='marketplace'}</span>
								</button>
							</div>
						</fieldset>	
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
.table_price {
  border-radius: 90px !important;
  padding: 20px;
}
.table_price td {
  padding: 10px;
}
.table_price thead {
  background-color: #ccc;
}
.saisie {
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 2px 1px 1px #000;
  text-align: center;
  width: 40px;
}


</style>
<script>
{literal}
$(document).ready(function()
{
	$('.saisie').click(function(){ 
	$(this).select ();
	});
	
	$('#update_seller').click(function()
	{
		$('.saisie').each(function()
		{
			jour = $(this).attr('jour');
			id_product = $(this).attr('id_product');
			val = $(this).val();
			//console.log( id_product + " " + jour+  " " + val);
			
			if (val>0) 
			{
				// appel ajax qui creer les regles
				$.post('/modules/marketplace/rule_product_seller.php', 
					{ 
						id_product: id_product,
						jour: jour,
						val: val,
						tax: $('#tax').val() 
					},
					function(data)
					{
						//console.log(data);
						$('.alert-success').show(1000);
						setTimeout(function(){ $('.alert-success').hide(1000); }, 3000);
						
					}
				);
			}
			
			
		});
	});
	
	$('.saisie[jour=1]').change(function()
	{
		val = $(this).val();
		id_product = $(this).attr('id_product');
		if (val>0) 
		$('.saisie[id_product='+id_product+']').removeAttr('disabled');
		else 
		$('.saisie[id_product='+id_product+']').not('.saisie[jour=1]').attr('disabled','disabled');
			
		
	});
	
});
{/literal}
</script>
{strip}
	{addJsDefL name=req_seller_name}{l s='Seller name is required.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=inv_seller_name}{l s='Invalid Seller name.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=req_shop_name}{l s='Shop name is required.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=inv_shop_name}{l s='Invalid Shop name.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=req_email}{l s='Email Id is required.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=inv_email}{l s='Invalid email address' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=req_phone}{l s='Phone is required.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=inv_phone}{l s='Invalid phone number.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=shop_name_exist_msg}{l s='Shop name already taken. Try another.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=seller_email_exist_msg}{l s='Email Id alreay exist.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDef validate_uniqueness_ajax_url=$link->getModuleLink('marketplace', 'validateuniqueshop')}
	{addJsDef terms_and_condition_active=0}
	{addJsDef editprofile_controller=$link->getModuleLink('marketplace', 'editprofile')}
	{addJsDef seller_default_img_path=$seller_default_img_path}
	{addJsDef shop_default_img_path=$shop_default_img_path}
	{addJsDefL name='choosefile_fileButtonHtml'}{l s='Choose File' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name='nofileselect_fileDefaultHtml'}{l s='No file selected' js=1 mod='marketplace'}{/addJsDefL}
{/strip}