{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='Seller Profile' mod='marketplace'}</span>
{/capture}

{if isset($review_submitted)}
	<p class="alert alert-success">
		{l s='Thanks for the feedback. Review will be active after admin approval.' mod='marketplace'}
	</p>
{/if}

{if isset($review_submit_default)}
	<p class="alert alert-success">
		{l s='Thanks for the feedback.' mod='marketplace'}
	</p>
{/if}

<div class="main_block">
<div class="wk_left_sidebar">
	<div style="margin-bottom:10px;">
		{if isset($seller_img_path)}
			<img class="left_img" src="{$seller_img_path|escape:'html':'UTF-8'}" alt="Seller Image"/>
		{else}
			<img src="{$modules_dir|escape:'htmlall':'UTF-8'}marketplace/views/img/seller_img/defaultimage.jpg" alt="Default Image"/>
		{/if}	
	</div>
	<div style="float:left;width:100%;">
		<a class="button btn btn-default button-medium" href="{$link->getModuleLink('marketplace','shopcollection',['shop'=>{$id_shop|escape:'html':'UTF-8'}, 'mp_shop_name' => $name_shop_rewrite])|escape:'html':'UTF-8'}">
			<span>{l s='View Collection' mod='marketplace'}</span>
		</a>
	</div>
	{hook h='DisplayMpsplefthook'}
</div>
<div class="dashboard_content">
	<div class="page-title" style="background-color:{$title_bg_color|escape:'html':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'html':'UTF-8'};">{l s='Seller Profile' mod='marketplace'}</span>
	</div>
	<div class="wk_right_col">
		{if $MP_SHOW_SELLER_DETAILS}
			<div class="box-account">
				<div class="box-head">
					<h2>{l s='About Seller' mod='marketplace'}</h2>
					<div class="wk_border_line"></div>
				</div>
				<div class="box-content" style="background-color:#F6F6F6;border-bottom: 3px solid #D5D3D4;">
					{if $MP_SELLER_DETAILS_ACCESS_1}
						<div class="seller_name">{$mp_seller_info['seller_name']|escape:'html':'UTF-8'}</div>
					{/if}
					<div class="wk-left-label">
						{if $MP_SELLER_DETAILS_ACCESS_3}
							<div class="wk_row">
								<label class="wk-mail-icon">{l s='Business Email -' mod='marketplace'}</label>
								<span>{$mp_seller_info['business_email']|escape:'html':'UTF-8'}</span>
							</div>
						{/if}
						{if $MP_SELLER_DETAILS_ACCESS_4}
							<div class="wk_row">
								<label class="wk-phone-icon">{l s='Phone -' mod='marketplace'}</label>
								<span>{$mp_seller_info['phone']|escape:'html':'UTF-8'}</span>
							</div>
						{/if}
						{if $MP_SELLER_DETAILS_ACCESS_10}
							<div class="wk_row">
								<label class="wk-address-icon">{l s='Address -' mod='marketplace'}</label>
								<span>{$mp_seller_info['address']|escape:'html':'UTF-8'}</span>
							</div>
						{/if}
						{if $MP_SELLER_DETAILS_ACCESS_5}
							<div class="wk_row">
								<label class="wk-share-icon">{l s='Social Profile -' mod='marketplace'}</label>
								<span class="wk-social-icon">
									{if {$mp_seller_info['facebook_id']} != ''}
										<a class="wk-facebook-button" target="_blank" href="http://www.facebook.com/{$mp_seller_info['facebook_id']|escape:'html':'UTF-8'}"></a>
									{/if}
									{if {$mp_seller_info['twitter_id']} != ''}
									<a class="wk-twitter-button" target="_blank" href="http://www.twitter.com/{$mp_seller_info['twitter_id']|escape:'html':'UTF-8'}"></a>
									{/if}
								</span>
							</div>
						{/if}
						{if $MP_SELLER_DETAILS_ACCESS_12}
							<div class="wk_row">
								<label class="wk-rating-icon">{l s='Seller Rating -' mod='marketplace'}</label>
								<span class="avg_rating"></span>
							</div>
						{/if}
						{hook h="displayExtraSellerDetails"}
					</div>
				</div>
			</div>
		{/if}
		<div class="box-account">
			<div class="box-head">
				<h2>{l s='Recent Products' mod='marketplace'}</h2>
				<div class="wk_border_line"></div>
			</div>
			<div class="box-content wk_slider_padding">
				{if isset($product_detail)}
					<div id="product-slider_block_center" class="wk-product-slider">
						<ul class="mp-prod-slider {if $product_detail|@count > 3}mp-bx-slider{/if}">
							{foreach $product_detail as $key => $product}
								<li {if $product_detail|@count <= 3}class="wk-product-out-slider"{/if} {if $key == 2}style="margin-right:0;"{/if}>
									<a href="{$link->getProductLink($product.product)|addslashes}" class="product_img_link" title="{$product.name|escape:'html':'UTF-8'}">
										<div class="wk-slider-product-img">
											{if $product.image}
												<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}">
											{else}
												<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.lang_iso|cat : '-default', 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}">
											{/if}
										</div>
										<div class="wk-slider-product-info">
											<div style="margin-bottom:5px;">{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}</div>
											{if $product.show_price}
											<div style="font-weight:bold;">{convertPrice price=$product.product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}</div>
											{/if}
										</div>
									</a>
								</li>
							{/foreach}
						</ul> 
					</div>
				{else}
					<div class="alert alert-info">
						{l s='No product found' mod='marketplace'}
					</div>
				{/if}
			</div>
		</div>
		<div class="box-account">
			<div class="box-head">
				<div class="wk_review_head">
					<h2>{l s='Reviews about seller' mod='marketplace'}</h2>
				</div>
				<div class="wk_write_review">
					<a class="btn btn-default button button-small open-review-form forloginuser" href="#wk_review_form">
						<span>{l s='Write a Review' mod='marketplace'} !</span>
					</a>
				</div>
				<div class="wk_border_line"></div>
			</div>
			<div class="box-content">
				{if !empty($reviews_info)}
					{foreach from=$reviews_details item=details}
						<div class="wk-reviews">
							<div class="wk-writer-info">
								<div class="wk-writer-details">
									<ul>
										<li class="wk-person-icon">{$details.customer_name|escape:'html':'UTF-8'}</li>
										<li class="wk-mail-icon">{$details.customer_email|escape:'html':'UTF-8'}</li>
										<li class="wk-watch-icon">{$details.time|escape:'html':'UTF-8'}</li>
									</ul>
								</div>
								<div class="wk-seller-rating">
									{assign var=i value=0}
									{while $i != $details.rating}
										<img src="{$modules_dir|escape:'html':'UTF-8'}/marketplace/views/img/star-on.png" />
									{assign var=i value=$i+1}
									{/while}

								  	{assign var=k value=0}	
								  	{assign var=j value=5-$details.rating}
								  	{while $k!=$j}
								   		<img src="{$modules_dir|escape:'html':'UTF-8'}/marketplace/views/img/star-off.png" />
								  	{assign var=k value=$k+1}
								 	{/while}
								</div>
							</div>
							{if !empty($details.review)}
								<div class="wk_review_content">
									{$details.review|escape:'html':'UTF-8'}
								</div>
							{/if}
						</div>
						<div class="wk_border_line"></div>
					{/foreach}
					<a class="btn btn-default button button-small" href="{$link->getModuleLink('marketplace', 'allreviews', ['shop' => $id_shop, 'seller' => $id_seller])|escape:'html':'UTF-8'}">
						<span>{l s='View all reviews' mod='marketplace'}</span>
					</a>
				{else}
					<p class="alert alert-info">{l s='No reviews found' mod='marketplace'}</p>
				{/if}
			</div>	
		</div>
		{hook h='DisplayMpspcontentbottomhook'}
	</div>
