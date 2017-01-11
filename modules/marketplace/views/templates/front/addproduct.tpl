{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Add Product' mod='marketplace'}</span>
{/capture}

{if $logged}
	{include file="$tpl_dir./errors.tpl"}
	{hook h='DisplayMpaddproductheaderhook'}
	<div class="main_block">
		{hook h="DisplayMpmenuhook"}
		<div class="dashboard_content">
			<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
				<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Add Product' mod='marketplace'}</span>
			</div>
			{hook h="displayAddProductFormTop"}
			<div class="wk_right_col">
				<form action="{$link->getModuleLink('marketplace', 'addproduct')|escape:'html':'UTF-8'}" method="post" id="create-account" class="std contact-form-box" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#information" data-toggle="tab">
								<i class="icon-info-sign"></i>
								{l s='Information' mod='marketplace'}
							</a>
						</li>
						{hook h='displayMpProductOption'}
					</ul>
					<div class="tab-content panel collapse in">
						<div class="tab-pane active" id="information">
							{hook h='displayMpaddproducttoppanel'}
							<div class="form-group">	
								<label for="product_name" class="control-label required">{l s='Product Name :' mod='marketplace'}</label>
								<input type="text" id="product_name" name="product_name" value="{if isset($smarty.post.product_name)}{$smarty.post.product_name|escape:'html':'UTF-8'}{/if}" class="form-control" />
							</div>
							{hook h='displayMpaddproductnamebottom'}
					        <div class="form-group">	
								<label for="short_description" class="control-label">{l s='Short Description :' mod='marketplace'}</label>
								<textarea name="short_description" id="short_description" cols="2" rows="3" class="wk_tinymce form-control">{if isset($smarty.post.short_description)}{$smarty.post.short_description|escape:'html':'UTF-8'}{/if}</textarea>
							</div>

							<div class="form-group">	
								<label for="product_description" class="control-label">{l s='Description :' mod='marketplace'}</label>
							  	<textarea class="wk_tinymce form-control" value="test" id="product_description" name="product_description">{if isset($smarty.post.product_description)}{$smarty.post.product_description|escape:'html':'UTF-8'}{/if}</textarea>
							</div>

{*
							<div class="form-group">
								<div class="row">	
									<label for="product_condition" class="control-label col-lg-12">
										{l s='Condition :' mod='marketplace'}
									</label>
								</div>
								<div class="row">	
									<div class="col-lg-3">
									  	<select class="form-control" name="product_condition">
									  		<option value="new">{l s='New' mod='marketplace'}</option>
									  		<option value="used">{l s='Used' mod='marketplace'}</option>
									  		<option value="refurbished">{l s='Refurbished' mod='marketplace'}</option>
									  	</select>
								  	</div>
							  	</div>
							</div>
*}

							<div class="form-group">
								<label for="product_price" class="control-label required">{l s='Base Price :' mod='marketplace'}</label>
								<div class="input-group">
							  		<input type="text" id="product_price" name="product_price" value="{if isset($smarty.post.product_price)}{$smarty.post.product_price|escape:'html':'UTF-8'}{/if}"  class="form-control bikeprice"/>
							  		<span class="input-group-addon currency-symbol">{$currency->sign|escape:'html':'UTF-8'}</span>
							  	</div>
							  	{if isset($admin_commission)}
							  		<p class="help-block">{l s='Admin commission will be' mod='marketplace'} {$admin_commission|escape:'html':'UTF-8'}% {l s='of base price you entered' mod='marketplace'}</p>
							  	{/if}
							</div>
							{hook h='DisplayMpaddproductpricehook'}
							<div class="form-group">
								<label for="product_quantity" class="control-label required">{l s='Quantity :' mod='marketplace'}</label>
							   	<input type="text" id="product_quantity" name="product_quantity" value="{if isset($smarty.post.product_quantity)}{$smarty.post.product_quantity|escape:'html':'UTF-8'}{/if}"  class="account_input form-control"  />
							</div>	

							<div class="form-group">
								<label for="product_category" class="control-label required" >{l s='Category :' mod='marketplace'}</label>
								<div>{$categoryTree}</div>
							</div>

							<div class="form-group">   
								<label for="product_image">{l s='Upload Image :' mod='marketplace'}</label>
								<input type="file" id="product_image" name="product_image" value="" class="account_input form-control" size="chars"  />
							</div>

							<div class="form-group"> 
								<a class="btn btn-default button button-small wk-btn-other-img">
									<span>{l s='Add More Images' mod='marketplace'}</span>
								</a>
								<div id="wk_prod_other_images"></div>
					        </div>
					        {hook h="DisplayMpaddproductfooterhook"}	
					    </div>
					    {hook h="displayMpaddproducttabhook"}
						<div class="form-group" style="text-align:center;">
							<button type="submit" id="SubmitProduct" name="SubmitProduct" class="btn btn-default button button-medium">
								<span>{l s='Add Product' mod='marketplace'}</span>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to add product.' mod='marketplace'}</span>
	</div>
{/if}

{strip}
{addJsDefL name=req_prod_name}{l s='Product name is required.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=char_prod_name}{l s='Product name should be character.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=req_price}{l s='Product price is required.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=num_price}{l s='Product price should be numeric.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=req_qty}{l s='Product quantity is required.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=num_qty}{l s='Product quantity should be numeric.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=amt_valid}{l s='Amount should be numeric only.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=req_catg}{l s='Please select atleast one category.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=img_remove}{l s='Remove' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=conf_delete}{l s='Want to delete selected slot?' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name='choosefile_fileButtonHtml'}{l s='Choose File' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name='nofileselect_fileDefaultHtml'}{l s='No file selected' js=1 mod='marketplace'}{/addJsDefL}
{/strip}