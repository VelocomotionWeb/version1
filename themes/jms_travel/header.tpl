 {*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"> <![endif]-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}" lang="{$lang_iso}" class="off-canvas">
	<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{/if}
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<link rel="icon" type="image/png" href="{$content_dir}img/FAVICON.png" />

    {literal}
	<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-82647738-1', 'auto');
      ga('send', 'pageview');

    </script>
    {/literal}
		<script type="text/javascript">
			var baseDir = '{$content_dir}';
			var baseUri = '{$base_uri}';
			var static_token = '{$static_token}';
			var token = '{$token}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>
<link href='https://fonts.googleapis.com/css?family=Raleway:400,300,500,700' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>


<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">


<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/linearicons.css" />
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/animate.css" />
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/jcarousel.css" />
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/owl.carousel.css" />
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/owl.theme.css" />
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/theme-responsive.css" />
{if $rtl}
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/rtl.css" />
{/if}
{if $skin!=''}
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/theme-{$skin}.css" />
{/if}
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/jmstools/jmstools.css" />
{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
	{$js_def}
	{foreach from=$js_files item=js_uri}
	<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
	{/foreach}
{/if}
<script type="text/javascript" src="{$content_dir}themes/{$themename}/js/jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="{$content_dir}themes/{$themename}/js/owl.carousel.js"></script>
<script type="text/javascript" src="{$content_dir}themes/{$themename}/js/jquery.cookie.js"></script>
<script type="text/javascript" src="{$content_dir}themes/{$themename}/js/jquery.viewportchecker.js"></script>
{if $tools && !$content_only}
<script type="text/javascript" src="{$content_dir}themes/{$themename}/jmstools/jmstools.js"></script>
{/if}
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
{$HOOK_HEADER}
<link href="https://fonts.googleapis.com/css?family=Asap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{$content_dir}themes/{$themename}/css/custom.css" />


<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1386471481371509&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
</head>
	<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="homepage_{$homepage|escape:'html':'UTF-8'} responsive {if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if $content_only} content_only{/if} lang_{$lang_iso} {if $rtl}rtl{/if}">
	<div id="pageloader">
		<div class="loader">
			<div class="sp9"></div>
		</div>
	</div>
	<nav id="off-canvas-menu">
		<div id="off-canvas-menu-title">MENU<span id="off-canvas-menu-close" class="lnr lnr-cross"></span></div>
		{hook h='displayTopColumn'}
	</nav>
	<div id="outer">
  		<div id="outer-canvas">
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}
		<div id="page" class="clearfix">
			<!-- Header -->
			{$HOOK_TOP}
			{if $page_name!='index' }
			<section class="breadcrumbs-section">
				{include file="$tpl_dir./breadcrumb.tpl"}
			</section>
			{/if}
			<!--  Columns -->
 			{if $page_name!='index'}
			<section class="container page-content">
				<div class="row">
					{if $page_name!='index' && $page_name!='product' && !$hide_left_column && !empty($HOOK_LEFT_COLUMN)}
					<!-- Left -->
					<aside class="col-sm-4 col-md-3 col-lg-3 col-xs-12 content-aside">
						<div class="content-aside-inner">
							{$HOOK_LEFT_COLUMN}
							{include file="./product-compare.tpl"}
						</div>
					</aside>
					{/if}
					<!-- Center -->
					{$show_left = !$hide_left_column && !empty($HOOK_LEFT_COLUMN)}
					{$show_right = !$hide_right_column && !empty($HOOK_RIGHT_COLUMN)}
					{if $page_name!='index'}
						{if $show_left && $show_right}
							<section class="content-center container">
						{elseif ($show_left && !$show_right) || (!$show_left && $show_right)}
							<section class="col-sm-8 col-md-9 col-lg-9 col-xs-12 content-center">
						{else}
							<section class="content-center container">
						{/if}

					{else}
					<section class="content-center container">
					{/if}
			{/if}
	{/if}
