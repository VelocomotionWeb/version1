<div id="bestkit_booking" class="pb-center-column col-xs-12 col-sm-4 col-md-3 range-type-{$bestkit_booking.range_type|escape:'htmlall':'UTF-8'} billing-type-{$bestkit_booking.billing_type|escape:'htmlall':'UTF-8'}">
	<div class="title alert alert-info">{l s='Booking / Reservation' mod='bestkit_booking'}</div>
	<div class="reservation">
		<div class="clearfix"></div>
		<fieldset>
			<div class="attribute_list">
				<div id="bestkit_booking_date1"/>
			</div>
			<div style="display:none">
				<label class="attribute_label">{l s='Date' mod='bestkit_booking'}{if $bestkit_booking.range_type eq 'time_fromto'} <span id="booking_date"></span>{/if}</label>
			</div>
			<div class="attribute_list">
				<div class="selected-dates" style="display:none;">
					{if $bestkit_booking.range_type neq 'time_fromto'}
						{l s='From:' mod='bestkit_booking'} <span id="booking_from"></span> 
						{l s='to:' mod='bestkit_booking'} <span id="booking_to"></span>
					{/if}
				</div>
			</div>
			{if $bestkit_booking.range_type eq 'time_fromto'}
				<div class="time_fromto_container">
					<div class="minimal-interval alert alert-warning">
						{l s='Choose a date!' mod='bestkit_booking'}
					</div>
				</div>
			{elseif $bestkit_booking.range_type eq 'datetime_fromto'}
				<div class="time_fromto_container">
					<div class="minimal-interval alert alert-warning">
						{l s='Choose a range of date!' mod='bestkit_booking'}
					</div>
				</div>
			{/if}
		</fieldset>
	</div>
</div>
{if $bestkit_booking.booking_obj->show_map}
	<div id="booking_map"></div>
	{if $bestkit_booking.api_key}
		<script async defer src="https://maps.googleapis.com/maps/api/js?key={$bestkit_booking.api_key|escape:'htmlall':'UTF-8'}&signed_in=true&callback=initMap"></script>
	{else}
		<script>alert("{l s='Please specify the Google Maps API key in the module configuration' mod='bestkit_booking' js=1}");</script>
	{/if}
{/if}
<script>
	var bestkit_booking = {literal}{{/literal}
		exclude_weekdays: {$bestkit_booking.exclude_weekdays}, {*is not possible |escape:'htmlall':'UTF-8'*}
		exclude_dates: {$bestkit_booking.exclude_dates}, {*is not possible |escape:'htmlall':'UTF-8'*}
		exclude_recurrent_dates: {$bestkit_booking.exclude_recurrent_dates}, {*is not possible |escape:'htmlall':'UTF-8'*}
		exclude_periods: {$bestkit_booking.exclude_periods}, {*is not possible |escape:'htmlall':'UTF-8'*}
		booked_days: {$bestkit_booking.booked_days}, {*is not possible |escape:'htmlall':'UTF-8'*}
		max_day: {$bestkit_booking.max_day|escape:'htmlall':'UTF-8'},
		range_type: "{$bestkit_booking.range_type|escape:'htmlall':'UTF-8'}",
		billing_type: "{$bestkit_booking.billing_type|escape:'htmlall':'UTF-8'}",
		interval: {$bestkit_booking.interval|escape:'htmlall':'UTF-8'},
		current_day: {$bestkit_booking.current_day|escape:'htmlall':'UTF-8'},
		date_from: "{$bestkit_booking.date_from|escape:'htmlall':'UTF-8'}",
		date_to: "{$bestkit_booking.date_to|escape:'htmlall':'UTF-8'}",
		time_from: "{$bestkit_booking.time_from|escape:'htmlall':'UTF-8'}",
		time_to: "{$bestkit_booking.time_to|escape:'htmlall':'UTF-8'}",
		address: " {$bestkit_booking.booking_obj->address1|escape:'htmlall':'UTF-8'}",
		map_latitude: {$bestkit_booking.booking_obj->latitude|escape:'htmlall':'UTF-8'},
		map_longitude: {$bestkit_booking.booking_obj->longitude|escape:'htmlall':'UTF-8'},
		map_zoom: {$bestkit_booking.booking_obj->zoom|escape:'htmlall':'UTF-8'}
	{literal}}{/literal}
</script>