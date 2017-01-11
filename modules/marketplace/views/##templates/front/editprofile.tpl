{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Edit Profile' mod='marketplace'}</span>
{/capture}
<div class="main_block" >
	{include file="$tpl_dir./errors.tpl"}
	{if isset($updated)}
		<p class="alert alert-success">{l s='Profile updated successfully' mod='marketplace'}</p>
	{/if}
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="dashboard">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Edit Profile' mod='marketplace'}</span>
			</div>
			<div class="wk_right_col">
				<div class="profile_content">
					<form action="{$link->getModuleLink('marketplace', 'editprofile')|escape:'html':'UTF-8'}" method="post"   enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" id="wk_edit_profile_form">
						<fieldset>
							<div class="form-group">	
								<label for="update_seller_name" class="control-label required">{l s='Seller Name' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['seller_name']|escape:'html':'UTF-8'}" name="update_seller_name" id="person_name1"/>
								<input type="hidden" name="id_seller" id="id_seller" value="{$mp_seller_info['id']|escape:'html':'UTF-8'}">
							</div>

							<div class="form-group left_floated">
								<div {if isset($seller_img_path)}class="wk_hover_img"{/if}>
									<img class="img-thumbnail wk_seller_img" width="100" height="100" src="{if isset($seller_img_path)}{$seller_img_path|escape:'htmlall':'UTF-8'}{else}{$seller_default_img_path|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($seller_img_path)}{l s='Seller Profile Image' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
									{if isset($seller_img_path)}
										<a class="wk_img_hover_btn wk_delete_seller_img" href="" data-id_seller="{$mp_seller_info['id']|escape:'html':'UTF-8'}" title="{l s='Delete' mod='marketplace'}">
											<i class="icon-trash"></i>
										</a>
										<img src="{$modules_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loading-small.gif" class="wk_loader_img wk_loader_seller_img_delete"/>
									{/if}
								</div>
								<div class="left_floated">
									<label for="update_seller_logo" class="control-label" style="display:block;">{l s='Upload Profile Image' mod='marketplace'}</label>
									<input class="required form-control" type="file" name="update_seller_logo" id="update_seller_logo"/>
									<div class="help-block">{l s='Image minimum size must be 200 x 200px' mod='marketplace'}</div>
								</div>
							</div>

							<div class="form-group">
								<label for="update_shop_name" class="control-label required">{l s='Shop Name' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['shop_name']|escape:'html':'UTF-8'}" name="update_shop_name" id="shop_name1"/>
								<p class="help-block wk-msg-shopname"></p>
							</div>

							<div class="form-group">
								<label for="update_business_email" class="control-label required">{l s='Business email' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['business_email']|escape:'html':'UTF-8'}" name="update_business_email" id="business_email_id1"/>
								<p class="help-block wk-msg-selleremail"></p>
							</div>

							<div class="form-group">
								<label for="update_phone" class="control-label required">{l s='Phone' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['phone']|escape:'html':'UTF-8'}" name="update_phone" id="phone1" maxlength="{$max_phone_digit|escape:'html':'UTF-8'}"/>
							</div>

							<div class="form-group">
								<label for="update_fax" class="control-label">{l s='Fax' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['fax']|escape:'html':'UTF-8'}" name="update_fax" id="update_fax"/>
							</div>

							<!--div class="form-group">
								<label for="update_facbook_id" class="control-label">{l s='Facebook Id' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['facebook_id']|escape:'html':'UTF-8'}" name="update_facbook_id" id="update_facbook_id"/>
							</div-->

							<!--div class="form-group">
								<label for="update_twitter_id" class="control-label">{l s='Twitter Id' mod='marketplace'}</label>
								<input class="required form-control" type="text" value="{$mp_seller_info['twitter_id']|escape:'html':'UTF-8'}" name="update_twitter_id" id="update_twitter_id"/>
							</div-->

							<div class="form-group">
								<label for="update_address" class="control-label">{l s='Address' mod='marketplace'}</label>
								<textarea class="required form-control"  name="update_address" id="update_address">{$marketplace_address|escape:'html':'UTF-8'}</textarea>
							</div>

							<div class="form-group">
								<label for="update_about_shop" class="control-label">{l s='About Shop' mod='marketplace'}</label>
								<textarea name="update_about_shop" id="update_about_shop" class="update_about_shop_detail wk_tinymce form-control">{$market_place_shop['about_us']|escape:'html':'UTF-8'}</textarea>
							</div>

							<div class="form-group left_floated">
								<div {if isset($shop_img_path)}class="wk_hover_img"{/if}>
									<img class="img-thumbnail wk_shop_img" width="100" height="100" src="{if isset($shop_img_path)}{$shop_img_path|escape:'htmlall':'UTF-8'}{else}{$shop_default_img_path|escape:'htmlall':'UTF-8'}{/if}" alt="{if isset($shop_img_path)}{l s='Shop Logo' mod='marketplace'}{else}{l s='Default Image' mod='marketplace'}{/if}"/>
									{if isset($shop_img_path)}
										<a class="wk_img_hover_btn wk_delete_shop_img" href="" data-id_seller="{$mp_seller_info['id']|escape:'html':'UTF-8'}" title="{l s='Delete' mod='marketplace'}">
											<i class="icon-trash"></i>
										</a>
										<img src="{$modules_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/loading-small.gif" class="wk_loader_img wk_loader_shop_img_delete"/>
									{/if}
								</div>
								<div class="left_floated">
									<label for="update_shop_logo" class="control-label" style="display:block;">{l s='Upload Shop Logo' mod='marketplace'}</label>
									<input class="required form control" type="file" name="update_shop_logo" id="update_shop_logo"/>
									<div class="help-block">{l s='Image minimum size must be 200 x 200px' mod='marketplace'}</div>
								</div>
							</div>
							{hook h="DisplayMpshopaddfooterhook"}
							<div class="submit-button">
								<button type="submit" id="update_profile" name="updateProfile" class="btn btn-default button button-medium">
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