{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block languages module -->
{if count($languages) > 1}
<div class="btn-group compact-hidden languages-info">
	<a href="#" id="languages-box-group"  class="languages-box box-group">			
		{foreach from=$languages key=k item=language name="languages"}
			{if $language.iso_code == $lang_iso}	
				<span class="text-top">{l s='Languages' mod='blocklanguages'} :</span>
				<span class="selected_language text_select">
					{$language.name|regex_replace:"/\s\(.*\)$/":""|truncate:"3":""}
				</span>	
				<span class="fa fa-angle-down icon-down"></span>
			{/if}
		{/foreach}
	</a>
	<ul id="languages-dropdown-box" class="dropdown-box">
		{foreach from=$languages key=k item=language name="languages"}
			<li {if $language.iso_code == $lang_iso}class="selected"{/if}>
			{if $language.iso_code != $lang_iso}
				{assign var=indice_lang value=$language.id_lang}
				{if isset($lang_rewrite_urls.$indice_lang)}
					<a href="{$lang_rewrite_urls.$indice_lang|escape:'html':'UTF-8'}" title="{$language.name}">
						<span class="image_language">
							<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code|escape:'html':'UTF-8'}" width="18" height="11" />
						</span>
						<span class="text_name">{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
					</a>
				{else}
					<a href="{$link->getLanguageLink($language.id_lang)|escape:'html':'UTF-8'}" title="{$language.name}">
						<span class="image_language">
							<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code|escape:'html':'UTF-8'}" width="18" height="11" />
						</span>
						<span class="text_name">{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
					</a>
				{/if}
			{/if}	
			{if $language.iso_code == $lang_iso}
				<a href="#" title="{$language.name}">
					<span class="image_language">
						<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code|escape:'html':'UTF-8'}" width="18" height="11" />
					</span>
					<span class="text_name">{$language.name|regex_replace:"/\s\(.*\)$/":""}</span>
				</a>
			{/if}
			</li>
		{/foreach}		
	</ul>
</div>
{/if}
<!-- /Block languages module -->
