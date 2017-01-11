<div class="tab-pane"  id="mp_product_seo">
	<div class="form-group">
		<label class="control-label {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-3 {/if}" for="meta_title">{l s='Meta Title : ' mod='mpproductseo'}</label>
		<div class="input-group {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-6 {/if}">
			<span class="input-group-addon">70</span>
	   		<input type="text" id="meta_title" name="meta_title" {if {isset($meta_info)|escape:'htmlall':'UTF-8'}}value="{$meta_info['meta_title']|escape:'htmlall':'UTF-8'}"{else}value=""{/if}  class="account_input form-control" />
	   	</div>
	   	<p class="help-block {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-offset-3 {/if}">{l s='Public title for the product\'s page, and for search engines. Leave blank to use the product name. The number of remaining characters is displayed to the left of the field.' mod='mpproductseo'}</p>
	</div>
	<div class="form-group">
		<label class="control-label {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-3 {/if}" for="meta_desc">{l s='Meta Description : ' mod='mpproductseo'}</label>
		<div class="input-group {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-6 {/if}">
			<span class="input-group-addon">160</span>
	    	<input type="text" id="meta_desc" name="meta_desc" {if {isset($meta_info)|escape:'htmlall':'UTF-8'}}value="{$meta_info['meta_description']|escape:'htmlall':'UTF-8'}"{else}value=""{/if}  class="account_input form-control" />
	    </div>
	    <p class="help-block {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-offset-3 {/if}">{l s='This description will appear in search engines. You need a single sentence, shorter than 160 characters (including spaces).' mod='mpproductseo'}</p>
	</div>
	<div class="form-group">
		<label class="control-label {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-3 {/if}" for="friendly_url">{l s='Friendly Url : ' mod='mpproductseo'}</label>
		<div class="{if {isset($admin)|escape:'htmlall':'UTF-8'}} input-group col-lg-6 {/if}">
	    	<input type="text" id="friendly_url" name="friendly_url" {if {isset($meta_info)|escape:'htmlall':'UTF-8'}}value="{$meta_info['friendly_url']|escape:'htmlall':'UTF-8'}"{else}value=""{/if}  class="account_input form-control" />
		</div>
	    <p class="help-block {if {isset($admin)|escape:'htmlall':'UTF-8'}} col-lg-offset-3 {/if}">{l s='This is the human-readable URL, as generated from the product\'s name. You can change it if you want.' mod='mpproductseo'}</p>
	</div>
</div>