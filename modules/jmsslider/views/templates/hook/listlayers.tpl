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

<form id="layer_form" action="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&layer=1" method="post">

<div class="panel area-display">
	<h3>
	<span title="" data-toggle="tooltip" class="label-tooltip toogle" data-original-title="Click to Toggle" data-html="true">
		<i class="icon-list-ul"></i> {l s='Layers list of' mod='jmsslider'}
	</span>	
	<div class="list-title-slides">
	{foreach from=$all_slides item=eve_slide}
		<a {if $eve_slide.id_slide==$slides.id_slide}class="btn-info"{else}title="{l s='Click here go to ' mod='jmsslider'}{$eve_slide.title|escape:'html':'UTF-8'}"{/if} href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&layers=1&id_slide={$eve_slide.id_slide|escape:'html':'UTF-8'}">{$eve_slide.title|escape:'html':'UTF-8'}</a>&nbsp;&nbsp;
	{/foreach}</div>
	</h3>
	<div class="panel-body ">
		<div class="wrap-slider">
			<div  class="slider" style="width:{$configs.JMS_SLIDER_WIDTH|escape:'html':'UTF-8'}px;height:{$configs.JMS_SLIDER_HEIGHT|escape:'html':'UTF-8'}px">
				<div id="frame_layer" class="slide" style="{if $slides.bg_type==1}background-image:url({$root_url|escape:'htmlall':'UTF-8'}modules/jmsslider/views/img/slides/{$slides.bg_image|escape:'htmlall':'UTF-8'});{else}background-color:{$slides.bg_color|escape:'htmlall':'UTF-8'}{/if};background-size:100% 100%;position:relative;width:100%;height:100%">
					{foreach from=$layers item=layer}
						{math assign="data_x" equation='(x/y)*100' x=$layer.data_x y=$configs.JMS_SLIDER_WIDTH}
						{math assign="data_y" equation='(w/z)*100' w=$layer.data_y z=$configs.JMS_SLIDER_HEIGHT}
						<div id="caption_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="tp-caption layer {$layer.data_class_suffix|escape:'htmlall':'UTF-8'}" style="position:absolute; {if $layer.data_video_bg}top:0; left:0;width:100%;height:100%;{else}top:{$data_y|escape:'htmlall':'UTF-8'}%; left:{$data_x|escape:'htmlall':'UTF-8'}%;{/if}font-size:{$layer.data_font_size|escape:'htmlall':'UTF-8'}px; color: {$layer.data_color|escape:'htmlall':'UTF-8'}; font-style:{$layer.data_style|escape:'htmlall':'UTF-8'};{if $layer.data_line_height}line-height:{$layer.data_line_height|escape:'htmlall':'UTF-8'}px;{/if}">
						{if $layer.data_type=="text"}
							<span>{$layer.data_html}</span>
						{elseif $layer.data_type=="image"}
							<img width="100%" height="100%" id="image_layer_{$layer.id_layer|escape:'htmlall':'UTF-8'}" src="{$root_url|escape:'htmlall':'UTF-8'}/modules/jmsslider/views/img/layers/{$layer.data_image|escape:'htmlall':'UTF-8'}">
						{else}
							<i class="icon-arrows move-toolbar" title="Keep mouse to move" ></i>
							{if $layer.videotype == 'youtube'}
							<iframe width="{$layer.data_width|escape:'htmlall':'UTF-8'}px;" height="{$layer.data_height|escape:'htmlall':'UTF-8'}px;" src="http://www.youtube.com/embed/{$layer.data_video|substr:($layer.data_video|strpos:'?v='+3)}?autoplay={$layer.data_video_autoplay|escape:'htmlall':'UTF-8'}&controls={$layer.data_video_controls|escape:'htmlall':'UTF-8'}&loop={$layer.data_video_loop|escape:'htmlall':'UTF-8'}" allowfullscreen frameborder="0">
							 </iframe> 
							 {elseif $layer.videotype == 'vimeo'}
							 {assign var=vimeo_link value = ("/"|explode:$layer.data_video)}
							 <iframe width="{$layer.data_width|escape:'htmlall':'UTF-8'}px;" height="{$layer.data_height|escape:'htmlall':'UTF-8'}px;" src="https://player.vimeo.com/video/{$vimeo_link[$vimeo_link|count-1]}?autoplay={$layer.data_video_autoplay|escape:'htmlall':'UTF-8'}&loop={$layer.data_video_loop|escape:'htmlall':'UTF-8'}" allowfullscreen frameborder="0">
							 </iframe>						 
							 {/if}
						{/if}
						</div>
					{/foreach}
				</div>
			</div> <!-- END SLIDE -->
		</div>
		<div id="layer-tools" class="btn-group-action clearfix" style="margin-bottom:30px;">
			<a class="btn btn-default" id="add-text"><i class="icon-file-text-o"></i> {l s='Add Text Layer' mod='jmsslider'}</a>		
			<a class="btn btn-default"  id="add-image"><i class="icon-file-image-o"></i> {l s='Add Image Layer' mod='jmsslider'}</a>
			<a class="btn btn-default"  id="add-video"><i class="icon-file-movie-o"></i> {l s='Add Video' mod='jmsslider'}</a>
			<a class="btn btn-success pull-right"  id="tips"><i class="icon-check-square"></i> {l s='Tips' mod='jmsslider'}</a>	
		</div> <!-- END ACTIONS  -->
		<div class="row content area-display" style="margin-bottom:30px;">
			<div id="layerConfig" class="col-xs-12 col-sm-7 col-md-7 col-cmd-12 form-horizontal">
				<div class="panel panel-default panel-layers">
					<div class="panel-heading">
						{l s='Layer Config' mod='jmsslider'}
					</div>
					{if $layers|@count > 0}
					{foreach from=$layers item=layer}
					<input type="hidden" name="layer_ids[]" value="{$layer.id_layer|escape:'htmlall':'UTF-8'}" />
					<div id="form_layer_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="form-layer panel-bodyclearfix" style="display:none">
						<div class="form-group">
							<label class="control-label col-lg-2">
								<span title="" data-html="true" data-toggle="tooltip" class="label-tooltip" data-original-title="Title of slide not show front end">{l s='Title' mod='jmsslider'}</span>
							</label>
							<div class="col-lg-4">
								<input type="text"  name="data_title_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_title|escape:'htmlall':'UTF-8'}">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Class suffix' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<input type="text" name="data_class_suffix_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_class_suffix|escape:'htmlall':'UTF-8'}">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Layer fixed' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" {if $layer.data_fixed == 1}checked="checked"{/if} value="1" id="data_fixed_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on" name="data_fixed_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_fixed_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on">Yes</label>
									<input type="radio" {if $layer.data_fixed == 0}checked="checked"{/if} value="0" id="data_fixed_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off" name="data_fixed_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_fixed_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off">No</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Start Moving In' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<div class="input-group">									
								<input type="text" id="data_delay" value="{$layer.data_delay|escape:'htmlall':'UTF-8'}" name="data_delay_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<span class="input-group-addon">ms</span>
								</div>
							</div>
							<label class="control-label col-lg-2">{l s='Stop Moving In' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<div class="input-group">									
								<input type="text" id="data_time" value="{$layer.data_time|escape:'htmlall':'UTF-8'}" name="data_time_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<span class="input-group-addon">ms</span>
								</div>
							</div>
						</div>
						<div class="form-group">

							<label class="control-label col-lg-2">
							<span data-original-title="Typing 'center' to center" class="label-tooltip" data-toggle="tooltip" data-html="true" title="">{l s='Data X' mod='jmsslider'}</span>
							</label>
							<div class="col-lg-4">
								<div class="input-group">												
									<input type="text" id="data_x_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-x" name="data_x_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_x|escape:'htmlall':'UTF-8'}" title="Typing 'center' to center">
									<span class="input-group-addon">pixel</span>
								</div>
							</div>
							<label class="control-label col-lg-2">
							<span data-original-title="Typing 'center' to center" class="label-tooltip" data-toggle="tooltip" data-html="true" title="">{l s='Data Y' mod='jmsslider'}</span>
							</label>
							<div class="col-lg-4">
								<div class="input-group">												
									<input type="text" id="data_y_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-y" name="data_y_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_y|escape:'htmlall':'UTF-8'}" title="Typing 'center' to center">
									<span class="input-group-addon">pixel</span>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-lg-2">
								<span data-original-title="Typing 'full' to full width, 'half' to a half width and 'quarter' to a quarter width" class="label-tooltip" data-toggle="tooltip" data-html="true" >{l s='Width' mod='jmsslider'}</span>
							</label>
							<div class="col-lg-4">
								<div class="input-group">												
									<input type="text" id="data_width_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-width" name="data_width_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_width|escape:'htmlall':'UTF-8'}"
									title="Typing 'full' to full width, 'half' to a half width and 'quarter' to a quarter width ">
									<span class="input-group-addon">pixel</span>
								</div>
							</div>
							<label class="control-label col-lg-2">
								<span data-original-title="Typing 'full' to full height, 'half' to a half height and 'quarter' to a quarter height" class="label-tooltip" data-toggle="tooltip" data-html="true" >{l s='Height' mod='jmsslider'}</span>
							</label>
							<div class="col-lg-4">
								<div class="input-group">												
									<input type="text" id="data_height_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-height" name="data_height_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_height|escape:'htmlall':'UTF-8'}" title="Typing 'full' to full height, 'half' to a half height and 'quarter' to a quarter height">
									<span class="input-group-addon">pixel</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Transiton In' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<select name="data_in_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_in">
									{foreach from=$transitions item=trans}
										<option {if $layer.data_in==$trans.id}selected{/if} value="{$trans.id|escape:'htmlall':'UTF-8'}">{$trans.name|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							</div>
							<label class="control-label col-lg-2">{l s='Transition Out' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<select name="data_out_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_out">
									{foreach from=$transitions item=trans}
										<option {if $layer.data_out==$trans.id}selected{/if} value="{$trans.id|escape:'htmlall':'UTF-8'}">
											{$trans.name|escape:'htmlall':'UTF-8'}
										</option>
									{/foreach}
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Ease In' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<select name="data_ease_in_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_ease_in">
									{foreach from=$eases item=ease}
										<option {if $layer.data_ease_in==$ease.id}selected{/if} value="{$ease.id|escape:'htmlall':'UTF-8'}">{$ease.name|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							</div>
							<label class="control-label col-lg-2">{l s='Ease Out' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<select name="data_ease_out_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_ease_out">
									{foreach from=$eases item=ease}
										<option {if $layer.data_ease_out==$ease.id}selected{/if} value="{$ease.id|escape:'htmlall':'UTF-8'}">{$ease.name|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Step' mod='jmsslider'}</label>
							<div class="col-lg-4" >
								<input type="text" value="{$layer.data_step|escape:'htmlall':'UTF-8'}" name="data_step_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
							</div>
							
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Data special' mod='jmsslider'}</label>
							<div class="col-lg-4" >
								<select name="data_special_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_special">
									{foreach from=$data_specials item=data_special}
										<option {if $layer.data_special==$data_special.id}selected{/if} value="{$data_special.id|escape:'htmlall':'UTF-8'}">{$data_special.name|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
							</div>
						</div>
						{if $layer.data_type=='text'}
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Font Size' mod='jmsslider'}</label>
							<div class="col-lg-2">
								<div class="input-group">												
									<input type="text" id="data_font_size_{$layer.id_layer|escape:'htmlall':'UTF-8'}" name="data_font_size_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_font_size|escape:'htmlall':'UTF-8'}" class="data-font-size">
									<span class="input-group-addon">pixel</span>
								</div>
							</div>
							<label class="control-label col-lg-2">{l s='Line Height' mod='jmsslider'}</label>
							<div class="col-lg-2">
								<div class="input-group">												
									<input type="text" id="data_line_height_{$layer.id_layer|escape:'htmlall':'UTF-8'}" name="data_line_height_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_line_height|escape:'htmlall':'UTF-8'}" class="data-line-height">
									<span class="input-group-addon">pixel</span>
								</div>
							</div>
							<label class="control-label col-lg-1">{l s='Style' mod='jmsslider'}</label>
							<div class="col-lg-1">
								<div class="input-group">
									<select   class="data-style" name="data_style_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_style_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
										<option {if $layer.data_style=='normal'}selected{/if} value="normal">Normal</option>
										<option {if $layer.data_style=='bold'}selected{/if} value="bold">Bold</option>
										<option {if $layer.data_style=='italic'}selected{/if} value="italic">Italic</option>
									</select>											
								</div>
							</div>
							<label class="control-label col-lg-1">{l s='Color' mod='jmsslider'}</label>
								<div class="col-lg-1">
									<div class="row">
										<div class="input-group">
											 <input type="color"  class="data-color btn color mColorPickerInput" name="data_color_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_color_{$layer.id_layer|escape:'htmlall':'UTF-8'}" value="{$layer.data_color|escape:'htmlall':'UTF-8'}">
										</div>
									</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Html or Text' mod='jmsslider'}</label>
							<div class="col-lg-10">
								<textarea name="data_html_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_html_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-html" cols="30" rows="10">{$layer.data_html|escape:'htmlall':'UTF-8'|trim}</textarea>
							</div>	
						</div>
						
						{/if}
						{if $layer.data_type == 'image'}
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Image' mod='jmsslider'}</label>
							<div class="col-lg-4" >
			            		<select name="data_image_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_image_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-image">
			            			{foreach from=$images item=image}
			            				<option {if $layer.data_image=={$image.id|escape:'htmlall':'UTF-8'}}selected{/if}  value="{$image.id|escape:'htmlall':'UTF-8'}">{$image.id|escape:'htmlall':'UTF-8'}</option>
			            			{/foreach}
			            		</select>
			            	</div>
		            	</div>
						
						{/if}
						{if $layer.data_type == 'video'}
						<div class="form-group">
							<label class="control-label col-lg-2">
							<span data-original-title="Video Url" class="label-tooltip" data-toggle="tooltip" data-html="true" title="">{l s='Video Url' mod='jmsslider'}</span>
							</label>
							<div class="col-lg-10">
								<textarea name="data_video_{$layer.id_layer|escape:'htmlall':'UTF-8'}" id="data_video_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data-video" cols="30" rows="3">{$layer.data_video|escape:'htmlall':'UTF-8'|trim}</textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Autoplay' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<span class="switch prestashop-switch">
									<input type="radio" {if $layer.data_video_autoplay==1}checked="checked"{/if} value="1" id="data_video_autoplay_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on" name="data_video_autoplay_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_video_autoplay_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on">Yes</label>
									<input type="radio" {if $layer.data_video_autoplay==0}checked="checked"{/if} value="0" id="data_video_autoplay_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off" name="data_video_autoplay_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_video_autoplay_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off">No</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
							<label class="control-label col-lg-2">{l s='Controls' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<span class="switch prestashop-switch">
									<input type="radio" {if $layer.data_video_controls==1}checked="checked"{/if} value="1" id="data_video_controls_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on" name="data_video_controls_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_video_controls_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on">Yes</label>
									<input type="radio" {if $layer.data_video_controls==0}checked="checked"{/if} value="0" id="data_video_controls_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off" name="data_video_controls_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_video_controls_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off">No</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Loop' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<span class="switch prestashop-switch">
									<input type="radio" {if $layer.data_video_loop==1}checked="checked"{/if} value="1" id="data_video_loop_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on" name="data_video_loop_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_video_loop_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on">Yes</label>
									<input type="radio" {if $layer.data_video_loop==0}checked="checked"{/if} value="0" id="data_video_loop_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off" name="data_video_loop_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_video_loop_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off">No</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
							
						</div>
						<div class="form-group">
							<div class="col-lg-2">
							</div>
							<div class="col-lg-10">
								<input type="checkbox" {if $layer.data_video_bg==1}checked{/if} value="1" id="data_video_bg_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="data_video_bg" name="data_video_bg_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
								<label for="data_video_bg_{$layer.id_layer|escape:'htmlall':'UTF-8'}">{l s='Set this video for slide background' mod='jmsslider'}</label>
							</div>
						</div>
						{/if}
						<div class="form-group">
							<label class="control-label col-lg-2">{l s='Active' mod='jmsslider'}</label>
							<div class="col-lg-4">
								<span class="switch prestashop-switch fixed-width-lg">
									<input type="radio" {if $layer.data_status==1}checked="checked"{/if} value="1" id="data_status_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on" name="data_status_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_status_{$layer.id_layer|escape:'htmlall':'UTF-8'}_on">Yes</label>
									<input type="radio" {if $layer.data_status==0}checked="checked"{/if} value="0" id="data_status_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off" name="data_status_{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<label for="data_status_{$layer.id_layer|escape:'htmlall':'UTF-8'}_off">No</label>
									<a class="slide-button btn"></a>
								</span>
							</div>
						</div>
						
					</div>
					
					{/foreach}
					
					{/if}
				</div>
			</div>	
			<div id="layerContent" class="col-xs-12 col-sm-5 col-md-5 col-cmd-12 form-horizontal">
				<div class="panel panel-default">
					<div class="panel-heading">
						{l s='List layer' mod='jmsslider'}
					</div>
					<div class="panel-body clearfix">
						<div id="layers">
							{if $layers|@count gt 0}				
								{foreach from=$layers item=layer}
								<div id="layers_{$layer.id_layer|escape:'htmlall':'UTF-8'}" class="panel layer layers-{$layer.id_layer|escape:'htmlall':'UTF-8'}">
									<div class="row">
										<div class="col-lg-1">
											<span><i class="icon-arrows "></i></span>
										</div>
										<div class="col-md-6">							
											{$layer.data_title|escape:'html':'UTF-8'}
										</div>
										<div class="col-md-5">							
											<div class="btn-group-action pull-right">
												<div class="btn btn-default show-hide-layer" title="Click to display/hide layer"><i class="icon-eye"></i><i class="icon-eye-slash" style="display:none"></i></div>		
												<div class="btn btn-default disabled" title="format"><i {if $layer.data_type=="text"}class="icon-file-text-o" {elseif $layer.data_type=="image"} class="icon-file-image-o" {else}class="icon-file-movie-o"{/if}></i></div>

												<a title="Delete" id='delete_{$layer.id_layer|escape:'htmlall':'UTF-8'}' class="delete_layer btn btn-danger" 
												>	
													<i class="icon-trash"></i>
												</a>
											</div>
										</div>
									</div>
								</div>
								{/foreach}
							{else}
								{l s='There is no layer' mod='jmsslider'}
							{/if}
						</div>
					</div>
				</div>
			</div>		
		</div>
	</div>
	<div class="panel-footer">
		<button class="btn btn-default pull-right btn-success" name="submitLayer" id="module_form_submit_btn" value="1" type="submit">
			<i class="process-icon-save"></i> {l s='Save' mod='jmsslider'}
		</button>
		<a class="btn btn-default btn-warning" href="{$link->getAdminLink('AdminModules')|escape:'htmlall':'UTF-8'}&configure=jmsslider" title="{l s='Back to Slides List' mod='jmsslider'}"><i class="process-icon-back"></i>{l s='Back' mod='jmsslider'}</a>
	</div>

	<input type="hidden" name="slide_id" id="id_slide" value="{$slides.id_slide|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="layer_id" id="id_layer" value="{if $layers|@count>0}{$layers[0].id_layer|escape:'htmlall':'UTF-8'}{/if}" />
	<input type="hidden" name="site_url" id="site_url" value="{$root_url|escape:'htmlall':'UTF-8'}" />
	<input type="hidden" name="id_slide" value="{$slides.id_slide|escape:'htmlall':'UTF-8'}">
<div id="modal_add_text" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{l s='Add text or html' mod='jmsslider'}</h4>
            </div>
            <div class="modal-body">
            	<p>{l s='Title layer' mod='jmsslider'}</p>
            	<p><input type="text" id="title_text_new" name="title_text_new"></p>
            	<p>{l s='Text or HTML' mod='jmsslider'}</p>
            	<p><textarea name="data_html" id="text_layer_new" cols="30" rows="10"></textarea></p>
            </div>
        	<p id='loading' class="loading loading-text" style="text-align:center; display:none;"><img  src="{$root_url|escape:'htmlall':'UTF-8'}/modules/jmsslider/views/img/settings/loading.gif" alt="loading"></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitLayerText" name="submitLayer">Save</button>
            </div>
        </div>

    </div>
</div>
<div id="modal_add_video" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{l s='Add video (Youtube or Vimeo)' mod='jmsslider'}</h4>
            </div>
            <div class="modal-body">
            	<p>{l s='Title layer' mod='jmsslider'}</p>
            	<p><input type="text" id="title_video_new" name="title_video_new"></p>
            	<p>{l s='Video Url' mod='jmsslider'}</p>
            	<p><textarea name="data_video_new" id="data_video_new" cols="30" rows="3"></textarea></p>
            	<p class="help-block">Eg: https://www.youtube.com/watch?v=2PEG82Udb90 or https://vimeo.com/23259282</p>
            </div>
        	<p id='loading' class="loading loading-text" style="text-align:center; display:none;"><img  src="{$root_url|escape:'htmlall':'UTF-8'}/modules/jmsslider/views/img/settings/loading.gif" alt="loading"></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitLayerVideo" name="submitLayer">Save</button>
            </div>
        </div>
    </div>
</div> <!-- end modal video -->
<div id="modal_tips" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">{l s='TIPS' mod='jmsslider'}</h4>
            </div>
            <div class="modal-body">
	            <ul style="list-style-type:decimal">
	            	<li class="text-danger">{l s='To set background for slide, click on layer video then set delay = 0, check in box "Set this video for slide background"-->Save' mod='jmsslider'}</li>
	            	<li  class="text-primary">{l s='To mute video, click on volume video youtube, then click Save' mod='jmsslider'}</li>
	            	<li  class="text-danger">{l s='Typing "center" in data X or data Y to center align for layer' mod='jmsslider'}</li>
	            	<li class="text-primary">{l s='Typing "full/half/quarter" in width or data height to set size (full/half/quarter slide) quickly for layer' mod='jmsslider'}</li>
	            	<li  class="text-danger">{l s='Using show/hide layer function (icon eye) to easly working.' mod='jmsslider'}</li>
	            </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 
</form>
<!-- end first form  -->
<form id="form_add_layer" action="" method="post" enctype="multipart/form-data">
<div id="modal_add_image" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	                <h4 class="modal-title">{l s='Add Image' mod='jmsslider'}</h4>
	            </div>
	            <div class="modal-body">
	            	<p>{l s='Title layer' mod='jmsslider'}</p>
            		<p><input type="text" id="title_image_new" name="title_image_new"></p>
	            	<div id="image_action" class="form-group clearfix" >
	            		<label class="control-label col-lg-3">{l s='Type Action' mod='jmsslider'}</label>
						<div class="col-lg-9">
							<span class="switch prestashop-switch fixed-width-lg">
								<input type="radio" value="select" id="data_image_action_select" checked name="data_image_action">
								<label for="data_image_action_select">{l s='Select' mod='jmsslider'}</label>
								<input type="radio" value="upload" id="data_image_action_upload" name="data_image_action">
								<label for="data_image_action_upload">{l s='Upload' mod='jmsslider'}</label>
								<a class="slide-button btn"></a>
							</span>
						</div>
	            	</div>
	            	<div id="form_upload_image" class="form-group clearfix">
						<label class="control-label col-lg-3">{l s='Upload file' mod='jmsslider'}</label>
						<div class="col-sm-9">				
							<input type="file" class="hide" name="data_image" id="data_image">
							<div class="dummyfile input-group">
								<span class="input-group-addon"><i class="icon-file"></i></span>
								<input type="text" readonly="" name="filename" id="data_image-name">
								<span class="input-group-btn">
									<button class="btn btn-default" name="submitAddAttachments" type="button" id="data_image-selectbutton">
										<i class="icon-folder-open"></i> {l s='Choose a file' mod='jmsslider'}
									</button>
								</span>
							</div>		
						</div>
					</div>
	            	<div id="form_select_image" class="form-group  clearfix">
	            		<label class="control-label col-lg-3">{l s='Select image' mod='jmsslider'}</label>
	            		<select class="col-lg-9" name="data_s_image" id="data_s_image">
	            			{foreach from=$images item=image}
	            				<option  value="{$image.id|escape:'htmlall':'UTF-8'}">{$image.id|escape:'htmlall':'UTF-8'}</option>
	            			{/foreach}
	            		</select>
	            	</div>
	            	<div class="show-error" style="color:#ff0000"></div>
	            
	            	<p id='loading' class="loading loading-image" style="text-align:center; display:none;"><img  src="{$root_url|escape:'htmlall':'UTF-8'}/modules/jmsslider/views/img/settings/loading.gif" alt="loading"></p>
	            </div>
	            <div class="modal-footer clearfix">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	                <button type="submit" class="btn btn-primary" id="submitLayerImage" name="submitLayer">Save</button>
	            </div>
        </div>
    </div>
</div> <!-- end modal add image -->
</form>

