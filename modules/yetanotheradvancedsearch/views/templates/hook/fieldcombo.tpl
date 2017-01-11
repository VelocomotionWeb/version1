{*
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*}
				
	{if count($field_values) > 0}
        
       <select data-allow-multiple="{$criterion['allow_multiple']|escape}" data-id-criteria-field="{$criterion['id_criteria_field']|escape}" class="yaas-select" {if ($criterion['allow_multiple']==1)}multiple="multiple" size="{if count($field_values) < 5 }{{count($field_values) + 1}}{else}5{/if}"{/if}>
		   <option selected="selected" class="yaas-not-specified" value="-1">{$yaas->translate('Not specified')|escape}</option>
          	
       {foreach $field_values as $fieldValue}
        {if $fieldValue['count'] > 0}

			{if ($criterion['id_criteria_type'] == CriteriaTypeEnum::CATEGORY)}
				{if $fieldValue['custom'] >= 3}
					{capture name="catIndent" assign="indent"}{$fieldValue['custom']|escape}{/capture}
				{else}
					{assign var="indent" value="2"}
				{/if}
			{else}
				{assign var="indent" value="2"}
			{/if}
        
			{if (!($criterion['id_criteria_type'] == CriteriaTypeEnum::FEATURE && $ignoreCustom == "true" && $fieldValue["custom"] == "1")) }
			
				{* {{if $criterion['custom'] == 'color'}}style="background-color:{$fieldValue['custom']}"{{/if}}  *}
				<option value="{$criterion['id_criteria_type']|escape}-{$fieldValue['id_criteria_field']|escape}v{$fieldValue['id_internal']|escape}" data-yaas-name="{$fieldValue['name']|escape}" data-yaas-count="{$fieldValue['count']|escape}" data-yaas-kept-count="{$fieldValue['count']|escape}" data-type="combo" class="yaas-criterion" data-internal-id="{$criterion['id_criteria_type']|escape}-{$fieldValue['id_criteria_field']|escape}v{$fieldValue['id_internal']|escape}" data-yaas-indent="{$indent|escape}">{section name=2 loop=$indent}&nbsp;&nbsp;{/section}{$fieldValue['name']|escape} ({$fieldValue['count']|escape})</option>
			
			{/if}
			
	    {/if}
	    
       {/foreach}
       </select>

	{/if}
