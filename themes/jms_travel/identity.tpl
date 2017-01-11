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

{capture name=path}<a href="{$link->getPageLink('my-account', true)}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Your personal information'}{/capture}

<h1 class="page-heading">{l s='Your personal information'}</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation}
	<p class="success">
		{l s='Your personal information has been successfully updated.'}
		{if isset($pwd_changed)}<br />{l s='Your password has been sent to your email:'} {$email}{/if}
	</p>
{else}
	<div id="identityarea" class="block">
	<p class="required"><sup>*</sup>{l s='Required field'}</p>
	<form action="{$link->getPageLink('identity', true)}" method="post" class="std form-horizontal">
		<div class="panel panel-default">
			<h3 class="panel-heading">{l s='Please be sure to update your personal information if it has changed.'}</h3>
			<div class="panel-body clearfix">
				<div class="radio form-group">
					<label class="col-sm-4 control-label">{l s='Title'}</label>
					<div class="col-sm-6">
						{foreach from=$genders key=k item=gender}							
							<label for="id_gender{$gender->id}" class="top radio-inline"><input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />{$gender->name}</label>
						{/foreach}
					</div>	
				</div>
				<div class="required text form-group">
					<label for="firstname" class="col-sm-4 control-label">{l s='First name'} <sup>*</sup></label>
					<div class="col-sm-6">
						<input type="text" id="firstname" class="form-control" name="firstname" value="{$smarty.post.firstname}" />
					</div>	
				</div>
				<div class="required text form-group">
					<label for="lastname" class="col-sm-4 control-label">{l s='Last name'} <sup>*</sup></label>
					<div class="col-sm-6">
						<input type="text" name="lastname" class="form-control" id="lastname" value="{$smarty.post.lastname}" />
					</div>	
				</div>
				<div class="required text form-group">
					<label for="email" class="col-sm-4 control-label">{l s='Email'} <sup>*</sup></label>
					<div class="col-sm-6">
						<input type="text" name="email" id="email" class="form-control" value="{$smarty.post.email}" />
					</div>	
				</div>
				<div class="required text form-group">
					<label for="old_passwd" class="col-sm-4 control-label">{l s='Current Password'} <sup>*</sup></label>
					<div class="col-sm-6">
						<input type="password" name="old_passwd" class="form-control" id="old_passwd" />
					</div>	
				</div>
				<div class="password form-group">
					<label for="passwd" class="col-sm-4 control-label">{l s='New Password'}</label>
					<div class="col-sm-6">
						<input type="password" name="passwd" class="form-control" id="passwd" />
					</div>	
				</div>
				<div class="password form-group">
					<label for="confirmation" class="col-sm-4 control-label">{l s='Confirmation'}</label>
					<div class="col-sm-6">
						<input type="password" name="confirmation" class="form-control" id="confirmation" />
					</div>	
				</div>
				<div class="select form-group">
					<label class="col-sm-4 control-label">{l s='Date of Birth'}</label>
					<div class="col-sm-6">
						<div class="row">
							<div class="col-sm-4 col-xs-4">
								<select name="days" id="days" class="form-control">
									<option value="">-</option>
									{foreach from=$days item=v}
										<option value="{$v}" {if ($sl_day == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
									{/foreach}
								</select>
								{*
									{l s='January'}
									{l s='February'}
									{l s='March'}
									{l s='April'}
									{l s='May'}
									{l s='June'}
									{l s='July'}
									{l s='August'}
									{l s='September'}
									{l s='October'}
									{l s='November'}
									{l s='December'}
								*}
							</div>
							<div class="col-sm-4 col-xs-4">	
								<select id="months" name="months" class="form-control">
									<option value="">-</option>
									{foreach from=$months key=k item=v}
										<option value="{$k}" {if ($sl_month == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
									{/foreach}
								</select>
							</div>
							<div class="col-sm-4 col-xs-4">		
								<select id="years" name="years" class="form-control">
									<option value="">-</option>
									{foreach from=$years item=v}
										<option value="{$v}" {if ($sl_year == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
									{/foreach}
								</select>
							</div>
						</div>		
					</div>	
				</div>
				{if $newsletter}
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<div class="checkbox">							
							<label for="newsletter"><input type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if} />{l s='Sign up for our newsletter!'}</label>
						</div>	
					</div>		
				</div>
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<div class="checkbox">		
							<label for="optin"><input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if} />{l s='Receive special offers from our partners!'}</label>
						</div>	
					</div>	
				</div>
				{/if}
				<div class="submit form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<input type="submit" class="btn btn-default" name="submitIdentity" value="{l s='Save'}" />
					</div>	
				</div>
				<div id="security_informations" class="pull-right">
					{l s='[Insert customer data privacy clause here, if applicable]'}
				</div>
			</div>	
		</div>
	</form>
	</div>
{/if}

<ul class="footer_links clearfix">
	<li><a class="btn btn-default" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='Back to your account'}</span></a></li>
	<li><a class="btn btn-default" href="{$base_dir}">{l s='Home'}</a></li>
</ul>
