<div style="display:none">
	<table id="timesPreview" width="100%">
		<tr class="header">
			<td>
				<h3>
					<img src="{$bestkit_booking.module_path}views/img/calendar.png" /> {*is not possible |escape:'htmlall':'UTF-8'*}
					{l s='Availability Table' mod='bestkit_booking'} {$choosed_date.from}{if $choosed_date.to}-{$choosed_date.to}{/if} {*is not possible |escape:'htmlall':'UTF-8'*}
				</h3>
				{if $choosed_date.to}{l s='Choose an hour for view details' mod='bestkit_booking'}{/if}
			</td>
		</tr>
		<tr>
			<td>
				<table>
					{if $bestkit_booking.range_type eq 'datetime_fromto'}
						{foreach from=$choosed_date.days item=day}
							<tr class="hours">
								<td>
									{$day|escape:'htmlall':'UTF-8'}
									<table>
										<tr class="minutes">
											{for $hour=$bestkit_booking.time_from|intval to $bestkit_booking.time_to}
												<td style="background-color: {if bestkit_booking::isTimeAvailable($bestkit_booking.id_product, $day, $hour)}rgb(255, 147, 147){else}rgb(158, 255, 142){/if};">
													{if $bestkit_booking.billing_type eq 'minutes'}
														<a href="javascript:;" class="hour_details_view" title="Show the hour details">
															{if $hour < 10}0{/if}{$hour|escape:'htmlall':'UTF-8'}
															{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $day|cat:' '|cat:$hour|cat:':00', null))}
														</a>
														<div class="hour_details" style="display:none">
															<table>
																{for $minute=0 to 59}
																	<td style="background-color: {if bestkit_booking::isTimeAvailable($bestkit_booking.id_product, $day, $hour, $minute)}rgb(255, 147, 147){else}rgb(158, 255, 142){/if};">
																		{if $minute < 10}0{/if}{$minute|escape:'htmlall':'UTF-8'}
																		{*<br>
																		<span class="bill-price">
																			{if $minute < 10}
																				{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $day|cat:' '|cat:$hour|cat:':0'|cat:$minute, null))}
																			{else}
																				{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $day|cat:' '|cat:$hour|cat:':'|cat:$minute, null))}
																			{/if}
																		</span>*}
																	</td>
																{/for}
															</table>
														</div>
													{else}
														{if $hour < 10}0{/if}{$hour|escape:'htmlall':'UTF-8'}
														{*<br>
														<span class="bill-price">
															{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $day|cat:' '|cat:$hour|cat:':00', null))}
														</span>*}
													{/if}
												</td>
											{/for}
										</tr>
									</table>
									<div class="hour_details_preview minutes" style="display: none;"></div>
								</td>
							</tr>
						{/foreach}
					{else}
						{if $bestkit_booking.billing_type eq 'hours'}
							<tr class="hours">
								{for $hour=$bestkit_booking.time_from|intval to $bestkit_booking.time_to}
									<td style="background-color: {if bestkit_booking::isTimeAvailable($bestkit_booking.id_product, $choosed_date.from, $hour)}rgb(255, 147, 147){else}rgb(158, 255, 142){/if};">
										{if $hour < 10}0{/if}{$hour|escape:'htmlall':'UTF-8'}:00
										{*<br>
										<span class="bill-price">
											{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $choosed_date.from|cat:' '|cat:$hour|cat:':00', null))}
										</span>*}
									</td>
								{/for}
							</tr>
						{else}
							{for $hour=$bestkit_booking.time_from|intval to $bestkit_booking.time_to}
								<tr class="hours">
									<td>
										{l s='The %d hour' sprintf=$hour mod='bestkit_booking'}
										<table>
											<tr class="minutes">
												{for $minute=0 to 59}
													<td style="background-color: {if bestkit_booking::isTimeAvailable($bestkit_booking.id_product, $choosed_date.from, $hour, $minute)}rgb(255, 147, 147){else}rgb(158, 255, 142){/if};">
														{if $minute < 10}0{/if}{$minute|escape:'htmlall':'UTF-8'}
														{*<br>
														<span class="bill-price">
															{if $minute < 10}
																{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $choosed_date.from|cat:' '|cat:$hour|cat:':0'|cat:$minute, null))}
															{else}
																{Tools::displayPrice($bestkit_booking.module->getBookingPrice($bestkit_booking.id_product, $choosed_date.from|cat:' '|cat:$hour|cat:':'|cat:$minute, null))}
															{/if}
														</span>*}
													</td>
												{/for}
											</tr>
										</table>
									</td>
								</tr>
							{/for}
						{/if}
					{/if}
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="available-time-container">
<a class="available-time" href="#timesPreview">
	<img src="{$bestkit_booking.module_path}views/img/calendar.png" />
	{l s='Show the table of available time' mod='bestkit_booking'}
