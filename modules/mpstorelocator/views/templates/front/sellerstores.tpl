<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Store Location' mod='mpstorelocator'}</span>
{/capture}
{if isset($success)}
	{if $success == 1}
		<p class="alert alert-success">
			{if $manage_status}
				{l s='Store location created.' mod='mpstorelocator'}
			{else}
				{l s='Store location created. Location will be activated after admin approval. Please wait.' mod='mpstorelocator'}
			{/if}
		</p>
	{else if $success == 2}
		<p class="alert alert-success">
			{l s='Store location updated.' mod='mpstorelocator'}
		</p>
	{/if}
{/if}

{if isset($deleted)}
	{if $deleted == 1}
		<p class="alert alert-success">
			{l s='Store deleted.' mod='mpstorelocator'}
		</p>
	{else if $deleted == 2}
		<p class="alert alert-danger">
			{l s='Some problem while deleting this store.' mod='mpstorelocator'}
		</p>
	{/if}
{/if}

{if isset($delete_logo_msg)}
	{if $delete_logo_msg == 1}
		<p class="alert alert-success">
			{l s='Store logo deleted successfully.' mod='mpstorelocator'}
		</p>
	{else if $delete_logo_msg == 2}
		<p class="alert alert-danger">
			{l s='Error while deleting image.' mod='mpstorelocator'}
		</p>
	{/if}
{/if}

{if isset($store_locations)}
<div id="wrapper_store">
	<div id="wrapper_header">
		<div id="wrapper_header_left">
			<h2>{l s='Store Location' mod='mpstorelocator'}</h2>
		</div>
		
		<div id="wrapper_header_right">
			<a class="btn btn-default button button-small pull-right" style="margin-bottom:5px;" href="{$link->getModuleLink('mpstorelocator', 'addstore')}">
				<span>{l s='Add New Store' mod='mpstorelocator'}</span>
			</a>
			<div id="search_products">
				{if isset($mp_products)}
					<select name="search_products" id="select_search_products">
						<option value="0">{l s='All Products' mod='mpstorelocator'}</option>
						{foreach $mp_products as $product}
							<option value="{$product.id_product}">{$product.product_name}</option>
						{/foreach}
					</select>
				{else}
					<select name="search_products">
						<option>{l s='No product found' mod='mpstorelocator'}</option>
					</select>
				{/if}
			</div>
			
			<!-- <div id="search_products">
				{if isset($sellers)}
					<select name="search_seller" id="select_search_seller">
						{foreach $sellers as $id_seller => $seller_name}
							<option value="{$id_seller}">{$seller_name}</option>
						{/foreach}
					</select>

					{foreach $sellers as $id_seller => $seller_name}
						<input type="hidden" value="{$id_seller}" id="id_seller"/>
					{/foreach}
				{/if}
			</div> -->
			<div id="search_city_block">
				<div id="search_city_field">
					<input id="search_city" type="text" placeholder="{l s='Name of city' mod='store'}" />
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
			{foreach $store_locations as $store}
				<div class="wk_full_store">
					<div class="wk_store" id="{$store.id}" addr="{$store.map_address}" lat="{$store.latitude}" lng="{$store.longitude}">
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
					<div class="col-sm-12">
						<div class="row pull-right">
							<a title="{l s='Edit' mod='mpstorelocator'}" href="{$link->getModuleLink('mpstorelocator', 'editstore', ['id_store' => $store.id])}"><i class="fa fa-edit"></i></a>&nbsp;
							<a title="{l s='Delete' mod='mpstorelocator'}" class="delete_store" href="{$link->getModuleLink('mpstorelocator', 'editstore', ['id_store' => $store.id, 'delete' => 1])}"><i class="fa fa-trash-o"></i></a>
							{if $store.active}
								<i class="fa fa-ok-sign" title="{l s='Active' mod='mpstorelocator'}"></i>
							{else}
								<i class="fa fa-remove" title="{l s='Pending' mod='mpstorelocator'}"></i>
							{/if}
						</div>
					</div>
				</div>
			{/foreach}
		</div>
		<div id="wrapper_content_right">
			<div id="map-canvas"></div>
		</div>
	</div>
</div>
{strip}
	{addJsDef first_lat = $store_locations['0'].latitude}
	{addJsDef first_lng = $store_locations['0'].longitude}
	{addJsDef address = $store_locations['0'].map_address}
{/strip}	
{else}
	<div class="alert alert-info">
		{l s='No store found' mod='mpstorelocator'}&nbsp;&nbsp;
		<a href="{$link->getModuleLink('mpstorelocator', 'addstore')}" class="btn btn-default">
			<span>{l s='Create Store Location' mod='mpstorelocator'}</span>
		</a>
	</div>
{/if}

{strip}
	{addJsDef url_getstore_by_seller=$link->getModuleLink('mpstorelocator', 'getstorebyseller')}
	{addJsDef url_getstorebyproduct=$link->getModuleLink('mpstorelocator', 'getstorebyproduct')}
	{addJsDef url_getstorebykey=$link->getModuleLink('mpstorelocator', 'getstorebykey')}
{/strip}

