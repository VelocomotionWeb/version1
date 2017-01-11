<div class="panel" id="fieldset_0">
	<div class="panel-heading">
		<i class="icon-cogs"></i>{l s='Map\'s configuration' mod='bestkit_booking'}
	</div>
	<div class="form-wrapper">
		<div class="form-group">									
			<label class="control-label col-lg-3">
				{l s='Show map' mod='bestkit_booking'}
			</label>			
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="bestkit_booking[show_map]" id="bestkit_booking[show_map]_on" value="1" {if $booking_map.show_map} checked="checked"{/if} />
					<label  for="bestkit_booking[show_map]_on">{l s='Yes' mod='bestkit_booking'}</label>
					<input type="radio" name="bestkit_booking[show_map]" id="bestkit_booking[show_map]_off" value="0" {if !$booking_map.show_map} checked="checked"{/if} />
					<label  for="bestkit_booking[show_map]_off">{l s='No' mod='bestkit_booking'}</label>
					<a class="slide-button btn"></a>
				</span>						
			</div>
		</div>								
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Address' mod='bestkit_booking'}
			</label>
			<div class="col-lg-9">
				<input type="text"
						name="bestkit_booking[address1]"
						id="address1"
						value="{$booking_map.address1|escape:'htmlall':'UTF-8'}"
						class=""/>						
			</div>
		</div>					
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Latitude' mod='bestkit_booking'}
			</label>
			<div class="col-lg-9">
				<input type="text"
						name="bestkit_booking[latitude]"
						id="latitude"
						value="{$booking_map.latitude|escape:'htmlall':'UTF-8'}"
						class="" />
				</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Longitude' mod='bestkit_booking'}
			</label>
			<div class="col-lg-9">
				<input type="text"
					name="bestkit_booking[longitude]"
					id="longitude"
					value="{$booking_map.longitude|escape:'htmlall':'UTF-8'}"
					class=""/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Zoom' mod='bestkit_booking'}
			</label>		
			<div class="col-lg-9">
				<input type="text"
						name="bestkit_booking[zoom]"
						id="zoom"
						value="{$booking_map.zoom|escape:'htmlall':'UTF-8'}"
						class=""
				/>							
			</div>
		</div>
	</div><!-- /.form-wrapper -->
</div>