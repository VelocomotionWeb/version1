{if $MP_SHOW_SELLER_DETAILS}
	<section class="page-product-box">
		<h3 class="idTabHrefShort page-product-heading">{l s='Seller Detail' mod='marketplace'}</h3>
		<div class="partnerdetails">
			<div class="sellerinfo">
				{if isset($mp_seller_info)}
					{if $MP_SELLER_DETAILS_ACCESS_1}
						<div class="wk_row">
							<label class="wk-person-icon">{l s='Seller Name' mod='marketplace'} - </label>
							<span>{$mp_seller_info.seller_name|escape:'html':'UTF-8'}</span>
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_2}
						<div class="wk_row">
							<label class="wk-shop-icon">{l s='Shop Name' mod='marketplace'} - </label>
							<span>{$mp_seller_info.shop_name|escape:'html':'UTF-8'}</span>	
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_3}
						<div class="wk_row">
							<label class="wk-mail-icon">{l s='Seller email' mod='marketplace'} - </label>
							<span>{$mp_seller_info.business_email|escape:'html':'UTF-8'}</span>
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_4}
						<div class="wk_row">
							<label class="wk-phone-icon">{l s='Phone' mod='marketplace'} - </label>
							<span>{$mp_seller_info.phone|escape:'html':'UTF-8'}</span>
						</div>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_5}
						<div class="wk_row">
							<label class="wk-share-icon">{l s='Social Profile' mod='marketplace'} - </label>
							<span class="wk-social-icon">
								{if $mp_seller_info.facebook_id != ""}
									<a class="wk-facebook-button" target="_blank" title="Facebook" href="http://www.facebook.com/{$mp_seller_info.facebook_id|escape:'html':'UTF-8'}"></a>
								{/if}
								{if $mp_seller_info.twitter_id != ""}
									<a class="wk-twitter-button" target="_blank" title="Twitter" href="http://www.twitter.com/{$mp_seller_info.twitter_id|escape:'html':'UTF-8'}"></a>
								{/if}
							</span>
						</div>
					{/if}
					{hook h='displayMpSellerDetailTabLeft'}
				{/if}
			</div>	
			<div class="sellerlink">
				<ul>
					{if $MP_SELLER_DETAILS_ACCESS_6}
						<li>
							<a id="profileconnect" title="Visit Profile" target="_blank" href="{$link->getModuleLink('marketplace', 'sellerprofile', ['mp_shop_name' => $name_shop|escape:'html':'UTF-8'])}">{l s='View Profile' mod='marketplace'}</a>
						</li>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_7}
						<li>
							<a id="siteconnect" title="Visit Collection" target="_blank" href="{$link->getModuleLink('marketplace', 'shopcollection',['mp_shop_name' => $name_shop|escape:'html':'UTF-8'])}">{l s='View Collection' mod='marketplace'}</a>
						</li>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_8}
						<li>
							<a id="storeconnect" title="Visit Store" target="_blank" href="{$link->getModuleLink('marketplace', 'shopstore', ['mp_shop_name' => $name_shop|escape:'html':'UTF-8'])}">{l s='View Store' mod='marketplace'}</a>
						</li>
					{/if}
					{if $MP_SELLER_DETAILS_ACCESS_9}
						<li>
							<a href="#wk_question_form" class="open-question-form" title="{l s='Contact Seller' mod='marketplace'}">{l s='Contact Seller' mod='marketplace'}</a>
						</li>
					{/if}
					{hook h='DisplayMpSellerDetailTabRight'}
				</ul>	
			</div>	
		</div>
		{hook h='DisplayMpSellerDetailTabBotttom'}


		<!-- Pop up ask question form using fancybox -->
		<div id="wk_question_form" style="display: none;">
			<div class="wk_ques_head">
				<h3>{l s='Write your query' mod='marketplace'}</h3>
			</div>
			<form id="ask-form" method="post" action="#">
				<span class="ques_form_error">{l s='Fill all the fields' mod='marketplace'}</span>
				<div class="form-group">
					<label class="label-control required">{l s='Email' mod='marketplace'}</label>
					<input type="text" name="customer_email" id="customer_email" class="form-control"/>
				</div>
				<div class="form-group">
					<label class="label-control required">{l s='Subject' mod='marketplace'}</label>
					<input type="text" name="query_subject" class="form-control" id="query_subject"/>
				</div>
				<div class="form-group">
					<label class="label-control required">{l s='Description ' mod='marketplace'}</label>
					<textarea name="query_description" class="form-control" id="query_description"></textarea>
				</div>

				<input type="hidden" name="id_seller" value="{$seller_id|escape:'html':'UTF-8'}"/>
				<input type="hidden" name="id_customer" value="{$id_customer|escape:'html':'UTF-8'}"/>
				<input type="hidden" name="id_product" value="{$id_product|escape:'html':'UTF-8'}"/>

				<div class="wk_ques_form_footer">
					<p class="fl required"><sup>*</sup> {l s='Required fields' mod='marketplace'}</p>
					<p class="fr">
						<button type="submit" class="btn button button-small" id="askbtn">
							<span>{l s='Send' mod='marketplace'}</span>
						</button>&nbsp;
						{l s='or' mod='marketplace'}&nbsp;
						<a class="closefb" href="#">
							{l s='Cancel' mod='marketplace'}
						</a>
					</p>
				</div>
			</form>
		</div>
	</section>
{/if}
{strip}
	{addJsDef contact_seller_ajax_link=$link->getModuleLink('marketplace', 'contactsellerprocess')|addslashes}
{/strip}

<script type="text/javascript">
	$(document).ready(function(){
		$('.open-question-form').fancybox({
			width: 550,
		    height: 340,
		    autoSize : false,
		    maxWidth : '100%',
			hideOnContentClick: false
		});

		$(document).on('click', '.closefb', function(e){
			e.preventDefault();
			$.fancybox.close();
		});

		$('#askbtn').on("click", function(e){
			var email = $("#customer_email").val();
			var query_subject = $("#query_subject").val();
			var query_description = $("#query_description").val();
			var mail = /^[a-zA-Z]*$/;
			var reg = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;

			if (!reg.test(email))
			{
				alert("Invalid email.");
				return false;
			}
			else if(query_subject == "")
			{
				alert("Fill Subject");
				return false;
			}
			else if(query_description == "")
			{
				alert("Fill Description");
				return false;
			}
			else
			{
				$.ajax({
					url: contact_seller_ajax_link,
					type: 'POST',
					dataType: 'json',
					async: false,
					data:$('#ask-form').serialize(),
					success:function(result)
					{
						if(result.status == 'ok')
							alert(result.msg);
						else
							alert(result.msg);
					}
				});
			}
		});
	});
</script>