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
<div id="social_block">
	<ul>
		{if $facebook_url != ''}<li class="facebook"><a class="_blank" href="{$facebook_url|escape:html:'UTF-8'}">
		<i class="fa fa-facebook"></i></a></li>{/if}
		{if $twitter_url != ''}<li class="twitter"><a class="_blank" href="{$twitter_url|escape:html:'UTF-8'}"><i class="fa fa-twitter"></i></a></li>{/if}
		{if $rss_url != ''}<li class="rss"><a class="_blank" href="{$rss_url|escape:html:'UTF-8'}"><i class="fa fa-rss"></i></a></li>{/if}
		{if $youtube_url != ''}<li class="youtube"><a class="_blank" href="{$youtube_url|escape:html:'UTF-8'}"><i class="fa fa-youtube-play"></i></a></li>{/if}
		{if $google_plus_url != ''}<li class="google_plus"><a class="_blank" href="{$google_plus_url|escape:html:'UTF-8'}"><i class="fa fa-google-plus"></i></a></li>{/if}
		{if $pinterest_url != ''}<li class="pinterest"><a class="_blank" href="{$pinterest_url|escape:html:'UTF-8'}"><i class="fa fa-pinterest"></i></a></li>{/if}
		{if $vimeo_url != ''}<li class="vimeo"><a href="{$vimeo_url|escape:html:'UTF-8'}"><i class="fa fa-vimeo-square"></i></a></li>{/if}
		{if $instagram_url != ''}<li class="instagram"><a class="_blank" href="{$instagram_url|escape:html:'UTF-8'}"><i class="fa fa-instagram"></i></a></li>{/if}
	</ul>
</div>