{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Payment Details' mod='marketplace'}</span>
{/capture}

{if isset($deleted)}
	<p class="alert alert-success">{l s='Payment method deleted successfully' mod='marketplace'}</p>
{elseif isset($edited)}
	<p class="alert alert-success">{l s='Payment method updated successfully' mod='marketplace'}</p>
{elseif isset($created)}
	<p class="alert alert-success">{l s='Payment method created successfully' mod='marketplace'}</p>
{/if}

{hook h="DisplayMpmenuhook"}
<div class="dashboard_content">
	<div class="dashboard">
		<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
			<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Payment Details' mod='marketplace'}</span>
		</div>
		<div class="wk_right_col">
			{if isset($seller_payment_details) && !isset($edit)}
				<div class="row-info">
					<div  style="float:right;">
						<a href="{$link->getModuleLink('marketplace', 'mppayment', ['id' => $seller_payment_details.id])|escape:'html':'UTF-8'}" class="btn btn-default button button-small">
							<span>{l s='Edit' mod='marketplace'}</span>
						</a>
						<a href="{$link->getModuleLink('marketplace', 'mppayment', ['id' => $seller_payment_details.id, 'delete' => 1])|escape:'html':'UTF-8'}" class="btn btn-default button button-small delete_mp_payment">
							<span>{l s='Delete' mod='marketplace'}</span>
						</a>
					</div>
				</div>
				<div class="row-info">
					<div class="row-info-left">
						<label class="pay" class="control-label">{l s='Payment Mode :' mod='marketplace'}</label>
					</div>
					<div class="row-info-right">
						<label id="label_payment_mode" style="font-weight:normal;" class="control-label">{$seller_payment_details.payment_mode|escape:'html':'UTF-8'}</label>
					</div>
				</div>
				<div class="row-info">
					<div class="row-info-left">
						<label class="pay" class="control-label">{l s='Account Details :' mod='marketplace'}</label>
					</div>
					<div class="row-info-right">
						<label id="label_payment_mode_details" style="font-weight:normal;" class="control-label">{$seller_payment_details.payment_detail|escape:'html':'UTF-8'}</label>
					</div>
				</div>
			{else}
				<form action="{if isset($edit)}{$link->getModuleLink('marketplace', 'paymentprocess', ['id' => $seller_payment_details.id])|escape:'html':'UTF-8'}{else}{$link->getModuleLink('marketplace', 'paymentprocess')|escape:'html':'UTF-8'}{/if}" method="post" class="form-horizontal" enctype="multipart/form-data" id="pay_form" role="form" accept-charset="UTF-8,ISO-8859-1,UTF-16">
					{if isset($mp_payment_option)}
						<div class="form-wrapper">
							<div class="form-group">
								<div class="row">
								    <label for="payment_mode" class="control-label col-lg-3 required">{l s='Payment Mode :' mod='marketplace'}</label>
									<select id="payment_mode" name="payment_mode" class="form-control col-lg-3" style="width:200px;">
										<option value="">{l s='Select' mod='marketplace'}</option>
										{foreach $mp_payment_option as $payment}
											<option id="{$payment['id']|escape:'html':'UTF-8'}" value="{$payment['id']|escape:'html':'UTF-8'}"
											{if isset($edit)}{if $seller_payment_details.payment_mode_id == $payment.id}selected{/if}{/if}>{$payment['payment_mode']|escape:'html':'UTF-8'}
											</option>
										{/foreach}
									</select>
								</div>
							</div>

							<div class="form-group">
							    <div class="row">
								    <label for="payment_detail" class="control-label col-lg-3">{l s='Account Details :' mod='marketplace'}</label>
								    <textarea id="payment_detail" name="payment_detail" value="" class="form-control col-lg-3" rows="4" cols="50">{if isset($edit)}{$seller_payment_details.payment_detail|escape:'html':'UTF-8'}{/if}</textarea>
								</div>
							</div>

							<input type="hidden" id="customer_id" name="customer_id" value="{$customer_id|escape:'html':'UTF-8'}" class="account_input"/>
							<div class="row-info" style="text-align:center;">
								<button type="submit" id="submit_payment_details" class="btn btn-default button button-medium">
									{if isset($edit)}
										<span>{l s='Update' mod='marketplace'}</span>
									{else}
										<span>{l s='Save' mod='marketplace'}</span>
									{/if}
								</button>
							</div>
						</div>
					{else}
						<div class="alert alert-info">
							{l s='Admin not created any payment method yet.' mod='marketplace'}
						</div>
					{/if}
				</form>
			{/if}
			<div class="left full">
				{hook h="DisplayMppaymentdetailfooterhook"}
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$("#submit_payment_details").on("click", function(){
	var payment_mode = $("#payment_mode").val();
	var error_msg = "{l s='Please select payment mode.' mod='marketplace' js='1'}";
	if (payment_mode == "")
	{
		alert(error_msg);
		return false;
	}
});

$(".delete_mp_payment").on("click", function(){
	var confirm_msg = "{l s='Are you sure to want to delete?' mod='marketplace' js='1'}";
	if (confirm(confirm_msg))
		return true;
	else
    	return false;
});
</script>