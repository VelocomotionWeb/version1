{**
* 2007-2015 PrestaShop
*
* Slider Layer module for prestashop
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2015 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*}

<div class="jms-slider-wrapper">
	<div class="responisve-container">
		<div class="slider" style="max-width:{$configs.JMS_SLIDER_WIDTH}px">
		<div class="fs_loader" style="padding-bottom:{math equation="h/w*100" h=$configs.JMS_SLIDER_HEIGHT w=$configs.JMS_SLIDER_WIDTH}%"></div>
		{foreach from=$slides item=slide}
			<div class="slide {$slide.class_suffix|escape:'htmlall':'UTF-8'}" style="background:{$slide.bg_color|escape:'htmlall':'UTF-8'} url({$root_url|escape:'htmlall':'UTF-8'}modules/jmsslider/views/img/slides/{$slide.bg_image|escape:'htmlall':'UTF-8'}) no-repeat left top;background-size:cover;" {if $slide.slide_link}onclick="document.location='{$slide.slide_link|escape:'htmlall':'UTF-8'}';"{/if}>
				{foreach from=$slide.layers item=layer}
					{if $layer.data_type=='text'}
					<div class="{$layer.data_class_suffix|escape:'htmlall':'UTF-8'} jms-slide-content" 
					{if $layer.data_fixed}data-fixed{/if} 
					data-position="{$layer.data_y|escape:'htmlall':'UTF-8'},{$layer.data_x|escape:'htmlall':'UTF-8'}" 
					data-fontsize = "{$layer.data_font_size|escape:'html':'UTF-8'}"
					{if $layer.data_line_height}
					data-lineheight = "{$layer.data_line_height|escape:'html':'UTF-8'}px"
					{/if}
					data-in="{$layer.data_in|escape:'html':'UTF-8'}" 
					data-out="{$layer.data_out|escape:'html':'UTF-8'}" 
					data-delay="{$layer.data_delay|escape:'html':'UTF-8'}" 
					data-ease-in="{$layer.data_ease_in|escape:'html':'UTF-8'}" 
					data-ease-out="{$layer.data_ease_out|escape:'html':'UTF-8'}" 
					data-step="{$layer.data_step|escape:'html':'UTF-8'}" 
					data-special="{$layer.data_special|escape:'html':'UTF-8'}"
					data-time = "{$layer.data_time|escape:'html':'UTF-8'}"
					style="font-size: {$layer.data_font_size|escape:'html':'UTF-8'}px; font-style:{$layer.data_style|escape:'html':'UTF-8'}; color: {$layer.data_color|escape:'html':'UTF-8'}; line-height:{if $layer.data_line_height}{$layer.data_line_height|escape:'html':'UTF-8'}px{/if};"					
					>{$layer.data_html}
					</div>
					{elseif $layer.data_type=='image'}					
					<img class="{$layer.data_class_suffix|escape:'htmlall':'UTF-8'} jms-slide-content" 
					src="{$root_url|escape:'htmlall':'UTF-8'}modules/jmsslider/views/img/layers/{$layer.data_image|escape:'htmlall':'UTF-8'}" 
					{if $layer.data_fixed}data-fixed{/if} 
					data-position="{$layer.data_y|escape:'htmlall':'UTF-8'},{$layer.data_x|escape:'htmlall':'UTF-8'}" 
					data-in="{$layer.data_in|escape:'htmlall':'UTF-8'}" 
					data-out="{$layer.data_out|escape:'htmlall':'UTF-8'}" 
					data-delay="{$layer.data_delay|escape:'htmlall':'UTF-8'}" 
					data-ease-in="{$layer.data_ease_in|escape:'htmlall':'UTF-8'}" 
					data-ease-out="{$layer.data_ease_out|escape:'htmlall':'UTF-8'}" 
					data-time = "{$layer.data_time|escape:'htmlall':'UTF-8'}"
					data-step="{$layer.data_step|escape:'htmlall':'UTF-8'}" 
					data-special="{$layer.data_special|escape:'htmlall':'UTF-8'}" 
					width="{$layer.data_width|escape:'htmlall':'UTF-8'}" 
					height="{$layer.data_height|escape:'htmlall':'UTF-8'}"/>
					{else}
						
					<iframe class="{$layer.data_class_suffix|escape:'htmlall':'UTF-8'} jms-slide-content"
					{if $layer.data_fixed || $layer.data_video_bg}data-fixed{/if} 
					data-position="{$layer.data_y|escape:'htmlall':'UTF-8'},{$layer.data_x|escape:'htmlall':'UTF-8'}" 
					data-in="{$layer.data_in|escape:'htmlall':'UTF-8'}" 
					data-out="{$layer.data_out|escape:'htmlall':'UTF-8'}" 
					{if $layer.data_video_bg}data-delay="0"{else}data-delay="{$layer.data_delay|escape:'htmlall':'UTF-8'}" {/if}
					data-ease-in="{$layer.data_ease_in|escape:'htmlall':'UTF-8'}" 
					data-ease-out="{$layer.data_ease_out|escape:'html':'UTF-8'}" 
					data-step="{$layer.data_step|escape:'htmlall':'UTF-8'}" 
					data-special="{$layer.data_special|escape:'htmlall':'UTF-8'}"
					data-time = "{$layer.data_time|escape:'htmlall':'UTF-8'}"
					{if $layer.data_video_bg}
						width="{$configs.JMS_SLIDER_WIDTH|escape:'htmlall':'UTF-8'}"
						height="{$configs.JMS_SLIDER_HEIGHT|escape:'htmlall':'UTF-8'}"
					{else}
						width="{$layer.data_width|escape:'htmlall':'UTF-8'}"
						height="{$layer.data_height|escape:'htmlall':'UTF-8'}"
					{/if}
					{if $layer.videotype == 'youtube'}
						src="http://www.youtube.com/embed/{$layer.data_video|substr:($layer.data_video|strpos:'?v='+3)}?autoplay={$layer.data_video_autoplay|escape:'htmlall':'UTF-8'}&controls={$layer.data_video_controls|escape:'htmlall':'UTF-8'}&loop={$layer.data_video_loop|escape:'htmlall':'UTF-8'}"
					{else if $layer.videotype == 'vimeo'}
						 {assign var=vimeo_link value = ("/"|explode:$layer.data_video)}
						src="https://player.vimeo.com/video/{$vimeo_link[$vimeo_link|count-1]}?autoplay={$layer.data_video_autoplay|escape:'htmlall':'UTF-8'}&loop={$layer.data_video_loop|escape:'htmlall':'UTF-8'}"
					{/if}	
					allowfullscreen 
					frameborder="0">
					</iframe> 
					{/if}
				{/foreach}
			</div>
		{/foreach}
		</div>
	</div>
