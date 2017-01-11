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
*  @copyright  BEST-KIT
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $booking_id_product}
    <div id="bestkit_booking_wrapper" class="panel product-tab">
        <input type="hidden" name="submitted_tabs[]" value="Bestkit_Booking" />
        <h3>{l s='Booking' mod='bestkit_booking'}</h3>
        <div class="separation"></div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="quantity">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Enabled' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="bestkit_booking[active]" id="booking_active_on" value="1" {if $booking_obj->active}checked="checked"{/if}>
                    <label for="booking_active_on" class="radioCheck">
                        {l s='Yes' mod='bestkit_booking'}
                    </label>
                    <input type="radio" name="bestkit_booking[active]" id="booking_active_off" value="0" {if !$booking_obj->active}checked="checked"{/if}>
                    <label for="booking_active_off" class="radioCheck">
                        {l s='No' mod='bestkit_booking'}
                    </label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="quantity">
                <span data-toggle="tooltip" title="">
                     {l s='Quantity' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <input type="text" id="quantity" name="bestkit_booking[quantity]" value="{$booking_obj->quantity|intval}">
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="available_period">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Available period' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <input type="text" id="available_period" name="bestkit_booking[available_period]" value="{$booking_obj->available_period|intval}">
                <div class="desc">{l s='Customer can choose date/time in this period' mod='bestkit_booking'}</div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="range_type">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Period type' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <select name="bestkit_booking[range_type]" id="range_type">
                    <option value="date_fromto" {if $booking_obj->range_type == 'date_fromto'}selected="selected"{/if}>{l s='Date' mod='bestkit_booking'}</option>
                    <option value="time_fromto" {if $booking_obj->range_type == 'time_fromto'}selected="selected"{/if}>{l s='Time' mod='bestkit_booking'}</option>
                    <option value="datetime_fromto" {if $booking_obj->range_type == 'datetime_fromto'}selected="selected"{/if}>{l s='Date and time' mod='bestkit_booking'}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="qratio_multiplier">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Billable period' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <select name="bestkit_booking[qratio_multiplier]" id="qratio_multiplier">
                    <option value="days" {if $booking_obj->qratio_multiplier == 'days'}selected="selected"{/if}>{l s='Days' mod='bestkit_booking'}</option>
                    <option value="hours" {if $booking_obj->qratio_multiplier == 'hours'}selected="selected"{/if}>{l s='Hours' mod='bestkit_booking'}</option>
                    <option value="minutes" {if $booking_obj->qratio_multiplier == 'minutes'}selected="selected"{/if}>{l s='Minutes' mod='bestkit_booking'}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="billable_interval">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Billable interval' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <input type="text" id="billable_interval" name="bestkit_booking[billable_interval]" value="{$booking_obj->billable_interval|intval}">
                <div class="desc">{l s='Customer will not be able choose less that this period' mod='bestkit_booking'}</div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="date_from" style="margin-right: 5px;">
                <span data-toggle="tooltip" title="">
                    {l s='Date from' mod='bestkit_booking'}
                </span>
            </label>
            <div class="input-group col-lg-5">
                <input class="datepicker hasDatepicker" type="text" name="bestkit_booking[date_from]" value="{$booking_obj->date_from|escape:'htmlall':'UTF-8'}"{*date_format:"%Y-%m-%d%"*} style="text-align: center" id="date_from">
                <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3" for="date_to" style="margin-right: 5px;">
                <span data-toggle="tooltip" title="">
                    {l s='Date to' mod='bestkit_booking'}
                </span>
            </label>
            <div class="input-group col-lg-5">
                <input class="datepicker hasDatepicker" type="text" name="bestkit_booking[date_to]" value="{$booking_obj->date_to|escape:'htmlall':'UTF-8'}" style="text-align: center" id="date_to">
                <span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
            </div>
        </div>

        <div class="form-group" id="time_from_container">
            <label class="control-label col-lg-3" for="time_from">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Time from' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <div class="col-lg-4" style="padding: 0px">
                    <select name="bestkit_booking[time_from][]" id="time_from_h">
                    {for $h=0 to 23}
                        <option value="{$h|string_format:"%02d"}" {if $booking_obj->time_from[0] == $h}selected="selected"{/if}>{$h|string_format:"%02d"}</option>
                    {/for}
                    </select>
                </div>
                <div class="col-lg-4" style="padding: 0px">
                    <select name="bestkit_booking[time_from][]" id="time_from_m">
                    {for $m=0 to 59}
                        <option value="{$m|string_format:"%02d"}" {if $booking_obj->time_from[1] == $m}selected="selected"{/if}>{$m|string_format:"%02d"}</option>
                    {/for}
                    </select>
                </div>
                {*<div class="col-lg-4">
                    <select name="bestkit_booking[time_from][]" id="time_from_s">
                    {for $s=0 to 59}
                        <option value="{$s|string_format:"%02d"}">{$s|string_format:"%02d"}</option>
                    {/for}
                    </select>
                </div>*}
            </div>
        </div>

        <div class="form-group" id="time_to_container">
            <label class="control-label col-lg-3" for="time_to">
                <span {*class="label-tooltip"*} data-toggle="tooltip" title="">
                     {l s='Time to' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-5">
                <div class="col-lg-4" style="padding: 0px">
                    <select name="bestkit_booking[time_to][]" id="time_to_h">
                    {for $h=0 to 23}
                        <option value="{$h|string_format:"%02d"}" {if $booking_obj->time_to[0] == $h}selected="selected"{/if}>{$h|string_format:"%02d"}</option>
                    {/for}
                    </select>
                </div>
                <div class="col-lg-4" style="padding: 0px">
                    <select name="bestkit_booking[time_to][]" id="time_to_m">
                    {for $m=0 to 59}
                        <option value="{$m|string_format:"%02d"}" {if $booking_obj->time_to[1] == $m}selected="selected"{/if}>{$m|string_format:"%02d"}</option>
                    {/for}
                    </select>
                </div>
                {*<div class="col-lg-4">
                    <select name="bestkit_booking[time_to][]" id="time_to_s">
                    {for $s=0 to 59}
                        <option value="{$s|string_format:"%02d"}">{$s|string_format:"%02d"}</option>
                    {/for}
                    </select>
                </div>*}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                <span>
                     {l s='Exclude periods' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-6">
                {$booking_excludeddays_html} {*|escape:'htmlall':'UTF-8' - is not possible using here*}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-3">
                <span>
                     {l s='Price rules' mod='bestkit_booking'}
                </span>
            </label>
            <div class="col-lg-6">
                {$booking_pricerules_html} {*|escape:'htmlall':'UTF-8' - is not possible using here*}
            </div>
        </div>

        <div class="separation"></div>
        <div class="clear">&nbsp;</div>
		
        <div class="form-group">
            {$booking_map_settings}
        </div>
		
        <div class="separation"></div>
        <div class="clear">&nbsp;</div>

        {literal}
        <script type="text/javascript">

            var booking = {
                init : function() {
                    $('#range_type').change(function(){
                        booking.periodCheck(this);
                    });

                    booking.periodCheck($('#range_type'));
                },

                periodCheck : function(el, price_rule_add) {
                    if(typeof(price_rule_add) === 'undefined') price_rule_add = false;
                    if ($(el).val() == 'date_fromto') {
                        if (!price_rule_add) {
                            booking.disableOptionsByValue('#qratio_multiplier', ['hours', 'minutes']);
                            booking.disablePriceRulesOptionsByValue(['from_to_time', 'from_to_datetime']);
                            $('#time_from_container, #time_to_container').hide();
                        }
                        booking.disableOptionsByValue('#pricerules_table .period_type:last', ['from_to_time', 'from_to_datetime']);
                    } else if ($(el).val() == 'time_fromto') {
                        if (!price_rule_add) {
                            booking.disableOptionsByValue('#qratio_multiplier', ['days']);
                            booking.disablePriceRulesOptionsByValue(['from_to_date', 'recurrent_day', 'recurrent_date', 'from_to_datetime']);
                            $('#time_from_container, #time_to_container').show();
                        }
                        booking.disableOptionsByValue('#pricerules_table .period_type:last', ['from_to_date', 'recurrent_day', 'recurrent_date', 'from_to_datetime']);
                    } else if ($(el).val() == 'datetime_fromto') {
                        if (!price_rule_add) {
                            booking.disableOptionsByValue('#qratio_multiplier', ['']);
                            booking.disablePriceRulesOptionsByValue(['']);
                            $('#time_from_container, #time_to_container').show();
                        }
                        booking.disableOptionsByValue('#pricerules_table .period_type:last', ['']);
                    }
                },

                disableOptionsByValue : function(selector, values) {
                    //disable options
                    $(selector + " option").each(function(i, item){
                        if (values.indexOf($(item).val()) != -1) {
                            $(item).attr('disabled', 'disabled');
                        } else {
                            $(item).removeAttr('disabled');
                        }
                    });
                    //choose the available
                    if (typeof $(selector + " option:selected").attr('disabled') !== typeof undefined && $(selector + " option:selected").attr('disabled') !== false && !$(selector).hasClass('pricerules_period_type')) {
                        $(selector + " option:selected").removeAttr('selected');
                        $(selector + " option").each(function(i, item) {
                            if (!(typeof $(item).attr('disabled') !== typeof undefined && $(item).attr('disabled') !== false)) {
                                $(item).attr('selected', 'selected');
                                return false;
                            }
                        });
                    }
                },

                disablePriceRulesOptionsByValue : function(values) {
                    $('#pricerules_table .period_type').each(function(i, item) {
                        $(item).parents('tr').first().find('select, input').removeAttr('disabled');
                    });

                    $('#pricerules_table .period_type').each(function(i, item) {
                        if (!$(item).parents('tr#booking_pricerules_template').first().length) {
                            //console.log(i);
                            //console.log($(item).val());

                            if (values.indexOf($(item).val()) != -1) {
                                $(item).parents('tr').first().find('select, input').attr('disabled', 'disabled')
                            }
                        }
                    })
                }

            }

            $( document ).ready(function() {
                $("#bestkit_booking_wrapper .datepicker").removeAttr("id").removeClass("hasDatepicker").datepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd'
                });

                booking.init();
            });
        </script>
        <style>
            #bestkit_booking_wrapper .desc {font-size: 11px;}
        </style>
        {/literal}

        <div class="panel-footer">
            <a href="{Context::getContext()->link->getAdminLink('AdminProducts')}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='bestkit_booking'}</a>
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='bestkit_booking'}</button>
            <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay' mod='bestkit_booking'}</button>
        </div>
    </div>
{else}
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        {l s='There is 1 warning.' mod='bestkit_booking'}
        <ul style="display:block;" id="seeMore">
            <li>{l s='You must save this product before adding booking.' mod='bestkit_booking'}</li>
        </ul>
    </div>
{/if}