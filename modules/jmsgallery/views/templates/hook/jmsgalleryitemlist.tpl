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
<div class="panel">
	<h3><i class="icon-list-ul"></i> 
		{l s='Image list' mod='jmsgallery'}
		<span class="panel-heading-action">
			<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&addImag=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
		</span>
	</h3>
	<script>
			$(document).ready(function(){									
				$('.toogle').click(function(e){
					$('#slidesContent').toggle(200);
				});
				$('.panel-heading').click(function(e){
					$(this).next('.form-wrapper').toggle(200);
				});
			});
				
	</script>
	<div id="slidesContent ">
		<div id="slides">
			<div id="items1" class="gallery_item">					
				<div class="panel" style="margin-bottom:4px;padding:4px;box-shadow:none;" >
					<div class="row">						
						<div class="col-md-2">
							<h4 class="pull-left">
								IMG
							</h4>
						</div>
						<div class="col-md-3">
							<h4 class="pull-left">
								Title
							</h4>							
						</div>
						<div class="col-md-3">
							<h4 class="pull-left">
								Category
							</h4>							
						</div>
						<div class="col-md-4">
							<h4 class="pull-action">
								Action
							</h4>							
						</div>
					</div>
				</div>
			</div>
			{foreach from=$gallerys item=gallery}
				<div id="slides_1" class="panel slideContent_">
					<div class="row">
						<div class="col-md-2">
							{if $gallery.image && $active}
								<img src="{$image_url|escape:'html'}resized_{$gallery.image|escape:'html'}" class="img-responsive" alt="{$gallery.title|escape:'html'}" class="img-thumbnail" />
							{elseif $gallery.image}
								<img src="{$image_url|escape:'html'}{$gallery.image|escape:'html'}" class="img-responsive" alt="{$gallery.title|escape:'html'}" class="img-thumbnail" />
							{/if}
						</div>
						<div class="col-md-3">
						<a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&id_item={$gallery.id_item|escape:'html'}">
							<h4 class="pull-left"> #{$gallery.id_item}- {$gallery.title|escape:'html'}</h4>		
						</a>
						</div>
						<div class="col-md-3">
							<h4 class="pull-left"> <a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&view=items&list_id_category={$gallery.id_category|escape:'html'}" />#{$gallery.name_category|escape:'html'}</a></h4>		
						</div>
						<div class="col-md-4">
							<div class="btn-group-action pull-right">
								{$gallery.status|escape:''}
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&id_item={$gallery.id_item|escape:'html'}"
									<i class="icon-edit"></i>
									{l s='Edit' mod='jmsgallery'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsgallery&delete_id_item={$gallery.id_item|escape:'html'}">
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