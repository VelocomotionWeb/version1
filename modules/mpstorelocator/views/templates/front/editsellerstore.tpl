{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Seller Location' mod='mpstorelocator'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="col-sm-4 pull-right" style="margin-bottom:10px;">
	<div class="row">
		<a href="{$link->getModuleLink('mpstorelocator', 'sellerstores')}" class="btn btn-default button button-small pull-right">
			<span>{l s='Back to list' mod='mpstorelocator'}</span>
		</a>
		<a href="{$link->getModuleLink('mpstorelocator', 'addstore')}" class="btn btn-default button button-small pull-right" style="margin-right:5px;">
			<span>{l s='Add new store' mod='mpstorelocator'}</span>
		</a>
	</div>
</div>

<form action="{$link->getModuleLink('mpstorelocator', 'editstore')}" method="post" id="tagform-full" class="form-horizontal" role="form" enctype="multipart/form-data" novalidate>
	<div class="row">
		<input type="hidden" name="latitude" id="latitude" value="">
		<input type="hidden" name="longitude" id="longitude" value="">
		<input type="hidden" name="map_address" id="map_address" value="">
		<input type="hidden" name="id_customer" id="id_customer" value="{$id_customer}" />
		<input type="hidden" name="map_address_text" id="map_address_text" value="">
		<input type="hidden" id="seller_name" name="id_seller" value="{$id_seller}" />
		<div class="col-sm-4">
			<div class="form-group">
				<label for="sellername" class="col-sm-3 control-label">{l s='Seller' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<p class="form-control-static">{$seller_name}</p>
			    </div>
			</div>

			<div class="form-group">
				<label for="name" class="col-sm-3 control-label required">{l s='Store Name' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="shop_name" id="shop_name" value="{$store.name}">
					<input type="hidden" name="id_store" id="id_store" value="{$store.id}">
				</div>
			</div>

			<div class="form-group">
				<label for="street" class="col-sm-3 control-label required">{l s='Street' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<textarea class="form-control" name="street" id="street">{$store.street}</textarea>
				</div>
			</div>

			<div class="form-group">
				<label for="name" class="col-sm-3 control-label required">{l s='City' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="city_name" id="city_name" value="{$store.city_name}">
				</div>
			</div>

			<div class="form-group">
				<label for="country" class="col-sm-3 control-label required">{l s='Country' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="hidden" id="filtercountrypage" value="{$filtercountrypage}"/>
					<select  name="countries" id="countries" class="form-control">
		    			<option value="">{l s='Select' mod='mpstorelocator'}</option>
			    		{foreach $countries as $country}
			    			<option value="{$country.id_country}" {if isset($store)}{if $store.country_id == $country.id_country}selected{/if}{/if}>{$country.name}</option>
			    		{/foreach}
		    		</select>
				</div>
			</div>

			<div class="form-group">
				<label for="country" class="col-sm-3 control-label">{l s='State' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="hidden" id="filtercountrypage" value="{$filtercountrypage}"/>
					<select  name="state" id="state" class="form-control">
						<option value="">{l s='Select' mod='mpstorelocator'}</option>
		    		</select>
				</div>
			</div>

			<div class="form-group">
				<label for="zipcode" class="col-sm-3 control-label required">{l s='Zip/Postal Code' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="zip_code" id="zip_code" maxlength="12" value="{$store.zip_code}">
				</div>
			</div>

			<div class="form-group">
				<label for="zipcode" class="col-sm-3 control-label">{l s='Phone' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="phone" id="phone" value="{$store.phone}">
				</div>
			</div>

			{if $manage_status}
				<div class="form-group">
					<label for="status" class="col-sm-3 control-label">{l s='Status' mod='mpstorelocator'}</label>
					<div class="col-sm-8">
						<label class="radio-inline">
							<input type="radio" name="store_status" id="store_status" value="1" {if $store.active == 1}checked="checked"{/if}> {l s='Active' mod='mpstorelocator'}
						</label>
						<label class="radio-inline">
							<input type="radio" name="store_status" id="store_status" value="0" {if $store.active == 0}checked="checked"{/if}> {l s='Inactive' mod='mpstorelocator'}
						</label>
					</div>
				</div>
			{/if}

			<div class="form-group">
				<label class="col-sm-3 control-label"></label>
				<div class="col-sm-8">
					<div>
						<p><img class="img-thumbnail" src="{$modules_dir}mpstorelocator/views/img/store_logo/{$store.id}.jpg" title="{$store.name}" alt="{l s='No image found' mod='mpstorelocator'}"/></p>
						<p><a class="btn btn-default delete_store_logo" href="{$link->getModuleLink('mpstorelocator', 'savestore', ['id_delete_logo' => $store.id])}"><i class="icon-trash"></i> {l s='Delete' mod='mpstorelocator'}</a></p>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="storelogo" class="col-sm-3 control-label">{l s='Store Logo' mod='mpstorelocator'}</label>
				<div class="col-sm-9">
					<input type="file" name="store_logo" id="store_logo"/>
					<p class="help-block">{l s='Image maximum size must be 800 x 800 px' mod='mpstorelocator'}</p>
				</div>
			</div>

			<div class="form-group">
				<label for="storeproducts" class="col-sm-3 control-label">{l s='Store Products' mod='mpstorelocator'}</label>
				<div class="col-sm-8">
					{if isset($mp_products)}
						<select name="store_products[]" class="form-control" multiple="multiple">
							{foreach $mp_products as $product}
								<option value="{$product.id_product}"
								{if !empty($store.products)}
									{foreach $store.products as $p}
										{if $p.id_product == $product.id_product}
											selected
										{/if}
									{/foreach}
								{/if}
								>{$product.product_name}</option>
							{/foreach}
						</select>
						<p class="help-block">{l s='Select the products located on this store.' mod='mpstorelocator'}</p>
					{else}
						<p class="form-control-static">{l s='No product(s) found' mod='mpstorelocator'}</p>
					{/if}
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-6">
					<button type="submit" id="submit_store" class="btn btn-default button button-medium" name="update_store_submit">
						<span>{l s='Update' mod='mpstorelocator'}</span>
					</button>
				</div>
			</div>
		</div>
		<div class="col-sm-8">
			<input id="pac-input" class="controls" type="text" value="{$store.map_address_text}"
		        placeholder="{l s='Enter location' mod='mpstorelocator'}">
		    <input type="button" value="{l s='Search' mod='mpstorelocator'}" id="btn_store_search" class="btn btn-primary controls">
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
	{addJsDef id_state = $store.state_id}
	{addJsDef id_store = $store.id}
	{addJsDef lat = $store.latitude}
	{addJsDef lng = $store.longitude}
	{addJsDef map_address = $store.map_address}
	{addJsDef country_id = $store.country_id}
	{addJsDef url_file_state_edit=$link->getModuleLink('mpstorelocator', filterstate)}
{/strip}