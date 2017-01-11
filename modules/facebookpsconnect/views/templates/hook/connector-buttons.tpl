{*
* 2003-2015 Business Tech
*
* @author Business Tech SARL <http://www.businesstech.fr/en/contact-us>
* @copyright  2003-2015 Business Tech SARL
*}
{if !empty($aHookConnectors) && empty($bCustomerLogged)}<div {if !empty($sStyle)}class={if !empty($bJS)}\{/if}"{$sStyle|escape:'htmlall':'UTF-8'}{if !empty($bJS)}\{/if}"{/if}>{if !empty($sPosition) && ($sPosition == "blockUser" || $sPosition == "top" || $sPosition == "bottom" || $sPosition == "authentication" || $sPosition == "newaccount")}<div>{if $sPosition == "blockUser" || $sPosition == "authentication"}{l s='Or' mod='facebookpsconnect'} {l s='connect with' mod='facebookpsconnect'} :{else}{l s='Connect with' mod='facebookpsconnect'} :{/if}</div>{/if}{foreach from=$aHookConnectors name=connector key=cName item=cValue}{if !empty($aConnectors[$cName].data.activeConnector) && $aConnectors[$cName].data.activeConnector == 'on'}{if !empty($bJS)}{include file="`$aConnectors[$cName].tpl`" bJS=$bJS}{else}{include file="`$aConnectors[$cName].tpl`" bJS=false}{/if}{/if}{/foreach}</div><div style={if !empty($bJS)}\{/if}"clear: both;{if !empty($bJS)}\{/if}"></div>{/if}