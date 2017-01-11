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
<!-- Block Newsletter module-->
<div id="newsletter_block">
	<p>{l s='Get all the latest information on Events and offers that we provide' mod='blocknewsletter'}</p>
	<div class="newsletter-block">
		<div class="newsletter-box">
			<div class="wraper-newsletter-content">
				<div class="wraper-newsletter">
					<form action="{$link->getPageLink('index')|escape:'html':'UTF-8'}" method="post" class="form-inline">
						<div class="input-group {if isset($msg) && $msg } {if $nw_error}form-error{else}form-ok{/if}{/if}" >
							<div class="newsletter-input-wrap">					
								<div class="newsletter-input">
									<input class="form-control" id="newsletter-input" type="text" name="email" size="18" placeholder="{if isset($msg) && $msg}{$msg}{elseif isset($value) && $value}{$value}{else}{l s='Your email...' mod='blocknewsletter'}{/if}" />
								</div>
							</div>	
                            <button type="submit" name="submitNewsletter" class="button input-group-addon">
                                {l s='Subscribe' mod='blocknewsletter'}
                            </button>	               
						</div>
						<input type="hidden" name="action" value="0" />				
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Block Newsletter module-->
<script type="text/javascript">
    var placeholder = "{l s='Your email address' mod='blocknewsletter' js=1}";
    {literal}
        $(document).ready(function() {
            $('#newsletter-input').on({
                focus: function() {
                    if ($(this).val() == placeholder) {
                        $(this).val('');
                    }
                },
                blur: function() {
                    if ($(this).val() == '') {
                        $(this).val(placeholder);
                    }
                }
            });
        });
    {/literal}
</script>