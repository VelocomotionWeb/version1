{hook h="DisplayMpmenuhook"}
{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>
	<span class="navigation_page">{l s='Orders' mod='marketplace'}</span>
{/capture}
<div class="dashboard_content">
<div class="dashboard">
	<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Orders' mod='marketplace'}</span>
	</div>
	<div class="wk_right_col">
		<div class="box-account box-recent">
			<div class="box-head">
				<h2>{l s='Recent Orders' mod='marketplace'}</h2>
				<div class="wk_border_line"></div>
			</div>
			<div class="box-content">
				<div class="wk_order_table">		
					<table class="table table-hover" id="my-orders-table">
						<thead>
							<tr>
								<th>{l s='ID' mod='marketplace'}</th>
								<th>{l s='Reference' mod='marketplace'}</th>
								<th>{l s='Customer' mod='marketplace'}</th>
								<th>{l s='Total' mod='marketplace'}</th>
								<th>{l s='Status' mod='marketplace'}</th>
								<th>{l s='Payment' mod='marketplace'}</th>
								<th>{l s='Date' mod='marketplace'}</th>
							</tr>
						</thead>
						<tbody>
							{if isset($mporders)}
								{foreach $mporders as $order}
									<tr class="mp_order_row" is_id_order="{$order.id_order|escape:'html':'UTF-8'}" is_id_order_detail="{$order.id_order_detail|escape:'html':'UTF-8'}">
										<td>{$order.id_order|escape:'html':'UTF-8'}</td>
										<td>{$order.reference|escape:'html':'UTF-8'}</td>
										<td>{$order.buyer_info->firstname|escape:'html':'UTF-8'} {$order.buyer_info->lastname|escape:'html':'UTF-8'}</td>
										<td>{convertPrice price=$order.total_paid}</td>
										<td>{$order.order_status|escape:'html':'UTF-8'}</td>
										<td>{$order.payment_mode|escape:'html':'UTF-8'}</td>
										<td>{$order.date_add|escape:'html':'UTF-8'}</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$(".mp_order_row").on("click", function(){
		var id_order =  $(this).attr('is_id_order');
		window.location.href = "{$order_detail_link|escape:'html':'UTF-8'}&id_order="+id_order;
	});

	$('#my-orders-table').DataTable({
        "language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "No product found",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        }
    });
});
</script>