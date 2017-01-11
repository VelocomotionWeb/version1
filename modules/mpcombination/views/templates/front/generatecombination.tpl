{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'htmlall':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='mpcombination'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Generate Combination' mod='mpcombination'}</span>
{/capture}

{if $logged}
	<div class="left full">
		<div class="box-account box-recent">
			<div class="box-head">
				<div class="box-head-left">
					<h2>{l s='Attribute Generator' mod='mpcombination'}</h2>
				</div>
				<div class="box-head-right">
				<a class="btn btn-default button button-small" href="{$link->getModuleLink('marketplace', 'productupdate',['id'=>$mp_product_id|escape:'htmlall':'UTF-8','editproduct'=>1])|escape:'htmlall':'UTF-8'}">
					<span>{l s='Back to product update' mod='mpcombination'}</span>
				</a>
				</div>
			</div>
			<div class="wk_border_line"></div>
			<div class="box-content">
				<div class="error alert alert-danger" {if $message==0}style="display:none;"{/if}>					
					{if $message==1}
						{l s='Please select at least one attribute' mod='mpcombination'}
					{elseif $message==2}
						{l s='Unable to initialize these parameters. A combination is missing or an object cannot be loaded.' mod='mpcombination'}
					{/if}
				</div>

				<script type="text/javascript">
					i18n_tax_exc = '{l s='Tax Excluded' mod='mpcombination'} ';
					i18n_tax_inc = '{l s='Tax Included' mod='mpcombination'} ';

					var product_tax = "{$tax_rates|escape:'htmlall':'UTF-8'}";
					function calcPrice(element, element_has_tax)
					{
						var element_price = element.val().replace(/,/g, '.');
						var other_element_price = 0;

						if (!isNaN(element_price) && element_price > 0)
						{
							if (element_has_tax)
								other_element_price = parseFloat(element_price / ((product_tax / 100) + 1)).toFixed(6);
							else
								other_element_price = ps_round(parseFloat(element_price * ((product_tax / 100) + 1)), 2).toFixed(2);
						}

						$('#related_to_'+element.attr('name')).val(other_element_price);
					}

					$(document).ready(function() { $('.price_impact').each(function() { calcPrice($(this), false); }); });
				</script>

				<div class="wk_attr_selector_form">
				<div class="form-group form-horizontal">
					<form enctype="multipart/form-data" method="post" id="generator" action="{$link->getModuleLink('mpcombination', 'addCombination',['id'=>$mp_product_id|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}">
						<input type="hidden" value="{$mp_product_id|escape:'htmlall':'UTF-8'}" id="mp_product_id" name="mp_product_id">
						<div class="wk_attr_selector col-lg-3 col-xs-8">
							<select multiple name="attributes[]" id="attribute_group" class="form-control">
								{foreach $attribute_groups as $k => $attribute_group}
									{if isset($attribute_js[$attribute_group['id_attribute_group']])}
										<optgroup name="{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}" id="{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}" label="{$attribute_group['name']|escape:'htmlall':'UTF-8'}">
											{foreach $attribute_js[$attribute_group['id_attribute_group']] as $k => $v}
												<option name="{$k|escape:'htmlall':'UTF-8'}" id="attr_{$k|escape:'htmlall':'UTF-8'}" value="{$v|escape:'htmlall':'UTF-8'}" title="{$v|escape:'htmlall':'UTF-8'}">{$v|escape:'htmlall':'UTF-8'}</option>
											{/foreach}
										</optgroup>
									{/if}
								{/foreach}
							</select>
							<div class="wk_attr_gp_btn">
								<input class="btn btn-primary" type="button" style="margin-right: 15px;" value="{l s='Add' mod='mpcombination'}" onclick="add_attr_multiple();" />
								<input class="btn btn-primary" type="button" value="{l s='Delete' mod='mpcombination'}" onclick="del_attr_multiple();" />
							</div>
						</div>
						<div class="col-lg-9 col-xs-10">
							{foreach $attribute_groups as $k => $attribute_group}
								{if isset($attribute_js[$attribute_group['id_attribute_group']])}
									<table class="table clear" cellpadding="0" cellspacing="0" style="margin-bottom: 10px; display: none;">
										<thead>
											<tr>
												<th id="tab_h1" style="width: 150px">{$attribute_group['name']|escape:'htmlall':'UTF-8'}</th>
												<th id="tab_h2" style="width: 350px" colspan="2">{l s='Impact on the product price' mod='mpcombination'} ({$currency_sign|escape:'htmlall':'UTF-8'})</th>
												<th style="width: 150px">{l s='Impact on the product weight' mod='mpcombination'} ({$weight_unit|escape:'htmlall':'UTF-8'})</th>
											</tr>
										</thead>
										<tbody id="table_{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}" name="result_table">
										</tbody>
									</table>									
									{if isset($attributes[$attribute_group['id_attribute_group']])}
										{foreach $attributes[$attribute_group['id_attribute_group']] AS $k => $attribute}
										{if isset($attribute[price]) || isset($attribute['weight'])}
											<script type="text/javascript">
												$('#table_{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}').append(create_attribute_row({$k|escape:'htmlall':'UTF-8'}, {$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}, '{$attribute['attribute_name']|addslashes}', {$attribute['price']|escape:'htmlall':'UTF-8'}, {$attribute['weight']|escape:'htmlall':'UTF-8'}));
												toggle(getE('table_' + {$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}).parentNode, true);
											</script>
										{else}
											<script type="text/javascript">
												$('#table_{$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}').append(create_attribute_row({$k|escape:'htmlall':'UTF-8'}, {$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}, '{$attribute['attribute_name']|addslashes}', {$attribute['mp_price']|escape:'htmlall':'UTF-8'}, {$attribute['mp_weight']|escape:'htmlall':'UTF-8'}));
												toggle(getE('table_' + {$attribute_group['id_attribute_group']|escape:'htmlall':'UTF-8'}).parentNode, true);
											</script>
										{/if}
										{/foreach}						
									{/if}
								{/if}
							{/foreach}
						
						<div class="left col-lg-12">
							<h4>{l s='Select a default quantity, and reference, for each combination the generator will create for this product.' mod='mpcombination'}</h4>
							<table border="0" class="table" cellpadding="0" cellspacing="0">
								<tr>
									<td>{l s='Default Quantity:' mod='mpcombination'}</td>
									<td><input type="text" size="20" name="quantity" value="0" class="form-control" style="width: 50px;" /></td>
								</tr>
								<tr>
									<td>{l s='Default Reference:' mod='mpcombination'}</td>
									<td><input type="text" size="20" class="form-control" name="reference" value="" /></td>
								</tr>
							</table>
							<h4>{l s='Please click on "Generate these Combinations"'  mod='mpcombination'}</h4>
							<p>
							<button type="submit" class="btn btn-default button button-small" style="margin-bottom:5px;" name="generate"/>
								<span>{l s='Generate these Combinations' mod='mpcombination'}</span>
							</button>
							</p>
						</div>
						</div>
					</form>
				</div>
				</div>
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to generate combination.' mod='mpcombination'}</span>
	</div>
{/if}

