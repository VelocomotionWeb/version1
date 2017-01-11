{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Category list' mod='jmsgallery'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&addCat=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="itemsContent">
		<div id="items1">
			{foreach from=$items item=image}
				<div class="panel lvl{$image.lvl}" style="margin-bottom:4px;padding:4px 4px 4px {$image.lvl*25+5}px;box-shadow:none;{if  $image.lvl > 1}background: url(../modules/jmsgallery/img/decalage.png) no-repeat {($image.lvl-1)*25+5}px center;{/if}">
					<div class="row">	
						<div class="col-md-2">
						{if $image.image && $active}
							<img src="{$image_baseurl|escape:'html'}resized_{$image.image|escape:'html'}" class="img-responsive" alt="{$image.name_category|escape:'html'}" />
						{elseif $image.image}
							<img src="{$image_baseurl|escape:'html'}{$image.image|escape:'html'}" class="img-responsive" alt="{$image.name_category|escape:'html'}" />
						{/if}
						</div>
						<div class="col-md-10" style="padding:0px;">
							<h4 class="pull-left">
							<a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&id_category={$image.id_category|escape:'html'}">
							#{$image.id_category|escape:'html'} - {$image.name_category|escape:'html'}
							</a>
							(<a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&view=items&list_id_category={$image.id_category|escape:'html'}">
							{$image.item_count|escape:'html'} items
							</a>)
							</h4>							
							<div class="btn-group-action pull-right" style="padding:12px 5px 5px;">
								{$image.status|escape:''}								
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&id_category={$image.id_category|escape:'html'}">
									<i class="icon-edit"></i>
									{l s='Edit' mod='jmsgallery'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&delete_id_cat={$image.id_category|escape:'html'}">
									<i class="icon-trash"></i>
									{l s='Delete' mod='jmsgallery'}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>