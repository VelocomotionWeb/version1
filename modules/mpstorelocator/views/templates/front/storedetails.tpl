{capture name=path}{l s='Store Location' mod='mpstorelocator'}{/capture}
<div id="wrapper_store">
	<div id="wrapper_header">
		<div id="wrapper_header_left">
			<h2>{l s='Store Locator' mod='mpstorelocator'}</h2>
		</div>
		<div id="wrapper_header_right">
			<div id="search_products">
				{if isset($all_products)}
					<select name="search_products" id="select_search_products">
						<option value="0" {if isset($all_products[0].id_product)} data-all_product="{$all_products[0].id_product}" {else} data-all_product="0" {/if}>{l s='All Products' mod='mpstorelocator'}</option>
						{for $i=0 to count($all_products)-1}
							<option value="{$all_products[$i].id_product}" {if $active_product_id == $all_products[$i].id_product} selected="selected" {/if}>{$all_products[$i].product_name}</option>
						{/for}
					</select>
				{else}
				{/if}
			</div>
			<div id="search_city_block">
				<div id="search_city_field">
					<input id="search_city" class="form-control" type="text" placeholder="{l s='Name of city' mod='store'}" />
					<div id="wk_sl_search_spyglass"></div>
					<img src="{$modules_dir}mpstorelocator/views/img/spinner.gif" id="wk_sl_loader" style="display: none;">
				</div>
				<button class="btn btn-default button button-small" id="go_btn">
					<span>{l s='Search' mod='mpstorelocator'}</span>
				</button>
				<button class="btn btn-default button button-small" id="reset_btn">
					<span>{l s='Reset' mod='mpstorelocator'}</span>
				</button>
			</div>
		</div>
	</div>

	<div id="wrapper_content">
		<div id="wrapper_content_left">
			{if isset($store_locations)}
				{foreach $store_locations as $store}
					<div class="wk_store" style="border-bottom:1px solid #dbdbdb" id="{$store.id}" addr="{$store.map_address}" lat="{$store.latitude}" lng="{$store.longitude}">
						<div class="wk_store_img">
							<img src="{$modules_dir}mpstorelocator/views/img/store_logo/{$store.id}.jpg"/>
						</div>
						<div class="wk_store_details">
							<ul>
								<li><strong>{$store.name}</strong></li>
								<li>{$store.street}</li>
								<li>{$store.city_name}, {$store.state_name} {$store.zip_code}</li>
								<li>{$store.country_name}</li>
								<li>{$store.phone}</li>
							</ul>
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
		<div id="wrapper_content_right">
			<div id="map-canvas"></div>
		</div>
	</div>
</div>
{strip}
	{addJsDefL name='no_store_msg'}{l s='No store found.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDef store_loc_lat = $store_locations['0'].latitude}
	{addJsDef first_lat = $store_locations['0'].latitude}
	{addJsDef first_lng = $store_locations['0'].longitude}
	{addJsDef address = $store_locations['0'].map_address}
	{addJsDef url_getstore_by_product=$link->getModuleLink('mpstorelocator', 'getstorebyproduct')}
	{addJsDef url_getstorebykey=$link->getModuleLink('mpstorelocator', 'getstorebykey')}
{/strip}
