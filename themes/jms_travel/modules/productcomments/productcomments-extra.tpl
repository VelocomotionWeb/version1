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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
$(function(){
	$('a[href=#idTab5]').click(function(){
		$('*[id^="idTab"]').addClass('block_hidden_only_for_screen');
		$('div#idTab5').removeClass('block_hidden_only_for_screen');

		$('ul#more_info_tabs a[href^="#idTab"]').removeClass('selected');
		$('a[href="#idTab5"]').addClass('selected');
	});
});
</script>
 {if ((($nbComments == 0 && $too_early == false && ($logged || $allow_guests)) || ($nbComments != 0)))}
<p class="rating">	
		{section name="i" start=0 loop=5 step=1}
			{if $averageTotal le $smarty.section.i.index}
				<i class="icon star-empty"></i>
			{else}
				<i class="icon star-full"></i>
			{/if}
		{/section}	
	<span class="nbcomments">{l s='%s reviews'|sprintf:$nbComments mod='productcomments'}&nbsp;</span>	
	{if (!$too_early AND ($logged OR $allow_guests))}	
	<a id="new_comment_tab_btn" class="open-comment-form" href="#new_comment_form">{l s='Add a review' mod='productcomments'}</a>	
	{/if}
</p>	
{/if}
<!--  /Module ProductComments -->
