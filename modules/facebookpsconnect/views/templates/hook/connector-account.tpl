{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
<link rel="stylesheet" type="text/css" href="{$smarty.const._FPC_URL_CSS|escape:'htmlall':'UTF-8'}bootstrap.min.css">
{if !empty($bCustomerCollect)}
{literal}
	<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
	$(document).ready(function(){
		$('#header').after('<div style="clear: both; height:0"></div><div class="alert valide form-error" style="display: block;">'+"{/literal}{l s='Thank you. Your Facebook Like has been recorded and correctly linked to your account. This will allow us to potentially suggest this product to your friends as a gift idea for you' mod='facebookpsconnect'}{literal}"+'</div><div style="clear: both; height:20px"></div>');
	});
	</script>
{/literal}
{/if}

{if !empty($bDisplay)}
	{literal}
	<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
		$(document).ready(function(){
			var content = $('#ao_fpsc_fancybox').html();
			$('#ao_fpsc_fancybox').remove();

			$.fancybox({
				'content': content
			});

			// init form div error
			$('.alert-email-error').hide();
			$('.alert-firstname-error').hide();
			$('.alert-name-error').hide();
			$('.alert-password-verify-error').hide();
			$('.alert-password-error').hide();

			//init var
			var delay = 1500;
			var fadeout = 300;

			function validateEmail(email)
			{
				var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
				if(reg.test(email))
				  {
					return(true);
				  }
				else
				  {
					return(false);
				  }
			}
			$(".CheckEmail").focusout(function() {
				if (validateEmail($(".CheckEmail").val()) == true && validateEmail($(".CheckEmail").val()) != '')
				{
					$(".CheckEmail").css({border:"1px solid #46a74e", color:"green", 'background-color': "#ddf9e1"});
					$('#btn-save').attr("disabled", false);
				}
				else
				{
					$(".CheckEmail").css({border:"1px solid #f13340", color:"red",'background-color': "#fff1f2"});
					$('#btn-save').attr("disabled", true);
					$('.alert-email-error').show();
					$(".alert-email-error").delay(delay).fadeOut(fadeout);
				}
			});

			$("#socialFirstName").focusout(function() {
				if ($("#socialFirstName").val() != "")
				{
					$("#socialFirstName").css({border:"1px solid #46a74e", color:"green", 'background-color': "#ddf9e1"});
					$('#btn-save').attr("disabled", false);
				}
				else
				{
					$("#socialFirstName").css({border:"1px solid #f13340", color:"red",'background-color': "#fff1f2"});
					$('#btn-save').attr("disabled", true);
					$('.alert-firstname-error').show();
					$(".alert-firstname-error").delay(delay).fadeOut(fadeout);
				}
			});

			$("#socialName").focusout(function() {
				if ($("#socialName").val() != "")
				{
					$("#socialName").css({border:"1px solid #46a74e", color:"green", 'background-color': "#ddf9e1"});
					$('#btn-save').attr("disabled", false);
				}
				else
				{
					$("#socialName").css({border:"1px solid #f13340", color:"red",'background-color': "#fff1f2"});
					$('#btn-save').attr("disabled", true);
					$('.alert-name-error').show();
					$(".alert-name-error").delay(delay).fadeOut(fadeout);
				}
			});

			$("#socialPassword").focusout(function() {
				if ($("#socialPassword").val() != "" )
				{
					$("#socialPassword").css({border:"1px solid #46a74e", color:"green", 'background-color': "#ddf9e1"});
					$('#btn-save').attr("disabled", false);
				}
				else
				{
					$("#socialPassword").css({border:"1px solid #f13340", color:"red",'background-color': "#fff1f2"});
					$('#btn-save').attr("disabled", true);
					$('.alert-password-error').show();
					$(".alert-password-error").delay(delay).fadeOut(fadeout);
				}
			});

			$("#socialPasswordVerify").focusout(function() {
				if ($("#socialPasswordVerify").val() != "" && $("#socialPasswordVerify").val() == $("#socialPassword").val())
				{
					$("#socialPasswordVerify").css({border:"1px solid #46a74e", color:"green", 'background-color': "#ddf9e1"});
					$('#btn-save').attr("disabled", false);
				}
				else
				{
					$("#socialPasswordVerify").css({border:"1px solid #f13340", color:"red",'background-color': "#fff1f2"});
					$('.alert-password-verify-error').show();
					$('#btn-save').attr("disabled", true);
					$(".alert-password-verify-error").delay(delay).fadeOut(fadeout);
				}
			});

			// check if one field is wrong
			if ((validateEmail($(".CheckEmail").val()) == false && validateEmail($(".CheckEmail").val()) == "") || ($("#socialName").val() == "") || ($("#socialFirstName").val() != "")|| ($("#socialPassword").val() == "" )|| ($("#socialPasswordVerify").val() == "" && $("#socialPasswordVerify").val() != $("#socialPassword").val()))
			{
				$('#btn-save').attr("disabled", true);
			}
			else
			{
				$('#btn-save').attr("disabled", false);
			}
		});
	</script>
	{/literal}
	<div id="ao_fpsc_fancybox" style="display:none; visibility:hidden;">
		<div id="socialMessage">
			{if !empty($bSocialCustomerExist)}
			<div id="fpcFancyboxContent">
				<h3 style="margin:0; padding:0;">{l s='Link your Prestashop account to your Facebook profile' mod='facebookpsconnect'}</h3>
				<div class="ao_fpsc_clr_hr"></div>
				<div class="ao_fpsc_clr_20"></div>
				<p id="connectorText">{l s='Link your PrestaShop account to your Facebook profile NOW, using the Facebook Connect button below !' mod='facebookpsconnect'}</p>
			</div>

			<div class="ao_fpsc_clr_20"></div>

			{include file="`$sConnectorButtonFacebook`" iCustomerId=$iCustomerId}

			<div class="ao_fpsc_clr_20"></div>

			<p align="right"><a onclick="{$sModuleName|escape:'htmlall':'UTF-8'}.ajax('{$sModuleURI|escape:'htmlall':'UTF-8'}','sAction=update&sType=customer&id={$iCustomerId}','ajax_customer_response');$.fancybox.close();" href="#">{l s='Do not show this message again' mod='facebookpsconnect'}</a></p>

			<div id="ajax_customer_response"></div>

			{elseif !empty($bTwitterCustomerExist)}
			<h3 style="margin:0; padding:0;">{l s='You have linked your Prestashop account to your Twitter profile' mod='facebookpsconnect'}</h3>
			<div class="ao_fpsc_clr_hr"></div>
			<div class="ao_fpsc_clr_20"></div>
			<form action="" class="form-horizontal" method="post" id="{$sModuleName|escape:'htmlall':'UTF-8'}TwitterForm" name=''>
				<input type="hidden" name="sAction" value="update" />
				<input type="hidden" name="sType" value="email" />
				<input type="hidden" name="connector" value="{$sConnector|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="customerId" value="{$iCustomerId}" />
				<div class="bootstrap">
					<p class="alert alert-info">
						{l s='Because Twitter does not give us your all your information, your account was created with a false generic e-mail and generic password. Please update your information now by filling it out below' mod='facebookpsconnect'}.
						<span class="ao_fpsc_clr_10"></span>
					</p>
					<div class="form-group">
						<label class="control-label col-lg-4">{l s='Your e-mail' mod='facebookpsconnect'}: </label>
						<div class="col-xs-3">
							<input name="socialEmail" id="socialEmail" type="text" class="CheckEmail" value="" size="35"/>
							<div class="ao_fpsc_clr_20"></div>
							<div class="alert-email-error">
								<p class="alert alert-danger">
									{l s='Please insert a correct Email' mod='facebookpsconnect'}
								</p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-4">{l s='Your firstname' mod='facebookpsconnect'}: </label>
						<div class="col-xs-3">
							<input name="socialFirstName" id="socialFirstName" type="text" value="" size="35" />
							<div class="ao_fpsc_clr_20"></div>
							<div class="alert-firstname-error">
								<p class="alert alert-danger">
									{l s='Please insert your firstname' mod='facebookpsconnect'}
								</p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-4">{l s='Your name' mod='facebookpsconnect'}: </label>
						<div class="col-xs-3">
							<input name="socialName" id="socialName" type="text" value="" size="35" />
							<div class="ao_fpsc_clr_20"></div>
							<div class="alert-name-error">
								<p class="alert alert-danger">
									{l s='Please insert your name' mod='facebookpsconnect'}
								</p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-4">{l s='Your password' mod='facebookpsconnect'}: </label>
						<div class="col-xs-3">
							<input name="socialPassword" id="socialPassword" type="password" value="" size="35" />
							<div class="ao_fpsc_clr_20"></div>
							<div class="alert-password-error">
								<p class="alert alert-danger">
									{l s='Please set your password' mod='facebookpsconnect'}
								</p>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-lg-4">{l s='Check your password' mod='facebookpsconnect'}: </label>
						<div class="col-xs-3">
							<input name="socialPasswordVerify" id="socialPasswordVerify" type="password" value="" size="35" />
							<div class="ao_fpsc_clr_20"></div>
							<div class="alert-password-verify-error">
								<p class="alert alert-danger">
									{l s='Please set your password again' mod='facebookpsconnect'}
								</p>
							</div>
						</div>
					</div>
					<center>
						<button id="btn-save" name="{$sModuleName|escape:'htmlall':'UTF-8'}Button" class="button btn btn-success" value="{l s='Send' mod='facebookpsconnect'}"  onclick="{$sModuleName|escape:'htmlall':'UTF-8'}.form('{$sModuleName|escape:'htmlall':'UTF-8'}TwitterForm', '{$sModuleURI|escape:'htmlall':'UTF-8'}', '', 'socialMessage', 'socialMessage', false, false, null, 'Email');return false;" >{l s='Send' mod='facebookpsconnect'}</button>
					</center>
				</div>
			</form>
			<div id="{$sModuleName|escape:'htmlall':'UTF-8'}EmailError"></div>
		</div>
		{/if}
	</div>
{/if}
