$(document).ready(function(){
	$(".mp_bulk_delete_btn").on("click", function(e){
		e.preventDefault();
		if (!$('.mp_bulk_select:checked').length)
		{
			alert(checkbox_select_warning);
			return false;
		}
		else
		{
			if(!confirm(confirm_delete_msg))
				return false;
			else
				$('#mp_productlist_form').submit();
		}
	});

	$("#mp_all_select").on("click", function(){
		if ($(this).is(':checked'))
		{
			$('.mp_bulk_select').parent().addClass('checker checked');
			$('.mp_bulk_select').attr('checked', 'checked');
		}
		else
		{
			$('.mp_bulk_select').parent().removeClass('checker checked');
			$('.mp_bulk_select').removeAttr('checked');
		}
	});

	$(".delete_img").on("click", function(){			
		if(!confirm(confirm_delete_msg))
			return false;
	});

	$('.fancybox').fancybox();

	if ($("#mp_product_list").length)
	{
		$('#mp_product_list').DataTable({
	        "language": {
	            "lengthMenu": "Display _MENU_ records per page",
	            "zeroRecords": "No product found",
	            "info": "Showing page _PAGE_ of _PAGES_",
	            "infoEmpty": "No records available",
	            "infoFiltered": "(filtered from _MAX_ total records)"
	        }
	    });
	}


	/* code for remoce sorting from datatable 
			"aoColumnDefs": [
			  { 'bSortable': false, 'aTargets': [-1,0,1,2,7,8] }
			],
		working but effecting to other codes
	*/

	// click on plus/minus image image edit column
	$(document).on('click', '.edit_seq', function(e) {
		e.preventDefault();
		var is_alt = $(this).attr("alt");
		var id_product = $(this).attr("product-id");
		$(".edit_seq").attr("alt","1");
		$(".content_seq").hide();
		$(".content_seq").empty();
		$(".img_detail").attr('src',img_ps_dir+'admin/more.png');
		if(is_alt == 1)
		{
			$("#edit_seq"+id_product).attr('src',img_ps_dir+'admin/less.png');
			$(this).attr("alt","0");
			$("#content"+id_product).show();
			$.ajax({
				type: 'POST',
				url: ajax_urlpath,
				data: {
					id_product: id_product,
					id_lang: id_lang,
					image_type: 'cart_default'
				},
				cache: 	true,
				success: function(data)
				{
					if(data != 0)
						$('#content'+id_product).html(data);
					else
						alert(space_error);
				}
			});
		}
		else
		{
			$(this).attr("alt","1");
			$("#content"+id_product).hide();
			$("#content"+id_product).empty();
		}
	});

	// delete active product image
	$(document).on('click', '.delete_pro_image', function(e) {
		e.preventDefault();
		var id_image = $(this).attr('id_image');
		var is_cover = $(this).attr('is_cover');
		var id_pro = $(this).attr('id_pro');
		if (confirm(confirm_delete_msg))
		{
			$.ajax({
				type: 'POST',
				url: ajax_urlpath,
				data: {
					id_image : id_image,
					is_cover: is_cover,
					id_pro: id_pro,
					is_delete: "1"
				},
				cache: true,
				success: function(data)
				{
					if (data == 0)
						alert(error_msg);
					else if (data == 1)
						$(".imageinforow"+id_image).remove();
					else if (data == 2)
						location.reload();
				}
			});
		
		}
	});

	// delete unactive product image
	$(document).on('click', '.delete_unactive_pro_image', function(e) {
		e.preventDefault();
		var id_image = $(this).attr('id_image');
		var img_name = $(this).attr('img_name');
		if (confirm(confirm_delete_msg))
		{
			$.ajax({
				type: 'POST',
				url:  ajax_urlpath,
				data: {
					id_image: id_image,
					img_name: img_name,
					unactive: "1"
				},
				cache: true,
				success: function(data)
				{
					if(data == 0)
						alert(error_msg);
					else
					{
						alert(delete_msg);
						$(".unactiveimageinforow"+id_image).remove();
					}
				}
			});
		}
		
	});

	// make image as cover
	$(document).on('click', '.covered', function(e) {
		e.preventDefault();
		var id_image = $(this).attr('alt');
		var is_cover = $(this).attr('is_cover');
		var id_pro = $(this).attr('id_pro');
		var prod_detail = $(this).attr('prod_detail'); // only if call from marketplace product details page
		if(is_cover == 0) 
		{
			$.ajax({
				type: 'POST',
				url:  ajax_urlpath,
				data: {
					id_image: id_image,
					is_cover: is_cover,
					id_pro: id_pro,
					changecover: "1"
				},
				cache: true,
				success: function(data)
				{
					if(data == 0)
						alert(error_msg);
					else
					{
						if(is_cover == 0)
						{
							$('.covered').attr('src', img_ps_dir+'admin/forbbiden.gif');
							$('.covered').attr('is_cover','0')
							$('#changecoverimage'+id_image).attr('src', img_ps_dir+'admin/enabled.gif')
							$('#changecoverimage'+id_image).attr('is_cover','1');
							if (typeof prod_detail != 'undefined') //reload for show cover image on page
								location.reload(true);
						}
					}
				}
			});
		}
	});
});