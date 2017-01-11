{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Dashboard' mod='marketplace'}</span>
{/capture}
<div class="main_block" >
	{hook h="DisplayMpmenuhook"}
	<div class="dashboard_content">
			<div class="dashboard">
				<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
					<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='My Dashboard' mod='marketplace'}</span>
				</div>
				<div class="wk_right_col">
				<div class="left full">
					<strong>{l s='Hello' mod='marketplace'}, {$seller_name|escape:'html':'UTF-8'}!</strong>
					<p>{l s='From your My Account Dashboard, You have the ability to view a snapshot of your recent account activity and update your account information.' mod='marketplace'}</p>
					{hook h='DisplayMpdashboardtophook'}
				</div>
				{hook h='DisplayMpdashboardbottomhook'}
				<div class="box-account box-recent">
					<div class="box-head">
						<div class="box-head-left">
						<h2>{l s='Recent Orders' mod='marketplace'}</h2>
						</div>
						<div class="box-head-right">
						<a class="btn btn-default button button-small" href="{$link->getModuleLink('marketplace','mporder')|escape:'html':'UTF-8'}"><span>{l s='View All' mod='marketplace'}</span></a>
						</div>
					</div>
					<div class="wk_border_line"></div>
					<div class="box-content" >
					<div class="wk_order_table">
						<table class="table" id="my-orders-table">
							<thead>
								<tr>
									<th>{l s='Order' mod='marketplace'} #</th>
									<th>{l s='Date' mod='marketplace'}</th>
									<th>{l s='Ship To' mod='marketplace'}</th>
									<th>{l s='Order Total' mod='marketplace'}</th>
									<th>{l s='Status' mod='marketplace'}</th>
								</tr>
							</thead>
							<tbody>
								{if isset($ordered_products)}
									{foreach $ordered_products as $order}
										<tr>
											<td>{$order.id_order|escape:'html':'UTF-8'}</td>
											<td>{$order.date_add|escape:'html':'UTF-8'}</td>
											<td>{$order.buyer_name|escape:'html':'UTF-8'}</td>
											<td>{convertPrice price=$order.total_price}</td>
											<td>{$order.order_status|escape:'html':'UTF-8'}</td>
										</tr>
									{/foreach}
								{/if}
							</tbody>
						</table>
					</div>
					</div>
				</div>	
				<div class="box-account box-recent">
					<div class="box-head">
						<h2>{l s='Orders Graph' mod='marketplace'}</h2>
						<div class="wk_border_line"></div>
					</div>
					<div class="box-content">
					<div class="wk_from_to">
						<div class="wk_from">
							<div class="labels">
								{l s='From' mod='marketplace'}
							</div>
							<div class="input_type">
								<input id="graph_from" class="datepicker form-control" type="text" style="text-align: center" value="{$from_date|escape:'html':'UTF-8'}" name="graph_from">
							</div>
						</div>
						<div class="wk_to">
							<div class="labels">
								{l s='To' mod='marketplace'}
							</div>
							<div class="input_type">
								<input id="graph_to" class="datepicker1 form-control" type="text" style="text-align: center" value="{$to_date|escape:'html':'UTF-8'}" name="graph_to">
							</div>
						</div>
					</div>
					<div id="chart_div" style="width:100%; height:500px;overflow:hidden;"></div>
				</div>
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
						var order = "{l s='order' js=1 mod='marketplace'}";
						var order_value = "{l s='value' js=1 mod='marketplace'}";
						google.load("visualization", "1", {
										  packages:["corechart"]
									});
						
						google.setOnLoadCallback(drawChart);  
						function drawChart()
						{
							{assign var = i value = 1}
							var data = google.visualization.arrayToDataTable([
							['date_add', order, order_value],
							{while $i>0}
							{if $i>1}
								['{$newdate[$i]|escape:'html':'UTF-8'}',{$count_order_detail[$i]|escape:'html':'UTF-8'},{$product_price_detail[$i]|escape:'html':'UTF-8'}],
							{else}
								['{$newdate[$i]|escape:'html':'UTF-8'}',{$count_order_detail[$i]|escape:'html':'UTF-8'},{$product_price_detail[$i]|escape:'html':'UTF-8'}],
							{/if}
							{assign var=i value=$i-1}
							{/while}
							]);
							var options = {
							  title: "{l s='Premium income' js=1 mod='marketplace'}",
							  pointSize:3,
							  vAxis: {
									minValue: 1
								}
							};
							var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
							chart.draw(data, options);
						}
						//for reqponsiveness
						$(window).resize(function(){
						  	drawChart();
						});
					</script>
				</div>
				</div>	
			</div>
	</div>
</div>

<script type="text/javascript">
	$('.wk_from .datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		defaultDate: -30
	});
	$('.wk_to .datepicker1').datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$('#graph_to').change(function(e) {
		var from_date = $('#graph_from').val();
		var to_date = $(this).val();
		var dashboard_link = "{$link->getModuleLink('marketplace','dashboard')|escape:'htmlall':'UTF-8'}";
		document.location.href=dashboard_link+'?from_date='+from_date+'&to_date='+to_date;
	});
</script>