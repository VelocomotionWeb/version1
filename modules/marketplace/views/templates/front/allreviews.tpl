{if !$logged}
<style>
	.dashboard_content{
		width:100% !important;
	}
</style>
{/if}
{capture name=path}
	<a {if $logged}href="{$link->getModuleLink('marketplace', 'dashboard')|escape:'html':'UTF-8'}"{/if}>
		{l s='Marketplace' mod='marketplace'}
	</a>
	<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>
	<span class="navigation_page">{l s='All Reviews' mod='marketplace'}</span>
{/capture}
<div class="main_block">
	{if $logged}
		{hook h="DisplayMpmenuhook"}
	{/if}
	<div class="dashboard_content">
	<div class="page-title" style="background-color:{$title_bg_color|escape:'htmlall':'UTF-8'};">
		<span style="color:{$title_text_color|escape:'htmlall':'UTF-8'};">{l s='All Reviews' mod='marketplace'} ({count($reviews_info|escape:'htmlall':'UTF-8')})</span>
	</div>
	<div class="wk_right_col">
		<div class="box-account">
			<div class="box-head">
			</div>
			<div class="box-content">
				{if isset($reviews_info)}
					{foreach from=$reviews_info item=details}
						<div class="wk-reviews">
							<div class="wk-writer-info">
								<div class="wk-writer-details">
									<ul>
										<li class="wk-person-icon">{$details.customer_name|escape:'html':'UTF-8'}</li>
										<li class="wk-mail-icon">{$details.customer_email|escape:'html':'UTF-8'}</li>
										<li class="wk-watch-icon">{$details.date_add|escape:'html':'UTF-8'}</li>
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
				{else}
					<p>{l s='No reviews available' mod='marketplace'}</p>
				{/if}
			</div>	
		</div>
	</div>
</div>