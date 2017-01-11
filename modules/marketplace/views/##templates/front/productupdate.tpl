{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Product Update' mod='marketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
{if isset($product_upload)}
	{if $product_upload == 1}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='Your product uploaded successfully' mod='marketplace'}
		</div>
	{else if $product_upload == 2}
		<div class="alert alert-success">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='There was some error occurs while uploading your product' mod='marketplace'}
		</div>		
	{/if}
{/if}

{if isset($smarty.get.edited_conf)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Updated Successfully' mod='marketplace'}
	</p>
{/if}

{hook h='DisplayMpupdateproductheaderhook'}
<div class="main_block">
{hook h="DisplayMpmenuhook"}
<div class="dashboard_content">
	<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='Update Product' mod='marketplace'}</span>
	</div>
	<div class="wk_right_col">
		<form action="{$link->getModuleLink('marketplace', 'productupdate', ['edited' => 1, 'id' => $id])|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" accept-charset="UTF-8,ISO-8859-1,UTF-16" >
			{hook h='displayMpUpdateProductBodyHeaderOption'}
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
					{hook h='displayMpupdateproducttoppanel'}
					<div class="required form-group">
						<label for="product_name" class="control-label required">{l s='Product Name :' mod='marketplace'}</label>
						<input type="text" id="product_name" name="product_name" value="{$pro_info['product_name']|escape:'html':'UTF-8'}" class="form-control" />
					</div>
					{hook h='displayMpupdateproductnamebottom'}
					<div class="form-group">
						<label for="prod_short_desc" class="control-label">
							{l s='Short Description :' mod='marketplace'}
						</label>
						<textarea class="short_description wk_tinymce form-control" id="short_description" name="short_description">{$pro_info['short_description']|escape:'html':'UTF-8'}</textarea>
					</div>
					<div class="form-group">
						<label for="prod_desc" class="control-label">
							{l s='Description :' mod='marketplace'}
						</label>
						<textarea class="product_description wk_tinymce form-control" id="product_description" name="product_description">{$pro_info['description']|escape:'html':'UTF-8'}</textarea>
					</div>
					<div class="form-group">
						<div class="row">	
							<label for="product_condition" class="control-label col-lg-12">
								{l s='Condition :' mod='marketplace'}
							</label>
						</div>
						<div class="row">	
							<div class="col-lg-3">
							  	<select class="form-control" name="product_condition">
							  		<option value="new" {if $pro_info['condition'] == 'new'}selected{/if}>{l s='New' mod='marketplace'}</option>
							  		<option value="used" {if $pro_info['condition'] == 'used'}selected{/if}>{l s='Used' mod='marketplace'}</option>
							  		<option value="refurbished" {if $pro_info['condition'] == 'refurbished'}selected{/if}>{l s='Refurbished' mod='marketplace'}</option>
							  	</select>
						  	</div>
					  	</div>
					</div>
					<div class="form-group">
						<label for="prod_price" class="control-label required">{l s='Base Price :' mod='marketplace'}</label>
						<div class="input-group">
							<input type="text" id="product_price" name="product_price" value="{$pro_info['price']|escape:'html':'UTF-8'}"  class="form-control" />
							<span class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'}</span>
						</div>
					</div>
					{hook h='DisplayMpaddproductpricehook'}
					<div class="form-group">
						<label for="prod_quantity" class="control-label required">{l s='Quantity :' mod='marketplace'}</label>
						<input type="text" id="product_quantity" name="product_quantity" value="{$pro_info['quantity']|escape:'html':'UTF-8'}"  class="form-control"/>
					</div>
					<div class="form-group">
						<label for="prod_category" class="control-label required">{l s='Category :' mod='marketplace'}</label>
						<div>{$categoryTree}</div>
					</div>
					<div class="form-group">
						<label for="upload_image" class="control-label">
							{l s='Upload Image :' mod='marketplace'}
						</label>
						<input type="file" id="product_image" name="product_image" value="" class="account_input form-control" size="chars" />	
						<p class="help-block">{l s='Valid image extensions are jpg,jpeg and png.' mod='marketplace'}</p>		
					</div>
					<div class="form-group"> 
						<a class="btn btn-default button button-small wk-btn-other-img">
							<span>{l s='Add More Images' mod='marketplace'}</span>
						</a>
						<div id="wk_prod_other_images"></div>
			        </div>
					{hook h="DisplayMpupdateproductfooterhook"}
				</div>
				{hook h="displayMpupdateproducttabhook"}
				<div class="form-group" style="text-align:center;">
					<button type="submit" id="SubmitCreate" class="btn btn-default button button-medium">
						<span>{l s='Update' mod='marketplace'}</span>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
</div>

{strip}
{addJsDefL name=req_prod_name}{l s='Product name is required.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=char_prod_name}{l s='Product name should be character.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=req_price}{l s='Product price is required.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=num_price}{l s='Product price should be numeric.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=req_qty}{l s='Product quantity is required.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=num_qty}{l s='Product quantity should be numeric.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=req_catg}{l s='Please select atleast one category.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=img_remove}{l s='Remove' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name='choosefile_fileButtonHtml'}{l s='Choose File' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name='nofileselect_fileDefaultHtml'}{l s='No file selected' js=1 mod='marketplace'}{/addJsDefL}
{/strip}