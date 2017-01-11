<div class="panel">
	<div class="panel-heading">
		<i class="icon-time"></i>
		{l s='Booking information' mod='bestkit_booking'}
	</div>
	<div class="booking-info table-responsive">
		<table class="table" id="orderBookings">
			<thead>
				<tr>
					<th></th>
					<th><span class="title_box ">{l s='Booking Id' mod='bestkit_booking'}</span></th>
					<th><span class="title_box ">{l s='Product' mod='bestkit_booking'}</span></th>
					<th><span class="title_box ">{l s='From' mod='bestkit_booking'}</span></th>
					<th><span class="title_box ">{l s='To' mod='bestkit_booking'}</span></th>
					<th><span class="title_box ">{l s='Period type' mod='bestkit_booking'}</span></th>
					<th><span class="title_box ">{l s='Billable period' mod='bestkit_booking'}</span></th>
					<th><span class="title_box ">{l s='Billable interval' mod='bestkit_booking'}</span></th>
				</tr>
			</thead>
			<tbody>
				{foreach $bestkit_booking.info as $info}
				<tr>
					<td></td>
					<td>{$info.id_bestkit_booking_order|intval}</td>
					<td>{$info.name|escape:'htmlall':'UTF-8'}</td>
					<td>{$info.from|escape:'htmlall':'UTF-8'}</td>
					<td>{$info.to|escape:'htmlall':'UTF-8'}</td>
					<td>{$bestkit_booking.module->getHumanRangeType($info.range_type)|escape:'htmlall':'UTF-8'}</td>
					<td>{$bestkit_booking.module->getHumanBillablePeriod($info.qratio_multiplier)|escape:'htmlall':'UTF-8'}</td>
					<td>{$info.billable_interval|escape:'htmlall':'UTF-8'}</td>
				</tr>
				{/foreach}
			<tbody>
		</table>
	</div>
</div>