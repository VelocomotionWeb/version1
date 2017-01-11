{*
*  @author  dSchoorens
*  @copyright  2015 PrestaMod
*}
<!-- FB Connect module -->
<div id="links_img_block_left" class="block">
	<h4 class="title_block">{$fbconnect_title|escape}</h4>
	<div class="block_content">
		<p style="text-align:center;padding-top:20px;">
		{if $fbconnect_link}
			<a href="{$fbconnect_link|escape}" title="{$fbconnect_title|escape}" >
			<img src="{$modules_dir}fbconnect/{$fbconnect_image}" alt="{$fbconnect_title|escape}"/>
			</a>
		{/if}
		{if $fbconnect_logout}
			<a href="{$fbconnect_logout|escape}" title="{$fbconnect_title_logout|escape}" >
			<img src="{$modules_dir}fbconnect/{$fbconnect_image}" alt="{$fbconnect_title_logout|escape}"/>
			</a>
		{/if}
		</p>
	</div>
</div>
<!-- /FB Connect module -->