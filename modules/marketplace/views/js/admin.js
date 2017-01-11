$(document).ready(function(){

	//show terms and conditions on page load
	if (terms_and_condision_status == 1)
		$(".wk_mp_termsncond").show();
	else
		$(".wk_mp_termsncond").hide();

	//hide and show text according to switch
	$('label[for="MP_TERMS_AND_CONDITIONS_STATUS_on"]').on("click", function(){
		$(".wk_mp_termsncond").show();
	});

	$('label[for="MP_TERMS_AND_CONDITIONS_STATUS_off"]').on("click", function(){
		$(".wk_mp_termsncond").hide();
	});

	// seller details show/hide functionality
	if (show_seller_details == 1)
		$(".wk_mp_custom_seller_details").show();
	else
		$(".wk_mp_custom_seller_details").hide();

	//hide and show group according to switch
	$('label[for="MP_SHOW_SELLER_DETAILS_on"]').on("click", function(){
		$(".wk_mp_custom_seller_details").show();
	});

	$('label[for="MP_SHOW_SELLER_DETAILS_off"]').on("click", function(){
		$(".wk_mp_custom_seller_details").hide();
	});
});