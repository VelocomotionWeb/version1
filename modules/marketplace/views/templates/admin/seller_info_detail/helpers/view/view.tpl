{if isset($customer_id) && $customer_id == 0}
<div class="alert alert-danger">
	<p>This seller has been removed by admin from prestashop</p>
</div>
{/if}
<div id="mp-container-customer">
	<div class="row">
		<div class="col-lg-6">
			<div class="panel clearfix">
				{if isset($mp_seller)}
					<div class="panel-heading">
						<i class="icon-user"></i>
						{$mp_seller.seller_name|escape:'htmlall':'UTF-8'} - 
						<a href="mailto:{$mp_seller.business_email|escape:'html':'UTF-8'}">
							<i class="icon-envelope"></i>
							{$mp_seller.business_email|escape:'htmlall':'UTF-8'}
						</a>
						<div class="panel-heading-action">
							<a href="{$current|escape:'html':'UTF-8'}&amp;updatemarketplace_seller_info&amp;id={$mp_seller.id|intval}&amp;token={$token|escape:'html':'UTF-8'}" class="btn btn-default">
								<i class="icon-edit"></i>
								{l s='Edit' mod='marketplace'}
							</a>
						</div>
					</div>
					<div class="form-horizontal">
						<div class="row">
							<label class="control-label col-lg-3">{l s='Social Title' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">{if $gender->name}{$gender->name|escape:'htmlall':'UTF-8'}{else}{l s='Unknown' mod='marketplace'}{/if}</p>
							</div>
						</div>

						<div class="row">
							<label class="control-label col-lg-3">{l s='Registration Date' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$mp_seller.date_add|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>

						<div class="row">
							<label class="control-label col-lg-3">{l s='Shop Name' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">
									{if $mp_seller.is_seller}
										<a href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $mp_seller.mp_shop_rewrite])|escape:'html':'UTF-8'}" target="_balnk" title="{l s='View Shop' mod='marketplace'}">
											{$mp_seller.shop_name|escape:'htmlall':'UTF-8'}
										</a>
									{else}
										{$mp_seller.shop_name|escape:'htmlall':'UTF-8'}
									{/if}
								</p>
							</div>
						</div>

						<div class="row">
							<label class="control-label col-lg-3">{l s='Phone' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$mp_seller.phone|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>

						<div class="row">
							<label class="control-label col-lg-3">{l s='Rating' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">
									{if isset($avg_rating)}
										<span class="avg_rating"></span>
									{else}
										{l s='No Rating' mod='marketplace'}
									{/if}

									<!-- <a href="{$link->getAdminLink('AdminReviews')|escape:'html':'UTF-8'}" class="btn btn-default">
										<i class="icon-edit"></i>
										{l s='View All Reviews' mod='marketplace'}
									</a> -->
								</p>
							</div>
						</div>
						

						<div class="row">
							<label class="control-label col-lg-3">{l s='Shop Logo' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">
									<img class="img-thumbnail" width="100" height="100" src="{$shopimagepath|escape:'htmlall':'UTF-8'}"/>
								</p>
							</div>
						</div>

						<div class="row">
							<label class="control-label col-lg-3">{l s='Seller Logo' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">
									<img class="img-thumbnail" width="100" height="100" src="{$sellerimagepath|escape:'htmlall':'UTF-8'}">
								</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-3">{l s='Status' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">
									{if $mp_seller.is_seller}
										<span class="label label-success">
											<i class="icon-check"></i>
											{l s='Active' mod='marketplace'}
										</span>
									{else}
										<span class="label label-danger">
											<i class="icon-remove"></i>
											{l s='Inactive' mod='marketplace'}
										</span>
									{/if}
								</p>
							</div>
						</div>

						<div class="row">
							<label class="control-label col-lg-3">{l s='Seller Products' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<a href="{$link->getAdminLink('AdminSellerProductDetail')|addslashes}&amp;id_seller={$mp_seller.id|intval}" class="btn btn-default" target="_blank"><i class="icon-search-plus"></i> {l s='View Products' mod='marketplace'}</a>
							</div>
						</div>
						{hook h='SellerDetailViewInformation'}
					</div>
				{/if}
			</div>
			{hook h='SellerDetailViewLeftColumn'}
		</div>

		<div class="col-lg-6">
			<div class="panel clearfix">
				<div class="panel-heading">
					<i class="icon-money"></i>
					{l s='Payment Account details' mod='marketplace'}
				</div>
				<div class="form-horizontal">
					{if isset($payment_detail)}
						<div class="row">
							<label class="control-label col-lg-3">{l s='Paymet Method' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$payment_detail.payment_mode|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-lg-3">{l s='Account Details' mod='marketplace'} :</label>
							<div class="col-lg-9">
								<p class="form-control-static">{$payment_detail.payment_detail|escape:'htmlall':'UTF-8'}</p>
							</div>
						</div>
					{else}
						<p class="text-muted text-center">{l s='No account details available' mod='marketplace'}</p>
					{/if}
				</div>
			</div>
			{hook h='SellerDetailViewRightColumn'}
		</div>
	</div>
</div>

{if isset($avg_rating)}
<script type="text/javascript">
	$('.avg_rating').raty(
	{
		path: '{$modules_dir|escape:'html':'UTF-8'}/marketplace/libs/rateit/lib/img',
		score: {$avg_rating|escape:'html':'UTF-8'},
		readOnly: true,
	});
</script>
{/if}
