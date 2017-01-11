$(function(){
	$('.open-review-form').fancybox({
		width: 600,
	    height: 310,
	    autoSize : false,
	    maxWidth : '100%',
		'hideOnContentClick': false
	});

	$(document).on('click', '#review_submit .closefb', function(e){
		e.preventDefault();
		$.fancybox.close();
	});

	$(".forloginuser").on("click", function(){
		if (typeof logged !== 'undefined' && !logged)
		{
			alert(not_logged_msg);
			return false;
		}
		else if (url_mp_shop_name == login_mp_shop_name)
		{
			alert(review_yourself_msg);
			return false;
		}
	});
});