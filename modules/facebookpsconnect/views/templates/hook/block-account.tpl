{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
{if !empty($bDisplay)}
	<div class="block {$sModuleName|escape:'htmlall':'UTF-8'}_block_user_account">
		<h4 class="title_block">{l s='Your account' mod='facebookpsconnect'}</h4>
		{* Customer is not logged *}
		{if empty($bCustomerLogged)}
			<div class="content">
				<form action="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{if $bVersion15}authentification{else}authentication.php{/if}" method="post">
					{if $bVersion15}<input type="hidden" class="hidden" name="back" value="my-account">{/if}
					<fieldset>
						<div class="form_content clearfix">
							<label for="email"><b>{l s='E-mail:' mod='facebookpsconnect'}</b></label>
							<br/>
							<input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="ao_input" />
							<div style="clear: both; height: 10px;"></div>
							<label for="passwd"><b>{l s='Password:' mod='facebookpsconnect'}</b></label><br />
							<input type="password" id="passwd" name="passwd" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd|escape:'htmlall':'UTF-8'|stripslashes}{/if}" class="ao_input" />
							{if isset($back)}
								<input type="hidden" class="hidden" name="back" value="{$back|escape:'UTF-8'}" />
							{/if}

							<div style="clear: both; height: 10px;"></div>

							<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="{l s='Log in' mod='facebookpsconnect'}" />
							{if !empty($bConnectorsActive) && !empty($aHookConnectors)}

							<div style="clear: both; height: 10px;"></div>

							<span>{l s='Or connect with' mod='facebookpsconnect'} : </span>{include file="`$sConnectorButtonsIncl`"}
							{/if}
							<div style="clear: both; height: 10px;"></div>
							<p class="lost_password">
								<a href="{$base_dir|escape:'UTF-8'}password.php">{l s='Forgot your password?' mod='facebookpsconnect'}</a>
							</p>
						</div>
					</fieldset>
				</form>
			</div>
		{* Customer is logged *}
		{else}
			<div class="logged">
				<div class="content">
					<br/>
					<p>{l s='Welcome' mod='facebookpsconnect'},<br/> <b>{$customerName|escape:'htmlall':'UTF-8'}</b> (<a href="{$base_dir}index.php?mylogout" title="{l s='Log out' mod='facebookpsconnect'}">{l s='Log out' mod='facebookpsconnect'}</a>)</p>

					<div>
						{if $bVersion16 == true}
							<span class="icon icon-user"/>
							<a href="{$sLinkAccount16|escape:'htmlall':'UTF-8'}" title="{l s='Your Account' mod='facebookpsconnect'}"><b>{l s='Your Account' mod='facebookpsconnect'}</b></a>
						{else}
							<img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/my-account.gif" alt="{l s='Your Account' mod='facebookpsconnect'}"/>
							<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}my-account.php" title="{l s='Your Account' mod='facebookpsconnect'}"><b>{l s='Your Account' mod='facebookpsconnect'}</b></a>
						{/if}
					</div>
					<br/>
					<div>
						{if $bVersion16 == true}
							<span class="icon icon-shopping-cart"/>
							<a href="{$link->getPageLink('order', true)}" title="{l s='Your Shopping Cart' mod='facebookpsconnect'}"><b>{l s='Cart:' mod='facebookpsconnect'}</b></a>
						{else}
							<img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/cart.gif" alt="{l s='Your Shopping Cart' mod='facebookpsconnect'}"/>
							<a href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}order.php" title="{l s='Your Shopping Cart' mod='facebookpsconnect'}"><b>{l s='Cart:' mod='facebookpsconnect'}</b></a>
						{/if}

						<span class="ajax_cart_quantity{if $iCartQty == 0} hidden{/if}">{$iCartQty}</span>
						<span class="ajax_cart_product_txt{if $iCartQty != 1} hidden{/if}">{l s='product' mod='facebookpsconnect'}</span>
						<span class="ajax_cart_product_txt_s{if $iCartQty < 2} hidden{/if}">{l s='products' mod='facebookpsconnect'}</span>
						<span class="ajax_cart_total{if $iCartQty == 0} hidden{/if}">
						{if $priceDisplay == 1}
							{convertPrice price=$oCart->getOrderTotal(false, 4)}
						{else}
							{convertPrice price=$oCart->getOrderTotal(true, 4)}
						{/if}
						</span>
						<span class="ajax_cart_no_product{if $iCartQty > 0} hidden{/if}">{l s='(empty)' mod='facebookpsconnect'}</span>
					</div>
					<br/>
				</div>
			</div>
		{/if}
	</div>
{/if}