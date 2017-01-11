$(document).ready(function()
{
	$('.mp_search_box').prop( "autocomplete", "off" );     /*browser autocomplete off*/
	$('body').on('keyup', '.mp_search_box',function(event)
	{
		if (($(".mp_search_sugg").html()) && (event.which == 40 || event.which == 38))
		{
			event.preventDefault();
			$(this).focusout();

			if (event.which == 40)
				$(".mp_search_sugg").find('li').first().find('a').attr('tabindex',0).focus();
			else if (event.which == 38)
				$(".mp_search_sugg").find('li').last().find('a').attr('tabindex',0).focus();
		}
		else
		{
			$(".mp_search_sugg").html('');
			var xhr = null;
			var key_word = '';
			key_word = $(this).val();
			var search_for = $('.search_value').data('value');
			var data = {key:key_word, search_type:search_for};
			if (xhr)
				xhr.abort();
			if (key_word) 
			{
				xhr = $.ajax({
					url: ajaxsearch_url,
					type: 'POST',
					dataType: 'json',
					data: data,
					success: function (result) 
					{
						if (result)
						{
							$.each(result, function(key, value)
							{
								console.log(value);
								$(".mp_search_sugg").show().append("<li><a href='"+shop_store_link+"&mp_shop_name="+value.shop_link_rewrite+"' class='search_a'>"+value.mp_seller_name+", "+value.mp_shop_name+"</a></li>");
							});
						}
						else
						{
							$(".mp_search_sugg").hide();
						}
					},
					error: function(error)
					{
						console.log(error);
					}
				});
			}
			else
				$(".mp_search_sugg").hide();
		}
	});

	$('body').on('click', function(event)
	{
		$(".mp_search_sugg").hide().html('');
	});

	$('.mp_search_sugg').on('click',function(event)
	{
		event.stopPropagation();
	});

	$('.search_category').on('click', function(e)
	{
		e.preventDefault();
		var search_value = $(this).data('value');
		var search_for = $(this).html();
		
		$('#search_for').html(search_for);
		$('.search_value').data('value', search_value);
		$('#dropdownMenu1').attr('data-value', search_value);
	});

	$('body').on('keyup', '.search_a', function(event)
	{
		if (event.which == 40 || event.which == 38) 
		{
			$(this).focusout();
			if (event.which == 40)
			{
				if ($(".mp_search_sugg").find('li').last() == $(this).parent())
				{
					$(".mp_search_sugg").find('li').first().find('a').attr('tabindex',0).focus();
				}
				else
				{
					$(this).parent().next().find('a').attr('tabindex',0).focus()
				}
			}
			else if (event.which == 38)
			{
				if ($(".mp_search_sugg").find('li').first() == $(this).parent())
				{
					$(".mp_search_sugg").find('li').last().find('a').attr('tabindex',0).focus();
				}
				else
				{
					$(this).parent().prev().find('a').attr('tabindex',0).focus()
				}
			}
		}
	});

	/*$(document).on('keydown','body', function (e) 
	{
		if((e.which == 40 || e.which == 38) && $('.search_a').is(':focus'))
		{
			e.preventDefault();
			return false;
		}
	});*/
	
	// sorting product based on product name price or asc desc order
	$('#wk_orderby').on('change', function(){
		if ($('#wk_orderby option:selected').index() == '1')
			ajaxsort_url = ajaxsort_url+"?orderby=price&orderway=asc";
		else if ($('#wk_orderby option:selected').index() == '2')
			ajaxsort_url = ajaxsort_url+"?orderby=price&orderway=desc";
		else if ($('#wk_orderby option:selected').index() == '3')
			ajaxsort_url = ajaxsort_url+"?orderby=name&orderway=asc";
		else if ($('#wk_orderby option:selected').index() == '4')
			ajaxsort_url = ajaxsort_url+"?orderby=name&orderway=desc";
		else
			ajaxsort_url = ajaxsort_url;
		window.location.href = ajaxsort_url;
	});

	// view more product
	$('#wk-more-product').on('click', function(e){
		e.preventDefault();
		$('div.wk_view_more').css('margin-bottom','65px');
		$('.btn-all').css('display', 'none');
		$('.view-more-img').css('display', 'block');
		var lastid = $(this).parent().attr('id');
		var orderby = $('#orderby').val();
		var orderway = $('#orderway').val();
		if (!orderby)
			orderby = 0;
		if (!orderway)
			orderway = 0;
		var moreproductid = +lastid+8;
		$.ajax({
			url : viewmore_url,
			type : 'POST',
			cache : false,
			data : {
				nextid : lastid,
				orderby : orderby,
				orderway : orderway,
			},
			success : function(data)
			{
				if (data == 0)
				{
					$('.view-more-img').css('display', 'none');
					$('.btn-all').css({'display':'inline-block','pointer-events':'none'}).text('No More Products');
					$('div.wk_view_more').css('margin-bottom','25px');
				}
				else if (data)
				{
					$('.view-more-img').css('display', 'none');
					$('.btn-all').css('display', 'inline-block');
					$('div.wk_view_more').css('margin-bottom','25px');
					$(data).insertBefore(".wk_view_more");
					$('.wk_view_more').attr('id', moreproductid);
				}
			}
		});
	});
	
	// seller search with seller shop and seller name
	$('#mpseller-search').on('click', function(){
		var key = $('#seller-search').val();
		var orderby = $('#dropdownMenu1').attr('data-value');
		key = $.trim(key);
		if (key && orderby)
		{
			if (orderby == 1)
				search_url = viewmorelist_link+'&orderby=seller_name&name='+key;
			else if (orderby == 2)
				search_url = viewmorelist_link+'&orderby=shop_name&name='+key;
			else if (orderby == 3)
				search_url = viewmorelist_link+'&orderby=address&name='+key;
			
			window.location.href = search_url;
		}
		else
		{
			$('#seller-search').css('border-color', 'red');
			return;
		}
	});
});