</div>
</div>


<!-- Fancybox -->
<div style="display: none;">
	<div id="wk_review_form">
		<form id="review_submit" method="post" action="{$link->getModuleLink('marketplace', 'sellerprofile', ['shop' => {$id_shop|escape:'html':'UTF-8'}])|escape:'html':'UTF-8'}">
			<h2 class="page-subheading">
				{l s='Write a review' mod='marketplace'}
			</h2>
			<div>
				<div class="wk_review_form_content col-xs-12">
					<label for="comment_title">
						{l s='Add Rating' mod='marketplace'}: <sup class="required">*</sup>
					</label>
					 <span id="rating_image"></span>
					<label for="content">
						{l s='Comment' mod='marketplace'} :
					</label>
					<textarea name="feedback"></textarea>
					<input type="hidden" name="seller_id" value="{$id_seller|escape:'html':'UTF-8'}">
					<div id="wk_review_form_footer">
						<p class="fl required"><sup>*</sup> {l s='Required fields' mod='marketplace'}</p>
						<p class="fr">
							<button name="submit_feedback" type="submit" class="btn button button-small">
								<span>{l s='Send' mod='marketplace'}</span>
							</button>&nbsp;
							{l s='or' mod='marketplace'}&nbsp;
							<a class="closefb" href="#">
								{l s='Cancel' mod='marketplace'}
							</a>
						</p>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
var rate_req = "{l s='Please give rating.' js='1' mod='marketplace'}";
var not_logged_msg = "{l s='Please login to write a review.' js='1' mod='marketplace'}";
var review_yourself_msg = "{l s='You can not write review to yourself.' js='1' mod='marketplace'}";
var logged = "{$logged|escape:'html':'UTF-8'}";
var url_mp_shop_name = "{$smarty.get.mp_shop_name|escape:'html':'UTF-8'}";
var login_mp_shop_name = "{$login_mp_shop_name|escape:'html':'UTF-8'}";

$(function()
{
	if($('.best-sell .best-sell-product').length==0)
		$('.best-sell-box').hide();
	
	var wk_slider=$('.best-sell .best-sell-product').length;
	$('.ed_right').click(function(){
		if(wk_slider>3)
		{
			var thisthis=$(this).siblings('.best-sell');
			thisthis.animate({
				"left":"-=480px"
			},1500);
			wk_slider = wk_slider-3;
		}
	});

	$('.ed_left').click(function() {
		var thisthis=$(this).siblings('.best-sell');
		if(wk_slider < $('.best-sell .best-sell-product').length){
			thisthis.animate({
				"left":"+=480px"
			},1500);
			wk_slider = wk_slider+3;
		}
	});
});

//Review form submit
$('#review_submit').submit(function()
{
	var rating_image = $( "input[name='rating_image']" ).val();
	if(rating_image == '' || rating_image == ' ' )
	{
		alert(rate_req);
		return false;
	}
});

//Rating image in review form
var id = 'rating_image';
$('#'+id).raty({
	path: '{$modules_dir|escape:'html':'UTF-8'}/marketplace/libs/rateit/lib/img',
	scoreName: id,							
});
</script>

{if isset($avg_rating)}
<script type="text/javascript">
$('.avg_rating').raty(
{
	path: '{$modules_dir|escape:'html':'UTF-8'}/marketplace/libs/rateit/lib/img',
	score: {$avg_rating|escape:'html':'UTF-8'},
	readOnly: true,
});
</script>
{/if}						
		






