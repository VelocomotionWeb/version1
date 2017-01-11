<!-- <link rel="stylesheet" href="{$module_dir|escape:'html':'UTF-8'}js/datepick/jquery.datepick.css">
<script type="text/javascript" src="{$module_dir|escape:'html':'UTF-8'}js/datepick/jquery.datepick.js"></script> -->
<!-- success message div for tracking number update -->
<div class="alert alert-success" id="tracking_number_update_success_message" style="display:none">
	{l s='Tracking number updated successful' mod='marketplace'}
</div>

<!-- fail message div for tracking number update -->
<div class="alert alert-danger" id="tracking_number_update_fail_message" style="display:none">
	{l s='Tracking number not updated due to some technical problem' mod='marketplace'}
</div>

<!-- success message div for order state update -->
{if isset($is_order_state_updated)}
	{if $is_order_state_updated == 1}
		<div class="alert alert-success">
			{l s='Order status updated successfully' mod='marketplace'}
		</div>
	{/if}
{/if}

<!-- Tab -->
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#status" data-toggle="tab">
			<i class="icon-time"></i>
			<span>{l s='Status' mod='marketplace'}</span>
			<span class="badge">{$history|@count|escape:'html':'UTF-8'}</span>
		</a>
	</li>
	<li>
		<a href="#shipping" data-toggle="tab">
			<i class="icon-truck"></i>
			<span>{l s='Shipping' mod='marketplace'}</span>
		</a>
	</li>
	{if isset($received_by) && isset($delivery_date) && $received_by != ''}
	<li>
		<a href="#delivery" data-toggle="tab">
			<i class="icon-truck"></i>
			{l s='Delivery Description' mod='marketplace'}</span>
		</a>
	</li>
	{/if}
</ul>

