{block name="other_fieldsets"}
{if isset($assignmpproduct)}
	{if isset($mp_sellers)}
		<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}&assignmpproduct=1" class="defaultForm form-horizontal {$name_controller|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-user"></i> {l s='Assign product' mod='marketplace'}
				</div>
				<div class="form-wrapper">
					<div class="form-group">
						<label class="control-label col-lg-3">
							<span>{l s='Select Seller' mod='marketplace'} : </span>
						</label>
						<div class="col-lg-4 ">
							<select class="fixed-width-xl" name="id_customer">
								{foreach $mp_sellers as $seller}
									<option value="{$seller.id_customer|escape:'html':'UTF-8'}">{$seller.business_email|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-lg-3">
							<span>{l s='Select Product' mod='marketplace'} : </span>
						</label>
						<div class="col-lg-4 ">
							{if isset($ps_products)}
								<select class="fixed-width-xl" name="id_product">
									{foreach $ps_products as $product}
										<option value="{$product.id_product|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'} ({$product.id_product|escape:'htmlall':'UTF-8'})</option>
									{/foreach}
								</select>
							{else}
								<p class="info-block">{l s='No products available' mod='marketplace'}</p>
							{/if}
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<a href="{$link->getAdminLink('AdminSellerProductDetail')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='marketplace'}</a>
					<button type="submit" name="submitAddmarketplace_seller_product" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Assign' mod='marketplace'}</button>
					<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
						<i class="process-icon-save"></i> {l s='Assign and stay' mod='marketplace'}
					</button>
				</div>
			</div>
		</form>
	{else}
		<div class="alert alert-danger">
			{l s='No seller found' mod='marketplace'}
		</div>
	{/if}
{else}
	<div class="panel">
		<div class="panel-heading">
			{if isset($edit)}
				{l s='Edit Product' mod='marketplace'}
			{else}
				{l s='Add New Product' mod='marketplace'}
			{/if}
		</div>
	    <form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
			{if !isset($edit)}
				{hook h='DisplayMpaddproductheaderhook'}
				<div class="form-group">	
					<label class="col-lg-3 control-label required">{l s='Choose Seller' mod='marketplace'}</label>
					<div class="col-lg-6">
						{if isset($customer_info)}
							<select name="shop_customer" class="fixed-width-xl">
								{foreach $customer_info as $cusinfo}
									<option value="{$cusinfo['id_customer']|escape:'html':'UTF-8'}" {if isset($smarty.post.shop_customer)}{if $smarty.post.shop_customer == $cusinfo['id_customer']}Selected="Selected"{/if}{/if}>
										{$cusinfo['business_email']|escape:'html':'UTF-8'}
									</option>
								{/foreach}
							</select>
						{else}
							<p>{l s='No seller found.' mod='marketplace'}</p>
						{/if}
					</div>
				</div>
			{else}
				<input type="hidden" value="{$product.id|escape:'html':'UTF-8'}" name="id" />
				{hook h='displayMpupdateproducttoppanel'}
			{/if}

			<div class="form-group">	
				<label class="col-lg-3 control-label required" for="product_name" >
					{l s='Product Name :' mod='marketplace'}
				</label>
				<div class="col-lg-6">
					<input type="text" id="product_name" name="product_name" class="form-control" {if isset($edit)}value="{$product.product_name|escape:'html':'UTF-8'}"{else}value="{if isset($smarty.post.product_name)}{$smarty.post.product_name|escape:'htmlall':'UTF-8'}{/if}"{/if}/> 
				</div>
			</div>
			
			<div class="form-group">	
				<label class="col-lg-3 control-label" for="short_description">{l s='Short Description :' mod='marketplace'}</label>
				<div class="col-lg-6">
				<textarea id="short_description" name="short_description" class="short_description wk_tinymce form-control">{if isset($edit)}{$product.short_description|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.short_description)}{$smarty.post.short_description|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
				</div>
			</div>

			<div class="form-group">	
				<label class="col-lg-3 control-label" for="product_description">{l s='Description :' mod='marketplace'}</label>
				<div class="col-lg-6">
					<textarea id="product_description" name="product_description" class="product_description wk_tinymce form-control">{if isset($edit)}{$product.description|escape:'htmlall':'UTF-8'}{else}{if isset($smarty.post.product_description)}{$smarty.post.product_description|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
				</div>
			</div>

			<div class="form-group">
					<label for="product_condition" class="control-label col-lg-3">
						{l s='Condition :' mod='marketplace'}
					</label>
					<div class="col-lg-3">
					  	<select class="form-control" name="product_condition">
					  		<option value="new" {if isset($edit)}{if $product.condition == 'new'}Selected="Selected"{/if}{else}{if isset($smarty.post.product_condition)}{if $smarty.post.product_condition == 'new'}Selected="Selected"{/if}{/if}{/if}>
					  			{l s='New' mod='marketplace'}
					  		</option>
					  		<option value="used" {if isset($edit)}{if $product.condition == 'used'}Selected="Selected"{/if}{else}{if isset($smarty.post.product_condition)}{if $smarty.post.product_condition == 'used'}Selected="Selected"{/if}{/if}{/if}>
					  			{l s='Used' mod='marketplace'}
					  		</option>
					  		<option value="refurbished" {if isset($edit)}{if $product.condition == 'refurbished'}Selected="Selected"{/if}{else}{if isset($smarty.post.product_condition)}{if $smarty.post.product_condition == 'refurbished'}Selected="Selected"{/if}{/if}{/if}>
					  			{l s='Refurbished' mod='marketplace'}
					  		</option>
					  	</select>
				  	</div>
			</div>

			<div class="form-group">	
				<label class="col-lg-3 control-label required" for="product_price">
					{l s='Price :' mod='marketplace'}
				</label>
				<div class="col-lg-6">
					<input type="text" class="form-control" id="product_price" name="product_price" {if isset($edit)}value="{$product.price|escape:'htmlall':'UTF-8'}"{else}value="{if isset($smarty.post.product_price)}{$smarty.post.product_price|escape:'htmlall':'UTF-8'}{/if}"{/if} />
				</div>
			</div>

			{hook h='DisplayMpaddproductpricehook'}
 
			<div class="form-group">
				<label class="col-lg-3 control-label required" for="product_quantity">
					{l s='Quantity :' mod='marketplace'}
				</label>
				<div class="col-lg-6">
					<input type="text" class="form-control" id="product_quantity" name="product_quantity" {if isset($edit)}value="{$product.quantity|escape:'htmlall':'UTF-8'}"{else}value="{if isset($smarty.post.product_quantity)}{$smarty.post.product_quantity|escape:'htmlall':'UTF-8'}{/if}"{/if} />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label required" for="product_category">
					{l s='Category :' mod='marketplace'}
				</label>
				<div class="col-lg-6">
					{$categoryTree}
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label" for="product_image">
					{l s='Upload Image :' mod='marketplace'}
				</label>
				<div class="col-lg-6">
					<input type="file" id="product_image" name="product_image" value="" size="chars" />
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label" for="other_image">
				</label>
				<div class="col-lg-6">
					<a class="btn btn-default wk-btn-other-img">
						<i class="icon-image"></i>
						<span>{l s='Add Other Image' mod='marketplace'}</span>
					</a>
					<div id="wk_prod_other_images"></div>
				</div>
			</div>

			<!-- Image details if edit -->
			{if isset($edit)}
				{if isset($active_product)} <!-- If product activated -->
					{if isset($image_detail)}
						<div class="form-group">
							<label for="product_image">{l s='Active Image for Product :' mod='marketplace'}</label>
						</div>
						<div class="form-group">
							<table class="table">
								<thead>
									<tr>
										<th>{l s='Image' mod='marketplace'}</th>
										<th>{l s='Position' mod='marketplace'}</th>
										<th>{l s='Cover' mod='marketplace'}</th>
										<th>{l s='Action' mod='marketplace'}</th>		
									</tr>
								</thead>
								<tbody>
								{foreach $image_detail as $image}
									<tr class="imageinforow{$image.id_image|escape:'html':'UTF-8'}">
										<td>
											<a class="fancybox" href="{$image.image_path|escape:'htmlall':'UTF-8'}">
												<img class="img-thumbnail" alt="{l s='no image' mod='marketplace'}" src="{$link->getImageLink($image.link_rewrite, $image.image, 'cart_default')|escape:'html':'UTF-8'}">
											</a>
										</td>
										<td>{$image.position|escape:'html':'UTF-8'}</td>
										<td>
											{if $image.cover}
												<img class="covered notcover{$image.cover|escape:'html':'UTF-8'}" id="changecoverimage{$image.id_image|escape:'html':'UTF-8'}" id_image="{$image.id_image|escape:'html':'UTF-8'}" src="../img/admin/enabled.gif" is_cover="1" id_pro="{$id_product|escape:'html':'UTF-8'}">
											{else}
												<img class="covered notcover{$image.cover|escape:'html':'UTF-8'}" id="changecoverimage{$image.id_image|escape:'html':'UTF-8'}" id_image="{$image.id_image|escape:'html':'UTF-8'}" src="../img/admin/forbbiden.gif" is_cover="0" id_pro="{$id_product|escape:'html':'UTF-8'}">
											{/if}
										</td>
										<td>
										{if $image.cover}
											<a class="delete_pro_image btn btn-default" href="#" is_cover="1" id_image="{$image.id_image|escape:'html':'UTF-8'}" id_pro="{$id_product|escape:'html':'UTF-8'}">
												<i class="icon-trash-o"></i>
												{l s='Delete this image' mod='marketplace'}
											</a>
										{else}
											<a class="delete_pro_image btn btn-default" href="#" is_cover="0" id_image="{$image.id_image|escape:'html':'UTF-8'}" id_pro="{$id_product|escape:'html':'UTF-8'}">
												<i class="icon-trash-o"></i>
												{l s='Delete this image' mod='marketplace'}
											</a>
										{/if}
										</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					{/if}
				{/if}

				{if isset($unactive_image)} <!-- If product not activated yet -->
					<div class="form-group">
						<label for="product_image">{l s='Unactive Image for Product :' mod='marketplace'}</a></label>
					</div>
					<div class="form-group">
						<table class="table">
							<thead>
								<tr>
									<th>{l s='Image' mod='marketplace'}</th>
									<th>{l s='Action' mod='marketplace'}</th>		
								</tr>
							</thead>
							<tbody>
							{foreach $unactive_image as $image}
								<tr class="unactiveimageinforow{$image.id|escape:'html':'UTF-8'}">
									<td>
										<a class="fancybox" href="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/product_img/{$image.seller_product_image_id|escape:'html':'UTF-8'}.jpg">
											<img width="45" height="45" alt="{l s='No Image' mod='marketplace'}" src="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/product_img/{$image.seller_product_image_id|escape:'html':'UTF-8'}.jpg" />
										</a>
									</td>
									<td>
										<a class="delete_unactive_pro_image btn btn-default" href="#" id_image="{$image.id|escape:'html':'UTF-8'}" img_name="{$image.seller_product_image_id|escape:'html':'UTF-8'}">
											<i class="icon-trash"></i>
											{l s='Delete this image' mod='marketplace'}
										</a>
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				{/if}
				{hook h="DisplayMpupdateproductfooterhook"}
			{else}
				{hook h="DisplayMpaddproductfooterhook"}
			{/if}
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminSellerProductDetail')|escape:'html':'UTF-8'}" class="btn btn-default">
					<i class="process-icon-cancel"></i>{l s='Cancel' mod='marketplace'}
				</a>
				<button type="submit" name="submitAddmarketplace_seller_product" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save' mod='marketplace'}
				</button>
				<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Save and stay' mod='marketplace'}
				</button>
			</div>
		</form>
	</div>
{/if}
{/block}

{block name=script}
<script type="text/javascript">
	// for tiny mce setup
	var iso = "{$iso|escape:'htmlall':'UTF-8'}";
	var pathCSS = "{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}";
	var ad = "{$ad|escape:'htmlall':'UTF-8'}";
	$(document).ready(function(){
		{block name="autoload_tinyMCE"}
			tinySetup({
				editor_selector :"wk_tinymce",
				width : 700
			});
		{/block}
	});

	// create other image browse link
	var server_error = "{l s='Some error occurs while deleting image' js='1' mod='marketplace'}";
	var confirm_msg = "{l s='Are you sure?' js='1' mod='marketplace'}";
	var img_remove = "{l s='Remove' js=1 mod='marketplace'}"

	$('.fancybox').fancybox();
	$('.delete_unactive_pro_image').on('click', function(e){
		e.preventDefault();
		var id_image = $(this).attr('id_image');
		var img_name = $(this).attr('img_name');
		var r = confirm(confirm_msg);
		if (r == true) 
		{
			$.ajax({
				type: "POST",
				url: "{$link->getAdminLink('AdminSellerProductDetail')|addslashes}",
				data:{
					ajax:true,
					action:'deleteUnactiveImage',
					id_image:id_image,
					img_name:img_name
				},
				dataType: "json",
				success: function(result)
				{
					if (result.status == 2) 
						alert(server_error);
					else if (result.status == 1)
					{
						$(".unactiveimageinforow"+id_image).fadeOut("normal", function() {
					        $(this).remove();
					    });
					}
					else
						alert("some server error");
				}
			});
		}
	});
	
	$('.delete_pro_image').on('click', function(e){
		e.preventDefault();
		var id_image = $(this).attr('id_image');
		var is_cover = $(this).attr('is_cover');
		var id_pro = $(this).attr('id_pro');
		var r = confirm(confirm_msg);
		if (r == true) 
		{
			$.ajax({
				type: "POST",
				url: "{$link->getAdminLink('AdminSellerProductDetail')|addslashes}",
				data: {
					ajax:true,
					action: 'deleteActiveImage',
					id_image:id_image,
					is_cover:is_cover,
					id_pro:id_pro,
				},
				dataType: "json",
				success: function(result)
				{
					if (result.status == 3)
						alert(server_error);
					else if (result.status == 1)
					{
						$(".imageinforow"+id_image).fadeOut("normal", function() {
					        $(this).remove();
					    });
					}
					else if (result.status == 2) // if cover image deleted, reload to show other maked as cover image
						location.reload(true); 
				}
			});
		}
	});

	$('.covered').on('click',function(e) {
		e.preventDefault();
		var id_image = $(this).attr('id_image');
		var is_cover = $(this).attr('is_cover');
		var id_pro = $(this).attr('id_pro');
		if(is_cover == 0) 
		{
			$.ajax({
				type: "POST",
				url: "{$link->getAdminLink('AdminSellerProductDetail')|addslashes}",
				data: {
					ajax:true,
					action:'changeImageCover',
					id_image:id_image,
					is_cover:is_cover,
					id_pro:id_pro
				},
				dataType: "json",
				success: function(result)
				{
					if (result.status == 2)
						alert(server_error);
					else if (result.status == 1)
					{
						if (is_cover == 0) 
						{
							$('.covered').attr('src','../img/admin/forbbiden.gif');
							$('.covered').attr('is_cover','0')
							$('#changecoverimage'+id_image).attr('src','../img/admin/enabled.gif')
							$('#changecoverimage'+id_image).attr('is_cover','1');
						}
					}
					else
						alert("some server error");
				}
			});
		}
	});
</script>
{/block}