{**
* Facebook Messenger - Live chat
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2016 idnovate.com
*  @license   See above
*}

<style>
    .fb-messengermessageus {
        position: fixed;
        bottom: 0;
        z-index: 9999;
        {if $position == 1}
        left: 0;
        {else}
        right: 0;
        {/if}
        margin: 10px;
    }
</style>

{literal}
<script>
    window.fbAsyncInit = function() {
        FB.init({
          appId      : '95100348886',
          xfbml      : true,
          version    : 'v2.6'
        });
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/{/literal}{$locale|escape:'htmlall':'UTF-8'}{literal}/sdk.js";
        //strtolower($iso_lang).'_'.strtoupper($iso_lang)
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
{/literal}

<div class="fb-messengermessageus" messenger_app_id="95100348886" page_id="{$page_id|escape:'htmlall':'UTF-8'}" color="blue" size="large"></div>
