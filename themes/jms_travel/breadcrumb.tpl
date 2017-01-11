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
{if isset($category)}
	{if $category->id AND $category->active}	
		{if $scenes || $category->description || $category->id_image}
		<div class="content_scene_cat block" {if $category->id_image}style="background-image:url({$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')});background-repeat:no-repeat;background-size:cover;"{/if}>
			<div class="cat-info">
				<div class="cat-info-inner">
					<div class="container">
						<div class="meta_title">
							{$category->name}
							<span class="meta_description">{$category->meta_description}</span>
						</div>	
						{if $category->description}
						   <div class="cat_desc rte">
							   {if Tools::strlen($category->description) > 350}
								 <div id="category_description_short">{$description_short}</div>
								 <div id="category_description_full" class="unvisible">{$category->description}</div>
								 <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a>
						{else}
							<div>{$category->description}</div>
						{/if}
						   </div>
						{/if}
					</div>
				</div>	
            </div>            			
		</div>
		{/if}
	{/if}
{else if}
	<div class="content_scene_img">
		<img src="{$content_dir}themes/jms_travel/img/ct.jpg" alt="" class="img-responsive" />
	</div>
{/if}

<!-- Breadcrumb -->
{if isset($smarty.capture.path)}{assign var='path' value=$smarty.capture.path}{/if}
<nav class="breadcrumbs">
	<div class="breadcrumbs-content">
		<div class="container">
			<a href="{$base_dir}" title="{l s='Return to Home'}"><span class="fa fa-home"></span> {l s='Home'}</a>
			{if isset($path) AND $path}
				<span {if isset($category) && isset($category->id_category) && $category->id_category == 1}style="display:none;"{/if}> - </span>
				{if !$path|strpos:'span'}
					{$path}
				{else}
					{$path}
				{/if}
			{/if}
		</div>
	</div>
</nav>

<!-- /Breadcrumb -->