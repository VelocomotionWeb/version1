<div class="panel">
	<div class="panel-heading">
		<i class="icon-user"></i>
		{if isset($edit)}
			{l s='Edit seller' mod='marketplace'}
		{else}
			{l s='Add new seller' mod='marketplace'}
		{/if}
	</div>
    <form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
		{if !isset($edit)}
			<div class="form-group">
				<label class="col-lg-3 control-label required">{l s='Choose Customer' mod='marketplace'}</label>	
				<div class="col-lg-5">				
					{if isset($customer_info)}
						<select name="shop_customer" class="fixed-width-xl">
							{foreach $customer_info as $cusinfo}
								<option value="{$cusinfo['id_customer']|escape:'html':'UTF-8'}" {if isset($smarty.post.shop_customer)}{if $smarty.post.shop_customer == $cusinfo['id_customer']}Selected="Selected"{/if}{/if}>
									{$cusinfo['email']|escape:'html':'UTF-8'}
								</option>
							{/foreach}
						</select>
					{else}
						<p class="alert alert-danger">{l s='There is no customer found on your shop to add as a Marketplace seller. You can add only registered customer as a marketplace seller' mod='marketplace'}</p>
					{/if}
				</div>	
			</div>
		{else}
			<input type="hidden" value="{$mp_seller_info.id|escape:'html':'UTF-8'}" name="mp_id_seller" id="mp_id_seller"/>
			<input type="hidden" value="{$mp_seller_info.shop_name|escape:'html':'UTF-8'}" name="pre_shop_name" />
		{/if}

		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s=' Shop Name' mod='marketplace'} </span></label>	
			<div class="col-lg-5">
				<input type="text" id="shop_name1" autocomplete="off" name="shop_name" {if isset($edit)}value="{if isset($smarty.post.shop_name)}{$smarty.post.shop_name|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.shop_name|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.shop_name)}{$smarty.post.shop_name|escape:'htmlall':'UTF-8'}{/if}"{/if}/>
				<p class="help-block wk-msg-shopname" style="color:#971414;"></p>
			</div>	
		</div>

		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Shop Description' mod='marketplace'}</label>
			<div class="col-lg-6">
				<textarea name="about_business" class="about_business" >{if isset($edit)}{if isset($smarty.post.about_business)}{$smarty.post.about_business|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.about_shop|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.about_business)}{$smarty.post.about_business|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
			</div>
		</div>

		<div id="person_name" class="form-group" >
			<label class="col-lg-3 control-label required">
				<span class="label-tooltip" "="" ?{}_$%:=" title="" data-html="true" data-toggle="tooltip" data-original-title=" Invalid characters 0-9!&lt;&gt;,;?=+()@#">{l s='Seller Name' mod='marketplace'}</span></label>
			<div class="col-lg-5">
			<input type="text" name="person_name" id="person_name1" {if isset($edit)}value="{if isset($smarty.post.person_name)}{$smarty.post.person_name|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.seller_name|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.person_name)}{$smarty.post.person_name|escape:'htmlall':'UTF-8'}{/if}"{/if}/></div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 control-label required">{l s='Phone' mod='marketplace'}</label>
			<div class="col-lg-5">
				<input type="text" name="phone" id="phone1" maxlength="{$max_phone_digit|escape:'htmlall':'UTF-8'}" {if isset($edit)}value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.phone|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'htmlall':'UTF-8'}{/if}"{/if}/>
			</div>
		</div>

		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Fax' mod='marketplace'}</label>
			<div class="col-lg-5">
				<input class="form-control-static" type="text" name="fax" id="fax1" maxlength="10" {if isset($edit)}value="{if isset($smarty.post.fax)}{$smarty.post.fax|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.fax|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.fax)}{$smarty.post.fax|escape:'htmlall':'UTF-8'}{/if}"{/if}/>
			</div>
		</div>
		
		<div class="form-group">	
			<label class="col-lg-3 control-label required">{l s='Business Email' mod='marketplace'}</label>
			<div class="col-lg-5">
				<div class="input-group">
					<span class="input-group-addon">
						<i class="icon-envelope-o"></i>
					</span>
					<input class="reg_sel_input form-control-static" type="text" name="business_email_id" id="business_email_id1"  {if isset($edit)}value="{if isset($smarty.post.business_email_id)}{$smarty.post.business_email_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.business_email|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.business_email_id)}{$smarty.post.business_email_id|escape:'htmlall':'UTF-8'}{/if}"{/if}/>
				</div>
				<p class="help-block wk-msg-selleremail" style="color:#971414;"></p>
			</div>
		</div>
					
		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Address' mod='marketplace'}</label>
			<div class="col-lg-5">
				<textarea name="address" rows="6" cols="35" >{if isset($edit)}{if isset($smarty.post.address)}{$smarty.post.address|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.address|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($smarty.post.address)}{$smarty.post.address|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
			</div>
		</div>
		
		<div id="facebook" class="form-group" >
			<label class="col-lg-3 control-label">{l s='Facebook ID' mod='marketplace'}</label>
			<div class="col-lg-5">
				<input class="reg_sel_input form-control-static" type="text" name="fb_id" id="fb_id1" {if isset($edit)}value="{if isset($smarty.post.fb_id)}{$smarty.post.fb_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.facebook_id|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.fb_id)}{$smarty.post.fb_id|escape:'htmlall':'UTF-8'}{/if}"{/if}/>
			</div>
		</div>
			
		<div id="twitter" class="form-group" >
			<label class="col-lg-3 control-label">{l s='Twitter ID' mod='marketplace'}</label>
			<div class="col-lg-5">
				<input class="reg_sel_input form-control-static"  type="text" name="tw_id" id="tw_id1" {if isset($edit)}value="{if isset($smarty.post.tw_id)}{$smarty.post.tw_id|escape:'htmlall':'UTF-8'}{else}{$mp_seller_info.twitter_id|escape:'htmlall':'UTF-8'}{/if}"{else}value="{if isset($smarty.post.tw_id)}{$smarty.post.tw_id|escape:'htmlall':'UTF-8'}{/if}"{/if}/>
			</div>
		</div>

		{if isset($edit)}
			<div class="form-group">  
				<label class="col-lg-3 control-label"></label>
				<div class="prev_image col-lg-5" style="float:left;">
					<img class="img-thumbnail" src="{$shopimagepath|escape:'html':'UTF-8'}" width="100" height="100"/>
				</div>
			</div>
		{/if}

		<div class="form-group">  
			<div id="upload_logo" class="sell_row">
				<label class="col-lg-3 control-label">{l s='Shop Logo' mod='marketplace'}</label>
				<div class="col-lg-6">
					<input type="file" name="shop_logo"/>
				</div>
			</div>
		</div>	
		{if isset($edit)}
			<div class="form-group">  
				<label class="col-lg-3 control-label"></label>
				<div class="prev_image col-lg-5" style="float:left;">
					<img class="img-thumbnail" src="{$sellerimagepath|escape:'html':'UTF-8'}" width="100" height="100" />
				</div>
			</div>
		{/if}
		<div class="form-group">  
			<div id="upload_logo" class="sell_row">
				<label class="col-lg-3 control-label">{l s='Seller Logo' mod='marketplace'}</label>
				<div class="col-lg-6">
					<input type="file" name="seller_logo"/>
				</div>
			</div> 
		</div>
		{if isset($edit)}
			{hook h="displayMpshopaddfooterhook"}
		{else}
			{hook h="displayMpshoprequestfooterhook"}
		{/if}
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminSellerInfoDetail')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='marketplace'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i>{l s='Save' mod='marketplace'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='marketplace'}
			</button>
		</div>
		{if isset($edit)}
			{hook h="displayUpdateMpSellerBootom"}
		{else}
			{hook h="displayAddMpSellerBootom"}
		{/if}
	</form>
</div>

<script type="text/javascript">
	var iso = "{$iso|escape:'htmlall':'UTF-8'}";
	var pathCSS = "{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}";
	var ad = "{$ad|escape:'htmlall':'UTF-8'}";

	var shop_name_msg = "{l s='Shop name already taken. Try another.' js=1 mod='marketplace'}";
	var seller_email_msg = "{l s='Seller email already exist.' js=1 mod='marketplace'}";
	var shop_name_exist = false;
	var seller_email_exist = false;
	var id_seller;

	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"about_business",
				width : 700
			});
		{/block}
	});

	if ($("#mp_id_seller").length)
		id_seller = $("#mp_id_seller").val();

	//Seller registration shop name validation(uniqueness)
	$("#shop_name1").on("blur", function(){
		checkUniqueShopName($(this).val(), id_seller);
	});

	$("#shop_name1").on("focus", function(){
		$(".wk-msg-shopname").empty();
	});


	//Seller registration email validation(uniqueness)
	$("#business_email_id1").on("blur", function(){
		checkUniqueSellerEmail($(this).val(), id_seller);
	});

	$("#business_email_id1").on("focus", function(){
		$(".wk-msg-selleremail").empty();
	});

	function checkUniqueShopName(shop_name, id_seller)
	{
		if (shop_name != "")
		{
			$.ajax({
				url: "{$link->getAdminLink('AdminSellerInfoDetail')|escape:'htmlall':'UTF-8'}",
				type: "POST",
				data: {
					shop_name: shop_name,
					ajax: true,
					action: "checkUniqueShopName",
					id_seller: id_seller !== 'undefined' ? id_seller : false,
				},
				async: false,
				success:function(result){
					if (result == 1)
					{
						$(".wk-msg-shopname").html(shop_name_msg);
						shop_name_exist = true;
					}
					else
					{
						$(".wk-msg-shopname").empty();
						shop_name_exist = false;
					}
				}
			});
		}
		return shop_name_exist;
	}

	function checkUniqueSellerEmail(seller_email, id_seller)
	{
		if (seller_email != "")
		{
			$.ajax({
				url: "{$link->getAdminLink('AdminSellerInfoDetail')|escape:'htmlall':'UTF-8'}",
				type: "POST",
				data: {
					seller_email: seller_email,
					ajax: true,
					action: "checkUniqueSellerEmail",
					id_seller: id_seller !== 'undefined' ? id_seller : false,
				},
				async: false,
				success:function(result){
					if (result == 1)
					{
						$(".wk-msg-selleremail").html(seller_email_msg);
						seller_email_exist = true;
					}
					else
					{
						$(".wk-msg-selleremail").empty();
						seller_email_exist = false;
					}
				}
			});
		}
		return seller_email_exist;
	}
</script>