<form action="" id="tagform-full" method="post" class="form-horizontal" role="form" enctype="multipart/form-data" novalidate>
	<div class="row"> 
		<input type="hidden" id="autocomplete_link"  value="{$autocomplete_link}" />
		<input type="hidden" name="latitude" id="latitude" value="">
		<input type="hidden" name="longitude" id="longitude" value="">
		<input type="hidden" name="map_address" id="map_address" value="">
		<input type="hidden" name="map_address_text" id="map_address_text" value="">
		<div class="col-sm-4">
			<div class="form-group">
				<label for="country" class="col-sm-4 control-label required">{l s='Select Seller' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<select name="seller_name" id="seller_name" class="form-control">
						{if isset($seller_info)}
							{if !isset($store)}<option value="0">{l s='Select Seller' mod='mpstorelocator'}</option>{/if}
							{foreach $seller_info as $seller}
								{if isset($store)}
									{if $store.id_seller == $seller.id}
										<option value="{$seller.id}" selected>{$seller.seller_name}</option>
									{/if}
								{else}
									<option value="{$seller.id}">{$seller.seller_name}</option>
								{/if}
							{/foreach}
						{else}
							<p>{l s='No seller found' mod='mpstorelocator'}
						{/if}
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="name" class="col-sm-4 control-label required">{l s='Store Name' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="shop_name" id="shop_name" value="{if isset($store)}{$store.name}{/if}">
					{if isset($store)}
						<input type="hidden" value="{$store.id}" name="id_store">
					{/if}
				</div>
			</div>

			<div class="form-group">
				<label for="street" class="col-sm-4 control-label required">{l s='Street' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<textarea class="form-control" name="street" id="street">{if isset($store)}{$store.street}{/if}</textarea>
				</div>
			</div>

			<div class="form-group">
				<label for="name" class="col-sm-4 control-label required">{l s='City' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="city_name" id="city_name" value="{if isset($store)}{$store.city_name}{/if}">
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-lg-4 required">{l s='Country' mod='mpstorelocator'}</label>
				<div class="col-lg-6">
					<select  name="countries" id="countries" class="form-control">
						<option value="">{l s='Select' mod='mpstorelocator'}</option>
						{foreach $countries as $country}
							<option value="{$country.id_country}" {if isset($store)}{if $store.country_id == $country.id_country}selected{/if}{/if}>{$country.name}</option>
						{/foreach}
					</select>
				</div>
			</div>

			<div class="form-group">
				<label for="country" class="col-sm-4 control-label">{l s='State' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<select  name="state" id="state" class="form-control">
						<option value="">{l s='Select' mod='mpstorelocator'}</option>
		    		</select>
				</div>
			</div>

			<div class="form-group">
				<label for="zipcode" class="col-sm-4 control-label required">{l s='Zip/Postal Code' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="zip_code" id="zip_code" maxlength="12" value="{if isset($store)}{$store.zip_code}{/if}">
				</div>
			</div>

			<div class="form-group">
				<label for="zipcode" class="col-sm-4 control-label">{l s='Phone' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="phone" id="phone" value="{if isset($store)}{$store.phone}{/if}">
				</div>
			</div>

			<div class="form-group">
				<label for="destine1" class="col-sm-4 control-label">{l s='Destination 1' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
                    <select name="destine1" id="destine1">
                    <option value="">Choisir</option>{$destine.meta_title}
                    {foreach from=$destines item=destine}
                    <option value="{$destine.meta_title}" {if $store.destine1 == $destine.meta_title}selected{/if}>{$destine.meta_title}</option>
                    {/foreach}
                    </select>
				</div>
			</div>

			<div class="form-group">
				<label for="destine2" class="col-sm-4 control-label">{l s='Destination 2' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
                    <select name="destine2" id="destine2">
                    <option value="">Choisir</option>
                    {foreach from=$destines item=destine}
                    <option value="{$destine.meta_title}" {if $store.destine2 == $destine.meta_title}selected{/if}>{$destine.meta_title}</option>
                    {/foreach}
                    </select>
				</div>
			</div>

			<div class="form-group">
				<label for="bic" class="col-sm-4 control-label">{l s='BIC' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="bic" id="bic" value="{if isset($store)}{$store.bic}{/if}">
				</div>
			</div>

			<div class="form-group">
				<label for="status" class="col-sm-4 control-label">{l s='Status' mod='mpstorelocator'}</label>
				<div class="col-lg-8">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" value="1" id="store_status_on" name="store_status" {if isset($store)}{if $store.active == 1} checked="checked"{/if}{/if}>
						<label for="store_status_on">{l s='Yes' mod='mpstorelocator'}</label>
						<input type="radio" value="0" id="store_status_off" name="store_status" {if isset($store)}{if $store.active == 0} checked="checked"{/if}{/if}>
						<label for="store_status_off">{l s='No' mod='mpstorelocator'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

			{if isset($store)}
				<div class="form-group">
					<label class="col-sm-4 control-label"></label>
					<div class="col-sm-8">
						<div class="form-group">
							<div>
								<p><img class="img-thumbnail" src="{$logo_path}" title="{$store.name}" alt="{l s='No image found' mod='mpstorelocator'}"/></p>
								<p><a class="btn btn-default delete_store_logo" data-id_store ="{$store.id}" href="#"><i class="icon-trash"></i> {l s='Delete' mod='mpstorelocator'}</a></p>
								<div class="alert alert-danger delete_store_logo_error">
								</div>
								<p class="alert alert-success delete_store_logo_success">
								</p>
							</div>
						</div>
					</div>
				</div>
			{/if}

			<div class="form-group">
				<label for="storelogo" class="col-sm-4 control-label">{l s='Store Logo' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="file" name="store_logo" id="store_logo"/>
					<p class="help-block">{l s='Image maximum size must be 800 x 800 px' mod='mpstorelocator'}</p>
				</div>
			</div>

			<div class="form-group">
				<label for="storeproducts" class="col-sm-4 control-label">{l s='Store Products' mod='mpstorelocator'}</label>
				<div class="col-sm-6">
					<select name="store_products[]" id="store_products" class="form-control" multiple="multiple">
						<option value="0">{l s='Select seller first' mod='mpstorelocator'}</option>
					</select>
					<p class="help-block">{l s='Select the products located on this store.' mod='mpstorelocator'}</p>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-6">
					<input type="submit" id="submit_store" name="submit_store" class="btn btn-default button button-medium" value="{l s='Submit' mod='mpstorelocator'}"/>
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<input id="pac-input" class="controls" type="text" value="{if isset($store)}{$store.map_address_text}{/if}"
		        placeholder="{l s='Enter location' mod='mpstorelocator'}">
			<input type="button" value="Search" id="btn_store_search" class="btn btn-primary controls">
		    <div id="map-canvas"></div>
		</div>
	</div>
</form>

{strip}
	{addJsDef url_filestate=$link->getModuleLink('mpstorelocator', filterstate)}
	{addJsDefL name='req_seller_name'}{l s='Seller name is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_shop_name'}{l s='Store name is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='inv_shop_name'}{l s='Store name is invalid.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_street'}{l s='Street is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_city_name'}{l s='City name is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='inv_city_name'}{l s='City name is invalid.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_countries'}{l s='Country is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_zip_code'}{l s='Zip/Postal code is required.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='inv_zip_code'}{l s='Zip/Postal code is inavlid.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDefL name='req_latitude'}{l s='Please select location on map' js=1 mod='mpstorelocator'}{/addJsDefL}
	
	{if isset($store)}
    	{addJsDef store = $store}
    	{addJsDef id_store = $store.id}
    	{addJsDef lat = $store.latitude}
    	{addJsDef lng = $store.longitude}
    	{addJsDef map_address = $store.map_address}
    	{addJsDef country_id = $store.country_id}
    	{addJsDef state_id = $store.state_id}
    	{addJsDef products = $store.products}
    	{addJsDef id_seller = $store.id_seller}
    {/if}
    
    {addJsDef admin_str_loc = $link->getAdminLink('AdminStoreLocator')}
    {addJsDefL name='no_product'}{l s='No product(s) found' js=1 mod='mpstorelocator'}{/addJsDefL}
{/strip}

<script type="text/javascript">
{if isset($store)}
	var id_store = {$store.id};
	var lat = {$store.latitude};
	var lng = {$store.longitude};
	var map_address = "{$store.map_address}";

	$("#latitude").val(lat);
	$("#longitude").val(lng);
	$("#map_address").val(map_address);
{/if}
$(document).ready(function(){
	var no_product = "{l s='No product(s) found' mod='mpstorelocator'}";
	$(".delete_store_logo_error").hide()
	$(".delete_store_logo_success").hide()
	// when edit store
	{if isset($store)}
		getStateJs({$store.country_id}, {$store.state_id});
		{if !empty($store.products)}
			getSellerProductsJs({$store.id_seller}, {$store.products});
		{/if}
	{/if}
	
	$("#countries").on("change", function(){
		var id_country = $(this).val();
		if (id_country == "")
			alert("Please select a country");
		else
			getStateJs(id_country);
	});


	$("#seller_name").on("change", function(){
		var id_seller = $(this).val();
		if (id_seller == 0)
		{
			alert("Please select seller");
			$("#store_products").empty();
			return false;
		}
		else
			getSellerProductsJs(id_seller);
	});

	$(document).on("click", ".delete_store_logo", function(e){
		e.preventDefault();
		var id_store = $(this).data("id_store");
		if (confirm("Are you sure?"))
		{
			$.ajax({
				url: admin_str_loc,
				dataType: "json",
				data: {
					ajax: "1",
					action: "deleteStoreLogo",
					id_store: id_store
				},
				success: function(result){
					if (result.status == "success")
					{
						$(".delete_store_logo_success").show();
						$(".delete_store_logo_success").html(result.msg);
						location.reload(true);
					}
					else
					{
						$(".delete_store_logo_error").show();
						$(".delete_store_logo_error").html(result.msg);
					}
				}
			})
		}
	});

	// filter state by country
	function getStateJs(id_country, id_state_selected)
	{
		$.ajax({
			url: admin_str_loc,
			dataType: "json",
			data: {
				ajax: "1",
				action: "filterStates",
				id_country: id_country
			},
			success: function(result){
				if (result != 'failed')
				{
					$("#state").empty();
					$("#state").append("<option value=''>Select</option>");
					$.each(result, function(index, value){
						if (id_state_selected == value.id_state)
							$("#state").append("<option value="+value.id_state+" selected>"+value.name+"</option>");
						else
							$("#state").append("<option value="+value.id_state+">"+value.name+"</option>");
					});
				}
			}
		});
	}

	// getting seller products
	function getSellerProductsJs(id_seller, id_products)
	{
		var selected_products = [];
		var all_products = [];
		$.ajax({
			url: admin_str_loc,
			dataType: "json",
			data: {
				ajax: "1",
				action: "getSellerProducts",
				id_seller: id_seller
			},
			success: function(result){
				$("#store_products").empty();
				if (result != 'failed')
				{
					$.each(result, function(index, value){
						if (id_products)
						{
							$.each(id_products, function(i, v){
								if (v.id_product == value.id_product)
								{
									$("#store_products").append("<option value="+value.id_product+" selected>"+value.product_name+"</option>");
									selected_products.push(value.id_product);
								}
							});
						}
						else
							$("#store_products").append("<option value="+value.id_product+">"+value.product_name+"</option>");

						all_products.push(value.id_product);
					});

					if (id_products)
					{
						var other = getArrayDiff(all_products, selected_products);
						$.each(result, function(index, value){
							$.each(other, function(i, v){
								if (value.id_product == v)
									$("#store_products").append("<option value="+value.id_product+">"+value.product_name+"</option>");
							});
						});
					}
				}
				else
					$("#store_products").append("<option value='0'>"+no_product+"</option>");
			}
		});
	}

	function getArrayDiff(large_array, small_array)
	{
		var diff = [];
		$.grep(large_array, function(el) {
	        if ($.inArray(el, small_array) == -1)
	        	diff.push(el);
		});

		return diff;
	}
});
</script>