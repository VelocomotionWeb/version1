<div class="col-lg-12">
{if isset($product_activated)}
	<table id="imageTable" class="table">
		<caption>{l s='Active Image' mod='marketplace'}</caption>
		<thead>
			<tr>
				<th>#</th>
				<th>{l s='Image Id' mod='marketplace'}</th>
				<th>{l s='Image' mod='marketplace'}</th>
				<th>{l s='Position' mod='marketplace'}</th>
				<th>{l s='Cover' mod='marketplace'}</th>
				<th>{l s='Action' mod='marketplace'}</th>		
			</tr>
		</thead>
	{if isset($image_detail)}
		{assign var=j value=1}
		<tbody>
			{foreach $image_detail as $image}
				<tr class="imageinforow{$image.id_image|escape:'html':'UTF-8'}">
					<td>{$j|escape:'html':'UTF-8'}</td>
					<td>{$image.id_image|escape:'html':'UTF-8'}</td>
					<td>
						<a class="fancybox" href="{$image.image_path|escape:'html':'UTF-8'}">
							<img class="img-thumbnail" width="80" height="80" src="http://{$image.image_link|escape:'html':'UTF-8'}"/>
						</a>
					</td>
					<td>
						{$image.position|escape:'html':'UTF-8'}
					</td>
					<td>
						{if $image.cover == 1 }
							<img class="covered" id="changecoverimage{$image.id_image|escape:'html':'UTF-8'}" alt="{$image.id_image|escape:'html':'UTF-8'}" src="{$img_ps_dir|escape:'html':'UTF-8'}enabled.gif" is_cover="1" id_pro="{$id_product|escape:'html':'UTF-8'}"/>
						{else}
							<img class="covered" id="changecoverimage{$image.id_image|escape:'html':'UTF-8'}" alt="{$image.id_image|escape:'html':'UTF-8'}" src="{$img_ps_dir|escape:'html':'UTF-8'}forbbiden.gif" is_cover="0" id_pro="{$id_product|escape:'html':'UTF-8'}"/>
						{/if}
					</td>
					<td>
						{if $image.cover == 1}
							<a class="delete_pro_image pull-left btn btn-default" href="" is_cover="1" id_pro="{$id_product|escape:'html':'UTF-8'}" id_image="{$image.id_image|escape:'html':'UTF-8'}">
								<i class="icon-trash"></i> {l s='Delete' mod='marketplace'}
							</a>
						{else}
							<a class="delete_pro_image pull-left btn btn-default" href="" is_cover="0" id_pro="{$id_product|escape:'html':'UTF-8'}" id_image="{$image.id_image|escape:'html':'UTF-8'}">
								<i class="icon-trash"></i> {l s='Delete' mod='marketplace'}
							</a>
						{/if}
					</td>
				</tr>
			{assign var=j value=$j+1}	
			{/foreach}
		</tbody>
	{else}
		<tbody>
			<tr>
				<td></td>
				<td colspan="2">{l s='No image available' mod='marketplace'}</td>
				<td></td>
			</tr>
		</tbody>
	{/if}
	</table>

	<!-- I think this code is not using, but not deleted may be there is some reason to code.
	{if isset($unactive_image)}
		<table id="imageTable" class="table">
			<caption>{l s='Unactive Image' mod='marketplace'}</caption>
			<tr>
				<th>{l s='Image' mod='marketplace'}</th>
				<th>{l s='Action' mod='marketplace'}</th>		
			</tr>
			{foreach $unactive_image as $image}
				<tr class="unactiveimageinforow{$image['id']|escape:'html':'UTF-8'}">
					<td>
						<a class="fancybox" href="{$modules_dir|escape:'html':'UTF-8'}marketplace/img/product_img/{$image.seller_product_image_id|escape:'html':'UTF-8'}.jpg">
							<img class="img-thumbnail" width="80" height="80" src="{$modules_dir|escape:'html':'UTF-8'}marketplace/img/product_img/{$image.seller_product_image_id|escape:'html':'UTF-8'}.jpg" />
						</a>
					</td>
					<td>
						<img title="Delete this image" class="delete_unactive_pro_image" alt="{$image['id']|escape:'html':'UTF-8'}" src="{$img_ps_dir|escape:'html':'UTF-8'}delete.gif" img_name="{$image['seller_product_image_id']|escape:'html':'UTF-8'}"/>
					</td>
				</tr>	
			{/foreach}
		</table>
	{/if} -->
{else}
	{if isset($unactive_image_only)}
		<table id="imageTable" cellspacing="0" cellpadding="0" class="table">
			<caption>{l s='Unactive Image' mod='marketplace'}</caption>
			<tr>
				<th>{l s='Image' mod='marketplace'}</th>
				<th>{l s='Action' mod='marketplace'}</th>		
			</tr>
			{foreach $unactive_image_only as $image}
				<tr class="unactiveimageinforow{$image['id']|escape:'html':'UTF-8'}">
					<td>
						<a class="fancybox" href="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/product_img/{$image['seller_product_image_id']|escape:'html':'UTF-8'}.jpg">
							<img class="img-thumbnail" width="80" height="80" src="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/product_img/{$image['seller_product_image_id']|escape:'html':'UTF-8'}.jpg" />
						</a>
					</td>
					<td>
						<a class="delete_unactive_pro_image pull-left btn btn-default" href="" id_image="{$image['id']|escape:'html':'UTF-8'}" img_name="{$image['seller_product_image_id']|escape:'html':'UTF-8'}">
							<i class="icon-trash"></i> {l s='Delete' mod='marketplace'}
						</a>
					</td>
				</tr>
			{/foreach}
		</table>
	{else}
		<div class="alert alert-info">
			{l s='No image available' mod='marketplace'}
		</div>	
	{/if}
{/if}		
</div>














