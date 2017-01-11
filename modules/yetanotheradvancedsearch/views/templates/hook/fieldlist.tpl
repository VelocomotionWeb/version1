{*
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*}

       {foreach $field_values as $fieldValue}
        
        {if $fieldValue['count'] > 0}
        
            {if (!($criterion['id_criteria_type'] == CriteriaTypeEnum::FEATURE && $ignoreCustom == "true" && $fieldValue["custom"] == "1")) }
						
				<li>
					<div data-type="list" class="yaas-criterion" data-internal-id="{$criterion['id_criteria_type']|escape}-{$fieldValue['id_criteria_field']|escape}v{$fieldValue['id_internal']|escape}">
					
					{if ($criterion['id_criteria_type'] == CriteriaTypeEnum::CATEGORY)}
						{if $fieldValue['custom'] >= 3}
							<div class="yaas-sub-category" style="width:{($fieldValue['custom']|escape - 1) * 10}px"></div>
						{/if}
					{/if}
					<a href="javascript:void(0);" >
					
					{{if $criterion['custom'] == 'color'}}
					  <div class="yaas_color" style="background-color:{$fieldValue['custom']|escape}" ></div>
					{{/if}}
					{$fieldValue['name']|escape}
					  <span class="yaasCount">
					  ({$fieldValue['count']|escape})
					  </span>
					  <span class="yaasKeptCount" style="display:none">{$fieldValue['count']|escape}</span>
					</a>
					</div>
				</li>
		
		  {/if}
	    {/if}
	    
       {/foreach}
