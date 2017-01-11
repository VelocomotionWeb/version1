{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA

*}
<script type="text/javascript">
jQuery(function ($) {
    "use strict";
	var productCarousel = $(".blog-carousel"),
	container = $(".container");	
	if (productCarousel.length > 0) productCarousel.each(function () {
	var items = {$widget_setting.JBW_ITEM_SHOW},		
	    itemsDesktop = {$widget_setting.JBW_ITEM_SHOW},
	    itemsDesktopSmall = 1,
	    itemsTablet = 1,
	    itemsMobile = 1;	
	$(this).owlCarousel({
	    responsiveClass:true,
		responsive:{
			1550:{
				items:items
			},
			1199:{
				items:itemsDesktop
			},
			991:{
				items:itemsDesktopSmall
			},
			768:{
				items:itemsTablet
			},
			318:{
				items:itemsMobile
			}
		},
		rtl: false,
	    nav: false,
		dots: false,
	    rewindNav: {if $widget_setting.JBW_REWIND == '1'}true{else}false{/if},
	    navigationText: ["", ""],
	    scrollPerPage: {if $widget_setting.JBW_SCROLLPERPAGE == '1'}true{else}false{/if},
	    slideSpeed: 500,
	    beforeInit: function rtlSwapItems(el) {
	        if ($("body").hasClass("rtl")) el.children().each(function (i, e) {
	            $(e).parent().prepend($(e))
	        })
	    },
	    afterInit: function afterInit(el) {
	        if ($("body").hasClass("rtl")) this.jumpTo(1000)
	    }
	})
	});
});
</script>
<div class="slider-product-title">
	<h3 class="title">
		<span>{l s='Latest blog' mod='jmsblogwidget'}</span>
		<span class="icon-title"></span>
	</h3>
</div>
<div class="jmsblog-home-widget">
	{if $posts|@count gt 0}
		{foreach from=$posts item=post}
			{assign var=params value=['post_id' => $post.post_id, 'category_slug' => $post.category_alias, 'slug' => $post.alias]}	
			{assign var=catparams value=['category_id' => $post.category_id, 'slug' => $post.category_alias]}	
			<div class="blog-item">
				<div class="row">
				{if $post.link_video && $widget_setting.JBW_SHOW_MEDIA}
					<div class="post-thumb col-md-6">
						{$post.link_video}
					</div>
					{elseif $post.image && $widget_setting.JBW_SHOW_MEDIA}
					<div class="post-thumb col-md-6">
						<a href="{jmsblog::getPageLink('jmsblog-post', $params)}">
							<img src="{$image_baseurl|escape:'html':'UTF-8'}thumb_{$post.image|escape:'html':'UTF-8'}" alt="{$post.title|escape:'htmlall':'UTF-8'}" class="img-responsive" />
						</a>				 		
					</div>
					{/if}	
					<div class="blog-info col-md-6">
						<h4 class="post-title"><a href="{jmsblog::getPageLink('jmsblog-post', $params)}">{$post.title|escape:'htmlall':'UTF-8'}</a></h4>
						{if $widget_setting.JBW_SHOW_CATEGORY}
						<div class="post-category">
							<a href="{jmsblog::getPageLink('jmsblog-category', $catparams)}">
								<span>{l s='Post in' mod='jmsblogwidget'} : </span>
								{$post.category_name|escape:'html':'UTF-8'}
							</a>
						</div>
						{/if}
						<ul class="post-meta">
							{if $widget_setting.JBW_SHOW_COMMENT}
							<li class="post-comments"><span class="fa fa-comments-o"></span> {$post.comment_count|escape:'html':'UTF-8'} <span>{l s='comments' mod='jmsblogwidget'}</span> </li>
							{/if}
							{if $widget_setting.JBW_SHOW_VIEWS}
							<li class="post-views"><span class="fa fa-eye"></span> {$post.views|escape:'html':'UTF-8'} <span> {l s='Views' mod='jmsblogwidget'}</span> </li>
							{/if}				
							{if $widget_setting.JBW_SHOW_CREATED}
							<li class="post-created">
								<span class="day-post">
									{$post.created|escape:'html':'UTF-8'|date_format:"%e"}
								</span>
								<span class="month-post">
									{$post.created|escape:'html':'UTF-8'|date_format:"%B"|truncate:'3':''}
								</span>
							</li>
							{/if}
						</ul>
						{if $widget_setting.JBW_SHOW_INTROTEXT}
						<div class="blog-intro">{$post.introtext|truncate:'80':''}</div>	
						{/if}
						{if $widget_setting.JBW_SHOW_READMORE}
						<a class="blog-readmore btn btn-default" href="{jmsblog::getPageLink('jmsblog-post', $params)}">{l s='Read more'}</a>	
						{/if}
					</div>
				</div>
			</div>	
		{/foreach}
	{else}
		{l s='Dont have any items in this section' mod='jmsblogwidget'} !
	{/if}
</div>
