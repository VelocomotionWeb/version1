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
<div id="idTab5">
	<div id="product_comments_block_tab">
	{if $comments}
		{foreach from=$comments item=comment}
			{if $comment.content}
			<div class="comment row">
				<div class="comment_author col-sm-3 col-md-3 col-lg-3">
					<span>{l s='Grade' mod='productcomments'}&nbsp</span>
					<span class="rating">
					{section name="i" start=0 loop=5 step=1}
						{if $comment.grade le $smarty.section.i.index}
							<i class="icon star-empty"></i>							
						{else}
							<i class="icon star-full"></i>
						{/if}
					{/section}
					</span>
					<div class="comment_author_infos">
						<strong>{$comment.customer_name|escape:'html':'UTF-8'}</strong><br/>
						<em>{dateFormat date=$comment.date_add|escape:'html':'UTF-8' full=0}</em>
					</div>
				</div>
				<div class="comment_details col-sm-9 col-md-9 col-lg-9">
					<strong>{$comment.title}</strong>
					<p>{$comment.content|escape:'html':'UTF-8'|nl2br}</p>
					<ul>
						{if $comment.total_advice > 0}
							<li>{l s='%1$d out of %2$d people found this review useful.' sprintf=[$comment.total_useful,$comment.total_advice] mod='productcomments'}</li>
						{/if}
						{if $logged}
							{if !$comment.customer_advice}
							<li>{l s='Was this comment useful to you?' mod='productcomments'}<button class="usefulness_btn" data-is-usefull="1" data-id-product-comment="{$comment.id_product_comment}">{l s='yes' mod='productcomments'}</button><button class="usefulness_btn" data-is-usefull="0" data-id-product-comment="{$comment.id_product_comment}">{l s='no' mod='productcomments'}</button></li>
							{/if}
							{if !$comment.customer_report}
							<li><span class="report_btn" data-id-product-comment="{$comment.id_product_comment}">{l s='Report abuse' mod='productcomments'}</span></li>
							{/if}
						{/if}
					</ul>
				</div>
			</div>
			{/if}
		{/foreach}
        {if (!$too_early AND ($logged OR $allow_guests))}
		<p class="align_center">
			<a id="new_comment_tab_btn" class="open-comment-form" href="#new_comment_form"><i class="fa fa-edit"></i>{l s='Write your review' mod='productcomments'} !</a>
		</p>
        {/if}
	{else}
		{if (!$too_early AND ($logged OR $allow_guests))}
		<p class="align_center">
			<a id="new_comment_tab_btn" class="open-comment-form" href="#new_comment_form">{l s='Be the first to write your review' mod='productcomments'} !</a>
		</p>
		{else}
		<p class="align_center">{l s='No customer comments for the moment.' mod='productcomments'}</p>
		{/if}
	{/if}	
	</div>
</div>

{if isset($product) && $product}
<!-- Fancybox -->
<div style="display: none;">
	<div id="new_comment_form">
		<form id="id_new_comment_form" action="#">
			<h2 class="title">{l s='Write your review' mod='productcomments'}</h2>
			{if isset($product) && $product}
			<div class="product clearfix">
				<img src="{$productcomment_cover_image}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$product->name|escape:html:'UTF-8'}" />
				<div class="product_desc">
					<p class="product_name"><strong>{$product->name}</strong></p>
					{$product->description_short}
				</div>
			</div>
			{/if}
			<div class="new_comment_form_content">
				<h2>{l s='Write your review' mod='productcomments'}</h2>

				<div id="new_comment_form_error" class="error" style="display: none; padding: 15px 25px">
					<ul></ul>
				</div>

				{if $criterions|@count > 0}
					<ul id="criterions_list">
					{foreach from=$criterions item='criterion'}
						<li>
							<label>{$criterion.name|escape:'html':'UTF-8'}:</label>
							<div class="star_content">
								<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="1" />
								<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="2" />
								<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="3" checked="checked" />
								<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="4" />
								<input class="star" type="radio" name="criterion[{$criterion.id_product_comment_criterion|round}]" value="5" />
							</div>
							<div class="clearfix"></div>
						</li>
					{/foreach}
					</ul>
				{/if}

				<label for="comment_title">{l s='Title' mod='productcomments'}: <sup class="required">*</sup></label>
				<input id="comment_title" name="title" type="text" value=""/>

				<label for="content">{l s='Comment' mod='productcomments'}: <sup class="required">*</sup></label>
				<textarea id="content" name="content"></textarea>

				{if $allow_guests == true && !$logged}
				<label>{l s='Your name' mod='productcomments'}: <sup class="required">*</sup></label>
				<input id="commentCustomerName" name="customer_name" type="text" value=""/>
				{/if}

				<div id="new_comment_form_footer">
					<input id="id_product_comment_send" name="id_product" type="hidden" value='{$id_product_comment_form}' />
					<p class="fl required"><sup>*</sup> {l s='Required fields' mod='productcomments'}</p>
					<p class="fr">
						<button id="submitNewMessage" name="submitMessage" type="submit" class="btn btn-default">{l s='Send' mod='productcomments'}</button>&nbsp;
						{l s='or' mod='productcomments'}&nbsp;<a href="#" onclick="$.fancybox.close();">{l s='Cancel' mod='productcomments'}</a>
					</p>
					<div class="clearfix"></div>
				</div>
			</div>
		</form><!-- /end new_comment_form_content -->
	</div>
</div>
<!-- End fancybox -->
{/if}
{strip}
{addJsDef productcomments_controller_url=$productcomments_controller_url|@addcslashes:'\''}
{addJsDef moderation_active=$moderation_active|boolval}
{addJsDef productcomments_url_rewrite=$productcomments_url_rewriting_activated|boolval}
{addJsDef secure_key=$secure_key}

{addJsDefL name=confirm_report_message}{l s='Are you sure that you want to report this comment?' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_added}{l s='Your comment has been added!' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_added_moderation}{l s='Your comment has been added and will be available once approved by a moderator.' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_title}{l s='New comment' mod='productcomments' js=1}{/addJsDefL}
{addJsDefL name=productcomment_ok}{l s='OK' mod='productcomments' js=1}{/addJsDefL}
{/strip}