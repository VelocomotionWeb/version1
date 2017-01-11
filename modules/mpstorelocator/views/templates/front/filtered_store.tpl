<!-- Displaying this using ajax response -->
{if isset($filtered_stores)}
	{foreach $filtered_stores as $store}
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
					{if isset($store.edit_store_link)} <!--if search by seller-->
						<li class="pull-right">
							<a href="{$store.edit_store_link}"><i class="icon-edit"></i></a>&nbsp;
							<a class="delete_store" href="{$store.edit_store_link}"><i class="icon-trash"></i></a>
							{if $store.active}
								&nbsp;<i class="icon-ok-sign" title="{l s='Active' mod='mpstorelocator'}"></i>
							{else}
								&nbsp;<i class="icon-remove" title="{l s='Pending' mod='mpstorelocator'}"></i>
							{/if}
						</li>
					{/if}
				</ul>
			</div>
		</div>
	{/foreach}
{/if}