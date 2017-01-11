{if isset($bestkit_bookings)}
<script type="text/javascript">
	{literal}
	(function($){
		$(document).ready(function(){
			{/literal}
			{foreach from=$bestkit_bookings item=_item}
				var elem = $('.cart_item[id^="product_{$_item.id_product|intval}_{$_item.id_product_attribute|intval}"]'); 
				if (elem.length) {
					var desc = elem.find('.cart_description');
					desc.find('.cart_booking').remove();
					desc.append('<small class="cart_booking">{l s='Booking : from' mod='bestkit_booking'} <u>{$_item.from|escape:'html':'UTF-8'}</u> {l s='to' mod='bestkit_booking'} <u>{$_item.to|escape:'html':'UTF-8'}</u></small>');
				}
			{/foreach}
			{literal}
		});
	})(jQuery);
	{/literal}
</script>
{/if}