<!-- Tab content -->
<div class="tab-content panel">
	<!-- Tab status -->
	<div class="tab-pane active" id="status">
		<h4 class="visible-print">{l s='Status' mod='marketplace'} <span class="badge">({$history|@count|escape:'html':'UTF-8'})</span></h4>
		<!-- History of status -->
		<div class="table-responsive">
			<table class="table history-status row-margin-bottom">
				<tbody>
					{foreach from=$history item=row key=key}
						{if ($key == 0)}
							<tr>
								<td style="background-color:{$row['color']|escape:'html':'UTF-8'}">
									<img src="{$img_url|escape:'html':'UTF-8'}os/{$row['id_order_state']|intval}.gif" width="16" height="16" alt="{$row['ostate_name']|stripslashes}" /></td>
								<td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">
									{$row['ostate_name']|escape:'html':'UTF-8'}
								</td>
								<td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">
								</td>
								<td style="background-color:{$row['color']|escape:'html':'UTF-8'};color:{$row['text-color']|escape:'html':'UTF-8'}">
									{dateFormat date=$row['date_add'] full=true}
								</td>
							</tr>
						{else}
							<tr>
								<td>
									<img src="{$img_url|escape:'html':'UTF-8'}os/{$row['id_order_state']|intval}.gif" width="16" height="16" />
								</td>
								<td>
									{$row['ostate_name']|escape:'html':'UTF-8'}
								</td>
								<td>
								</td>
								<td>
									{dateFormat date=$row['date_add'] full=true}
								</td>
							</tr>
						{/if}
					{/foreach}
				</tbody>
			</table>
		</div>
		<!-- Change status form -->
		<form action="{$update_url_link|escape:'html':'UTF-8'}" method="post" class="form-horizontal well" id="change_order_status_form">
			<div class="row">
				<div class="col-lg-9 form-group" id="select_ele_id">
					<select id="id_order_state" class="chosen form-control" name="id_order_state" style="width:500px;">
					{foreach from=$states item=state}
						<option value="{$state['id_order_state']|intval}"{if $state['id_order_state'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$state['name']|escape:'html':'UTF-8'}</option>
					{/foreach}
					</select>
					<input type="hidden" name="id_order_state_checked" class="id_order_state_checked" value="{$currentState->id|escape:'html':'UTF-8'}" />
				</div>
				<div class="col-lg-3">
					<a href="#wk_shipping_form" style="color:white;display:none;" class="btn btn-primary" id="update_order_status_shipping">
						<span>{l s='Update status' mod='marketplace'}</span>
					</a>
					<a href="#wk_delivery_form" style="color:white;display:none;" class="btn btn-primary" id="update_order_status_delivary">
						<span>{l s='Update status' mod='marketplace'}</span>
					</a>
					<button type="submit" name="submitState" class="btn btn-primary" id="update_order_status">
						<span>{l s='Update status' mod='marketplace'}</span>
					</button>
				</div>
			</div>
		</form>
	</div>
	<!-- Tab shipping -->
	<div class="tab-pane" id="shipping">
		<h4 class="visible-print">{l s='Shipping' mod='marketplace'}</h4>
		<!-- Shipping block -->
		{if !$order->isVirtual()}
		<div class="table-responsive">
			<table class="table" id="shipping_table">
				<thead>
					<tr>
						<th>
							<span class="title_box ">{l s='Date' mod='marketplace'}</span>
						</th>
						<th>
							<span class="title_box ">{l s='Type' mod='marketplace'}</span>
						</th>
						<th>
							<span class="title_box ">{l s='Carrier' mod='marketplace'}</span>
						</th>
						<th>
							<span class="title_box ">{l s='Weight' mod='marketplace'}</span>
						</th>
						<th>
							<span class="title_box ">{l s='Shipping cost' mod='marketplace'}</span>
						</th>
						<th>
							<span class="title_box ">{l s='Tracking number' mod='marketplace'}</span>
						</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$order->getShipping() item=line}
					<tr>
						<td>{dateFormat date=$line.date_add full=true}</td>
						<td>{$line.type|escape:'html':'UTF-8'}</td>
						<td>{$line.carrier_name|escape:'html':'UTF-8'}</td>
						<td class="weight">{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')|escape:'html':'UTF-8'}</td>
						<td class="center">
							{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}
								{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}
							{else}
								{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}
							{/if}
						</td>
						<td class="actions">
							<span id="shipping_number_show">
								{if $line.url && $line.tracking_number}
									<a target="_blank" href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">
										{$line.tracking_number|escape:'html':'UTF-8'}
									</a>
								{else}
									{$line.tracking_number|escape:'html':'UTF-8'}
								{/if}
							</span>
							{if $line.can_edit}
								<span id="shipping_number_edit" style="display:none;">
									<input type="hidden" name="id_order_carrier" id="id_order_carrier" value="{$line.id_order_carrier|escape:'html':'UTF-8'}" />
									<input type="hidden" name="id_order_tracking" id="id_order_tracking" value="{$order->id|escape:'html':'UTF-8'}" />
									<input type="text" class="form-control" name="tracking_number" id="tracking_number" value="{$line.tracking_number|escape:'html':'UTF-8'}" />
									<button type="submit" class="btn btn-primary" id="submit_shipping_number">
										<i class="icon-ok"></i>
										{l s='Update' mod='marketplace'}
									</button>
									<button type="submit" class="btn btn-primary" id="cancel_shipping_number_link">
										<i class="icon-remove"></i>
										{l s='Cancel' mod='marketplace'}
									</button>
								</span>
								<a href="#" class="btn btn-primary" id="edit_shipping_number_link">
									<i class="icon-pencil"></i>
									{l s='Edit' mod='marketplace'}
								</a>
							{/if}
						</td>
					</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		{/if}

		{if isset($shipping_description) && isset($shipping_date) && $shipping_description != ''}
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>
								<span class="title_box ">{l s='Shipping Description' mod='marketplace'}</span>
							</th>
							<th>
								<span class="title_box ">{l s='Date' mod='marketplace'}</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{if isset($shipping_description)}
									{$shipping_description|escape:'html':'UTF-8'}
								{/if}
							</td>
							<td>
								{if isset($shipping_date)}
									{$shipping_date|escape:'html':'UTF-8'}
								{/if}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		{/if}
	</div>
	<!-- Tab delivery -->
	<div class="tab-pane" id="delivery">
		<h4 class="visible-print">{l s='Delivery Description' mod='marketplace'}</h4>
		<!-- delivery block -->
		<div class="table-responsive">
			<table class="table" id="shipping_table">
				<thead>
					<tr>
						<th>
							<span class="title_box ">{l s='Received By' mod='marketplace'}</span>
						</th>
						<th>
							<span class="title_box ">{l s='Delivery Date' mod='marketplace'}</span>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							{if isset($received_by)}
								{$received_by|escape:'html':'UTF-8'}
							{/if}
						</td>
						<td>
							{if isset($delivery_date)}
								{$delivery_date|escape:'html':'UTF-8'}
							{/if}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Shipping fancybox -->
<form action="{$update_url_link|escape:'html':'UTF-8'}" method="POST" id="wk_shipping_form" style="display:none;">
	<div class="box-account box-recent" id="shipping_block">
		<div class="box-head">
			<div class="box-head-left">
				<h2 class="form-label">{l s='Shipping Address' mod='marketplace'}</h2>
			</div>
			<div class="box-head-right">
			<a class="btn btn-primary" id="edit_shipping" href="#">
				<span>{l s='Edit' mod='marketplace'}</span>
			</a>
			</div>
		</div>
		<div class="wk_divider_line"></div>
		<div class="box-content" id="shipping_address">
			<div id="wk_address_details">
				{displayAddressDetail address=$addresses.delivery newLine='<br />'}
				{if $addresses.delivery->other}
					<hr />{$addresses.delivery->other|escape:'html':'UTF-8'}<br />
				{/if}
			</div>
		</div>

		<div class="box-head">
			<div class="box-head-left">
				<h2>{l s='Shipping Description' mod='marketplace'}</h2>
			</div>
		</div>
		<div class="wk_divider_line"></div>
		<div id="shipping_desc">
			<div id="desc">
				<textarea id="edit_textarea_shipping_description" style="width:100%;display:none;" class="form-control" name="edit_shipping_description">{if isset($shipping_description)}{$shipping_description|escape:'html':'UTF-8'}{/if}</textarea>
				<p id="label_shipping_description">
					{if isset($shipping_description) && $shipping_description != ''}
						{$shipping_description|escape:'html':'UTF-8'}
					{else}
						{l s='No data found' mod='marketplace'}
					{/if}
				</p>
			</div>
		</div>

		<div class="box-head">
			<div class="box-head-left">
				<h2>{l s='Shipping Date' mod='marketplace'}</h2>
			</div>
		</div>
		<div class="wk_divider_line"></div>

		<div class="wk_shipping_head">
			<div id="shipping_date">
				<span>
					<input type="hidden" name="id_order_state_checked" class="id_order_state_checked" value="{$currentState->id|escape:'html':'UTF-8'}" />
					<input type="hidden" name="shipping_info_set" value="1">
					<input id="text_shipping_date" type="text" class="datepicker form-control" name="shipping_date" style="display:none;" {if isset($shipping_date)} value="{$shipping_date|escape:'html':'UTF-8'}" {/if}/>
					<p id="label_shipping_date">
						{if isset($shipping_date)}
							{$shipping_date|escape:'html':'UTF-8'}
						{else}
							{l s='No data found' mod='marketplace'}
						{/if}
					</p>
				</span>
			</div>
		</div>

		<div id="submit_block">
			<button name="shipping_info" class="btn btn-primary" type="submit">
				<span>{l s='Submit' mod='marketplace'}</span>
			</button>
		</div>
	</div>	
</form>

<!-- Delivery fancybox -->
<form action="{$update_url_link|escape:'html':'UTF-8'}" method="post" id="wk_delivery_form" style="display:none;">
	<div class="box-account box-recent" id="delivery_block">
			<div class="box-head">
				<div class="box-head-left">
					<h2>{l s='Shipping Address' mod='marketplace'}</h2>
				</div>
				<div class="box-head-right">
				<a class="btn btn-primary" id="edit_delivery" href="#">
					<span>{l s='Edit' mod='marketplace'}</span>
				</a>
				</div>
			</div>
			<div class="wk_divider_line"></div>
			<div class="box-content" id="shipping_address">
				<div id="wk_address_details">
					<div class="">
						{displayAddressDetail address=$addresses.delivery newLine='<br />'}
						{if $addresses.delivery->other}
							<hr />{$addresses.delivery->other|escape:'html':'UTF-8'}<br />
						{/if}
					</div>
				</div>
			</div>

			<div class="box-head">
				<div class="box-head-left">
					<h2>{l s='Delivery Date' mod='marketplace'}</h2>
				</div>
			</div>
			<div class="wk_divider_line"></div>
			
			<div id="shipping_date_block">
				<div class="wk_shipping_head">
					<span>
						<input id="text_delivery_date" type="text" class="datepicker form-control" name="delivery_date" style="display:none;" {if isset($delivery_date)} value="{$delivery_date|escape:'html':'UTF-8'}" {/if} />
						<p id="label_delivery_date">
							{if isset($delivery_date)}
								{$delivery_date|escape:'html':'UTF-8'}
							{else}
								{l s='No data found' mod='marketplace'}
							{/if} 
						</p>
					</span>
				</div>
			</div>

			<div class="box-head">
				<div class="box-head-left">
					<h2>{l s='Received By' mod='marketplace'}</h2>
				</div>
			</div>
			<div class="wk_divider_line"></div>
			<div class="wk_received">
				<span>
					<input type="hidden" name="id_order_state_checked" class="id_order_state_checked" value="{$currentState->id|escape:'html':'UTF-8'}" />
					<input type="hidden" name="delivery_info_set" value="1">
					<input type="text" id="edit_text_received_by" class="form-control"  name="edit_received_by" style="display:none;" {if isset($received_by)} value="{$received_by|escape:'html':'UTF-8'}" {/if}/>
					<p id="label_received_by">
						{if isset($received_by) && $received_by != ''}
							{$received_by|escape:'html':'UTF-8'}
						{else}
							{l s='No data found' mod='marketplace'}
						{/if}
					</p>
				</span>
			</div>
				
			<div id="submit_delivery_block">
				<button name="delivery_info" class="btn btn-primary" type="submit">
					<span>{l s='Submit' mod='marketplace'}</span>
				</button>
			</div>
	</div>
</form>

{strip}
	{addJsDef update_tracking_number_link=$link->getModuleLink('marketplace', 'updateordertrackingnumber')}
{/strip}

<!--Datepicker-->
<script type="text/javascript">
/*(function($) {
  {literal}
	$(".datepicker").datepick({dateFormat: 'yyyy-mm-dd'});
	{/literal}
  })(jQuery);*/
</script>