{*
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*}

{if floatval($field_values[0]['name']) > floatval($field_values[1]['name']) }
	{assign var="offsetMax" value="0"}
	{assign var="offsetMin" value="1"}
{else}
	{assign var="offsetMax" value="1"}
	{assign var="offsetMin" value="0"}
{/if}
<div class="yaasSliderContainer">
    <span class="yaasSlider">
		<input type="text" class="yaas-criterion" data-yaas-count="{$fieldValue['count']|escape}" data-yaas-kept-count="{$fieldValue['count']|escape}" data-symbol="{$symbol|escape}" data-type="slide" name="area" data-internal-id-min="{$criterion['id_criteria_type']|escape}-{$field_values[$offsetMin]['id_criteria_field']|escape}v{$field_values[$offsetMin]['id_internal']|escape}" data-internal-id-max="{$criterion['id_criteria_type']|escape}-{$field_values[$offsetMax]['id_criteria_field']|escape}v{$field_values[$offsetMax]['id_internal']|escape}" data-selected-min="-1" data-selected-max="-1" data-max="{ceil($field_values[$offsetMax]['name'])|escape}" data-min="{floor($field_values[$offsetMin]['name'])|escape}" />
	</span>
</div>
			  
