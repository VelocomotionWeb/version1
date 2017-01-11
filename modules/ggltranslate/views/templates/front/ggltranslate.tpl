{*

* 2013-2016 hb50.fr

*

* NOTICE OF LICENSE

*

* This source file is subject to a commercial license from HB50

* Use, copy, modification or distribution of this source file without written

* license agreement from the HB50 is strictly forbidden.

* In order to obtain a license, please contact us: admin@hb50.fr

*

*  @author     hb50 <admin@hb50.fr>

*  @copyright  2013-2015 hb50

*  @license    Commercial license

*  @link http://hb50.fr

*}



<!-- MODULE Ajax google translate -->

<div id="google_translate_element" style="position: fixed; z-index: 99999; bottom: 10px; left:5px ;border:1px solid #646464;"></div><script type="text/javascript">{literal}

function googleTranslateElementInit() {

  new google.translate.TranslateElement({pageLanguage: '{/literal}{$lang_iso|escape:'htmlall':'UTF-8'}{literal}', {/literal}{if $PS_GGL_TRANSLATION_COUNTRIES neq 'All'}{literal}includedLanguages: '{/literal}{$PS_GGL_TRANSLATION_COUNTRIES|escape:'htmlall':'UTF-8'}{literal}', {/literal}{/if}{literal}layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');

}

{/literal}

</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>



{if $PS_GGL_TRANSLATION_AUTODETECT eq 1 && $LangBrowser neq $lang_iso}

<script type="text/javascript">

{literal}

function readCookie(name) {

    var c = document.cookie.split('; '),

    cookies = {}, i, C;



    for (i = c.length - 1; i >= 0; i--) {

        C = c[i].split('=');

        cookies[C[0]] = C[1];

     }



     return cookies[name];

}

var cookLang = readCookie('googtrans');

if (cookLang == null) {

    document.location.href=document.location.href + '#googtrans({/literal}{$lang_iso|escape:'htmlall':'UTF-8'}{literal}|{/literal}{$LangBrowser|escape:'htmlall':'UTF-8'}{literal})';

}

{/literal}

</script>

{/if}

<!-- /MODULE Ajax google translate -->