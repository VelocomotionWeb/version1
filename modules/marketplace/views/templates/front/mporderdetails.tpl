{hook h="DisplayMpmenuhook"}
{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Orders Details' mod='marketplace'}</span>
{/capture}
<div class="dashboard_content">
	<div class="dashboard">
		<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
			<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Order Details' mod='marketplace'}</span>
		</div>
		<div class="wk_right_col">
			<div class="box-account box-recent">
				<div class="box-head">
					<div class="box-head-left">
						<h2><i class="icon-shopping-cart"></i> {l s='Products' mod='marketplace'} ({count($order_products)|escape:'html':'UTF-8'})</h2>
					</div>
					<div class="box-head-right">
					<a class="btn btn-default button button-small" href="{$link->getModuleLink('marketplace','mporder',['shop'=> $id_shop|escape:'html':'UTF-8'])|escape:'html':'UTF-8'}">
						<span>{l s='Back to orders' mod='marketplace'}</span>
					</a>
					</div>
					<div class="wk_border_line"></div>
				</div>
				<div class="box-content">
					<table class="table">
						<thead>
							<th>{l s='Product' mod='marketplace'}</th>
							<th>{l s='Quantity' mod='marketplace'}</th>
							<th>{l s='Total' mod='marketplace'}</th>
						</thead>
						<tbody>
							{foreach $order_products as $product}
								<tr>
									<td><a href="{$link->getProductLink($product.product_id)|addslashes}" target="_blank" title="{l s='View Products' mod='marketplace'}">{$product.product_name|escape:'html':'UTF-8'}</a></td>
									<td>{$product.product_quantity|escape:'html':'UTF-8'}</td>
									<td>{convertPrice price=$product.total_price_tax_incl}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
					{hook h='DisplayMpbottomorderproductdetailhook'}
				</div>
			</div>

			<div class="box-account box-recent">
				<div class="box-head">
					<h2><i class="icon-user"></i> {l s='Customer Details' mod='marketplace'}</h2>
					<div class="wk_border_line"></div>
				</div>
				<div class="box-content">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#invoice_addr" data-toggle="tab">
								<i class="icon-truck"></i>
								{l s='Shipping Address' mod='marketplace'}
							</a>
						</li>
						<li>
							<a href="#ship_addr" data-toggle="tab">
								<i class="icon-file-text"></i>
								{l s='Invoice Address' mod='marketplace'}
							</a>
						</li>
						{hook h='displayMpOrderCustomerDetailsTab'}
					</ul>
					<div class="tab-content panel collapse in">
						<div class="tab-pane active" id="invoice_addr">
							<!-- Invoice address -->
							<h4 class="visible-print">{l s='Invoice address' mod='marketplace'}</h4>
							<div class="well">
								<div class="row">
									<div class="col-sm-12">
										{displayAddressDetail address=$addresses.delivery newLine='<br />'}
										{if $addresses.delivery->other}
											<hr />{$addresses.delivery->other|escape:'html':'UTF-8'}<br />
										{/if}
									</div>
								</div>
							</div>
							{hook h='DisplayMpbottomordercustomerhook'}
						</div>
						<div class="tab-pane" id="ship_addr">
							<div class="well">
								<div class="row">
									<div class="col-sm-6">
										{displayAddressDetail address=$addresses.invoice newLine='<br />'}
										{if $addresses.invoice->other}
											<hr />{$addresses.invoice->other|escape:'html':'UTF-8'}<br />
										{/if}
									</div>
								</div>
							</div>
							{hook h='DisplayMpbottomorderstatushook'}
						</div>
					</div>
				</div>
			</div>

			<div class="box-account box-recent">
				<div class="box-head">
					<h2><i class="icon-credit-card"></i> {l s='Shipping Details' mod='marketplace'}</h2>
					<div class="wk_border_line"></div>
				</div>
				<div class="box-content">
					{include file="./mporderdetails-shipping-details.tpl"}
					{hook h='displayMpOrderDetialShippingBottom'}
					<!-- {hook h='DisplayMpordershippinghook'} -->
				</div>
			</div>

			<div class="box-account box-recent">
				<div class="box-head">
					<h2><i class="icon-envelope"></i> {l s='Messages' mod='marketplace'} ({if isset($messages)}{count($messages)|escape:'html':'UTF-8'}{else}0{/if})</h2>
					<div class="wk_border_line"></div>
				</div>
				
				<div class="box-content">
					{if isset($messages)}
						<table class="table">
							<thead>
								<tr>
									<th>{l s='Customer' mod='marketplace'}</th>
									<th>{l s='Email' mod='marketplace'}</th>
									<th>{l s='Messages' mod='marketplace'}</th>
									<th>{l s='Last message' mod='marketplace'}</th>
								</tr>
							</thead>
							<tbody>
								{foreach $messages as $msg}
									<tr>
										<td>{$msg.firstname|escape:'html':'UTF-8'} {$msg.lastname|escape:'html':'UTF-8'}</td>
										<td>{$msg.email|escape:'html':'UTF-8'}</td>
										<td>{$msg.message|escape:'html':'UTF-8'}</td>
										<td>{$msg.date_add|escape:'html':'UTF-8'}</td>
									</tr>
								{/foreach}
							</tbody>
						</table>
					{else}
						<div class="alert alert-info">
							{l s='No Messages' mod='marketplace'}
						</div>
					{/if}
				</div>
			</div>
			{hook h='displayMpOrderDetailsBottom'}
		</div>
	</div>
</div>