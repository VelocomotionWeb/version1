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

<table class="table bestkit_booking" id="excludeddays_table">
    <thead>
        <tr class="headings">
            <th>{l s='Period type' mod='bestkit_booking'}</th>
            <th>{l s='Period/Date' mod='bestkit_booking'}</th>
            <th class="last">{l s='Action' mod='bestkit_booking'}</th>
        </tr>
    </thead>
    <tbody>
        <tr id="booking_singleday_template" class="booking_tr active">
            <td>
                <select onchange="changePeriod(this)" class="input-text required-entry period_type fixed-width-xl" name="bestkit_booking[excludeddays][period_type][]">
                    <option value="single">{l s='Single day' mod='bestkit_booking'}</option>
                    <option value="recurrent_day">{l s='Recurrent day of week' mod='bestkit_booking'}</option>
                    <option value="recurrent_date">{l s='Recurrent date' mod='bestkit_booking'}</option>
                    <option value="period">{l s='Period, from-to' mod='bestkit_booking'}</option>
                    <option value="time">{l s='Time' mod='bestkit_booking'}</option>
                </select>
            </td>
            <td>
                <div class="input-group fixed-width-xl date_1 dd_field">
                    <input type="text" name="bestkit_booking[excludeddays][date_1][]" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
                    <div class="input-group-addon">
                        <i class="icon-calendar-o"></i>
                    </div>
                </div>

                <select name="bestkit_booking[excludeddays][recurrent_day][]" class="recurrent_day dd_field fixed-width-xl">
                    <option value="1">{l s='Monday' mod='bestkit_booking'}</option>
                    <option value="2">{l s='Tuesday' mod='bestkit_booking'}</option>
                    <option value="3">{l s='Wednesday' mod='bestkit_booking'}</option>
                    <option value="4">{l s='Thursday' mod='bestkit_booking'}</option>
                    <option value="5">{l s='Friday' mod='bestkit_booking'}</option>
                    <option value="6">{l s='Saturday' mod='bestkit_booking'}</option>
                    <option value="0">{l s='Sunday' mod='bestkit_booking'}</option>
                </select>

                <div class="input-group fixed-width-xl date_2 dd_field">
                    <input type="text" name="bestkit_booking[excludeddays][date_2][]" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
                    <div class="input-group-addon">
                        <i class="icon-calendar-o"></i>
                    </div>
                </div>
								
				<div class="form-group period-control time-from dd_field">
					<label class="col-lg-12">
						{l s='Time from' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from ">
						<input type="text" name="bestkit_booking[excludeddays][time_from][]" class="timepicker" value="" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
				
				<div class="form-group period-control time-to dd_field">
					<label class="col-lg-12">
						{l s='Time to' mod='bestkit_booking'}
					</label>
					<div class="input-group fixed-width-xl from ">
						<input type="text" name="bestkit_booking[excludeddays][time_to][]" class="timepicker" value="" />
						<div class="input-group-addon">
							<i class="icon-calendar-o"></i>
						</div>
					</div>
				</div>
            </td>
            <td>
                <button id="id_delete_excludedday_table" title="{l s='Delete Rule' mod='bestkit_booking'}" type="button" class="btn btn-default"  onclick="excludedBookingDaysControl.deleteItem(this)" >
                    <i class="icon-minus-sign-alt"></i>{l s='Delete' mod='bestkit_booking'}
                </button>
            </td>
        </tr>
        {if is_array($booking_excludeddays)}
            {foreach $booking_excludeddays as $key => $excludedday}
                <tr id="booking_singleday_{$key|escape:'html':'UTF-8'}" class="booking_tr active">
                    <td>
                        <select onchange="changePeriod(this)" class="input-text required-entry period_type fixed-width-xl" name="bestkit_booking[excludeddays][period_type][]">
                            <option value="single" {if $excludedday['type'] == 'single'}selected='selected'{/if}>{l s='Single day' mod='bestkit_booking'}</option>
                            <option value="recurrent_day" {if $excludedday['type'] == 'recurrent_day'}selected='selected'{/if}>{l s='Recurrent day of week' mod='bestkit_booking'}</option>
                            <option value="recurrent_date" {if $excludedday['type'] == 'recurrent_date'}selected='selected'{/if}>{l s='Recurrent date' mod='bestkit_booking'}</option>
                            <option value="period" {if $excludedday['type'] == 'period'}selected='selected'{/if}>{l s='Period, from-to' mod='bestkit_booking'}</option>
                            <option value="time" {if $excludedday['type'] == 'time'}selected='selected'{/if}>{l s='Time' mod='bestkit_booking'}</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group fixed-width-xl date_1 dd_field" style="display: {if $excludedday['type'] == 'single' || $excludedday['type'] == 'recurrent_date' || $excludedday['type'] == 'period'}table{else}none{/if};">
                            {if $excludedday['type'] == 'single' || $excludedday['type'] == 'recurrent_date'}
                                <input type="text" name="bestkit_booking[excludeddays][date_1][]" class="datepicker" value="{$excludedday['date']|escape:'html':'UTF-8'}" />
                            {elseif ($excludedday['type'] == 'period')}
                                <input type="text" name="bestkit_booking[excludeddays][date_1][]" class="datepicker" value="{$excludedday['from']|escape:'html':'UTF-8'}" />
                            {else}
                                <input type="text" name="bestkit_booking[excludeddays][date_1][]" class="datepicker" value="{date('Y-m-d')|escape:'html':'UTF-8'}" />
                            {/if}
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>

                        <select name="bestkit_booking[excludeddays][recurrent_day][]" class="recurrent_day dd_field fixed-width-xl" style="display: {if $excludedday['type'] == 'recurrent_day'}table{else}none{/if};">
                            <option value="1" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 1}selected='selected'{/if}>{l s='Monday' mod='bestkit_booking'}</option>
                            <option value="2" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 2}selected='selected'{/if}>{l s='Tuesday' mod='bestkit_booking'}</option>
                            <option value="3" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 3}selected='selected'{/if}>{l s='Wednesday' mod='bestkit_booking'}</option>
                            <option value="4" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 4}selected='selected'{/if}>{l s='Thursday' mod='bestkit_booking'}</option>
                            <option value="5" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 5}selected='selected'{/if}>{l s='Friday' mod='bestkit_booking'}</option>
                            <option value="6" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 6}selected='selected'{/if}>{l s='Saturday' mod='bestkit_booking'}</option>
                            <option value="0" {if $excludedday['type'] == 'recurrent_day' && $excludedday['day'] == 0}selected='selected'{/if}>{l s='Sunday' mod='bestkit_booking'}</option>
                        </select>


                        <div class="input-group fixed-width-xl date_2 dd_field" style="display: {if $excludedday['type'] == 'period'}table{else}none{/if};">
                            <input type="text" name="bestkit_booking[excludeddays][date_2][]" class="datepicker" value="{if $excludedday['type'] == 'period'}{$excludedday['to']|escape:'htmlall':'UTF-8'}{else}{date('Y-m-d')|escape:'htmlall':'UTF-8'}{/if}" />
                            <div class="input-group-addon">
                                <i class="icon-calendar-o"></i>
                            </div>
                        </div>
								
						<div class="form-group period-control time-from dd_field" style="display: {if $excludedday['type'] == 'time'}table{else}none{/if};">
							<label class="col-lg-12">
								{l s='Time from' mod='bestkit_booking'}
							</label>
							<div class="input-group fixed-width-xl from ">
								<input type="text" name="bestkit_booking[excludeddays][time_from][]" class="timepicker" value="{if $excludedday['type'] == 'time'}{$excludedday['from']|escape:'htmlall':'UTF-8'}{else}{date('H:m')|escape:'htmlall':'UTF-8'}{/if}" />
								<div class="input-group-addon">
									<i class="icon-calendar-o"></i>
								</div>
							</div>
						</div>
						
						<div class="form-group period-control time-to dd_field" style="display: {if $excludedday['type'] == 'time'}table{else}none{/if};">
							<label class="col-lg-12">
								{l s='Time to' mod='bestkit_booking'}
							</label>
							<div class="input-group fixed-width-xl from ">
								<input type="text" name="bestkit_booking[excludeddays][time_to][]" class="timepicker" value="{if $excludedday['type'] == 'time'}{$excludedday['to']|escape:'htmlall':'UTF-8'}{else}{date('H:m')|escape:'htmlall':'UTF-8'}{/if}" />
								<div class="input-group-addon">
									<i class="icon-calendar-o"></i>
								</div>
							</div>
						</div>

                    </td>
                    <td>
                        <button id="id_delete_excludedday_table" title="{l s='Delete Rule' mod='bestkit_booking'}" type="button" class="btn btn-default"  onclick="excludedBookingDaysControl.deleteItem(this)" >
                            <i class="icon-minus-sign-alt"></i>{l s='Delete' mod='bestkit_booking'}
                        </button>
                    </td>
                </tr>
            {/foreach}
        {/if}
        <tr id="booking_excludedday_add_template">
            <td colspan="3">
                <button id="id_add_excludeddays_table" title="{l s='Add Rule' mod='bestkit_booking'}" type="button" class="btn btn-default"  data-style="expand-right" data-size="s" onclick="excludedBookingDaysControl.addItem()" >
                    <i class="icon-plus-sign-alt"></i>{l s='Add' mod='bestkit_booking'}
                </button>
            </td>
        </tr>
    </tbody>
