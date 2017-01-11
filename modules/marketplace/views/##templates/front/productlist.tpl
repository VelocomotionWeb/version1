{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Product List' mod='marketplace'}</span>
{/capture}

{if isset($smarty.get.deleted)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Deleted Successfully' mod='marketplace'}
	</p>
{/if}

{if isset($smarty.get.status_updated)}
	<p class="alert alert-success">
		<button data-dismiss="alert" class="close" type="button">×</button>
		{l s='Status updated Successfully' mod='marketplace'}
	</p>
{/if}
{include file="$tpl_dir./errors.tpl"}
{hook h="DisplayMpmenuhook"}
<div class="dashboard_content">
	<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Product List' mod='marketplace'}</span>
	</div>
	<div class="wk_right_col">
		<div class="wk_product_list">
			<div class="left full">
				{hook h="DisplayMpproductdetailheaderhook"}
			</div>
			<form action="{$link->getModuleLink('marketplace', productlist)|escape:'html':'UTF-8'}" method="post" id="mp_productlist_form">
				<table class="table" id="mp_product_list">
					<thead>
						<tr>
							{if $product_lists|@count > 1}
								<th><input type="checkbox" title="{l s='Select all' mod='marketplace'}" id="mp_all_select"/></th>
							{/if}
							<th>{l s='Product Id' mod='marketplace'}</th>
							<th data-sort-ignore="true">{l s='Image' mod='marketplace'}</th>
							<th>{l s='Name' mod='marketplace'}</th>
							<th>{l s='Price' mod='marketplace'}</th>
							<th>{l s='Quantity' mod='marketplace'}</th>
							<th>{l s='Status' mod='marketplace'}</th>
							<th data-sort-ignore="true">{l s='Action' mod='marketplace'}</th>
							<th data-sort-ignore="true">{l s='Edit Image' mod='marketplace'}</th>
						</tr>
					</thead>
					<tbody>
						{if $product_lists != 0}
							{foreach $product_lists as $key => $product}
								<tr>
									{if $product_lists|@count > 1}<td><input type="checkbox" name="mp_product_selected[]" class="mp_bulk_select" value="{$product.id|escape:'html':'UTF-8'}"/></td>{/if}
									<td>{$product.id|escape:'html':'UTF-8'}</td>
									<td>
										{if isset($product.unactive_image)} <!--product is not activated yet-->
											<a class="fancybox" href="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/product_img/{$product.unactive_image|escape:'html':'UTF-8'}.jpg">
												<img class="img-thumbnail" width="45" height="45" src="{$modules_dir|escape:'html':'UTF-8'}marketplace/views/img/product_img/{$product.unactive_image|escape:'html':'UTF-8'}.jpg">
											</a>
										{else if isset($product.cover_image)} <!--product is atleast one time activated-->
											<a class="fancybox" href="{$product.image_path|escape:'html':'UTF-8'}">
												<img class="img-thumbnail" width="45" height="45" src="{$link->getImageLink($product.obj_product->link_rewrite, $product.cover_image, 'small_default')|escape:'html':'UTF-8'}">
											</a>
										{else if isset($product.id_product)}
											<img class="img-thumbnail" width="45" height="45" src="{$link->getImageLink($product.obj_product->link_rewrite, $product.lang_iso|cat : '-default', 'small_default')|escape:'html':'UTF-8'}">
										{else}
											{l s='No image' mod='marketplace'}
										{/if}
									</td>
									<td>
										<a href="{$link->getModuleLink('marketplace', 'productdetails', ['id' => $product.id])|escape:'html':'UTF-8'}">
										{$product.product_name|escape:'html':'UTF-8'}
										</a>
									</td>
									<td>{if isset($product.obj_product)}{convertPrice price=$product.obj_product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}{else}{convertPrice price=$product.price}{/if}</td>
									<td>{$product.quantity|escape:'html':'UTF-8'}</td>
									<td>
										{if isset($product.id_product)}
											{if $product.active}
												{if $products_status == 1}
													<a href="{$link->getModuleLink('marketplace', 'productlist', ['id_product' => {$product.id_product|escape:'html':'UTF-8'}, 'mp_product_status' => 1])|addslashes}">
														<img alt="{l s='Enabled' mod='marketplace'}" title="{l s='Enabled' mod='marketplace'}" class="mp_product_status" src="{$img_ps_dir|escape:'html':'UTF-8'}admin/enabled-2.gif" />
													</a>
												{else}
													{l s='Approved' mod='marketplace'}
												{/if}
											{else}
												{if $products_status == 1}
													<a href="{$link->getModuleLink('marketplace', 'productlist', ['id_product' => {$product.id_product|escape:'html':'UTF-8'}, 'mp_product_status' => 1])|addslashes}">
														<img alt="{l s='Disabled' mod='marketplace'}" title="{l s='Disabled' mod='marketplace'}" class="mp_product_status" src="{$img_ps_dir|escape:'html':'UTF-8'}admin/disabled.gif" />
													</a>
												{else}
													{l s='Pending' mod='marketplace'}
												{/if}
											{/if}
										{else}
											{l s='Pending' mod='marketplace'}
										{/if}
									</td>
									<td>
										<a title="{l s='Edit' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'productupdate', ['id' => $product.id, 'editproduct' => 1])|escape:'html':'UTF-8'}">
											<i class="icon-edit"></i>
										</a>
										&nbsp;
										<a title="{l s='Delete' mod='marketplace'}" href="{$link->getModuleLink('marketplace', 'productupdate', ['id' => $product.id, 'deleteproduct' => 1])|escape:'html':'UTF-8'}" class="delete_img">
											<i class="icon-trash"></i>
										</a>
										{hook h="MpPriceListAction" id_product=$product.id|escape:'html':'UTF-8'}
									</td>
									<td>
										<a href="" class="edit_seq" alt="1" product-id="{$product['id']|escape:'html':'UTF-8'}" id="">
											<img title="{l s='View Images' mod='marketplace'}" class="img_detail" alt="{l s='Image Details' mod='marketplace'}" id="edit_seq{$product['id']|escape:'html':'UTF-8'}" src="{$img_ps_dir|escape:'html':'UTF-8'}admin/more.png">
										</a>
										<input type="hidden" id="urlimageedit" value="{$imageediturl|escape:'html':'UTF-8'}"/>
									</td>
								</tr>
								<div  class="row_info">
									<div id="content{$product['id']|escape:'html':'UTF-8'}" class="content_seq">
									</div>
								</div>
							{/foreach}
						{/if}
					</tbody>
				</table>
				{if $product_lists|@count > 1}
					<div class="btn-group">
						<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
						{l s='Bulk actions' mod='marketplace'} <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="" class="mp_bulk_delete_btn"><i class='icon-trash'></i> {l s='Delete selected' mod='marketplace'}</a></li>
						</ul>
					</div>
				{/if}
			</form>
		</div>
	</div>
</div>
<div class="left full">
	{hook h="DisplayMpproductdetailfooterhook"}
</div>

{strip}
	{addJsDef img_ps_dir=$img_ps_dir|escape:'html':'UTF-8'}
	{addJsDef ajax_urlpath=$imageediturl}
	{addJsDef id_lang=$id_lang|escape:'html':'UTF-8'}
	{addJsDefL name=space_error}{l s='Space is not allowed.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=confirm_delete_msg}{l s='Are you sure?' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=delete_msg}{l s='Deleted.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=error_msg}{l s='An error occurred.' js=1 mod='marketplace'}{/addJsDefL}
	{addJsDefL name=checkbox_select_warning}{l s='You must select at least one element to delete.' js=1 mod='marketplace'}{/addJsDefL}
{/strip}