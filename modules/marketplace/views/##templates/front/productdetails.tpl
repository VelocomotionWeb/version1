{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Product Details' mod='marketplace'}</span>
{/capture}

<div class="main_block">
{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
		<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
			<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Product Details' mod='marketplace'}</span>
		</div>
		<div class="wk_right_col">
			<div class="wk_head">
				{if $is_approve == 1}
					<a href="{$link->getProductLink($obj_product)|escape:'html':'UTF-8'}" class="btn btn-default button button-small">
						<span>{l s='View Product' mod='marketplace'}</span>
					</a>
				{/if}
				<a href="{$link->getModuleLink('marketplace','productlist')|escape:'html':'UTF-8'}" class="btn btn-default button button-small">
					<span>{l s='Back to product list' mod='marketplace'}</span>
				</a>
			</div>
			<div class="wk_product_details">
				<div class="wk_details">
					<div class="row">
						<label class="col-md-3">{l s='Product Name' mod='marketplace'} - </label>
						<div class="col-md-9">{$product.product_name|escape:'html':'UTF-8'}</div>
					</div>
					<div class="row">
						<label class="col-md-3">{l s='Description' mod='marketplace'} -	</label>
						<div class="col-md-9">{$product.description|escape:'quotes':'UTF-8'}</div>
					</div>
					<div class="row">
						<label class="col-md-3">{l s='Price' mod='marketplace'} -</label>
						<div class="col-md-9">{convertPrice price=$product.price}</div>
					</div>
					<div class="row">
						<label class="col-md-3">{l s='Quantity' mod='marketplace'} -</label>
						<div class="col-md-9">{$product.quantity|escape:'html':'UTF-8'}</div>
					</div>
					<div class="row">
						<label class="col-md-3">{l s='Status' mod='marketplace'} -</label>
						<div class="col-md-9">
							{if $product.active == 1}
							   {l s='Approved' mod='marketplace'}
							 {else}
					           {l s='Pending' mod='marketplace'}
					         {/if}	
						</div>
					</div>
				</div>
				<div class="wk_image">
					{if $is_approve == 1}
						{foreach $img_info as $image}
							{if $image.cover == 1}
								<a class="fancybox" href="{$image.image_path|escape:'html':'UTF-8'}">
									<img src="{$link->getImageLink($link_rewrite, $image.product_image, 'home_default')|escape:'html':'UTF-8'}"/>
								</a>
							{/if}
						{/foreach}
					{else}
						{if $mp_pro_image != 0}
							<img src="{$modules_dir|escape:'html':'UTF-8'}/marketplace/views/img/product_img/{$cover_img|escape:'html':'UTF-8'}.jpg"  style="max-width:280px;max-height:200px;"/>
						{/if}
					{/if}
				</div>
			</div>
			<div style="float:left;width:100%;margin-bottom:20px;">
				{if $is_approve == 1}
				    <div id="image_details">
						<table class="table">
							<tr>
								<th>{l s='Image' mod='marketplace'}</th>
								<th>{l s='Position' mod='marketplace'}</th>
								<th>{l s='Cover' mod='marketplace'}</th>
								<th>{l s='Action' mod='marketplace'}</th>
							</tr>
							{if !empty($img_info)}
								{foreach $img_info as $image}
									<tr class="unactiveimageinforow{$image.id_image|escape:'html':'UTF-8'}">
										<td>
											<a class="fancybox" href="{$image.image_path|escape:'html':'UTF-8'}">
												<img class="img-thumbnail" alt="{l s='No Image' mod='marketplace'}" src="{$link->getImageLink($link_rewrite, $image.product_image, 'cart_default')|escape:'html':'UTF-8'}" />
											</a>
										</td>
										<td>{$image.position|escape:'html':'UTF-8'}</td>
										<td>
											{if {$image.cover} == 1}
												<img class="covered" id="changecoverimage{$image.id_image|escape:'html':'UTF-8'}" alt="{$image.id_image|escape:'html':'UTF-8'}" src="{$img_ps_dir|escape:'html':'UTF-8'}admin/enabled.gif" is_cover="1"  id_pro="{$id|escape:'html':'UTF-8'}" />
											{else}
												<img class="covered" id="changecoverimage{$image.id_image|escape:'html':'UTF-8'}" alt="{$image.id_image|escape:'html':'UTF-8'}" src="{$img_ps_dir|escape:'html':'UTF-8'}admin/forbbiden.gif" is_cover="0"  id_pro="{$id|escape:'html':'UTF-8'}"  prod_detail="1"/>
											{/if}
										</td>
										<td>
											{if {$image.cover} == 1}
												<a class="delete_pro_image btn btn-default" href="" is_cover="1" id_pro="{$id_product|escape:'html':'UTF-8'}" id_image="{$image.id_image|escape:'html':'UTF-8'}">
													<i class="icon-trash"></i> {l s='Delete' mod='marketplace'}
												</a>
											{else}
												<a class="delete_pro_image btn btn-default" href="" is_cover="0" id_pro="{$id_product|escape:'html':'UTF-8'}" id_image="{$image.id_image|escape:'html':'UTF-8'}">
													<i class="icon-trash"></i> {l s='Delete' mod='marketplace'}
												</a>
											{/if}		   
										</td>
									</tr>
								{/foreach}
							{else}
								<tr>
									<td colspan="4">{l s='No image available' mod='marketplace'}</td>
								</tr>
							{/if}
						</table>
				    </div>
				{else}
					<div id="image_details" style="float:left;margin-top:10px;">
						<table class="table">
							<thead>
								<tr><td>{l s='Image' mod='marketplace'}</td></tr>
							</thead>
							<tbody>
								{foreach $mp_pro_image as $mp_image}
									<tr>
										<td>
											<a class="fancybox" href="{$modules_dir|escape:'html':'UTF-8'}/marketplace/views/img/product_img/{$mp_image['seller_product_image_id']|escape:'html':'UTF-8'}.jpg">
												<img width="45" height="45" alt="{l s='No Image' mod='marketplace'}" src="{$modules_dir|escape:'html':'UTF-8'}/marketplace/views/img/product_img/{$mp_image['seller_product_image_id']|escape:'html':'UTF-8'}.jpg" />
											</a>
										</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					</div>
				{/if}
			</div>
			<div class="left full">
				{hook h="DisplayMpproductdescriptionfooterhook"}
			</div>
		</div>
	</div>
</div>

{strip}
{addJsDef ajax_urlpath=$imageediturl|escape:'html':'UTF-8'}
{addJsDef img_ps_dir=$img_ps_dir|escape:'html':'UTF-8'}
{addJsDefL name=space_error}{l s='Space is not allowed.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=confirm_delete_msg}{l s='Do you want to delete the photo?' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=delete_msg}{l s='Deleted.' js=1 mod='marketplace'}{/addJsDefL}
{addJsDefL name=error_msg}{l s='An error occurred.' js=1 mod='marketplace'}{/addJsDefL}
{/strip}
