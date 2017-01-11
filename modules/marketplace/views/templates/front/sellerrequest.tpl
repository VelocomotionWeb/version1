{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Seller Registration' mod='marketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($is_seller)}
	{if !$logged}
		<div class="alert alert-danger">
			{l s='You have to login to make a seller request.' mod='marketplace'}
		</div>
	{else}
		{if $is_seller == 0}
			<div class="alert alert-info">
				{l s='Your request has been sent to admin. Please wait till the approval from admin' mod='marketplace'}
			</div>
		{else}
			<div class="alert alert-info">
				{l s='Your request has been approved by admin. ' mod='marketplace'}
				<a class="btn btn-deafult button button-small" href="{$link->getModuleLink('marketplace','addproduct')|escape:'html':'UTF-8'}">
					<span>{l s='Add Your First Product' mod='marketplace'}</span>
				</a>
			</div>
		{/if}
	{/if}
{else}

<div class="seller_registration_form">
	<div class="container">
		<div class="page-title">
			<span>{l s='Seller Request' mod='marketplace'}</span>
		</div>
		<div class="wk_right_col">
		<p><sup>*</sup> {l s='Required field' mod='marketplace'}</p>
		<form action="{$link->getModuleLink('marketplace', 'sellerrequest')|escape:'htmlall':'UTF-8'}" method="post" id="createaccountform" class="std contact-form-box" enctype="multipart/form-data">
			<fieldset>
				<div class="form-group seller_shop_name">
					<label for="shop_name1" class="control-label required">{l s='Shop Name' mod='marketplace'}</label>	
					<input class="is_required validate form-control" type="text" data-validate="isGenericName" value="{if isset($smarty.post.shop_name)}{$smarty.post.shop_name|escape:'html':'UTF-8'}{/if}" id="shop_name1" name="shop_name" autocomplete="off"/>
					<p class="help-block wk-msg-shopname"></p>
				</div>

				<div class="form-group">
					<label for="about_business" class="control-label">{l s='Shop Description' mod='marketplace'}</label>
					<textarea class="about_business wk_tinymce form-control" name="about_business">{if isset($smarty.post.about_business)}{$smarty.post.about_business|escape:'html':'UTF-8'}{/if}</textarea>
				</div>
					 
				<div class="form-group">  
					<label for="upload_logo" class="control-label">{l s='Shop Logo' mod='marketplace'}</label>
					<input class="form-control" id="upload_logo1" type="file"  name="upload_logo" />
					<p class="help-block">{l s='Image minimum size must be 200 x 200px' mod='marketplace'}</p>
				</div>
		
				<div id="person_name" class="form-group" >
					<label for="person_name" class="control-label required">{l s='Seller Name' mod='marketplace'}</label>
					<input class="is_required validate form-control" data-validate="isName" type="text" value="{if isset($smarty.post.person_name)}{$smarty.post.person_name|escape:'html':'UTF-8'}{/if}" name="person_name" id="person_name1" />
				</div>
				
				<div class="form-group">
					<label for="phone1" class="control-label required">{l s='Phone' mod='marketplace'}</label>
					<input class="is_required validate form-control" data-validate="isPhoneNumber" type="text" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'html':'UTF-8'}{/if}" name="phone" id="phone1" maxlength="{$max_phone_digit|escape:'html':'UTF-8'}" />
				</div>		 
					
				<div class="form-group">
					<label for="phone1" class="control-label">{l s='Fax' mod='marketplace'}</label>
					<input class="form-control" type="text" value="{if isset($smarty.post.fax)}{$smarty.post.fax|escape:'html':'UTF-8'}{/if}" name="fax" id="fax1" maxlength="10"/>
				</div>
				
				<div class="form-group">	
					<label for="business_email_id1" class="control-label required">{l s='Business Email' mod='marketplace'}</label>
					<input class="is_required validate form-control" type="text" value="{if isset($smarty.post.business_email_id)}{$smarty.post.business_email_id|escape:'html':'UTF-8'}{/if}" data-validate="isEmail" name="business_email_id" id="business_email_id1" />
					<p class="help-block wk-msg-selleremail"></p>
				</div>
							
				<div class="form-group">	
					<label for="address">{l s='Address' mod='marketplace'}</label>
					<textarea class="validate form-control" name="address" data-validate="isAddress">{if isset($smarty.post.address)}{$smarty.post.address|escape:'html':'UTF-8'}{/if}</textarea>
				</div>
				
				<div id="facebook" class="form-group" >
					<label for="fb_id1">{l s='Facebook Id' mod='marketplace'}</label>
					<input class="reg_sel_input form-control" value="{if isset($smarty.post.fb_id)}{$smarty.post.fb_id|escape:'html':'UTF-8'}{/if}" type="text" name="fb_id" id="fb_id1" />
				</div>
					
				<div id="twitter" class="form-group" >
					<label for="tw_id1">{l s='Twitter Id' mod='marketplace'}</label>
					<input class="reg_sel_input form-control" value="{if isset($smarty.post.tw_id)}{$smarty.post.tw_id|escape:'html':'UTF-8'}{/if}" type="text" name="tw_id" id="tw_id1" />
				</div>
	
				{if $terms_and_condition_active}
					<div class="form-group">
						<label for="terms_and_conditions">{l s='Terms and Conditions' mod='marketplace'}</label>
						<textarea class="form-control" name="terms_and_conditions" rows="6" id="terms_and_conditions" readonly>{if isset($terms_and_conditions)}{$terms_and_conditions|escape:'html':'UTF-8'}{/if}</textarea>
					</div>
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="terms_and_conditions_checkbox">
								<strong>{l s='Please agree the terms and condition' mod='marketplace'}</strong>
							</label>
						</div>
					</div>
				{/if}
				{hook h="DisplayMpshoprequestfooterhook"}
			</fieldset>
			<div class="form-group" style="text-align:center;">
				<button type="submit" id="seller_save" class="btn btn-default button button-medium" name="seller_save">
					<span>{l s='Register' mod='marketplace'}<i class="icon-chevron-right right"></i></span>
				</button>
			</div>
		</form>
		</div>
	</div>
</div>
{/if}

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
	{addJsDefL name=shop_name_error_msg}{l s='Shop name can not contain any special character except underscrore. Try another.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=seller_email_exist_msg}{l s='Email Id alreay exist.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=agree_terms_and_conditions}{l s='Please agree the terms and conditions.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDef validate_uniqueness_ajax_url=$link->getModuleLink('marketplace', 'validateuniqueshop')}
	{addJsDef terms_and_condition_active=$terms_and_condition_active}
{/strip}