</a>
</div>
<div class="minimal-interval alert alert-warning">
	{l s='Booking interval is' mod='bestkit_booking'} {$bestkit_booking.interval|escape:'htmlall':'UTF-8'} {$bestkit_booking.module->getHumanBillablePeriod($bestkit_booking.billing_type)|escape:'htmlall':'UTF-8'}
</div>
<div class="time-selection-container">
	<div class="time-selection time-to">
		<label class="attribute_label">{l s='Time to' mod='bestkit_booking'}</label>
		<div class="attribute_list">
			<select class="form-control attribute_select1 hour" id="booking_to_hour">
				{for $hour=$bestkit_booking.time_from|intval to $bestkit_booking.time_to|intval}
				    <option value="{$hour|escape:'htmlall':'UTF-8'}">
				    	{if $hour < 10}0{/if}{$hour|escape:'htmlall':'UTF-8'}{*if $bestkit_booking.billing_type neq 'minutes'}:00{/if*}
				    </option>
				{/for}
			</select>
			{*if $bestkit_booking.billing_type eq 'minutes'*}
				<select class="form-control attribute_select1 minute" id="booking_to_minute">
					{for $minute=0 to 59}
					    <option value="{$minute|escape:'htmlall':'UTF-8'}">{if $minute < 10}0{/if}{$minute|escape:'htmlall':'UTF-8'}</option>
					{/for}
				</select>
			{*/if*}
		</div>
	</div>
	<div class="time-selection time-from">
		<label class="attribute_label">{l s='Time from' mod='bestkit_booking'}</label>
		<div class="attribute_list">
			<select class="form-control attribute_select1 hour" id="booking_from_hour">
				{for $hour=$bestkit_booking.time_from|intval to $bestkit_booking.time_to|intval}
				    <option value="{$hour|escape:'htmlall':'UTF-8'}">
				    	{if $hour < 10}0{/if}{$hour|escape:'htmlall':'UTF-8'}{*if $bestkit_booking.billing_type neq 'minutes'}:00{/if*}
				    </option>
				{/for}
			</select>
			{*if $bestkit_booking.billing_type eq 'minutes'*}
				<select class="form-control attribute_select1 minute" id="booking_from_minute">
					{for $minute=0 to 59}
					    <option value="{$minute|escape:'htmlall':'UTF-8'}">{if $minute < 10}0{/if}{$minute|escape:'htmlall':'UTF-8'}</option>
					{/for}
				</select>
			{*/if*}
		</div>
	</div>
</div>
<div class="check_selected_params">
	<a href="javascript:;">
		<img src="{$bestkit_booking.module_path}views/img/arrow_right.png" /> {*is not possible |escape:'htmlall':'UTF-8'*}
		{l s='Check the reservation to continue!' mod='bestkit_booking'}
	</a>
	<div class="reservation_status alert alert-danger" style="display:none"></div>
</div>
<script>
	prepareTimeInterval();
</script>