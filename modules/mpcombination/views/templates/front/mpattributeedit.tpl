{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='mpcombination'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Update Combination' mod='mpcombination'}</span>
{/capture}

{if $logged}
	{include file="$tpl_dir./errors.tpl"}
	
	<div class="main_block">
		{hook h="DisplayMpmenuhook"}
		<div class="dashboard_content">
			<div class="page-title">
				<span>{l s='Edit Combination' mod='mpcombination'}</span>
			</div>
			<div class="wk_right_col">
				<a href="{$link->getModuleLink('marketplace', 'productupdate', ['flag' => 1, 'id' => $mp_id_product|escape:'htmlall':'UTF-8','editproduct' => 1])|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small pull-right">
					<span>{l s='Back to product' mod='mpcombination'}</span>
				</a>
				<form action="{$link->getModuleLink('mpcombination', 'mpattributemanage', ['mp_product_attr_id' => $mp_id_product_attribute|escape:'htmlall':'UTF-8'])|escape:'htmlall':'UTF-8'}" method="post" id="mp_attribute_edit_pro" class="form-horizontal">
					<div class="full left">
						<input type="hidden" value="{$mp_id_product_attribute|escape:'htmlall':'UTF-8'}" id="mp_id_product_attribute" name="mp_id_product_attribute"/>
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='Attribute' mod='mpcombination'}
							</label>
							<div class="col-lg-9">
								<span style="float:left;margin-right:5px;">
									<input type="hidden" id="pathh" value="{$path_edit_attr|escape:'htmlall':'UTF-8'}"/>
									<input type="hidden" id="mp_id_product" name="mp_id_product" value="{$mp_id_product|escape:'htmlall':'UTF-8'}"/>
									
									<select id="attribute_select" class="form-control"  style="width: 140px;" name="attribute_select">
										<option value="">{l s='Select' mod='mpcombination'}</option>
										{foreach $att_group as $attribut_group}
										<option  value= "{$attribut_group['id_attribute_group']|escape:'htmlall':'UTF-8'}">{l s="{$attribut_group['name']|escape:'htmlall':'UTF-8'}" mod='mpcombination'}</option>
										{/foreach}
									</select>
								</span>
							</div>
						</div>

						<div class="full left form-group">
								<label class="control-label col-lg-3">
									 {l s='Value' mod='mpcombination'}
								</label>
							<div class="col-lg-9">
								<div class="col-lg-9">
									<div class="form-group">
										<span style="float:left;margin-right:5px;">
											<select class="form-control" id="attribute_value_select"  style="width: 140px;" name="attribute_value_select">
												<option value="">{l s='Select' mod='mpcombination'}</option>
											</select>
										</span>
										<div class="col-lg-4">
											<button class="btn btn-primary btn-xs" id ="add_attr_button" type="button">
												<span><i class="icon-plus-sign-alt"></i> {l s='Add' mod='mpcombination'}</span>
											</button>
										</div>
									</div>
								</div>
								<div class="col-lg-8" >
									<div class="form-group">
										<span style="float:left;margin-right:5px;">
											<select class="form-control" id="product_att_list"  style="width: 140px; margine-top:5px;" multiple="multiple" name="attribute_combination_list[]">
											{if isset($attribute_box)}
											{foreach $attribute_box as $attribute_box1}
												<option  value="{$attribute_box1['id']|escape:'htmlall':'UTF-8'}" groupid="{$attribute_box1['groupid']|escape:'htmlall':'UTF-8'}">{l s="{$attribute_box1['name']|escape:'htmlall':'UTF-8'}" mod='mpcombination'}</option>
											{/foreach}
											{/if}
											</select>
										</span>
										<div class="col-lg-4">
											<button class="btn btn-primary btn-xs" id ="del_attr_button" type="button">
												<span><i class="icon-minus-sign-alt"></i> {l s='Delete' mod='mpcombination'}</span>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="full left form-group">
							<label class="control-label col-lg-3">{l s='Quantity' mod='mpcombination'}</label>
							<div class="col-lg-3">
								<input type="text" value="{$quantity|escape:'htmlall':'UTF-8'}" name="mp_quantity" id="mp_quantity" class="form-control"/>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								{l s='Reference' mod='mpcombination'}
							</label>
							<div class="col-lg-3">
								<input type="text" value="{$mp_reference|escape:'htmlall':'UTF-8'}" placeholder="Reference" name="mp_reference" id="mp_reference" class="form-control"/>
								<p class="preference_description">{l s='Special characters allowed' mod='mpcombination'}.-_#</p>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='EAN13' mod='mpcombination'}
							</label>
							<div class="col-lg-3">
								<input type="text" value="{$mp_ean13|escape:'htmlall':'UTF-8'}" placeholder="EAN13" name="mp_ean13" id="mp_ean13" class="form-control"/>
								<p class="preference_description">{l s='Special characters allowed' mod='mpcombination'} .-_#</p>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='UPC' mod='mpcombination'}
							</label>
							<div class="col-lg-3">
								<input type="text" value="{$mp_upc|escape:'htmlall':'UTF-8'}" placeholder="UPC" name="mp_upc" id="mp_upc" class="form-control"/>
								<p class="preference_description">{l s='Special characters allowed' mod='mpcombination'} .-_#</p>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='Wholesale price' mod='mpcombination'} 
							</label>
							<div class="col-lg-3">
								<input type="text" value="{$mp_wholesale_price|escape:'htmlall':'UTF-8'}" name="mp_wholesale_price" id="mp_wholesale_price" class="form-control"/>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='Impact on price' mod='mpcombination'}
							</label>
							<div class="col-lg-9">
								<span style="float:left;margin-right:5px;">
									<select class="form-control" id="attribute_price_impact" onchange="calcImpactPriceTI();" style="width: 140px;" name="attribute_price_impact">
										<option value="0" {if $mp_price == (float)0}selected="selected"{/if}>{l s='None' mod='mpcombination'}</option>
										<option value="1" {if $mp_price > (float)0}selected="selected"{/if}>{l s='Increase' mod='mpcombination'}</option>
										<option value="-1" {if $mp_price < (float)0}selected="selected"{/if}>{l s='Reduction' mod='mpcombination'}</option>
									</select>
								</span>
								<span id="impact_price_span" class="col-lg-3" {if $mp_price == (float)0} style="display:none"{/if}>
									<input id="attribute_priceTEReal" type="hidden" value="{abs($mp_price)|escape:'htmlall':'UTF-8'}" name="attribute_price">
									<input type="text" value="{abs($mp_price)|escape:'htmlall':'UTF-8'}"  name="mp_price" id="mp_price" class="form-control"/>
									<input id="attribute_priceTI" type="text" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.'); calcImpactPriceTE();" value="0.00" name="attribute_priceTI" size="6" style="display:none;" class="form-control"/>
									<p class="preference_description">{l s='Price Without tax' mod='mpcombination'}</p>
								</span>
							</div>
						</div>
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								  {l s='Impact on weight' mod='mpcombination'}
							</label>
							<div class="col-lg-9">
								<span style="float:left;margin-right:5px;">
									<select class="form-control" id="attribute_weight_impact" style="width: 140px;" name="attribute_weight_impact">
										<option value="0" {if $mp_weight == (float)0}selected="selected"{/if}>{l s='None' mod='mpcombination'}</option>
										<option value="1" {if $mp_weight > (float)0}selected="selected"{/if}>{l s='Increase' mod='mpcombination'}</option>
										<option value="-1" {if $mp_weight < (float)0}selected="selected"{/if}>{l s='Reduction' mod='mpcombination'}</option>
									</select>
								</span>
								<span id="impact_weight_span" class="col-lg-3" {if $mp_weight==(float)0} style="display:none"{/if}>
									<input id="attribute_weight" type="text" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');"  value="{abs($mp_weight|escape:'htmlall':'UTF-8')}" name="attribute_weight" size="6" class="form-control"/>
								</span>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								{l s='Impact on unit price' mod='mpcombination'}
							</label>
							<div class="col-lg-9">
								<span style="float:left;margin-right:5px;">
									<select class="form-control" id="attribute_unit_impact" style="width: 140px;" name="attribute_unit_impact">										
										<option value="0" {if $mp_unit_price_impact == (float)0}selected="selected"{/if}>{l s='None' mod='mpcombination'}</option>
										<option value="1" {if $mp_unit_price_impact > (float)0}selected="selected"{/if}>{l s='Increase' mod='mpcombination'}</option>
										<option value="-1" {if $mp_unit_price_impact < (float)0}selected="selected"{/if}>{l s='Reduction' mod='mpcombination'}</option>
									</select>
								</span>
								<span id="impact_unit_price_span" class="col-lg-3" {if $mp_unit_price_impact == (float)0} style="display:none"{/if}>
									<input id="attribute_unity" type="text" class="form-control" onkeyup="if (isArrowKey(event)) return ;this.value = this.value.replace(/,/g, '.');" value="{abs($mp_unit_price_impact|escape:'htmlall':'UTF-8')}" name="attribute_unity" size="6" />
								</span>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='Minimum quantity' mod='mpcombination'}
							</label>
							<div class="col-lg-3">
								<input id="attribute_minimal_quantity" class="form-control" type="text" value="{$mp_minimal_quantity|escape:'htmlall':'UTF-8'}" name="attribute_minimal_quantity" maxlength="6" size="3">
								<p class="preference_description"> {l s='The minimum quantity to buy this product (set to 1 to disable this feature)' mod='mpcombination'}</p>
							</div>
						</div>
						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								 {l s='Available date' mod='mpcombination'}
							</label>
							<div class="col-lg-3">
								<input id="available_date_attribute" class="datepicker1 form-control" type="text" style="text-align: center;" value="{$mp_available_date|escape:'htmlall':'UTF-8'}" name="available_date_attribute">
								<p class="preference_description">{l s='If this product is out of stock, you can indicate when the product will be available again.' mod='mpcombination'}</p>
							</div>
						</div>						
						<div class="full left form-group">
							<label class="control-label col-lg-3">
								{l s='Image:' mod='mpcombination'}
							</label>
							<div class="col-lg-9">
								{if isset($mp_pro_image) && $mp_pro_image}
									<ul id="id_image_attr" class="list-inline">
										{foreach from=$mp_pro_image key=k item=image}
											<li>
												<input type="checkbox" name="id_image_attr[]" value="{$image.id_image|escape:'htmlall':'UTF-8'}" id="id_image_attr_{$image.id_image|escape:'htmlall':'UTF-8'}" class="attri_images" {if isset($ps_atrribute_images)}{foreach $ps_atrribute_images as $ps_image}{if $image.id_image == $ps_image['id_image']}checked{/if}{/foreach}{/if}/>
												<label for="id_image_attr_{$image.id_image|escape:'htmlall':'UTF-8'}">
													<img class="img-thumbnail" src="{$smarty.const._THEME_PROD_DIR_|escape:'htmlall':'UTF-8'}{$image.obj->getExistingImgPath()|escape:'htmlall':'UTF-8'}-small_default.jpg" alt="{$image.legend|escape:'htmlall':'UTF-8'}" title="{$image.legend|escape:'htmlall':'UTF-8'}" />
												</label>
											</li>
										{/foreach}
									</ul>
									<img id="pic" alt="" title="" style="display: none; width: 100px; height: 100px; float: left; border: 1px dashed #BBB; margin-left: 20px;" >
								{else}
									<div class="alert alert-warning">
										{if isset($img_available)}
											{l s='You must upload an image before you can select one for your combination.' mod='mpcombination'}
										{else}
											{l s='Product must approved before you can select one for your combination.' mod='mpcombination'}
										{/if}
									</div>	
								{/if}							
							</div>
						</div>
						<div class="left full" style="text-align:center;">
							<button class="btn btn-default button button-medium" type="submit" id="btn_combination_validate" name="range_submit">
								<span>{l s='Update' mod='mpcombination'}</span>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
{else}
	<div class="alert alert-danger">
		{l s='You are logged out. Please login to update combination.' mod='mpcombination'}</span>
	</div>
{/if}
<style type="text/css">
	#ui-datepicker-div
	{
	    background-color: gainsboro;
	}
</style>
{strip}
	{addJsDefL name=error_msg1}{l s='Combination attribute cannot be blank.' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=error_msg2}{l s='Quantity should be integer.' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=error_msg3}{l s='Impact price must be numeric.' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=error_msg4}{l s='Impact on weight must be numeric.' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=error_msg5}{l s='Impact on unit price must be numeric.' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=error_msg6}{l s='Minimum quantity  should be integer and greater than 0.' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=option_select}{l s='Select' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=req_attr}{l s='Attribute is not selected' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=req_attr_val}{l s='Value is not selected' js=1 mod='mpcombination'}{/addJsDefL}
	{addJsDefL name=attr_already_selected}{l s='Attribute is already selected' js=1 mod='mpcombination'}{/addJsDefL}
{/strip}
<script type="text/javascript">
	var selected_attribute_group = "{$attribute_box_group_id_json|escape:'quotes':'UTF-8'}"; // json array here bcz it convert in plain string in {strip} tag above
</script>