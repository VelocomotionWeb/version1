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
<script type="text/javascript">
jQuery(function ($) {
    "use strict";
    $(".expander-list").find("ul").hide().end().find(" .expander").text("+").end().find(".active").each(function () {
        $(this).parents("li ").each(function () {
            var $this = $(this),
                $ul = $this.find("> ul"),
                $name = $this.find("> .name a"),
                $expander = $this.find("> .name .expander");
            $ul.show();
            $name.css("font-weight", "bold");
            $expander.html("&minus;")
        })
    }).end().find(" .expander").each(function () {
        var $this = $(this),
            hide = $this.text() === "+",
            $ul = $this.parent(".name").next("ul"),
            $name = $this.next("a");
        $this.click(function () {
            if ($ul.css("display") ==
                "block") $ul.slideUp("slow");
            else $ul.slideDown("slow");
            $(this).html(hide ? "&minus;" : "+");
            $name.css("font-weight", hide ? "700" : "normal");
            hide = !hide
        })
    })
});
</script>
{if $blockCategTree && $blockCategTree.children|@count}
<!-- Block categories module -->
<div id="categories_block_left" class="block blue">
	<h3 class="title_block">
		<span>{l s='Categories' mod='blockcategories'}</span>
	</h3>
	<div class="block_content">
		<ul class="expander-list">
		{foreach from=$blockCategTree.children item=child name=blockCategTree}
			{if $smarty.foreach.blockCategTree.last}
				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child}
			{/if}
		{/foreach}
		</ul>
		{* Javascript moved here to fix bug #PSCFI-151 *}
		<script type="text/javascript">
		// <![CDATA[
			// we hide the tree only if JavaScript is activated
			$('div#categories_block_left ul.dhtml').hide();
		// ]]>
		</script>
	</div>
</div>
<!-- /Block categories module -->
{/if}
<div class="section-divider"></div>
