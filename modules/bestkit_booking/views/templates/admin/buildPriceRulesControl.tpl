{*
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT
*  @copyright  best-kit
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<table class="table bestkit_booking" id="pricerules_table">
    <thead>
        <tr class="headings">
            <th class="fixed-width-xs">{l s='Period type' mod='bestkit_booking'}</th>
            <th class="fixed-width-xs">{l s='Date/Time Rules' mod='bestkit_booking'}</th>
            <th class="fixed-width-xs">{l s='Price' mod='bestkit_booking'}</th>
            <th class="last fixed-width-xs">{l s='Action' mod='bestkit_booking'}</th>
        </tr>
    </thead>
    <tbody>
        <tr id="booking_pricerules_template" class="booking_tr active">
            <td>
                <select onchange="priceRulesBookingControl.changePeriod(this)" class="input-text required-entry period_type pricerules_period_type fixed-width-xl" name="bestkit_booking[pricerules][type][]">
                    <option value="from_to_date">{l s='From-To Date' mod='bestkit_booking'}</option>
                    <option value="from_to_time">{l s='From-To Time' mod='bestkit_booking'}</option>
                    <option value="from_to_datetime">{l s='From-To Date and Time' mod='bestkit_booking'}</option>
                    <option value="recurrent_day">{l s='Recurrent day' mod='bestkit_booking'}</option>
                    <option value="recurrent_date">{l s='Recurrent date' mod='bestkit_booking'}</option>
                </select>
            </td>
            <td>
				<div class="form-group period-control date-from">
					<label class="col-lg-12">
						{l s='Date from' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from dd_field">
						<input type="text" name="bestkit_booking[pricerules][date_from][]" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
				
				<div class="form-group period-control date-to">
					<label class="col-lg-12">
						{l s='Date to' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl to dd_field">
						<input type="text" name="bestkit_booking[pricerules][date_to][]" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
				
				<div class="form-group period-control time-from" style="display: none">
					<label class="col-lg-12">
						{l s='Time from' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from dd_field">
						<input type="text" name="bestkit_booking[pricerules][time_from][]" class="timepicker" value="" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
				
				<div class="form-group period-control time-to" style="display: none">
					<label class="col-lg-12">
						{l s='Time to' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from dd_field">
						<input type="text" name="bestkit_booking[pricerules][time_to][]" class="timepicker" value="" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
				
				<div class="form-group period-control week-day" style="display: none">
					<label class="col-lg-12">
						{l s='Day of week' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from dd_field">
						<select name="bestkit_booking[pricerules][day][]" class="day dd_field fixed-width-xl">
							<option value="1">{l s='Monday' mod='bestkit_booking'}</option>
							<option value="2">{l s='Tuesday' mod='bestkit_booking'}</option>
							<option value="3">{l s='Wednesday' mod='bestkit_booking'}</option>
							<option value="4">{l s='Thursday' mod='bestkit_booking'}</option>
							<option value="5">{l s='Friday' mod='bestkit_booking'}</option>
							<option value="6">{l s='Saturday' mod='bestkit_booking'}</option>
							<option value="7">{l s='Sunday' mod='bestkit_booking'}</option>
						</select>
					</div>
				</div>
				
				<div class="form-group period-control recurrent-date" style="display: none">
					<label class="col-lg-12">
						{l s='Recurrent Date' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from dd_field">
						<input type="text" name="bestkit_booking[pricerules][recurrent_date][]" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
            </td>
            <td>
                <div class="input-group fixed-width-sm price dd_field">
                    <input type="text" name="bestkit_booking[pricerules][price][]" class="price" value="" />
                </div>
            </td>
            <td>
                <button id="id_delete_pricerules_table" title="{l s='Delete Rule' mod='bestkit_booking'}" type="button" class="btn btn-default"  onclick="priceRulesBookingControl.deleteItem(this)" >
                    <i class="icon-minus-sign-alt"></i>{l s='Delete' mod='bestkit_booking'}
                </button>
            </td>
        </tr>
		{foreach $booking_pricerules as $key => $pricerule}
            <tr id="booking_pricerules_{$key|escape:'html':'UTF-8'}" class="booking_tr active">
                <td>
                    <select onchange="priceRulesBookingControl.changePeriod(this)" class="input-text required-entry period_type pricerules_period_type fixed-width-xl" name="bestkit_booking[pricerules][type][]">
                        <option value="from_to_date" {if $pricerule.type == 'from_to_date'}selected="selected"{/if}>{l s='From-To Date' mod='bestkit_booking'}</option>
                        <option value="from_to_time" {if $pricerule.type == 'from_to_time'}selected="selected"{/if}>{l s='From-To Time' mod='bestkit_booking'}</option>
                        <option value="from_to_datetime" {if $pricerule.type == 'from_to_datetime'}selected="selected"{/if}>{l s='From-To Date and Time' mod='bestkit_booking'}</option>
                        <option value="recurrent_day" {if $pricerule.type == 'recurrent_day'}selected="selected"{/if}>{l s='Recurrent day' mod='bestkit_booking'}</option>
                        <option value="recurrent_date" {if $pricerule.type == 'recurrent_date'}selected="selected"{/if}>{l s='Recurrent date' mod='bestkit_booking'}</option>
                    </select>
                </td>
                <td>
                    <div class="form-group period-control date-from">
                        <label class="col-lg-12">
                            {l s='Date from' mod='bestkit_booking'}
                        </label>
                        <div class="input-group fixed-width-xl from dd_field">
                            <input type="text" name="bestkit_booking[pricerules][date_from][]" class="datepicker" value="{$pricerule.date_from|escape:'html':'UTF-8'}" />
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group period-control date-to">
                        <label class="col-lg-12">
                            {l s='Date to' mod='bestkit_booking'}
                        </label>
                        <div class="input-group fixed-width-xl to dd_field">
                            <input type="text" name="bestkit_booking[pricerules][date_to][]" class="datepicker" value="{$pricerule.date_to|escape:'html':'UTF-8'}" />
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group period-control time-from" style="display: none">
                        <label class="col-lg-12">
                            {l s='Time from' mod='bestkit_booking'}
                        </label>
                        <div class="input-group fixed-width-xl from dd_field">
                            <input type="text" name="bestkit_booking[pricerules][time_from][]" class="timepicker" value="{$pricerule.time_from|escape:'html':'UTF-8'}" />
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group period-control time-to" style="display: none">
                        <label class="col-lg-12">
                            {l s='Time to' mod='bestkit_booking'}
                        </label>
                        <div class="input-group fixed-width-xl from dd_field">
                            <input type="text" name="bestkit_booking[pricerules][time_to][]" class="timepicker" value="{$pricerule.time_to|escape:'html':'UTF-8'}" />
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group period-control week-day" style="display: none">
                        <label class="col-lg-12">
                            {l s='Day of week' mod='bestkit_booking'}
                        </label>
                        <div class="input-group fixed-width-xl from dd_field">
                            <select name="bestkit_booking[pricerules][day][]" class="day dd_field fixed-width-xl">
                                <option value="1" {if $pricerule.day == 1}selected="selected"{/if}>{l s='Monday' mod='bestkit_booking'}</option>
                                <option value="2" {if $pricerule.day == 2}selected="selected"{/if}>{l s='Tuesday' mod='bestkit_booking'}</option>
                                <option value="3" {if $pricerule.day == 3}selected="selected"{/if}>{l s='Wednesday' mod='bestkit_booking'}</option>
                                <option value="4" {if $pricerule.day == 4}selected="selected"{/if}>{l s='Thursday' mod='bestkit_booking'}</option>
                                <option value="5" {if $pricerule.day == 5}selected="selected"{/if}>{l s='Friday' mod='bestkit_booking'}</option>
                                <option value="6" {if $pricerule.day == 6}selected="selected"{/if}>{l s='Saturday' mod='bestkit_booking'}</option>
                                <option value="7" {if $pricerule.day == 7}selected="selected"{/if}>{l s='Sunday' mod='bestkit_booking'}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group period-control recurrent-date" style="display: none">
                        <label class="col-lg-12">
                            {l s='Recurrent Date' mod='bestkit_booking'}
                        </label>
                        <div class="input-group fixed-width-xl from dd_field">
                            <input type="text" name="bestkit_booking[pricerules][recurrent_date][]" class="datepicker" value="{$pricerule.recurrent_date|escape:'html':'UTF-8'}" />
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group fixed-width-sm price dd_field">
                        <input type="text" name="bestkit_booking[pricerules][price][]" class="price" value="{$pricerule.price|floatval}" />
                    </div>
                </td>
                <td>
                    <button id="id_delete_pricerules_table" title="{l s='Delete Rule' mod='bestkit_booking'}" type="button" class="btn btn-default"  onclick="priceRulesBookingControl.deleteItem(this)" >
                        <i class="icon-minus-sign-alt"></i>{l s='Delete' mod='bestkit_booking'}
                    </button>
                </td>
            </tr>
        {/foreach}
        <tr id="booking_pricerules_add_template">
            <td colspan="9">
                <button id="id_add_booking_pricerules_table" title="{l s='Add Rule' mod='bestkit_booking'}" type="button" class="btn btn-default"  data-style="expand-right" data-size="s" onclick="priceRulesBookingControl.addItem()" >
                    <i class="icon-plus-sign-alt"></i>{l s='Add' mod='bestkit_booking'}
                </button>
            </td>
        </tr>
    </tbody>
</table>

{literal}
<style type="text/css">
	#booking_pricerules_template, 
	.booking_tr.new .recurrent_day, 
	.booking_tr.new .date_2, 
	.booking_tr.new .date_1 {display: none}
	.booking_tr .date_1 {float: left}
</style>
{/literal}

<script type="text/javascript">
    //<![CDATA[
	var priceRulesBookingControl = {
		init : function() {
			$('.timepicker').timepicker();

            $("#pricerules_table .period_type").each(function(){
                priceRulesBookingControl.changePeriod(this)
            })
		},
		
		addItem : function() {
			dd_tr_size = $('#pricerules_table tr').size();
			$("#booking_pricerules_add_template").before('<tr class="booking_tr new" id="dd_tr_' + dd_tr_size + '">' + $("#booking_pricerules_template").html() + '</tr>');
			//$("#dd_tr_" + dd_tr_size + " .from").show().css('display', 'table');
			
			$(".booking_tr.new .datepicker").removeAttr("id").removeClass("hasDatepicker").datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd'
			});
			
			$(".booking_tr.new .timepicker").removeAttr("id").removeClass("hasDatepicker").timepicker({
				dateFormat: '',
				timeFormat: 'hh:mm tt',
				timeOnly: true    
			});

            //disable options for rules
            if (typeof(booking) != undefined) {
                booking.periodCheck($('#range_type'), true);
            }
		},
		
		deleteItem : function(el) {
			$(el).parents('tr').remove();
		},
		
		changePeriod : function(el) {
			var tmp_period = $(el).val();
			$(el).parents('tr').find('.period-control').each(function(){
				$(this).hide();
			});
			
			if (tmp_period == 'from_to_date') {
				$(el).parents('tr').find('.date-from').show();
				$(el).parents('tr').find('.date-to').show();
			} else if (tmp_period == 'from_to_time') {
				$(el).parents('tr').find('.time-from').show();
				$(el).parents('tr').find('.time-to').show();
			
				$(el).parents('tr').find(".timepicker").removeAttr("id").removeClass("hasDatepicker").timepicker({
					dateFormat: '',
					timeFormat: 'hh:mm tt',
					timeOnly: true    
				});
			} else if (tmp_period == 'from_to_datetime') {
				$(el).parents('tr').find('.date-from').show();
				$(el).parents('tr').find('.date-to').show();
				$(el).parents('tr').find('.time-from').show();
				$(el).parents('tr').find('.time-to').show();
			} else if (tmp_period == 'recurrent_day') {
				$(el).parents('tr').find('.week-day').show();
			} else /*if (tmp_period == 'recurrent_date')*/ {
				$(el).parents('tr').find('.recurrent-date').show();
			}
		},
	}

    $( document ).ready(function() {
        priceRulesBookingControl.init();
    });
    //]]>
</script>