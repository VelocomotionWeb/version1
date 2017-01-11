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
<div id="setting-overlay"></div>
<div id="jmstools" class="jmsclose hidden-xs hidden-sm">
	<a id="jmstools-arrow" class="pull-right"><i class="lnr lnr-cog"></i></a>
	<div id="jmstools-content" class="pull-left">
		<form action="index.php" method="POST">
			{if $themeskins|@count > 0}
			<div class="form-group">
				<label class="form-label">Theme Skin</label>
					<a class="skin-box {if $skin=='default' || $skin==''}active{/if}" title="Default">
					<img src="{$content_dir}themes/{$themename}/skin-icons/default.png" alt="Default" />
					</a>
				{foreach from=$themeskins item=sk}
					<a class="skin-box {if $skin=={$sk}}active{/if}" title="{$sk}" data-color="{$sk}">
					<img src="{$content_dir}themes/{$themename}/skin-icons/{$sk}.png" alt="{$sk}" />
					</a>					
				{/foreach}
			</div>
			{/if}	
			{if isset($homepages) && $homepages|@count > 1}
			<div class="form-group">
					<label>Home Page</label>
					<select name="jmshomepage" id="jmshomepage">
					{foreach from=$homepages item=hp}
						<option value="{$hp.id_homepage}" {if $homepage=={$hp.id_homepage}}selected="selected"{/if}>{$hp.title}</option>					
					{/foreach}	
					</select>
			</div>
			{/if}			
			{if isset($producthovers)}
			<div class="form-group">
					<label>Product Box Hover</label>
					<select name="jmsphover" id="jmsphover">
					{foreach from=$producthovers item=ph key=phkey}
						<option value="{$phkey}" {if $phover=={$phkey}}selected="selected"{/if}>{$ph}</option>					
					{/foreach}	
					</select>
			</div>
			{/if}
			{if isset($productboxs)}
				<div class="form-group">
						<label>Product Box</label>
						<select name="jmspbox" id="jmspbox">
						{foreach from=$productboxs item=pb key=pbkey}
							<option value="{$pbkey}" {if $pbox=={$pbkey}}selected="selected"{/if}>{$pb}</option>					
						{/foreach}	
						</select>
				</div>
			{/if}		
			<div class="form-group">		
				<label>Direction</label>	
				<select name="jmsrtl" id="jmsrtl">
					<option value="0" {if $rtl=='0'}selected="selected"{/if}>LTR</option>										
					<option value="1" {if $rtl=='1'}selected="selected"{/if}>RTL</option>										
				</select>				
			</div>
			<input id="jmsskin" type="hidden" name="jmsskin" value="{$skin}" />
			
			<div class="form-group btn-action">
				<button type="submit" class="btn" name="apply" value="1">Apply</button>
				<a class="btn" href="index.php?settingreset=1">Reset</a>
			</div>
			<input type="hidden" name="settingdemo" value="1" />
		</form>	
	</div>
</div>