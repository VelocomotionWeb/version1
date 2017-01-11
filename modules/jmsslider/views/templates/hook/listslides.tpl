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
<div class="panel">
	<h3>
	<span title="" data-toggle="tooltip" class="label-tooltip toogle" data-original-title="Click to Toggle" data-html="true">
		<i class="icon-list-ul"></i> {l s='Slides list' mod='jmsslider'}
	</span>
	
	<span class="panel-heading-action">
		<a  href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&addSlide=1" class="btn btn-default btn-success" id="addSlide" title="{l s='Add Slide' mod='jmsslider'}">
			<i class="icon-plus"></i>
		</a>
		
	</span>
	</h3>
	<script>
	$(document).ready(function(){
		$('.toogle').click(function(e){
			$('#slidesContent').toggle(200);
		});				
	});
				
	</script>
	<div id="slidesContent">
		<div id="slides">
			{if $slides|@count gt 0}
			{foreach from=$slides item=slide}
			<div id="slides_{$slide.id_slide|escape:'html':'UTF-8'}" class="panel">
				<div class="row">
					<div class="col-lg-1">
						<span><i class="icon-arrows "></i></span>
					</div>
					<div class="col-md-2">
						{$slide.title|escape:'html':'UTF-8'}
					</div>
					<div class="col-md-1">
						{if $slide.iso_lang}
						<strong>{l s='Language' mod='jmsslider'}</strong> : <i>{$slide.iso_lang|escape:'html':'UTF-8'}</i>
						{else}
						<i>{l s='All Language' mod='jmsslider'}</i>
						{/if}
					</div>
					<div class="col-lg-2">
						<span><strong>{l s='Home:' mod='jmsslider'}</strong></span>
						{if $slide.homes}
							{$slide.homes}
						{else}
							<span>{l s='All Home' mod='jmsslider'}</span>
						{/if}
						
					</div>
					<div class="col-md-2">
						<div class="btn-group-action pull-right">
							<a class="btn btn-default"
								href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&layers=1&id_slide={$slide.id_slide|escape:'html':'UTF-8'}">
								<i class="icon-edit"></i>
								{l s='Layers Manager' mod='jmsslider'}
							</a>
						</div>
					</div>
					<div class="col-md-4">
						<div class="btn-group-action pull-right">
							
							<a class="btn btn-default" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&copySlide=1&id_slide={$slide.id_slide|escape:'html':'UTF-8'}">
								<i class="icon-copy"></i>
								{l s='Duplicate' mod='jmsslider'}
							</a>
							<a class="btn {if $slide.status}btn-success{else}btn-danger{/if}"
								href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&changeStatus&id_slide={$slide.id_slide|escape:'html':'UTF-8'}" title="{if $slide.status}Enabled{else}Disabled{/if}">
								<i class="{if $slide.status}icon-check{else}icon-remove{/if}"></i>{if $slide.status}Enabled{else}Disabled{/if}
							</a>
							<a class="btn btn-default"
								href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&editSlide=1&id_slide={$slide.id_slide|escape:'html':'UTF-8'}">
								<i class="icon-edit"></i>
								{l s='Edit' mod='jmsslider'}
							</a>
							<a class="btn btn-default" onclick="if(confirm('Are you sure want to remove this slide?')) { document.location='{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsslider&delete_id_slide={$slide.id_slide|escape:'htmlall'}'; } else { return true;}"
								>
								<i class="icon-trash"></i>
								{l s='Delete' mod='jmsslider'}
							</a>
						</div>
					</div>
				</div>
			</div>
			{/foreach}
			{else}
			{l s='There is no slide' mod='jmsslider'}
			{/if}
		</div>
	</div>
</div>