{*
* 2007-2013 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Contact'}{/capture}
<div class="contact row">
	<div class="col-lg-6">
		<div class="contact-box contact-add">
			<img src="{$content_dir}themes/jms_travel/img/demo/contact.jpg" class="img-responsive" alt="" />
			<div class="contact_address">
				<h3 class="title">
					<span>{l s='Our location'}</span>
				</h3>
				<ul>
					<li>
						<i class="fa fa-map-marker"></i>
						{l s='Address:'}&nbsp;<span>{l s='No. 1122 South Large Street, California City, United Stated.'}</span>
					</li>
					<li>
						<i class="fa fa-phone"></i>
						{l s='Phone:'}&nbsp;<span>{l s='+00 1 1312 567'}</span>
					</li>
					<li>
						<i class="fa fa-envelope"></i>
						{l s='Email:'}&nbsp;<span>{l s='Customercare@jms.com'}</span>
					</li>
					<li>
						<i class="fa fa-clock-o"></i>
						{l s='Working:'}&nbsp;<span>8h30 - 20h30, everyday</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
	<div class="contact-box">
	{if isset($confirmation)}
		<p>{l s='Your message has been successfully sent to our team.'}</p>
		<ul class="footer_links">
			<li><a href="{$base_dir}"><span class="fa fa-home"></span></a><a href="{$base_dir}">{l s='Home'}</a></li>
		</ul>
	{elseif isset($alreadySent)}
		<p>{l s='Your message has already been sent.'}</p>
		<ul class="footer_links">
			<li><a href="{$base_dir}"><span class="fa fa-home"></span></a><a href="{$base_dir}">{l s='Home'}</a></li>
		</ul>
	{else}	
		{include file="$tpl_dir./errors.tpl"}
		<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std form-horizontal" enctype="multipart/form-data">
			<div class="contacts-form">			
				<div class="wrap-paper">
				<div class="paper">
					<div class="form-group">
						<h3 class="title col-sm-offset-3 col-sm-9">
							<span>{l s='Send a message'}</span>
						</h3>
					</div>	
					<div class="select form-group">
						<label for="id_contact" class="col-sm-3 control-label">{l s='Subject Heading'}</label>
					{if isset($customerThread.id_contact)}
						<div class="col-sm-9">
						{foreach from=$contacts item=contact}
							{if $contact.id_contact == $customerThread.id_contact}
								<input type="text" id="contact_name" name="contact_name" class="form-control" value="{$contact.name|escape:'htmlall':'UTF-8'}" readonly="readonly" />
								<input type="hidden" name="id_contact" value="{$contact.id_contact}" />
							{/if}
						{/foreach}
						</div>
					</div>
					{else}
						<div class="col-sm-9">
							<select id="id_contact" name="id_contact" class="form-control" onchange="showElemFromSelect('id_contact', 'desc_contact')">
								<option value="0">{l s='-- Choose --'}</option>
							{foreach from=$contacts item=contact}
								<option value="{$contact.id_contact|intval}" {if isset($smarty.post.id_contact) && $smarty.post.id_contact == $contact.id_contact}selected="selected"{/if}>{$contact.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
							</select>
						</div>	
					</div>			
					<p id="desc_contact0" class="desc_contact col-sm-offset-3 col-sm-9">&nbsp;</p>
						{foreach from=$contacts item=contact}
							<p id="desc_contact{$contact.id_contact|intval}" class="desc_contact col-sm-offset-3 col-sm-9" style="display:none;">
								{$contact.description|escape:'htmlall':'UTF-8'}
							</p>
						{/foreach}
					{/if}
				
					<div class="text form-group">
						<label for="email" class="col-sm-3 control-label">{l s='Email address'}</label>
						<div class="col-sm-9">
						{if isset($customerThread.email)}
							<input type="text" id="email" class="form-control" name="from" value="{$customerThread.email|escape:'htmlall':'UTF-8'}" readonly="readonly" />
						{else}
							<input type="text" id="email" class="form-control" name="from" value="{$email|escape:'htmlall':'UTF-8'}" />
						{/if}
						</div>
					</div>
				{if !$PS_CATALOG_MODE}
					{if (!isset($customerThread.id_order) || $customerThread.id_order > 0)}
					<div class="text select form-group">
						<label for="id_order" class="col-sm-3 control-label">{l s='Order reference'}</label>
						<div class="col-sm-9">
							{if !isset($customerThread.id_order) && isset($isLogged) && $isLogged == 1}
								<select name="id_order" class="form-control">
									<option value="0">{l s='-- Choose --'}</option>
									{foreach from=$orderList item=order}
										<option value="{$order.value|intval}" {if $order.selected|intval}selected="selected"{/if}>{$order.label|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							{elseif !isset($customerThread.id_order) && !isset($isLogged)}
								<input type="text" name="id_order" class="form-control" id="id_order" value="{if isset($customerThread.id_order) && $customerThread.id_order > 0}{$customerThread.id_order|intval}{else}{if isset($smarty.post.id_order)}{$smarty.post.id_order|intval}{/if}{/if}" />
							{elseif $customerThread.id_order > 0}
								<input type="text" name="id_order" class="form-control" id="id_order" value="{$customerThread.id_order|intval}" readonly="readonly" />
							{/if}
						</div>	
					</div>
					{/if}
					{if isset($isLogged) && $isLogged}
					<div class="text select form-group">
					<label for="id_product" class="col-sm-3 control-label">{l s='Product'}</label>
						<div class="col-sm-9">
						{if !isset($customerThread.id_product)}
						{foreach from=$orderedProductList key=id_order item=products name=products}
							<select name="id_product" id="{$id_order}_order_products" class="product_select form-control" style="width:300px;{if !$smarty.foreach.products.first} display:none; {/if}" {if !$smarty.foreach.products.first}disabled="disabled" {/if}>
								<option value="0">{l s='-- Choose --'}</option>
								{foreach from=$products item=product}
									<option value="{$product.value|intval}">{$product.label|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
						{/foreach}
						{elseif $customerThread.id_product > 0}
							<input type="text" name="id_product" id="id_product" class="form-control" value="{$customerThread.id_product|intval}" readonly="readonly" />
						{/if}
						</div>
					</div>
					{/if}
				{/if}
				{if $fileupload == 1}
					<div class="text form-group">
						<label for="fileUpload" class="col-sm-3 control-label">{l s='Attach File'}</label>
						<div class="col-sm-9">
							<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
							<div class="form-control fileupload">
								<input type="file" name="fileUpload" id="fileUpload" />
							</div>
						</div>
					</div>	
				{/if}
				<div class="textarea form-group">
					<label for="message" class="col-sm-3 control-label">{l s='Message'}</label>
					<div class="col-sm-9">
						<textarea id="message" name="message" class="form-control" rows="2" cols="10">{if isset($message)}{$message|escape:'htmlall':'UTF-8'|stripslashes}{/if}</textarea>
					 </div>	
				</div>
				<div class="submit form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<input type="submit" name="submitMessage" id="submitMessage" value="{l s='Send'}" class="btn btn-default btn-mega" />
					</div>	
				</div>
			</div>		
		</div>	
		</div>
		
	</form>
	{/if}
	</div>
	</div>
</div>