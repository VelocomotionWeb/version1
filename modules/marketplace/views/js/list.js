$(document).ready(function(){
	$(document).on('change', '.selectMpProductSort', function(){
		var splitData = $(this).val().split(':');
		if ($(this).val() == 'id') //default sorting by last added product
			document.location.href = requestSortProducts;
		else
			document.location.href = requestSortProducts + ((requestSortProducts.indexOf('?') < 0) ? '?' : '&') + 'orderby=' + splitData[0] + '&orderway=' + splitData[1];
	});
});