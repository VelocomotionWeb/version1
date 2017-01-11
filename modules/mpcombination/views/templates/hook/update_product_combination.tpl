<div class="tab-pane" id="combination">
	<div class="box-account box-recent">
		<div class="box-head">
			<div class="box-head-left">
				<h2>{l s='Product combination' mod='mpcombination'}</h2>
			</div>
			<div class="box-head-right">
				<a class="btn btn-default button button-small"  href="{$createcombination_link|escape:'htmlall':'UTF-8'}">
					<span>{l s='Create combination' mod='mpcombination'}</span>
				</a>
				<a class="btn btn-default button button-small" id="generate_combination" href="{$link->getModuleLink('mpcombination', 'generatecombination', ['mp_product_id'=>$mp_product_id|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
					<span>{l s='Generate combination' mod='mpcombination'}</span>
				</a>
			</div>
		</div>
		<div class="wk_border_line"></div>
		<div class="box-content" id="wk_product_combination">
			<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
					<th>{l s='Attributes' mod='mpcombination'}</th>
					<th>{l s='Impact' mod='mpcombination'}</th>
					<th>{l s='Weight' mod='mpcombination'}</th>
					<th>{l s='Reference' mod='mpcombination'}</th>
					<th>{l s='EAN13' mod='mpcombination'}</th>
					<th>{l s='UPC' mod='mpcombination'}</th>
					<th style="width:15%;"><center>{l s='Action' mod='mpcombination'}</center></th>
					</tr>
				</thead>
				<tbody>
					{if $combination_detail!=-1}
						{foreach $combination_detail as $combination_det}
							<tr>
								<td>{$combination_det['attribute_designation']|escape:'htmlall':'UTF-8'}</td>
								<td>{$combination_det['mp_price']|escape:'htmlall':'UTF-8'}</td>
								<td>{$combination_det['mp_weight']|escape:'htmlall':'UTF-8'}</td>
								<td>{$combination_det['mp_reference']|escape:'htmlall':'UTF-8'}</td>
								<td>{$combination_det['mp_ean13']|escape:'htmlall':'UTF-8'}</td>
								<td>{$combination_det['mp_upc']|escape:'htmlall':'UTF-8'}</td>
								<td>									
									<a href="{$link->getModuleLink('mpcombination', 'mpattributemanage', ['mp_product_attr_id' => $combination_det.mp_id_product_attribute|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" title="{l s='Edit' mod='mpcombination'}" class="edit_attribute col-md-2" id="edit_attribute{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}" alt="{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}">
										<i class="icon-edit"></i>
									</a>									
									<a href="" title="{l s='Delete' mod='mpcombination'}" class="delete_attribute col-md-2" id="delete_attribute{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}" alt="{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}">
										<i class="icon-trash"></i>										
									</a>
									{if $combination_det['mp_default_on']==1}
										<input type="hidden" id="default_product_attribute" value="{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}">
									{/if}
									<a href="" title="{l s='Default' mod='mpcombination'}" class="default_attribute col-md-2" id="default_attribute{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}" alt="{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}" {if $combination_det['mp_default_on']==1}style="display:none;"{/if}>
										<i class="icon-star"></i>
									</a>									
								</td>
							</tr>
							<div class="left basciattr_update" id="attribute_div_{$combination_det['mp_id_product_attribute']|escape:'htmlall':'UTF-8'}">
							</div>
						{/foreach}
					{else}
						<tr>
							<td colspan="7">
								<div class="full left planlistcontent call" style="text-align:center;">{l s='No combination available for this product' mod='mpcombination'}</div>
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
			</div>
		</div>
	</div>
</div>

{strip}
	{addJsDefL name=generate_combination_confirm_msg}{l s='You will lose all unsaved modifications. Are you sure that you want to proceed?' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDef attribute_delete_ajax_link=$attribute_delete_ajax_link}		
	{addJsDef createcombination_link=$createcombination_link}	
	{addJsDefL name=confirmation_msg}{l s='Are you sure?' js=1 mod='mpcombination'}{/addJsDefL}
{/strip}

<script type="text/javascript" src="{$modules_dir|escape:'htmlall':'UTF-8'}mpcombination/views/js/attributesBack.js">
</script>