</table>

{literal}
<style type="text/css">
	#booking_singleday_template,
	.booking_tr.new .recurrent_day,
	.booking_tr.new .date_2,
	.booking_tr.new .date_1,
	.booking_tr.new .time-from,
	.booking_tr.new .time-to {display: none}
	.booking_tr .date_1 {float: left}
</style>
{/literal}

<script type="text/javascript">
    //<![CDATA[
	$( document ).ready(function() {
		$('.timepicker').timepicker();
		
		/*$('.booking_tr').each(function(index, element) {
console.log($(element));
			if ($(element).not('.active')) {
				$(element).hide();
			}
		});*/

		//$('#booking_singleday_template').hide();
	});

    var changePeriod = function(el){
//console.log($(el).val());

		$(el).parents('tr').find('.dd_field').hide();
//console.log($(el).parents('tr'));
		period = $(el).val();
		if (period == 'single') {
			$(el).parents('tr').find('.date_1').show().css('display', 'table');
		} else if (period == 'recurrent_day') {
			$(el).parents('tr').find('.recurrent_day').show().css('display', 'table');
		} else if (period == 'recurrent_date') {
			$(el).parents('tr').find('.date_1').show().css('display', 'table');
		} else if (period == 'period') {
			$(el).parents('tr').find('.date_1').show().css('display', 'table');
			$(el).parents('tr').find('.date_2').show().css('display', 'table');
		} else if (period == 'time') {
			$(el).parents('tr').find('.time-from').show().css('display', 'table');
			$(el).parents('tr').find('.time-to').show().css('display', 'table');
			
			$(el).parents('tr').find(".timepicker").removeAttr("id").removeClass("hasDatepicker").timepicker({
				dateFormat: '',
				timeFormat: 'hh:mm', // tt
				timeOnly: true    
			});
		}
    };

	var excludedBookingDaysControl = {
		addItem : function() {
//console.log('addItem');
			dd_tr_size = $('#excludeddays_table tr').size();
			$("#booking_excludedday_add_template").before('<tr class="booking_tr new" id="dd_tr_' + dd_tr_size + '">' + $("#booking_singleday_template").html() + '</tr>');
			$("#dd_tr_" + dd_tr_size + " .date_1").show().css('display', 'table');

			$(".booking_tr.new .datepicker").removeAttr("id").removeClass("hasDatepicker").datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd'
			});
			
			$(".booking_tr.new .timepicker").removeAttr("id").removeClass("hasDatepicker").timepicker({
				dateFormat: '',
				timeFormat: 'hh:mm', // tt
				timeOnly: true    
			});
		},

		deleteItem : function(el) {
//console.log('deleteItem');
			$(el).parents('tr').remove();
		},
	}
    //]]>
</script>
