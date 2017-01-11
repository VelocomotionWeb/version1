{capture name=path}{l s='Store Location' mod='mpstorelocator'}{/capture}
<div id="wrapper_store">
	<button class="btn btn-default button button-small other_btn" id="reset_btn" style="float:left;">
		<span>{l s='Reset' mod='mpstorelocator'}</span>
	</button>
	<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'html':'UTF-8'};">
			<i class="icon-map-marker"></i>
			{l s='Store Locator' mod='mpstorelocator'}
		</span>
	</div>

	<div id="wrapper_content">
		<div class="wrapper_left_div">
			<div id="search_city_block" style="border-bottom:1px solid #dbdbdb;">
				<div id="search_city_field">
					<input id="search_city" class="form-control" type="text" placeholder="{l s='Enter City Name' mod='mpstorelocator'}" />
					<div id="wk_sl_search_spyglass">
						<!-- <img src="{$modules_dir|escape:'html':'UTF-8'}mpstorelocator/views/img/icon-searchimg.png" class="img-responsive"> -->
					</div>
				</div>
				<button class="btn btn-default button button-small" id="go_btn">
					<span>{l s='Go' mod='mpstorelocator'}</span>
				</button>
			</div>

			<div id="wrapper_content_left">
				{if isset($store_locations)}
					{foreach $store_locations as $store}
						<div class="wk_store" style="border-bottom:1px solid #dbdbdb" id="{$store.id|escape:'htmlall':'UTF-8'}" addr="{$store.map_address|escape:'htmlall':'UTF-8'}" lat="{$store.latitude|escape:'htmlall':'UTF-8'}" lng="{$store.longitude|escape:'htmlall':'UTF-8'}">
							<div class="wk_store_img">
								{if $store.img_exist}
									<img src="{$modules_dir|escape:'html':'UTF-8'}mpstorelocator/views/img/store_logo/{$store.id|escape:'htmlall':'UTF-8'}.jpg"/>
								{else}
									<img src="{$modules_dir|escape:'html':'UTF-8'}mpstorelocator/views/img/store_logo/default.jpg"/>
								{/if}
							</div>
							<div class="wk_store_details">
								<ul>
									<li class="store_name">{$store.name|escape:'html':'UTF-8'}</li>
									<li>{$store.street|escape:'html':'UTF-8'}, {$store.city_name|escape:'html':'UTF-8'}</li>
									<li>{$store.state_name|escape:'html':'UTF-8'} {$store.zip_code|escape:'html':'UTF-8'}</li>
									<li>{$store.country_name|escape:'html':'UTF-8'}</li>
									<li>{$store.phone|escape:'html':'UTF-8'}</li>s
								</ul>
							</div>
						</div>
					{/foreach}
					<input type="hidden" name="store_loc_lat" id="store_loc_lat" value="{$store_locations['0'].latitude}">
					<input type="hidden" name="first_lat" id="first_lat" value="{$store_locations['0'].latitude}">
					<input type="hidden" name="first_lng" id="first_lng" value="{$store_locations['0'].longitude}">
					<input type="hidden" name="address" id="address" value="{$store_locations['0'].map_address}">
				{/if}
			</div>
		</div>
		<div id="wrapper_content_right">
			<div id="map-canvas"></div>
		</div>
		<div style="clear:both;"></div>
	</div>
</div>
{strip}
	{addJsDefL name='no_store_msg'}{l s='No store found.' js=1 mod='mpstorelocator'}{/addJsDefL}
	{addJsDef url_getstore_by_product=$link->getModuleLink('mpstorelocator', 'getstorebyproduct')}
	{addJsDef url_getstorebykey=$link->getModuleLink('mpstorelocator', 'getstorebykey')}
{/strip}