</div>

  
<script type="text/javascript">
	$(window).load(function(){
		$('.slider').fractionSlider({	
			'slideTransition' : "{$configs.JMS_SLIDER_TRANS|escape:'html':'UTF-8'}",
			'slideEndAnimation' : {if $configs.JMS_SLIDER_END_ANIMATE}true{else}false{/if},
			'transitionIn' : "{$configs.JMS_SLIDER_TRANS_IN|escape:'html':'UTF-8'}", // default in - transition
			'transitionOut' : "{$configs.JMS_SLIDER_TRANS_OUT|escape:'html':'UTF-8'}", // default out - transition
			'fullWidth' : {if $configs.JMS_SLIDER_FULL_WIDTH}true{else}false{/if}, // transition over the full width of the window
			'delay' : {$configs.JMS_SLIDER_DELAY|escape:'html':'UTF-8'}, // default delay for elements
			'timeout' : {$configs.JMS_SLIDER_DURATION|escape:'html':'UTF-8'}, // default timeout before switching slides
			'speedIn' : {$configs.JMS_SLIDER_SPEED_IN|escape:'html':'UTF-8'},
			'speedOut' : {$configs.JMS_SLIDER_SPEED_OUT|escape:'html':'UTF-8'}, // default in - transition speed
			'easeIn' : "{$configs.JMS_SLIDER_EASE_IN|escape:'html':'UTF-8'}", // default easing in
			'easeOut' : "{$configs.JMS_SLIDER_EASE_OUT|escape:'html':'UTF-8'}", // default easing out
			'controls' : {if $configs.JMS_SLIDER_SHOW_CONTROLS}true{else}false{/if}, // controls on/off
			'pager' : {if $configs.JMS_SLIDER_SHOW_PAGES}true{else}false{/if}, // pager inside of the slider on/off OR $('someselector') for a pager outside of the slider
			'autoChange' : {if $configs.JMS_SLIDER_AUTO_CHANGE}true{else}false{/if}, // auto change slides
			'pauseOnHover' : {if $configs.JMS_SLIDER_PAUSE_HOVER}true{else}false{/if}, // Pauses slider on hover (current step will still be completed)
			'backgroundAnimation' : {if $configs.JMS_SLIDER_BG_ANIMATE}true{else}false{/if},
			'backgroundEase' : "{$configs.JMS_SLIDER_BG_EASE|escape:'html':'UTF-8'}",
			'responsive' : {if $configs.JMS_SLIDER_RESPONSIVE}true{else}false{/if}, // responsive slider (see below for some implementation tipps)
			'dimensions' : "{$configs.JMS_SLIDER_WIDTH|escape:'html':'UTF-8'},{$configs.JMS_SLIDER_HEIGHT|escape:'html':'UTF-8'}",
		});
	});
</script>